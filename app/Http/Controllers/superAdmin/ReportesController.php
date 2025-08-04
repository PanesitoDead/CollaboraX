<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Usuario;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportesController extends Controller
{
    private $client;
    private $baseUrl;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false
        ]);
        $this->baseUrl = env('PAGOS_MICROSERVICE_URL', 'http://34.173.216.37:3000/api');
    }

    /**
     * Vista principal de reportes
     */
    public function index()
    {
        try {
            // Estadísticas básicas para el resumen
            $totalEmpresas = Empresa::count();
            $totalUsuarios = Usuario::count();
            $usuariosActivos = Usuario::where('activo', true)->count();
            $empresasActivas = Empresa::whereNull('deleted_at')->count();

            $resumenBasico = [
                'total_empresas' => $totalEmpresas,
                'empresas_activas' => $empresasActivas,
                'usuarios_totales' => $totalUsuarios,
                'usuarios_activos' => $usuariosActivos,
            ];

            return view('super-admin.reportes.index', compact('resumenBasico'));
            
        } catch (\Exception $e) {
            Log::error('Error cargando vista de reportes: ' . $e->getMessage());
            return view('super-admin.reportes.index', ['resumenBasico' => []]);
        }
    }

    /**
     * Obtener reporte de usuarios
     */
    public function reporteUsuarios(Request $request)
    {
        try {
            $formato = $request->get('formato', 'tabla');
            $periodo = $request->get('periodo', 'mes-actual');
            
            $query = Usuario::with(['empresa', 'trabajador']);
            
            // Aplicar filtros de período
            if ($periodo !== 'todo') {
                $fechaInicio = $this->calcularFechaInicio($periodo);
                if ($fechaInicio) {
                    $query->where('fecha_registro', '>=', $fechaInicio);
                }
            }

            $usuarios = $query->orderBy('fecha_registro', 'desc')->get();

            $data = [
                'usuarios' => $usuarios->map(function ($usuario) {
                    // Obtener el nombre del usuario (puede ser empresa, trabajador o usuario directo)
                    $nombre = $usuario->nombre;
                    
                    // Si no tiene nombre pero es un trabajador, usar sus datos completos
                    if (!$nombre && $usuario->trabajador) {
                        $nombreCompleto = trim(
                            ($usuario->trabajador->nombres ?? '') . ' ' . 
                            ($usuario->trabajador->apellido_paterno ?? '') . ' ' . 
                            ($usuario->trabajador->apellido_materno ?? '')
                        );
                        if ($nombreCompleto) {
                            $nombre = $nombreCompleto;
                        }
                    }
                    
                    // Si no tiene nombre pero tiene empresa asociada, usar el nombre de la empresa
                    if (!$nombre && $usuario->empresa) {
                        $nombre = $usuario->empresa->nombre;
                    }
                    
                    // Fallback final
                    if (!$nombre) {
                        $nombre = 'Usuario sin nombre';
                    }
                    
                    // Obtener la empresa del usuario
                    $empresaNombre = 'No asignado';
                    
                    // Primero verificar si el usuario tiene empresa directamente
                    if ($usuario->empresa) {
                        $empresaNombre = $usuario->empresa->nombre;
                    }
                    // Si no tiene empresa directa pero es trabajador con empresa_id
                    elseif ($usuario->trabajador && $usuario->trabajador->empresa_id) {
                        $empresaTrabajador = $usuario->trabajador->empresa;
                        if ($empresaTrabajador) {
                            $empresaNombre = $empresaTrabajador->nombre;
                        }
                    }
                    // Si es trabajador y pertenece a un área con empresa
                    elseif ($usuario->trabajador) {
                        $area = $usuario->trabajador->area_from_colab;
                        if ($area && $area->empresa) {
                            $empresaNombre = $area->empresa->nombre;
                        }
                    }
                    
                    return [
                        'id' => $usuario->id,
                        'nombre' => $nombre,
                        'correo' => $usuario->correo,
                        'rol' => $usuario->rol->nombre ?? 'No asignado',
                        'empresa' => $empresaNombre,
                        'activo' => $usuario->activo ? 'Activo' : 'Inactivo',
                        'fecha_registro' => Carbon::parse($usuario->fecha_registro)->format('d/m/Y H:i'),
                        'ultima_conexion' => $usuario->ultima_conexion 
                            ? Carbon::parse($usuario->ultima_conexion)->format('d/m/Y H:i')
                            : 'Nunca'
                    ];
                }),
                'total_usuarios' => $usuarios->count(),
                'fecha_generacion' => now()->format('d/m/Y H:i:s'),
                'periodo' => $periodo
            ];

            if ($request->get('export') === 'pdf') {
                return $this->exportarUsuariosPDF($data);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo reporte de usuarios: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte de usuarios'
            ], 500);
        }
    }

    /**
     * Obtener reporte de ingresos usando la API de MercadoPago
     */
    public function reporteIngresos(Request $request)
    {
        try {
            $formato = $request->get('formato', 'tabla');
            $periodo = $request->get('periodo', 'mes-actual');
            $agrupacion = $request->get('agrupacion', 'fecha');
            $planId = $request->get('plan_id');
            $estado = $request->get('estado');
            $fechaInicio = $request->get('fecha_inicio');
            $fechaFin = $request->get('fecha_fin');

            // Construir URL de la API
            $endpoint = '/reportes/ingresos';
            $params = [
                'formato' => $formato,
                'periodo' => $periodo,
                'agrupacion' => $agrupacion
            ];

            if ($planId) $params['plan_id'] = $planId;
            if ($estado) $params['estado'] = $estado;
            if ($fechaInicio) $params['fecha_inicio'] = $fechaInicio;
            if ($fechaFin) $params['fecha_fin'] = $fechaFin;

            $queryString = http_build_query($params);
            $url = $this->baseUrl . $endpoint . '?' . $queryString;

            $response = $this->client->get($url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (isset($responseData['success']) && $responseData['success']) {
                if ($request->get('export') === 'pdf') {
                    return $this->exportarIngresosPDF($responseData['data'], $agrupacion);
                }

                return response()->json($responseData);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error en la API de reportes de ingresos'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error obteniendo reporte de ingresos: ' . $e->getMessage());
            
            // Datos de respaldo
            $datosRespaldo = $this->obtenerDatosIngresosRespaldo($agrupacion);
            
            if ($request->get('export') === 'pdf') {
                return $this->exportarIngresosPDF($datosRespaldo, $agrupacion);
            }

            return response()->json([
                'success' => true,
                'data' => $datosRespaldo,
                'message' => 'Datos de demostración (API no disponible)'
            ]);
        }
    }

    /**
     * Obtener reporte de transacciones
     */
    public function reporteTransacciones(Request $request)
    {
        try {
            $limite = $request->get('limite', 10);
            
            $endpoint = '/reportes/transacciones?limite=' . $limite;
            $url = $this->baseUrl . $endpoint;

            $response = $this->client->get($url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (isset($responseData['success']) && $responseData['success']) {
                if ($request->get('export') === 'pdf') {
                    return $this->exportarTransaccionesPDF($responseData['data']);
                }

                return response()->json($responseData);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error en la API de reportes de transacciones'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error obteniendo reporte de transacciones: ' . $e->getMessage());
            
            // Datos de respaldo
            $datosRespaldo = $this->obtenerDatosTransaccionesRespaldo();
            
            if ($request->get('export') === 'pdf') {
                return $this->exportarTransaccionesPDF($datosRespaldo);
            }

            return response()->json([
                'success' => true,
                'data' => $datosRespaldo,
                'message' => 'Datos de demostración (API no disponible)'
            ]);
        }
    }

    /**
     * Obtener reporte de rendimiento de planes
     */
    public function reporteRendimientoPlanes(Request $request)
    {
        try {
            $endpoint = '/reportes/rendimiento-planes';
            $url = $this->baseUrl . $endpoint;

            $response = $this->client->get($url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (isset($responseData['success']) && $responseData['success']) {
                if ($request->get('export') === 'pdf') {
                    return $this->exportarRendimientoPDF($responseData['data']);
                }

                return response()->json($responseData);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error en la API de reportes de rendimiento'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error obteniendo reporte de rendimiento: ' . $e->getMessage());
            
            // Datos de respaldo
            $datosRespaldo = $this->obtenerDatosRendimientoRespaldo();
            
            if ($request->get('export') === 'pdf') {
                return $this->exportarRendimientoPDF($datosRespaldo);
            }

            return response()->json([
                'success' => true,
                'data' => $datosRespaldo,
                'message' => 'Datos de demostración (API no disponible)'
            ]);
        }
    }

    // Métodos auxiliares para cálculos de fechas y PDFs

    private function calcularFechaInicio($periodo)
    {
        switch ($periodo) {
            case 'mes-actual':
                return Carbon::now()->startOfMonth();
            case 'ultimos-3-meses':
                return Carbon::now()->subMonths(3);
            case 'ultimos-6-meses':
                return Carbon::now()->subMonths(6);
            case 'anual':
                return Carbon::now()->startOfYear();
            default:
                return null;
        }
    }

    private function exportarUsuariosPDF($data)
    {
        $pdf = Pdf::loadView('super-admin.reportes.pdf.usuarios', compact('data'));
        return $pdf->download('reporte_usuarios_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    private function exportarIngresosPDF($data, $agrupacion)
    {
        $pdf = Pdf::loadView('super-admin.reportes.pdf.ingresos', compact('data', 'agrupacion'));
        return $pdf->download('reporte_ingresos_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    private function exportarTransaccionesPDF($data)
    {
        $pdf = Pdf::loadView('super-admin.reportes.pdf.transacciones', compact('data'));
        return $pdf->download('reporte_transacciones_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    private function exportarRendimientoPDF($data)
    {
        $pdf = Pdf::loadView('super-admin.reportes.pdf.rendimiento', compact('data'));
        return $pdf->download('reporte_rendimiento_planes_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    // Datos de respaldo en caso de que la API falle

    private function obtenerDatosIngresosRespaldo($agrupacion)
    {
        if ($agrupacion === 'plan') {
            return [
                'configuracion' => [
                    'formato' => 'tabla',
                    'periodo' => 'mes-actual',
                    'agrupacion' => 'plan'
                ],
                'filas' => [
                    [
                        'plan_id' => 1,
                        'plan_nombre' => 'Plan Standard',
                        'plan_precio' => '29.90',
                        'plan_frecuencia' => 'mensual',
                        'total_transacciones' => 1,
                        'pagos_aprobados' => 1,
                        'pagos_pendientes' => 0,
                        'pagos_rechazados' => 0,
                        'ingresos_aprobados' => '29.90',
                        'ingresos_pendientes' => '0.00',
                        'tasa_aprobacion' => '100.00',
                        'ticket_promedio' => '29.90'
                    ]
                ],
                'resumen_periodo' => [
                    'total_transacciones_periodo' => 1,
                    'total_pagos_aprobados' => 1,
                    'ingresos_totales_periodo' => '29.90'
                ],
                'fecha_generacion' => now()->toISOString()
            ];
        }

        return [
            'configuracion' => [
                'formato' => 'tabla',
                'periodo' => 'mes-actual',
                'agrupacion' => 'fecha'
            ],
            'filas' => [
                [
                    'fecha' => now()->toDateString(),
                    'total_transacciones' => 1,
                    'pagos_aprobados' => 1,
                    'ingresos_aprobados' => '29.90',
                    'ticket_promedio' => '29.90'
                ]
            ],
            'resumen_periodo' => [
                'total_transacciones_periodo' => 1,
                'ingresos_totales_periodo' => '29.90'
            ],
            'fecha_generacion' => now()->toISOString()
        ];
    }

    private function obtenerDatosTransaccionesRespaldo()
    {
        return [
            'transacciones' => [
                [
                    'pago_id' => 1,
                    'usuario_id' => 1,
                    'plan_id' => 1,
                    'plan_nombre' => 'Plan Standard',
                    'monto' => '29.90',
                    'estado' => 'approved',
                    'metodo_pago' => 'tarjeta_credito',
                    'fecha_pago' => now()->toDateString()
                ]
            ],
            'total_transacciones' => 1,
            'fecha_generacion' => now()->toISOString()
        ];
    }

    private function obtenerDatosRendimientoRespaldo()
    {
        return [
            'planes_rendimiento' => [
                [
                    'plan_id' => 1,
                    'plan_nombre' => 'Plan Standard',
                    'plan_precio' => '29.90',
                    'total_intentos_pago' => 1,
                    'pagos_exitosos' => 1,
                    'tasa_conversion' => '100.00',
                    'ingresos_totales' => '29.90',
                    'usuarios_unicos' => 1
                ]
            ],
            'resumen_general' => [
                'total_planes_activos' => 1,
                'plan_mejor_conversion' => [
                    'plan_nombre' => 'Plan Standard',
                    'tasa_conversion' => '100.00'
                ]
            ],
            'fecha_generacion' => now()->toISOString()
        ];
    }
}
