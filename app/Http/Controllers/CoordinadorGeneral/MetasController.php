<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use App\Repositories\MetaRepositorio;
use App\Repositories\EquipoRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MetasController extends Controller
{
    protected $metaRepositorio;
    protected $equipoRepositorio;

    public function __construct(MetaRepositorio $metaRepositorio, EquipoRepositorio $equipoRepositorio)
    {
        $this->metaRepositorio = $metaRepositorio;
        $this->equipoRepositorio = $equipoRepositorio;
    }

    public function index()
    {
        try {
            // Por ahora usaremos empresa ID = 1 para pruebas
            $empresaId = 1;
            
            // Obtener SOLO metas de equipos válidos de la empresa
            $metas = $this->metaRepositorio->getAllByEmpresa($empresaId);
            
            // Obtener TODOS los equipos válidos de la empresa (con coordinador válido)
            $equipos = $this->equipoRepositorio->getAllByEmpresa($empresaId);
            $estados = $this->metaRepositorio->getEstadosDisponibles();

            // Transformar datos para la vista
            $metasTransformadas = $metas->map(function($meta) {
                // Calcular progreso basado en tareas completadas
                $totalTareas = $meta->tareas->count();
                $tareasCompletadas = $meta->tareas->filter(function($tarea) {
                    return $tarea->estado && $tarea->estado->nombre === 'Completo';
                })->count();
                
                $progreso = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0;

                return [
                    'id' => $meta->id,
                    'titulo' => $meta->nombre,
                    'descripcion' => $meta->descripcion,
                    'estado' => $meta->estado ? $meta->estado->nombre : 'Sin estado',
                    'estado_id' => $meta->estado_id,
                    'equipo' => $meta->equipo ? $meta->equipo->nombre : 'Sin equipo',
                    'equipo_id' => $meta->equipo_id,
                    'fecha_creacion' => $meta->fecha_creacion ? \Carbon\Carbon::parse($meta->fecha_creacion)->format('Y-m-d') : null,
                    'fecha_entrega' => $meta->fecha_entrega ? \Carbon\Carbon::parse($meta->fecha_entrega)->format('Y-m-d') : null,
                    'progreso' => $progreso,
                    'tareas_count' => $totalTareas,
                    'tareas_completadas' => $tareasCompletadas
                ];
            });

            // Transformar equipos para la vista (SOLO equipos sin metas)
            $equiposTransformados = $equipos->map(function($equipo) {
                return [
                    'id' => $equipo->id,
                    'nombre' => $equipo->nombre
                ];
            });

            return view('coordinador-general.metas.index', [
                'metas' => $metasTransformadas,
                'equipos' => $equiposTransformados,
                'estados' => $estados
            ]);

        } catch (\Exception $e) {
            Log::error('Error en metas index', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Error al cargar las metas: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'equipo_id' => 'required|integer|exists:equipos,id',
            'estado_id' => 'required|integer|exists:estados,id',
            'fecha_entrega' => 'nullable|date'
        ]);

        try {
            // Verificar que el equipo pertenece a la empresa y tiene coordinador válido
            $equipo = $this->equipoRepositorio->getById($request->equipo_id);
            if (!$equipo) {
                return back()->with('error', 'El equipo seleccionado no es válido o no pertenece a esta empresa')
                            ->withInput();
            }

            $meta = $this->metaRepositorio->create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'equipo_id' => $request->equipo_id,
                'estado_id' => $request->estado_id,
                'fecha_creacion' => now(),
                'fecha_entrega' => $request->fecha_entrega
            ]);

            return redirect()->route('coordinador-general.metas')
                           ->with('success', 'Meta creada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error en store meta', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Error al crear la meta: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $meta = $this->metaRepositorio->getById($id);
            
            if (!$meta) {
                return response()->json(['error' => 'Meta no encontrada o no válida'], 404);
            }

            return response()->json([
                'id' => $meta->id,
                'titulo' => $meta->nombre,
                'descripcion' => $meta->descripcion,
                'estado' => $meta->estado ? $meta->estado->nombre : 'Sin estado',
                'estado_id' => $meta->estado_id,
                'equipo' => $meta->equipo ? $meta->equipo->nombre : 'Sin equipo',
                'equipo_id' => $meta->equipo_id,
                'fecha_creacion' => $meta->fecha_creacion ? \Carbon\Carbon::parse($meta->fecha_creacion)->format('d/m/Y') : null,
                'fecha_entrega' => $meta->fecha_entrega ? \Carbon\Carbon::parse($meta->fecha_entrega)->format('d/m/Y') : null,
                'tareas' => $meta->tareas->map(function($tarea) {
                    return [
                        'id' => $tarea->id,
                        'nombre' => $tarea->nombre,
                        'estado' => $tarea->estado ? $tarea->estado->nombre : 'Sin estado'
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error en show meta', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'id' => $id]);
            return response()->json(['error' => 'Error al cargar la meta'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'equipo_id' => 'required|integer|exists:equipos,id',
            'estado_id' => 'required|integer|exists:estados,id',
            'fecha_entrega' => 'nullable|date'
        ]);

        try {
            // Verificar que el equipo pertenece a la empresa y tiene coordinador válido
            $equipo = $this->equipoRepositorio->getById($request->equipo_id);
            if (!$equipo) {
                return response()->json(['error' => 'El equipo seleccionado no es válido o no pertenece a esta empresa'], 400);
            }

            $actualizado = $this->metaRepositorio->update($id, [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'equipo_id' => $request->equipo_id,
                'estado_id' => $request->estado_id,
                'fecha_entrega' => $request->fecha_entrega
            ]);

            if (!$actualizado) {
                return response()->json(['error' => 'Meta no encontrada'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Meta actualizada exitosamente']);

        } catch (\Exception $e) {
            Log::error('Error en update meta', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'id' => $id,
                'data' => $request->all()
            ]);
            
            return response()->json(['error' => 'Error al actualizar la meta: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $eliminado = $this->metaRepositorio->delete($id);

            if (!$eliminado) {
                return response()->json(['error' => 'Meta no encontrada'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Meta eliminada exitosamente']);

        } catch (\Exception $e) {
            Log::error('Error en destroy meta', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'id' => $id]);
            return response()->json(['error' => 'Error al eliminar la meta: ' . $e->getMessage()], 500);
        }
    }
}
