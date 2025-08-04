<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarea;
use App\Models\Meta;
use App\Models\Reunion;
use App\Models\Area;
use App\Models\Equipo;
use App\Models\Estado;
use App\Models\Rol;
use App\Models\Trabajador;
use App\Models\MiembroEquipo;
use App\Services\SuscripcionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EstadisticaController extends Controller
{
    protected $suscripcionService;

    public function __construct(SuscripcionService $suscripcionService)
    {
        $this->suscripcionService = $suscripcionService;
    }

    /**
     * Mostrar estadísticas del panel de administración de la empresa
     * 
     * El usuario que ingresa es el ADMIN/DUEÑO de la empresa, por lo tanto:
     * - Ve estadísticas de TODOS los trabajadores de su empresa
     * - Ve metas, tareas, reuniones de TODOS los equipos de su empresa
     * - Ve rendimiento de TODAS las áreas de su empresa
     */
    public function index(Request $request)
    {
        // Obtener período seleccionado (por defecto 30 días)
        $periodo = $request->get('periodo', 30);
        
        // Obtener la empresa del usuario admin autenticado
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();
        
        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder a las estadísticas.');
        }
        
        // El admin ES el dueño de la empresa, obtener la empresa asociada al usuario admin
        $empresa = \App\Models\Empresa::where('usuario_id', $usuario->id)->first();
        
        if (!$empresa) {
            return redirect()->back()->with('error', 'No se puede acceder a las estadísticas. Usuario no es administrador de ninguna empresa.');
        }
        
        $empresaId = $empresa->id;
        
        // Obtener información del plan actual desde el microservicio
        $suscripcionActual = $empresa->getPlanInfo();
        $infoDelPlan = $this->obtenerInfoDelPlan($suscripcionActual);
        
        // Obtener fechas para cálculos
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $startOfPeriod = Carbon::now()->subDays($periodo);
        
        // Estadísticas principales (KPIs)
        $stats = $this->calculateMainStats($empresaId, $today, $startOfWeek, $endOfWeek, $startOfPeriod);
        
        // Distribución de roles
        $roles_dist = $this->calculateRolesDistribution($empresaId);
        
        // Actividad semanal
        $actividad_semanal = $this->calculateWeeklyActivity($empresaId, $startOfWeek);
        
        // Mejores áreas por rendimiento
        $top_performers = $this->calculateTopPerformingAreas($empresaId);
        
        // Estado de metas
        $metas_estado = $this->calculateMetasStatus($empresaId);
        
        // Reuniones por semana
        $reuniones_semana = $this->calculateWeeklyMeetings($empresaId, $startOfWeek);

        return view('private.admin.estadisticas', compact(
            'stats',
            'roles_dist',
            'actividad_semanal',
            'top_performers',
            'metas_estado',
            'reuniones_semana',
            'infoDelPlan'
        ));
    }

    /**
     * Obtener información procesada del plan actual
     */
    private function obtenerInfoDelPlan($suscripcionActual)
    {
        if (!$suscripcionActual) {
            return [
                'nombre' => 'Sin Suscripción',
                'estado' => 'Sin suscripción',
                'limites' => [
                    'trabajadores' => 0,
                ],
                'funciones_avanzadas' => false,
                'color_estado' => 'bg-gray-500',
                'fecha_vencimiento' => null,
                'renovacion_automatica' => false
            ];
        }

        $estado = $suscripcionActual['estado'] ?? 'inactiva';
        
        // Color del estado
        $colorEstado = match($estado) {
            'activa' => 'bg-green-500',
            'vencida' => 'bg-red-500',
            'cancelada' => 'bg-gray-500',
            'Sin suscripción' => 'bg-gray-500',
            'Error' => 'bg-red-500',
            default => 'bg-yellow-500'
        };

        return [
            'nombre' => $suscripcionActual['nombre'] ?? 'Sin Suscripción',
            'estado' => ucfirst($estado),
            'limites' => $suscripcionActual['limites'] ?? [
                'trabajadores' => 0,
            ],
            'funciones_avanzadas' => $suscripcionActual['funciones_avanzadas'] ?? false,
            'color_estado' => $colorEstado,
            'fecha_vencimiento' => $suscripcionActual['fecha_vencimiento'] ?? null,
            'renovacion_automatica' => $suscripcionActual['renovacion_automatica'] ?? false,
            'dias_restantes' => $suscripcionActual['dias_restantes'] ?? 0,
            'plan' => $suscripcionActual['plan'] ?? null
        ];
    }

    /**
     * Calcular estadísticas principales de la empresa
     * Incluye todas las actividades, metas y reuniones de la empresa
     */
    private function calculateMainStats($empresaId, $today, $startOfWeek, $endOfWeek, $startOfPeriod = null)
    {
        // Si no se proporciona período, usar últimos 30 días
        if (!$startOfPeriod) {
            $startOfPeriod = Carbon::now()->subDays(30);
        }

        // Verificar que la empresa existe y tiene áreas
        $empresa = \App\Models\Empresa::find($empresaId);
        if (!$empresa) {
            throw new \Exception("Empresa no encontrada con ID: {$empresaId}");
        }

        // Obtener estado "Completo" y variaciones posibles
        $estadoCompleto = Estado::whereIn('nombre', ['Completo', 'Completado', 'Terminado', 'Finalizado'])->first();
        $estadoCompletoId = $estadoCompleto ? $estadoCompleto->id : null;

        // Actividades terminadas total (solo de la empresa del usuario)
        $actividades_terminadas = $estadoCompletoId 
            ? Tarea::where('estado_id', $estadoCompletoId)
                ->whereHas('meta.equipo.area', function($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId);
                })
                ->count() 
            : 0;
        
        // Actividades nuevas esta semana (solo de la empresa del usuario)
        $actividades_nuevas = Tarea::whereBetween('fecha_creacion', [$startOfWeek, $endOfWeek])
            ->whereHas('meta.equipo.area', function($query) use ($empresaId) {
                $query->where('empresa_id', $empresaId);
            })
            ->count();
        
        // Metas completadas y total (solo de la empresa del usuario)
        $metas_completadas = $estadoCompletoId 
            ? Meta::where('estado_id', $estadoCompletoId)
                ->whereHas('equipo.area', function($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId);
                })
                ->count() 
            : 0;
            
        $metas_total = Meta::whereHas('equipo.area', function($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })->count();
        
        // Asistencias a reuniones (basado en reuniones realizadas de la empresa)
        $reuniones_realizadas = Reunion::where('estado', 'realizada')
            ->whereHas('equipo.area', function($query) use ($empresaId) {
                $query->where('empresa_id', $empresaId);
            })
            ->count();
        
        // Para calcular asistencias reales, necesitaríamos una tabla de asistentes
        // Por ahora estimamos basado en el número de miembros por equipo
        $asistencias_totales = 0;
        if ($reuniones_realizadas > 0) {
            $reuniones_con_equipos = Reunion::where('estado', 'realizada')
                ->whereHas('equipo.area', function($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId);
                })
                ->with(['equipo' => function($query) {
                    $query->withCount('miembros');
                }])
                ->get();
                
            foreach ($reuniones_con_equipos as $reunion) {
                $miembros_equipo = $reunion->equipo->miembros_count ?? 0;
                // Estimamos que asisten: coordinador (1) + 80% de los miembros
                $asistencias_estimadas = 1 + round($miembros_equipo * 0.8);
                $asistencias_totales += max(1, $asistencias_estimadas);
            }
        }
        
        $reuniones_semana = Reunion::where('estado', 'realizada')
            ->whereBetween('fecha', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->whereHas('equipo.area', function($query) use ($empresaId) {
                $query->where('empresa_id', $empresaId);
            })
            ->count();
            
        $asistencias_semana = 0;
        if ($reuniones_semana > 0) {
            $reuniones_semana_equipos = Reunion::where('estado', 'realizada')
                ->whereBetween('fecha', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                ->whereHas('equipo.area', function($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId);
                })
                ->with(['equipo' => function($query) {
                    $query->withCount('miembros');
                }])
                ->get();
                
            foreach ($reuniones_semana_equipos as $reunion) {
                $miembros_equipo = $reunion->equipo->miembros_count ?? 0;
                $asistencias_estimadas = 1 + round($miembros_equipo * 0.8);
                $asistencias_semana += max(1, $asistencias_estimadas);
            }
        }
        
        // Porcentaje de avance de metas
        $porcentaje_avance = $metas_total > 0 ? round(($metas_completadas / $metas_total) * 100, 1) : 0;

        return [
            'actividades_terminadas' => $actividades_terminadas,
            'actividades_nuevas' => $actividades_nuevas,
            'metas_completadas' => $metas_completadas,
            'metas_total' => $metas_total,
            'asistencias_totales' => $asistencias_totales,
            'asistencias_semana' => $asistencias_semana,
            'porcentaje_avance' => $porcentaje_avance,
        ];
    }

    /**
     * Calcular distribución de roles de TODOS los trabajadores de la empresa
     * Muestra cómo están distribuidos los roles dentro de la empresa
     */
    private function calculateRolesDistribution($empresaId)
    {
        $total_trabajadores = Trabajador::where('empresa_id', $empresaId)->count();
        
        if ($total_trabajadores == 0) {
            return [
                ['nombre' => 'Sin trabajadores registrados', 'porcentaje' => 100, 'color' => '#9CA3AF']
            ];
        }

        // Obtener IDs de coordinadores (trabajadores que coordinan equipos de la empresa)
        $coordinadoresIds = Equipo::whereNotNull('coordinador_id')
            ->whereHas('area', function($query) use ($empresaId) {
                $query->where('empresa_id', $empresaId);
            })
            ->pluck('coordinador_id')
            ->unique()
            ->toArray();

        // Obtener IDs de miembros de equipos (de la empresa)
        $miembrosIds = MiembroEquipo::whereHas('equipo.area', function($query) use ($empresaId) {
                $query->where('empresa_id', $empresaId);
            })
            ->pluck('trabajador_id')
            ->unique()
            ->toArray();

        // Contar trabajadores por rol (prioridad: Coordinador > Miembro > Sin Asignar)
        $coordinadores = count($coordinadoresIds);
        
        // Miembros que NO son coordinadores
        $solo_miembros = count(array_diff($miembrosIds, $coordinadoresIds));
        
        // Trabajadores que no son ni coordinadores ni miembros
        $todos_con_rol = array_merge($coordinadoresIds, $miembrosIds);
        $trabajadores_con_empresa = Trabajador::where('empresa_id', $empresaId)->pluck('id')->toArray();
        $sin_asignar = count(array_diff($trabajadores_con_empresa, $todos_con_rol));

        $roles_dist = [];
        
        if ($coordinadores > 0) {
            $roles_dist[] = [
                'nombre' => 'Coordinadores',
                'porcentaje' => round(($coordinadores / $total_trabajadores) * 100),
                'color' => '#3B82F6'
            ];
        }
        
        if ($solo_miembros > 0) {
            $roles_dist[] = [
                'nombre' => 'Miembros de Equipo',
                'porcentaje' => round(($solo_miembros / $total_trabajadores) * 100),
                'color' => '#10B981'
            ];
        }
        
        if ($sin_asignar > 0) {
            $roles_dist[] = [
                'nombre' => 'Sin Asignar',
                'porcentaje' => round(($sin_asignar / $total_trabajadores) * 100),
                'color' => '#F97316'
            ];
        }

        // Si no hay roles específicos, mostrar todos como trabajadores
        if (empty($roles_dist)) {
            $roles_dist[] = [
                'nombre' => 'Trabajadores',
                'porcentaje' => 100,
                'color' => '#6B7280'
            ];
        }

        return $roles_dist;
    }

    /**
     * Calcular actividad semanal de TODOS los equipos de la empresa
     * Muestra metas y actividades creadas por día
     */
    private function calculateWeeklyActivity($empresaId, $startOfWeek)
    {
        $actividad_semanal = [];
        $dias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'];
        
        for ($i = 0; $i < 5; $i++) {
            $fecha = $startOfWeek->copy()->addDays($i);
            
            $metas_dia = Meta::whereDate('fecha_creacion', $fecha->format('Y-m-d'))
                ->whereHas('equipo.area', function($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId);
                })
                ->count();
                
            $actividades_dia = Tarea::whereDate('fecha_creacion', $fecha->format('Y-m-d'))
                ->whereHas('meta.equipo.area', function($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId);
                })
                ->count();
            
            $actividad_semanal[] = [
                'dia' => $dias[$i],
                'metas' => $metas_dia,
                'actividades' => $actividades_dia,
            ];
        }

        return $actividad_semanal;
    }

    /**
     * Calcular las áreas con mejor rendimiento de la empresa
     * Ranking de todas las áreas basado en porcentaje de metas completadas
     */
    private function calculateTopPerformingAreas($empresaId)
    {
        $estadoCompleto = Estado::whereIn('nombre', ['Completo', 'Completado', 'Terminado', 'Finalizado'])->first();
        $estadoCompletoId = $estadoCompleto ? $estadoCompleto->id : null;

        $areas = Area::where('empresa_id', $empresaId)
            ->with(['equipos.metas'])
            ->get()
            ->map(function ($area) use ($estadoCompletoId) {
                $total_metas = $area->equipos->sum(function ($equipo) {
                    return $equipo->metas->count();
                });
                
                $metas_completadas = $estadoCompletoId ? $area->equipos->sum(function ($equipo) use ($estadoCompletoId) {
                    return $equipo->metas->where('estado_id', $estadoCompletoId)->count();
                }) : 0;
                
                $puntuacion = $total_metas > 0 ? round(($metas_completadas / $total_metas) * 100) : 0;
                
                return [
                    'area' => $area->nombre,
                    'puntuacion' => $puntuacion,
                    'total_metas' => $total_metas,
                ];
            })
            ->filter(function ($item) {
                return $item['total_metas'] > 0; // Solo mostrar áreas con metas
            })
            ->sortByDesc('puntuacion')
            ->take(4)
            ->values();

        // Si no hay áreas con metas, devolver un array vacío o datos de ejemplo
        if ($areas->isEmpty()) {
            return [
                ['area' => 'Sin áreas con metas asignadas', 'puntuacion' => 0]
            ];
        }

        return $areas->toArray();
    }

    /**
     * Calcular estado de TODAS las metas de la empresa
     * Distribución por estados de todas las metas de todos los equipos
     */
    private function calculateMetasStatus($empresaId)
    {
        $total_metas = Meta::whereHas('equipo.area', function($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })->count();
        
        if ($total_metas == 0) {
            return [
                ['nombre' => 'Sin metas registradas', 'cantidad' => 0, 'porcentaje' => 100, 'color' => '#9CA3AF']
            ];
        }

        $estados = Estado::withCount(['metas' => function($query) use ($empresaId) {
            $query->whereHas('equipo.area', function($subQuery) use ($empresaId) {
                $subQuery->where('empresa_id', $empresaId);
            });
        }])->having('metas_count', '>', 0)->get();
        
        $metas_estado = [];
        
        // Mapeo de colores por tipo de estado
        $colores = [
            'Completo' => '#10B981',
            'Completado' => '#10B981',
            'Terminado' => '#10B981',
            'Finalizado' => '#10B981',
            'En Progreso' => '#F59E0B',
            'En Proceso' => '#F59E0B',
            'Activo' => '#F59E0B',
            'Pendiente' => '#EF4444',
            'Por Hacer' => '#EF4444',
            'Nuevo' => '#EF4444',
            'Cancelado' => '#9CA3AF',
            'Cancelada' => '#9CA3AF',
            'Suspendido' => '#9CA3AF',
            'Pausado' => '#F97316',
        ];

        foreach ($estados as $estado) {
            $color = '#6B7280'; // Color por defecto
            
            // Buscar color basado en el nombre del estado
            foreach ($colores as $nombre => $colorEstado) {
                if (stripos($estado->nombre, $nombre) !== false) {
                    $color = $colorEstado;
                    break;
                }
            }
            
            $metas_estado[] = [
                'nombre' => $estado->nombre,
                'cantidad' => $estado->metas_count,
                'porcentaje' => round(($estado->metas_count / $total_metas) * 100, 2),
                'color' => $color
            ];
        }

        // Ordenar por cantidad descendente
        usort($metas_estado, function($a, $b) {
            return $b['cantidad'] - $a['cantidad'];
        });

        return $metas_estado;
    }

    /**
     * Calcular reuniones por semana de TODOS los equipos de la empresa
     * Conteo de reuniones programadas por día de todos los equipos
     */
    private function calculateWeeklyMeetings($empresaId, $startOfWeek)
    {
        $reuniones_semana = [];
        $dias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'];
        
        for ($i = 0; $i < 5; $i++) {
            $fecha = $startOfWeek->copy()->addDays($i);
            
            $conteo = Reunion::whereDate('fecha', $fecha->format('Y-m-d'))
                ->whereHas('equipo.area', function($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId);
                })
                ->count();
            
            $reuniones_semana[] = [
                'dia' => $dias[$i],
                'conteo' => $conteo,
            ];
        }

        return $reuniones_semana;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
