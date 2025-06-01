<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use App\Repositories\EquipoRepositorio;
use App\Repositories\MetaRepositorio;
use App\Repositories\TareaRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboarController extends Controller
{
    protected $equipoRepositorio;
    protected $metaRepositorio;
    protected $tareaRepositorio;

    public function __construct(
        EquipoRepositorio $equipoRepositorio,
        MetaRepositorio $metaRepositorio,
        TareaRepositorio $tareaRepositorio
    ) {
        $this->equipoRepositorio = $equipoRepositorio;
        $this->metaRepositorio = $metaRepositorio;
        $this->tareaRepositorio = $tareaRepositorio;
    }

    public function index()
    {
        try {
            // Obtener la empresa del coordinador general autenticado
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$trabajador) {
                return back()->with('error', 'No se encontró información del trabajador');
            }

            // Obtener la empresa del trabajador directamente por ID
            $empresaId = $trabajador->empresa_id;
            if (!$empresaId) {
                return back()->with('error', 'No se encontró la empresa asociada al trabajador');
            }

            $empresa = DB::table('empresas')->find($empresaId);
            if (!$empresa) {
                return back()->with('error', 'No se encontró la empresa en la base de datos');
            }
            
            // Obtener las áreas asignadas al coordinador general
            $areasCoordinador = $this->equipoRepositorio->getAreasCoordinadorGeneral($trabajador->id);
            
            if ($areasCoordinador->isEmpty()) {
                return back()->with('error', 'No tienes áreas asignadas como coordinador general');
            }

            // Obtener datos reales de la base de datos (SOLO equipos válidos con coordinadores de equipo)
            $equipos = $this->equipoRepositorio->getEquiposByAreas($areasCoordinador->pluck('id')->toArray());
            $metas = $this->metaRepositorio->getMetasByAreas($areasCoordinador->pluck('id')->toArray());
            $tareas = $this->tareaRepositorio->getTareasByAreas($areasCoordinador->pluck('id')->toArray());
            
            // Debug: Ver qué se está obteniendo
            Log::info('Datos obtenidos para dashboard coordinador general', [
                'empresa_id' => $empresaId,
                'trabajador_id' => $trabajador->id,
                'areas_count' => $areasCoordinador->count(),
                'equipos_count' => $equipos->count(),
                'metas_count' => $metas->count(),
                'tareas_count' => $tareas->count()
            ]);
            
            // Calcular métricas reales
            $metricas = [
                'equipos_activos' => $equipos->count(),
                'metas_activas' => $metas->count(),
                'total_actividades' => $tareas->count(),
                'actividades_completadas' => $tareas->where('estado.nombre', 'Completo')->count(),
                'actividades_en_progreso' => $tareas->where('estado.nombre', 'En proceso')->count(),
                'actividades_pendientes' => $tareas->where('estado.nombre', 'Incompleta')->count(),
                'reuniones_proximas' => 8 // Mantener fijo por ahora
            ];

            // Obtener las 3 metas más recientes
            $metasRecientes = $metas->sortByDesc('fecha_creacion')->take(3)->map(function($meta) {
                // Calcular progreso basado en tareas
                $totalTareas = $meta->tareas->count();
                $tareasCompletadas = $meta->tareas->filter(function($tarea) {
                    return $tarea->estado && $tarea->estado->nombre === 'Completo';
                })->count();
                
                $progreso = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0;
                
                // Calcular días hasta vencimiento
                $diasVencimiento = null;
                if ($meta->fecha_entrega) {
                    $fechaEntrega = \Carbon\Carbon::parse($meta->fecha_entrega);
                    $diasVencimiento = $fechaEntrega->diffInDays(now(), false);
                    if ($diasVencimiento < 0) {
                        $diasVencimiento = abs($diasVencimiento);
                    } else {
                        $diasVencimiento = "Vencida hace " . $diasVencimiento . " días";
                    }
                }

                return [
                    'id' => $meta->id,
                    'nombre' => $meta->nombre,
                    'descripcion' => $meta->descripcion,
                    'progreso' => $progreso,
                    'equipo' => $meta->equipo ? $meta->equipo->nombre : 'Sin equipo',
                    'dias_vencimiento' => $diasVencimiento,
                    'total_tareas' => $totalTareas,
                    'tareas_completadas' => $tareasCompletadas
                ];
            });

            // Obtener los 3 equipos más recientes
            $equiposRecientes = $equipos->sortByDesc('fecha_creacion')->take(3)->map(function($equipo) {
                // Contar solo colaboradores como miembros
                $colaboradoresMiembros = $equipo->miembros->where('activo', true)->filter(function($miembro) {
                    return $miembro->trabajador->usuario && 
                           $miembro->trabajador->usuario->rol && 
                           in_array($miembro->trabajador->usuario->rol->nombre, ['Colaborador', 'Coord. Equipo']);
                });

                // Calcular progreso promedio basado en metas del equipo
                $metasEquipo = $equipo->metas;
                $progresoPromedio = 0;
                
                if ($metasEquipo->count() > 0) {
                    $progresoTotal = 0;
                    foreach ($metasEquipo as $meta) {
                        $totalTareas = $meta->tareas->count();
                        $tareasCompletadas = $meta->tareas->filter(function($tarea) {
                            return $tarea->estado && $tarea->estado->nombre === 'Completo';
                        })->count();
                        
                        $progresoMeta = $totalTareas > 0 ? ($tareasCompletadas / $totalTareas) * 100 : 0;
                        $progresoTotal += $progresoMeta;
                    }
                    $progresoPromedio = round($progresoTotal / $metasEquipo->count());
                }

                return [
                    'id' => $equipo->id,
                    'nombre' => $equipo->nombre,
                    'area' => $equipo->area->nombre,
                    'miembros_count' => $colaboradoresMiembros->count(),
                    'metas_activas' => $metasEquipo->count(),
                    'progreso' => $progresoPromedio,
                    'coordinador' => $equipo->coordinador ? $equipo->coordinador->nombres . ' ' . $equipo->coordinador->apellido_paterno : 'Sin coordinador'
                ];
            });

            // Obtener las 3 actividades más recientes
            $actividadesRecientes = $tareas->sortByDesc('fecha_creacion')->take(3)->map(function($tarea) {
                // Verificar si está vencida
                $estaVencida = false;
                if ($tarea->fecha_entrega) {
                    $fechaEntrega = \Carbon\Carbon::parse($tarea->fecha_entrega);
                    $estaVencida = $fechaEntrega->isPast() && $tarea->estado && $tarea->estado->nombre !== 'Completo';
                }

                return [
                    'id' => $tarea->id,
                    'titulo' => $tarea->nombre,
                    'descripcion' => $tarea->descripcion,
                    'estado' => $tarea->estado ? $tarea->estado->nombre : 'Sin estado',
                    'equipo' => $tarea->meta && $tarea->meta->equipo ? $tarea->meta->equipo->nombre : 'Sin equipo',
                    'meta' => $tarea->meta ? $tarea->meta->nombre : 'Sin meta',
                    'fecha_creacion' => $tarea->fecha_creacion ? \Carbon\Carbon::parse($tarea->fecha_creacion)->format('d/m/Y') : null,
                    'esta_vencida' => $estaVencida
                ];
            });

            return view('coordinador-general.dashboard.index', compact(
                'metricas',
                'metasRecientes',
                'equiposRecientes',
                'actividadesRecientes',
                'empresa'
            ));

        } catch (\Exception $e) {
            Log::error('Error en dashboard', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            // En caso de error, mostrar datos por defecto
            $metricas = [
                'equipos_activos' => 0,
                'metas_activas' => 0,
                'total_actividades' => 0,
                'actividades_completadas' => 0,
                'actividades_en_progreso' => 0,
                'actividades_pendientes' => 0,
                'reuniones_proximas' => 8
            ];

            return view('coordinador-general.dashboard.index', [
                'metricas' => $metricas,
                'metasRecientes' => collect([]),
                'equiposRecientes' => collect([]),
                'actividadesRecientes' => collect([]),
                'empresa' => null
            ])->with('error', 'Error al cargar los datos del dashboard: ' . $e->getMessage());
        }
    }
}
