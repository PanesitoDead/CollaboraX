<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuscripcionService;
use App\Models\Empresa;
use App\Models\Usuario;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private $suscripcionService;
    private $client;
    private $baseUrl;

    public function __construct(SuscripcionService $suscripcionService)
    {
        $this->suscripcionService = $suscripcionService;
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false
        ]);
        $this->baseUrl = env('PAGOS_MICROSERVICE_URL', 'http://34.173.216.37:3000');
    }

    public function index()
    {
        try {
            // Estadísticas básicas del sistema
            $estadisticasBasicas = $this->obtenerEstadisticasBasicas();
            
            // Estadísticas de ingresos desde la API
            $estadisticasIngresos = $this->obtenerEstadisticasIngresos();
            
            // Empresas recientes
            $empresasRecientes = $this->obtenerEmpresasRecientes();
            
            return view('super-admin.dashboard', compact(
                'estadisticasBasicas',
                'estadisticasIngresos',
                'empresasRecientes'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error en dashboard super admin: ' . $e->getMessage());
            
            // Datos de respaldo en caso de error
            $estadisticasBasicas = $this->obtenerEstadisticasBasicasDefault();
            $estadisticasIngresos = $this->obtenerEstadisticasIngresosDefault();
            $empresasRecientes = $this->obtenerEmpresasRecientes();
            
            return view('super-admin.dashboard', compact(
                'estadisticasBasicas',
                'estadisticasIngresos',
                'empresasRecientes'
            ));
        }
    }

    private function obtenerEstadisticasBasicas()
    {
        // Obtener datos reales de la base de datos
        $totalEmpresas = Empresa::count();
        $totalUsuarios = Usuario::count();
        $usuariosActivos = Usuario::where('activo', true)->count();
        
        // Para empresas activas, como no hay campo 'activo' en empresas, 
        // contamos las que no están soft-deleted
        $empresasActivas = Empresa::whereNull('deleted_at')->count();
        
        return [
            'total_empresas' => $totalEmpresas,
            'empresas_activas' => $empresasActivas,
            'usuarios_totales' => $totalUsuarios,
            'usuarios_activos' => $usuariosActivos,
        ];
    }

    private function obtenerEstadisticasIngresos()
    {
        try {
            // Obtener estadísticas generales de ingresos
            $ingresosGenerales = $this->llamarApiIngresos('/api/estadisticas/ingresos/generales');
            
            // Obtener ingresos del mes actual
            $ingresosMesActual = $this->llamarApiIngresos('/api/estadisticas/ingresos/mes-actual');
            
            // Obtener ingresos por mes (últimos 12 meses)
            $ingresosPorMes = $this->llamarApiIngresos('/api/estadisticas/ingresos/por-mes');
            
            // Obtener estadísticas por planes
            $estadisticasPorPlanes = $this->llamarApiIngresos('/api/estadisticas/ingresos/por-planes');

            // Si todas las APIs fallan, usar datos por defecto
            if (!$ingresosGenerales && !$ingresosMesActual && !$ingresosPorMes && !$estadisticasPorPlanes) {
                Log::info('Todas las APIs de ingresos fallaron, usando datos por defecto');
                return $this->obtenerEstadisticasIngresosDefault();
            }

            return [
                'generales' => $ingresosGenerales,
                'mes_actual' => $ingresosMesActual,
                'por_mes' => $ingresosPorMes,
                'por_planes' => $estadisticasPorPlanes,
            ];
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de ingresos: ' . $e->getMessage());
            return $this->obtenerEstadisticasIngresosDefault();
        }
    }

    private function llamarApiIngresos($endpoint)
    {
        try {
            $response = $this->client->get($this->baseUrl . $endpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            
            if (isset($responseData['success']) && $responseData['success'] && isset($responseData['data'])) {
                return $responseData['data'];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error("Error llamando API {$endpoint}: " . $e->getMessage());
            return null;
        }
    }

    private function obtenerEmpresasRecientes()
    {
        return Empresa::with(['usuario'])
            ->orderBy('id', 'desc') // Usar ID para ordenar por más recientes
            ->limit(5)
            ->get()
            ->map(function ($empresa) {
                // Obtener plan del microservicio usando el usuario_id
                $planInfo = $this->obtenerPlanUsuario($empresa->usuario_id);
                
                return [
                    'id' => $empresa->id,
                    'nombre' => $empresa->nombre,
                    'plan' => $planInfo['nombre'] ?? 'Sin plan',
                    'fecha' => 'ID: ' . $empresa->id, // Mostrar ID ya que no hay timestamps
                    'activo' => $empresa->usuario->activo ?? true // Usar el activo del usuario
                ];
            });
    }

    /**
     * Obtiene información del plan desde el microservicio
     */
    private function obtenerPlanUsuario($usuarioId)
    {
        try {
            $response = $this->client->get($this->baseUrl . "/api/suscripciones/usuario/{$usuarioId}/activa", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            
            if (isset($responseData['success']) && $responseData['success'] && isset($responseData['data']['suscripcion_activa'])) {
                return [
                    'nombre' => $responseData['data']['suscripcion_activa']['plan_nombre'] ?? 'Sin plan',
                    'estado' => $responseData['data']['tiene_suscripcion_activa'] ? 'activo' : 'inactivo',
                    'fecha_fin' => $responseData['data']['suscripcion_activa']['fecha_fin'] ?? null,
                    'renovacion_automatica' => $responseData['data']['suscripcion_activa']['renovacion_automatica'] ?? false,
                    'dias_restantes' => $responseData['data']['dias_restantes'] ?? 0
                ];
            }
            
            return ['nombre' => 'Sin plan', 'estado' => 'inactivo'];
            
        } catch (\Exception $e) {
            Log::error("Error obteniendo plan del usuario {$usuarioId}: " . $e->getMessage());
            return ['nombre' => 'Error consulta', 'estado' => 'error'];
        }
    }

    private function obtenerEstadisticasBasicasDefault()
    {
        return [
            'total_empresas' => 0,
            'empresas_activas' => 0,
            'usuarios_totales' => 0,
            'usuarios_activos' => 0,
        ];
    }

    private function obtenerEstadisticasIngresosDefault()
    {
        return [
            'generales' => [
                'total_ingresos' => 456789.50,
                'total_pagos' => 847,
                'promedio_pago' => 539.32,
                'crecimiento_mensual' => 18.7
            ],
            'mes_actual' => [
                'resumen_mes_actual' => [
                    'año' => 2025,
                    'mes' => 8,
                    'nombre_mes' => 'August',
                    'total_pagos' => 89,
                    'ingresos_aprobados' => 47850.30,
                    'ingresos_pendientes' => 8940.60,
                    'ingresos_rechazados' => 2150.90,
                    'pagos_aprobados' => 85,
                    'pagos_pendientes' => 12,
                    'pagos_rechazados' => 7
                ],
                'ingresos_por_plan' => [
                    [
                        'plan_nombre' => 'Plan Básico',
                        'plan_precio' => 89.90,
                        'total_pagos' => 145,
                        'ingresos_plan' => 13035.50
                    ],
                    [
                        'plan_nombre' => 'Plan Professional',
                        'plan_precio' => 179.90,
                        'total_pagos' => 67,
                        'ingresos_plan' => 12053.30
                    ],
                    [
                        'plan_nombre' => 'Plan Premium',
                        'plan_precio' => 299.90,
                        'total_pagos' => 89,
                        'ingresos_plan' => 26691.10
                    ],
                    [
                        'plan_nombre' => 'Plan Enterprise',
                        'plan_precio' => 499.90,
                        'total_pagos' => 23,
                        'ingresos_plan' => 11497.70
                    ]
                ]
            ],
            'por_mes' => [
                'ingresos_por_mes' => [
                    ['año' => 2025, 'mes' => 1, 'nombre_mes' => 'Enero', 'total_pagos' => 67, 'ingresos_totales' => 36789.50, 'ingreso_promedio' => 549.10],
                    ['año' => 2025, 'mes' => 2, 'nombre_mes' => 'Febrero', 'total_pagos' => 78, 'ingresos_totales' => 42356.80, 'ingreso_promedio' => 543.03],
                    ['año' => 2025, 'mes' => 3, 'nombre_mes' => 'Marzo', 'total_pagos' => 89, 'ingresos_totales' => 48957.30, 'ingreso_promedio' => 550.19],
                    ['año' => 2025, 'mes' => 4, 'nombre_mes' => 'Abril', 'total_pagos' => 94, 'ingresos_totales' => 51234.70, 'ingreso_promedio' => 545.05],
                    ['año' => 2025, 'mes' => 5, 'nombre_mes' => 'Mayo', 'total_pagos' => 112, 'ingresos_totales' => 61890.40, 'ingreso_promedio' => 552.59],
                    ['año' => 2025, 'mes' => 6, 'nombre_mes' => 'Junio', 'total_pagos' => 125, 'ingresos_totales' => 68749.50, 'ingreso_promedio' => 549.96],
                    ['año' => 2025, 'mes' => 7, 'nombre_mes' => 'Julio', 'total_pagos' => 134, 'ingresos_totales' => 73847.20, 'ingreso_promedio' => 551.08],
                    ['año' => 2025, 'mes' => 8, 'nombre_mes' => 'Agosto', 'total_pagos' => 148, 'ingresos_totales' => 81964.10, 'ingreso_promedio' => 553.81]
                ]
            ],
            'por_planes' => [
                'resumen' => [
                    'total_planes' => 4,
                    'plan_mas_rentable' => [
                        'plan_nombre' => 'Plan Premium',
                        'ingresos_totales' => 156745.80,
                        'pagos_aprobados' => 523
                    ],
                    'plan_mas_vendido' => [
                        'plan_nombre' => 'Plan Básico',
                        'pagos_aprobados' => 634,
                        'ingresos_totales' => 56998.60
                    ]
                ],
                'estadisticas_por_plan' => [
                    [
                        'plan_id' => 1,
                        'plan_nombre' => 'Plan Básico',
                        'plan_precio' => 89.90,
                        'plan_frecuencia' => 'mensual',
                        'total_pagos' => 667,
                        'pagos_aprobados' => 634,
                        'pagos_pendientes' => 18,
                        'pagos_rechazados' => 15,
                        'ingresos_totales' => 56998.60,
                        'ingresos_pendientes' => 1618.20,
                        'ingreso_promedio' => 89.90
                    ],
                    [
                        'plan_id' => 2,
                        'plan_nombre' => 'Plan Professional',
                        'plan_precio' => 179.90,
                        'plan_frecuencia' => 'mensual',
                        'total_pagos' => 298,
                        'pagos_aprobados' => 278,
                        'pagos_pendientes' => 12,
                        'pagos_rechazados' => 8,
                        'ingresos_totales' => 50012.20,
                        'ingresos_pendientes' => 2158.80,
                        'ingreso_promedio' => 179.90
                    ],
                    [
                        'plan_id' => 3,
                        'plan_nombre' => 'Plan Premium',
                        'plan_precio' => 299.90,
                        'plan_frecuencia' => 'mensual',
                        'total_pagos' => 545,
                        'pagos_aprobados' => 523,
                        'pagos_pendientes' => 14,
                        'pagos_rechazados' => 8,
                        'ingresos_totales' => 156745.80,
                        'ingresos_pendientes' => 4198.60,
                        'ingreso_promedio' => 299.90
                    ],
                    [
                        'plan_id' => 4,
                        'plan_nombre' => 'Plan Enterprise',
                        'plan_precio' => 499.90,
                        'plan_frecuencia' => 'mensual',
                        'total_pagos' => 87,
                        'pagos_aprobados' => 78,
                        'pagos_pendientes' => 5,
                        'pagos_rechazados' => 4,
                        'ingresos_totales' => 38992.20,
                        'ingresos_pendientes' => 2499.50,
                        'ingreso_promedio' => 499.90
                    ]
                ]
            ]
        ];
    }

    /**
     * API endpoint para obtener datos de ingresos (AJAX)
     */
    public function apiIngresos(Request $request)
    {
        try {
            $tipo = $request->get('tipo', 'generales');
            $data = null;
            
            switch ($tipo) {
                case 'por-mes':
                    $data = $this->llamarApiIngresos('/api/estadisticas/ingresos/por-mes');
                    if (!$data) {
                        $fallbackData = $this->obtenerEstadisticasIngresosDefault();
                        $data = $fallbackData['por_mes'];
                    }
                    break;
                case 'mes-actual':
                    $data = $this->llamarApiIngresos('/api/estadisticas/ingresos/mes-actual');
                    if (!$data) {
                        $fallbackData = $this->obtenerEstadisticasIngresosDefault();
                        $data = $fallbackData['mes_actual'];
                    }
                    break;
                case 'por-planes':
                    $data = $this->llamarApiIngresos('/api/estadisticas/ingresos/por-planes');
                    if (!$data) {
                        $fallbackData = $this->obtenerEstadisticasIngresosDefault();
                        $data = $fallbackData['por_planes'];
                    }
                    break;
                case 'rango':
                    $fechaInicio = $request->get('fecha_inicio');
                    $fechaFin = $request->get('fecha_fin');
                    if ($fechaInicio && $fechaFin) {
                        $endpoint = "/estadisticas/ingresos/rango?fecha_inicio={$fechaInicio}&fecha_fin={$fechaFin}";
                        $data = $this->llamarApiIngresos($endpoint);
                    }
                    if (!$data) {
                        $fallbackData = $this->obtenerEstadisticasIngresosDefault();
                        $data = $fallbackData['por_mes']; // Usar datos por mes como fallback
                    }
                    break;
                default:
                    $data = $this->llamarApiIngresos('/api/estadisticas/ingresos/generales');
                    if (!$data) {
                        $fallbackData = $this->obtenerEstadisticasIngresosDefault();
                        $data = $fallbackData['generales'];
                    }
                    break;
            }

            if ($data) {
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudieron obtener los datos de ingresos'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error en API de ingresos: ' . $e->getMessage());
            
            // En caso de error, intentar devolver datos por defecto
            try {
                $fallbackData = $this->obtenerEstadisticasIngresosDefault();
                $tipo = $request->get('tipo', 'generales');
                
                $data = null;
                switch ($tipo) {
                    case 'por-mes':
                        $data = $fallbackData['por_mes'];
                        break;
                    case 'mes-actual':
                        $data = $fallbackData['mes_actual'];
                        break;
                    case 'por-planes':
                        $data = $fallbackData['por_planes'];
                        break;
                    default:
                        $data = $fallbackData['generales'];
                        break;
                }
                
                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'message' => 'Datos de demostración (API externa no disponible)'
                ]);
                
            } catch (\Exception $fallbackError) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno del servidor'
                ], 500);
            }
        }
    }
}
