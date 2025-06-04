<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use App\Repositories\MetaRepositorio;
use App\Repositories\EquipoRepositorio;
use App\Repositories\EstadoRepositorio;
use App\Repositories\TareaRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MetasController extends Controller
{
    protected $metaRepositorio;
    protected $equipoRepositorio;
    protected $estadoRepositorio;
    protected $tareaRepositorio;

    public function __construct(
        MetaRepositorio $metaRepositorio, 
        EquipoRepositorio $equipoRepositorio,
        EstadoRepositorio $estadoRepositorio,
        TareaRepositorio $tareaRepositorio
    ) {
        $this->metaRepositorio = $metaRepositorio;
        $this->equipoRepositorio = $equipoRepositorio;
        $this->estadoRepositorio = $estadoRepositorio;
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

            // Obtener SOLO metas de equipos válidos de las áreas del coordinador
            $metas = $this->metaRepositorio->getMetasByAreas($areasCoordinador->pluck('id')->toArray());
            
            // Obtener TODOS los equipos válidos de las áreas del coordinador (con coordinador válido)
            $equipos = $this->equipoRepositorio->getEquiposByAreas($areasCoordinador->pluck('id')->toArray());
            $estados = $this->metaRepositorio->getEstadosDisponibles();

            // Debug: Ver qué se está obteniendo
            Log::info('Datos obtenidos para coordinador general - metas', [
                'empresa_id' => $empresaId,
                'trabajador_id' => $trabajador->id,
                'areas_count' => $areasCoordinador->count(),
                'metas_count' => $metas->count(),
                'equipos_count' => $equipos->count()
            ]);

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

            // Transformar equipos para la vista
            $equiposTransformados = $equipos->map(function($equipo) {
                return [
                    'id' => $equipo->id,
                    'nombre' => $equipo->nombre
                ];
            });

            return view('coordinador-general.metas.index', [
                'metas' => $metasTransformadas,
                'equipos' => $equiposTransformados,
                'estados' => $estados,
                'empresa' => $empresa
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
            // Obtener la empresa del coordinador general autenticado
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$trabajador) {
                return back()->with('error', 'No se encontró información del trabajador');
            }

            // Verificar que el equipo pertenece a las áreas del coordinador general
            if (!$this->equipoRepositorio->equipoPerteneceeACoordinadorGeneral($request->equipo_id, $trabajador->id)) {
                return back()->with('error', 'El equipo seleccionado no está bajo tu coordinación')
                            ->withInput();
            }

            // Verificar que el equipo es válido (tiene coordinador de equipo)
            $equipo = $this->equipoRepositorio->getById($request->equipo_id);
            if (!$equipo) {
                return back()->with('error', 'El equipo seleccionado no es válido o no tiene coordinador de equipo')
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

            Log::info('Meta creada exitosamente', [
                'meta_id' => $meta->id,
                'equipo_id' => $request->equipo_id,
                'trabajador_id' => $trabajador->id
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
            // Verificar permisos
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->metaRepositorio->metaPerteneceeACoordinadorGeneral($id, $trabajador->id)) {
                return response()->json(['error' => 'No tienes permisos para ver esta meta'], 403);
            }

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
            // Verificar permisos
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->metaRepositorio->metaPerteneceeACoordinadorGeneral($id, $trabajador->id)) {
                return response()->json(['error' => 'No tienes permisos para editar esta meta'], 403);
            }

            // Verificar que el equipo pertenece a las áreas del coordinador general
            if (!$this->equipoRepositorio->equipoPerteneceeACoordinadorGeneral($request->equipo_id, $trabajador->id)) {
                return response()->json(['error' => 'El equipo seleccionado no está bajo tu coordinación'], 400);
            }

            // Verificar que el equipo es válido (tiene coordinador de equipo)
            $equipo = $this->equipoRepositorio->getById($request->equipo_id);
            if (!$equipo) {
                return response()->json(['error' => 'El equipo seleccionado no es válido o no tiene coordinador de equipo'], 400);
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
            // Verificar permisos
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->metaRepositorio->metaPerteneceeACoordinadorGeneral($id, $trabajador->id)) {
                return response()->json(['error' => 'No tienes permisos para eliminar esta meta'], 403);
            }

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

    /**
     * Función para actualizar el estado de una meta basado en sus tareas
     * Esta función puede ser llamada directamente desde la API
     */
    public function actualizarEstadoMeta(Request $request)
    {
        try {
            $request->validate([
                'meta_id' => 'required|integer|exists:metas,id',
            ]);

            $metaId = $request->meta_id;
            
            // Verificar permisos
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->metaRepositorio->metaPerteneceeACoordinadorGeneral($metaId, $trabajador->id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permisos para actualizar esta meta'
                ], 403);
            }

            $meta = $this->metaRepositorio->getById($metaId);
            
            if (!$meta) {
                return response()->json([
                    'success' => false,
                    'error' => 'Meta no encontrada'
                ], 404);
            }
            
            // Guardar el estado anterior para saber si cambió
            $estadoAnterior = $meta->estado ? $meta->estado->nombre : null;
            
            // Verificar si todas las tareas están completadas
            $totalTareas = $meta->tareas->count();
            $tareasCompletadas = $meta->tareas->filter(function($tarea) {
                return $tarea->estado && $tarea->estado->nombre === 'Completo';
            })->count();
            
            // Determinar el nuevo estado basado en las tareas
            $estadoNombre = ($totalTareas > 0 && $tareasCompletadas === $totalTareas) ? 'Completo' : 'En proceso';
            $estado = $this->estadoRepositorio->findOneBy('nombre', $estadoNombre);
            
            if (!$estado) {
                return response()->json([
                    'success' => false,
                    'error' => 'Estado no encontrado: ' . $estadoNombre
                ], 404);
            }
            
            // Solo actualizar si el estado es diferente
            $actualizado = false;
            $estadoActualizado = false;
            
            if ($meta->estado_id != $estado->id) {
                $actualizado = $this->metaRepositorio->update($metaId, ['estado_id' => $estado->id]);
                $estadoActualizado = true;
            } else {
                // No hubo cambio de estado, pero consideramos exitosa la operación
                $actualizado = true;
            }
            
            if ($actualizado) {
                Log::info('Estado de meta actualizado automáticamente', [
                    'meta_id' => $metaId,
                    'meta_nombre' => $meta->nombre,
                    'estado_anterior' => $estadoAnterior,
                    'nuevo_estado' => $estadoNombre,
                    'cambio_realizado' => $estadoActualizado,
                    'total_tareas' => $totalTareas,
                    'tareas_completadas' => $tareasCompletadas
                ]);
                
                return response()->json([
                    'success' => true,
                    'metaNombre' => $meta->nombre,
                    'estadoAnterior' => $estadoAnterior,
                    'nuevoEstado' => $estadoNombre,
                    'estadoActualizado' => $estadoActualizado,
                    'totalTareas' => $totalTareas,
                    'tareasCompletadas' => $tareasCompletadas
                ]);
            } else {
                Log::error('No se pudo actualizar el estado de la meta', [
                    'meta_id' => $metaId
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'No se pudo actualizar el estado de la meta'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de meta por API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }
}
