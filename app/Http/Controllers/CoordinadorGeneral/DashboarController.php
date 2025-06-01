<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use App\Repositories\EquipoRepositorio;
use App\Repositories\MetaRepositorio;
use App\Repositories\TareaRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            // Por ahora usaremos empresa ID = 1 para pruebas
            $empresaId = 1;
            
            // Obtener datos reales de la base de datos
            //$equipos = $this->equipoRepositorio->getAllByEmpresa($empresaId);
           
            $tareas = $this->tareaRepositorio->getAllByEmpresa($empresaId);
            
            // Calcular métricas reales
            $metricas = [
                //'equipos_activos' => $equipos->count(),
              
                'total_actividades' => $tareas->count(),
                'actividades_completadas' => $tareas->where('estado.nombre', 'Completo')->count(),
                'actividades_en_progreso' => $tareas->where('estado.nombre', 'En proceso')->count(),
                'actividades_pendientes' => $tareas->where('estado.nombre', 'Incompleta')->count(),
                'reuniones_proximas' => 8 // Mantener fijo por ahora
            ];

            // Obtener las 3 metas más recientes
            $metasRecientes = $metas->take(3)->map(function($meta) {
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
            $equiposRecientes = $equipos->take(3)->map(function($equipo) {
                // Contar solo colaboradores como miembros
                $colaboradoresMiembros = $equipo->miembros->where('activo', true)->filter(function($miembro) {
                    return $miembro->trabajador->usuario && 
                           $miembro->trabajador->usuario->rol && 
                           $miembro->trabajador->usuario->rol->nombre === 'Colaborador';
                });

                return [
                    'id' => $equipo->id,
                    'nombre' => $equipo->nombre,
                    'area' => $equipo->area->nombre,
                    'miembros_count' => $colaboradoresMiembros->count(),
                    'metas_activas' => $equipo->metas_activas_count,
                    'progreso' => $equipo->progreso_promedio,
                    'coordinador' => $equipo->coordinador_nombre_completo
                ];
            });

            // Obtener las 3 actividades más recientes
            $actividadesRecientes = $tareas->take(3)->map(function($tarea) {
                return [
                    'id' => $tarea->id,
                    'titulo' => $tarea->nombre,
                    'descripcion' => $tarea->descripcion,
                    'estado' => $tarea->estado ? $tarea->estado->nombre : 'Sin estado',
                    'equipo' => $tarea->meta && $tarea->meta->equipo ? $tarea->meta->equipo->nombre : 'Sin equipo',
                    'meta' => $tarea->meta ? $tarea->meta->nombre : 'Sin meta',
                    'fecha_creacion' => $tarea->fecha_creacion ? \Carbon\Carbon::parse($tarea->fecha_creacion)->format('d/m/Y') : null,
                    'esta_vencida' => $tarea->esta_vencida
                ];
            });

            return view('coordinador-general.dashboard.index', compact(
                'metricas',
                'metasRecientes',
                'equiposRecientes',
                'actividadesRecientes'
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
                'actividadesRecientes' => collect([])
            ])->with('error', 'Error al cargar los datos del dashboard');
        }
    }
}
