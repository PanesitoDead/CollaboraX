<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SuscripcionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SuscripcionController extends Controller
{
    private $suscripcionService;

    public function __construct(SuscripcionService $suscripcionService)
    {
        $this->suscripcionService = $suscripcionService;
    }

    /**
     * Obtener datos para la vista de configuración (sección suscripciones)
     * Ahora usa el nuevo endpoint de resumen para mejor rendimiento
     */
    public function obtenerDatosSuscripcion()
    {
        try {
            $usuario = Auth::user();
            $usuarioId = $usuario->id;
            
            // Usar el nuevo endpoint de resumen completo que es más eficiente
            $resumenCompleto = $this->suscripcionService->obtenerResumenUsuario($usuarioId);
            
            if ($resumenCompleto) {
                // Obtener planes disponibles en paralelo
                $planesDisponibles = $this->suscripcionService->obtenerPlanes();
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'planes_disponibles' => $planesDisponibles,
                        'suscripcion_activa' => $resumenCompleto['suscripcion_activa'] ?? null,
                        'tiene_suscripcion_activa' => $resumenCompleto['tiene_suscripcion_activa'] ?? false,
                        'dias_restantes' => $resumenCompleto['dias_restantes'] ?? 0,
                        'historial_pagos' => $resumenCompleto['historial_pagos'] ?? [],
                        'historial_suscripciones' => $resumenCompleto['historial_suscripciones'] ?? [],
                        'paginacion' => [
                            'pagina_actual' => 1,
                            'total_paginas' => 1,
                            'total_elementos' => count($resumenCompleto['historial_pagos'] ?? []),
                            'elementos_por_pagina' => 5
                        ]
                    ]
                ]);
            }
            
            // Fallback al método anterior si el resumen no está disponible
            $planesDisponibles = $this->suscripcionService->obtenerPlanes();
            $suscripcionActual = $this->suscripcionService->obtenerSuscripcionActual($usuarioId);
            $historialResult = $this->suscripcionService->obtenerHistorialPagos($usuarioId, 1, 5);

            return response()->json([
                'success' => true,
                'data' => [
                    'planes_disponibles' => $planesDisponibles,
                    'suscripcion_activa' => $suscripcionActual,
                    'historial_pagos' => $historialResult['pagos'],
                    'paginacion' => $historialResult['paginacion']
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener datos de suscripción: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos de suscripción'
            ], 500);
        }
    }

    /**
     * Obtener planes disponibles
     */
    public function obtenerPlanes()
    {
        try {
            $planes = $this->suscripcionService->obtenerPlanes();
            
            return response()->json([
                'success' => true,
                'data' => $planes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener planes'
            ], 500);
        }
    }

    /**
     * Obtener suscripción activa del usuario autenticado
     */
    public function obtenerSuscripcionActual()
    {
        try {
            $usuario = Auth::user();
            $usuarioId = $usuario->id;
            
            Log::info('Obteniendo suscripción actual para usuario', ['usuario_id' => $usuarioId]);
            
            $suscripcionActual = $this->suscripcionService->obtenerSuscripcionActual($usuarioId);
            
            if ($suscripcionActual) {
                return response()->json([
                    'success' => true,
                    'data' => $suscripcionActual
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró suscripción activa',
                    'data' => null
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al obtener suscripción actual: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener suscripción actual'
            ], 500);
        }
    }

    /**
     * Crear preferencia de pago en MercadoPago
     */
    public function crearPreferenciaPago(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $usuario = Auth::user();
            $usuarioId = $usuario->id;
            $planId = $request->input('plan_id');
            
            // Log para debugging
            Log::info('Usuario autenticado creando preferencia', [
                'usuario_id' => $usuarioId,
                'plan_id' => $planId,
                'user_email' => $usuario->correo ?? 'sin email'
            ]);
            
            $datosAdicionales = [
                'usuario_email' => $usuario->correo ?? '',
                'usuario_nombre' => $usuario->correo ?? '' // Usando correo como identificador
            ];

            $resultado = $this->suscripcionService->crearPreferenciaPago($usuarioId, $planId, $datosAdicionales);

            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $resultado['message'],
                    'data' => $resultado['data'],
                    'redirect_url' => $resultado['data']['init_point'] ?? null
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'],
                    'errors' => $resultado['errors'] ?? []
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error al crear preferencia de pago: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Manejar webhook desde la API externa (no desde MercadoPago directamente)
     */
    public function webhookPago(Request $request)
    {
        try {
            $data = $request->all();
            
            // Log del webhook para debugging
            Log::info('Webhook de pago recibido:', $data);
            
            // Aquí podrías procesar las notificaciones que vengan de tu API
            // Por ejemplo, limpiar cache de suscripciones si hay cambios
            if (isset($data['usuario_id'])) {
                Cache::forget("suscripcion_actual_{$data['usuario_id']}");
            }
            
            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('Error en webhook de pago: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno'], 500);
        }
    }

    /**
     * Obtener historial de pagos
     */
    public function obtenerHistorialPagos(Request $request)
    {
        try {
            $usuario = Auth::user();
            $usuarioId = $usuario->id;
            $pagina = $request->input('pagina', 1);
            $limite = $request->input('limite', 10);

            $resultado = $this->suscripcionService->obtenerHistorialPagos($usuarioId, $pagina, $limite);

            return response()->json([
                'success' => true,
                'data' => $resultado['pagos'],
                'paginacion' => $resultado['paginacion']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial de pagos'
            ], 500);
        }
    }

    /**
     * Verificar estado de un pago
     */
    public function verificarEstadoPago($pagoId)
    {
        try {
            $estado = $this->suscripcionService->verificarEstadoPago($pagoId);
            
            if ($estado) {
                return response()->json([
                    'success' => true,
                    'data' => $estado
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo verificar el estado del pago'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar estado del pago'
            ], 500);
        }
    }

    /**
     * Cancelar suscripción actual
     */
    public function cancelarSuscripcion(Request $request)
    {
        try {
            $usuario = Auth::user();
            $usuarioId = $usuario->id;
            $suscripcionId = $request->input('suscripcion_id');

            Log::info('=== CONTROLADOR: Cancelación solicitada ===', [
                'usuario_id' => $usuarioId,
                'suscripcion_id' => $suscripcionId,
                'request_data' => $request->all()
            ]);

            if (!$suscripcionId) {
                Log::warning('ID de suscripción faltante en request');
                return response()->json([
                    'success' => false,
                    'message' => 'ID de suscripción requerido'
                ], 422);
            }

            $resultado = $this->suscripcionService->cancelarSuscripcion($usuarioId, $suscripcionId);
            
            Log::info('Resultado del servicio de cancelación', ['resultado' => $resultado]);

            return response()->json($resultado);
        } catch (\Exception $e) {
            Log::error('Error en controlador al cancelar suscripción: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al cancelar suscripción'
            ], 500);
        }
    }

    /**
     * Descargar comprobante de pago - Redireccionar a la API externa
     */
    public function descargarComprobante($pagoId)
    {
        try {
            // Redirigir directamente a la API externa para descargar el comprobante
            $url = env('PAGOS_MICROSERVICE_URL', 'http://34.173.216.37:3000') . "/api/pagos/{$pagoId}/comprobante";
            return redirect($url);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener comprobante'
            ], 500);
        }
    }

    /**
     * Renovar manualmente una suscripción
     */
    public function renovarSuscripcion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'suscripcion_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ID de suscripción requerido',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $suscripcionId = $request->input('suscripcion_id');
            $resultado = $this->suscripcionService->renovarSuscripcion($suscripcionId);

            return response()->json($resultado);
        } catch (\Exception $e) {
            Log::error('Error al renovar suscripción: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al renovar suscripción'
            ], 500);
        }
    }

    /**
     * Cancelar renovación automática
     */
    public function cancelarRenovacionAutomatica(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'suscripcion_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ID de suscripción requerido',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $suscripcionId = $request->input('suscripcion_id');
            $resultado = $this->suscripcionService->cancelarRenovacionAutomatica($suscripcionId);

            return response()->json($resultado);
        } catch (\Exception $e) {
            Log::error('Error al cancelar renovación automática: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar renovación automática'
            ], 500);
        }
    }

    /**
     * Activar renovación automática
     */
    public function activarRenovacionAutomatica(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'suscripcion_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ID de suscripción requerido',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $suscripcionId = $request->input('suscripcion_id');
            $resultado = $this->suscripcionService->activarRenovacionAutomatica($suscripcionId);

            return response()->json($resultado);
        } catch (\Exception $e) {
            Log::error('Error al activar renovación automática: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al activar renovación automática'
            ], 500);
        }
    }

    /**
     * Cambiar estado de renovación automática (método genérico)
     * Nuevo método unificado según API v1.1.0
     */
    public function cambiarRenovacionAutomatica(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'suscripcion_id' => 'required|integer',
            'renovacion_automatica' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos requeridos: suscripcion_id y renovacion_automatica',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $suscripcionId = $request->input('suscripcion_id');
            $activar = $request->boolean('renovacion_automatica');
            
            if ($activar) {
                $resultado = $this->suscripcionService->activarRenovacionAutomatica($suscripcionId);
            } else {
                $resultado = $this->suscripcionService->cancelarRenovacionAutomatica($suscripcionId);
            }

            // Limpiar cache del usuario para forzar actualización de datos
            if (isset($resultado['success']) && $resultado['success']) {
                $usuario = Auth::user();
                $usuarioId = $usuario->id;
                Cache::forget("resumen_usuario_{$usuarioId}");
                Log::info("Cache limpiado para usuario {$usuarioId} después de cambiar renovación automática");
            }

            return response()->json($resultado);
        } catch (\Exception $e) {
            Log::error('Error al cambiar renovación automática: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar renovación automática'
            ], 500);
        }
    }

    /**
     * Obtener resumen completo del usuario
     */
    public function obtenerResumenCompleto()
    {
        try {
            $usuario = Auth::user();
            $usuarioId = $usuario->id;
            
            // Verificar si se solicita actualización forzada
            $forzarActualizacion = request()->boolean('forzar_actualizacion', true); // Por defecto forzar para obtener datos frescos

            $resumen = $this->suscripcionService->obtenerResumenUsuario($usuarioId, $forzarActualizacion);

            if ($resumen) {
                return response()->json([
                    'success' => true,
                    'data' => $resumen,
                    'message' => $forzarActualizacion ? 'Datos actualizados desde el servidor' : 'Datos obtenidos correctamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo obtener el resumen del usuario'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al obtener resumen completo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen del usuario'
            ], 500);
        }
    }

    /**
     * Limpiar cache del usuario para forzar actualización
     */
    public function limpiarCache()
    {
        try {
            $usuario = Auth::user();
            $usuarioId = $usuario->id;
            
            $this->suscripcionService->limpiarCacheUsuario($usuarioId);
            
            return response()->json([
                'success' => true,
                'message' => 'Cache limpiado correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al limpiar cache: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar cache'
            ], 500);
        }
    }

    /**
     * Verificar suscripciones vencidas (método administrativo)
     */
    public function verificarSuscripcionesVencidas()
    {
        try {
            $resultado = $this->suscripcionService->verificarSuscripcionesVencidas();
            return response()->json($resultado);
        } catch (\Exception $e) {
            Log::error('Error al verificar suscripciones vencidas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar suscripciones'
            ], 500);
        }
    }
}
