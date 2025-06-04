<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use App\Repositories\TareaRepositorio;
use App\Repositories\EquipoRepositorio;
use App\Repositories\MetaRepositorio;
use App\Repositories\EstadoRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActividadesController extends Controller
{
    protected $tareaRepositorio;
    protected $equipoRepositorio;
    protected $metaRepositorio;
    protected $estadoRepositorio;

    public function __construct(
        TareaRepositorio $tareaRepositorio,
        EquipoRepositorio $equipoRepositorio,
        MetaRepositorio $metaRepositorio,
        EstadoRepositorio $estadoRepositorio
    ) {
        $this->tareaRepositorio = $tareaRepositorio;
        $this->equipoRepositorio = $equipoRepositorio;
        $this->metaRepositorio = $metaRepositorio;
        $this->estadoRepositorio = $estadoRepositorio;
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

            // Obtener SOLO tareas de equipos válidos de las áreas del coordinador
            $tareas = $this->tareaRepositorio->getTareasByAreas($areasCoordinador->pluck('id')->toArray());
            
            // Obtener TODOS los equipos válidos de las áreas del coordinador (con coordinador válido)
            $equipos = $this->equipoRepositorio->getEquiposByAreas($areasCoordinador->pluck('id')->toArray());
            $estados = $this->tareaRepositorio->getEstadosDisponibles();

            // Debug: Ver qué se está obteniendo
            Log::info('Datos obtenidos para coordinador general - actividades', [
                'empresa_id' => $empresaId,
                'trabajador_id' => $trabajador->id,
                'areas_count' => $areasCoordinador->count(),
                'tareas_count' => $tareas->count(),
                'equipos_count' => $equipos->count()
            ]);

            // Transformar datos para la vista
            $tareasTransformadas = $tareas->map(function($tarea) {
                return [
                    'id' => $tarea->id,
                    'titulo' => $tarea->nombre,
                    'descripcion' => $tarea->descripcion,
                    'estado' => $tarea->estado ? $tarea->estado->nombre : 'Sin estado',
                    'estado_id' => $tarea->estado_id,
                    'meta' => $tarea->meta ? $tarea->meta->nombre : 'Sin meta',
                    'meta_id' => $tarea->meta_id,
                    'equipo' => $tarea->meta && $tarea->meta->equipo ? $tarea->meta->equipo->nombre : 'Sin equipo',
                    'equipo_id' => $tarea->meta && $tarea->meta->equipo ? $tarea->meta->equipo->id : null,
                    'fecha_creacion' => $tarea->fecha_creacion ? \Carbon\Carbon::parse($tarea->fecha_creacion)->format('Y-m-d') : null,
                    'fecha_entrega' => $tarea->fecha_entrega ? \Carbon\Carbon::parse($tarea->fecha_entrega)->format('Y-m-d') : null,
                    'esta_vencida' => $tarea->esta_vencida,
                    'esta_completada' => $tarea->esta_completada
                ];
            });

            // Transformar equipos para la vista
            $equiposTransformados = $equipos->map(function($equipo) {
                return [
                    'id' => $equipo->id,
                    'nombre' => $equipo->nombre
                ];
            });

            return view('coordinador-general.actividades.index', [
                'tareas' => $tareasTransformadas,
                'equipos' => $equiposTransformados,
                'estados' => $estados,
                'empresa' => $empresa
            ]);

        } catch (\Exception $e) {
            Log::error('Error en actividades index', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Error al cargar las actividades: ' . $e->getMessage());
        }
    }

    public function getMetasPorEquipo($equipoId)
    {
        try {
            // Validar que el equipoId sea un número válido
            if (!is_numeric($equipoId) || $equipoId <= 0) {
                return response()->json(['error' => 'ID de equipo inválido'], 400);
            }

            // Verificar permisos del coordinador general
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->equipoRepositorio->equipoPerteneceeACoordinadorGeneral($equipoId, $trabajador->id)) {
                return response()->json(['error' => 'No tienes permisos para ver las metas de este equipo'], 403);
            }

            // Verificar que el equipo existe y es válido
            $equipo = $this->equipoRepositorio->getById($equipoId);
            
            if (!$equipo) {
                return response()->json(['error' => 'Equipo no encontrado o no válido'], 404);
            }

            // Obtener las metas del equipo
            $metas = $this->metaRepositorio->getByEquipo($equipoId);

            // Transformar las metas para la respuesta
            $metasTransformadas = $metas->map(function($meta) {
                return [
                    'id' => $meta->id,
                    'nombre' => $meta->nombre,
                    'descripcion' => $meta->descripcion ?? '',
                    'estado' => $meta->estado ? $meta->estado->nombre : 'Sin estado'
                ];
            });

            return response()->json($metasTransformadas);

        } catch (\Exception $e) {
            Log::error('Error en getMetasPorEquipo', [
                'error' => $e->getMessage(),
                'equipo_id' => $equipoId
            ]);
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'meta_id' => 'required|integer|exists:metas,id',
            'estado_id' => 'required|integer|exists:estados,id',
            'fecha_entrega' => 'nullable|date'
        ]);

        try {
            // Obtener la empresa del coordinador general autenticado
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$trabajador) {
                return response()->json(['error' => 'No se encontró información del trabajador'], 400);
            }

            // Verificar que la meta pertenece a las áreas del coordinador general
            if (!$this->metaRepositorio->metaPerteneceeACoordinadorGeneral($request->meta_id, $trabajador->id)) {
                return response()->json(['error' => 'La meta seleccionada no está bajo tu coordinación'], 400);
            }

            // Verificar que la meta es válida (pertenece a un equipo válido)
            $meta = $this->metaRepositorio->getById($request->meta_id);
            if (!$meta) {
                return response()->json(['error' => 'La meta seleccionada no es válida o no tiene equipo con coordinador válido'], 400);
            }

            $tarea = $this->tareaRepositorio->create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'meta_id' => $request->meta_id,
                'estado_id' => $request->estado_id,
                'fecha_creacion' => now(),
                'fecha_entrega' => $request->fecha_entrega
            ]);

            // Cargar relaciones para la respuesta
            $tarea->load(['meta.equipo', 'estado']);

            // Actualizar el estado de la meta basado en sus tareas
            $this->actualizarEstadoMeta($request->meta_id);

            Log::info('Tarea creada exitosamente', [
                'tarea_id' => $tarea->id,
                'meta_id' => $request->meta_id,
                'trabajador_id' => $trabajador->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Actividad creada exitosamente',
                'tarea' => [
                    'id' => $tarea->id,
                    'titulo' => $tarea->nombre,
                    'descripcion' => $tarea->descripcion,
                    'estado' => $tarea->estado ? $tarea->estado->nombre : 'Sin estado',
                    'estado_id' => $tarea->estado_id,
                    'meta' => $tarea->meta ? $tarea->meta->nombre : 'Sin meta',
                    'meta_id' => $tarea->meta_id,
                    'equipo' => $tarea->meta && $tarea->meta->equipo ? $tarea->meta->equipo->nombre : 'Sin equipo',
                    'equipo_id' => $tarea->meta && $tarea->meta->equipo ? $tarea->meta->equipo->id : null,
                    'fecha_creacion' => $tarea->fecha_creacion ? \Carbon\Carbon::parse($tarea->fecha_creacion)->format('Y-m-d') : null,
                    'fecha_entrega' => $tarea->fecha_entrega ? \Carbon\Carbon::parse($tarea->fecha_entrega)->format('Y-m-d') : null,
                    'esta_vencida' => $tarea->esta_vencida,
                    'esta_completada' => $tarea->esta_completada
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en store actividad', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error al crear la actividad: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            // Verificar permisos
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->tareaRepositorio->tareaPerteneceeACoordinadorGeneral($id, $trabajador->id)) {
                return response()->json(['error' => 'No tienes permisos para ver esta actividad'], 403);
            }

            $tarea = $this->tareaRepositorio->getById($id);
            
            if (!$tarea) {
                return response()->json(['error' => 'Actividad no encontrada o no válida'], 404);
            }

            return response()->json([
                'id' => $tarea->id,
                'titulo' => $tarea->nombre,
                'descripcion' => $tarea->descripcion,
                'estado' => $tarea->estado ? $tarea->estado->nombre : 'Sin estado',
                'estado_id' => $tarea->estado_id,
                'meta' => $tarea->meta ? $tarea->meta->nombre : 'Sin meta',
                'meta_id' => $tarea->meta_id,
                'equipo' => $tarea->meta && $tarea->meta->equipo ? $tarea->meta->equipo->nombre : 'Sin equipo',
                'equipo_id' => $tarea->meta && $tarea->meta->equipo ? $tarea->meta->equipo->id : null,
                'fecha_creacion' => $tarea->fecha_creacion ? \Carbon\Carbon::parse($tarea->fecha_creacion)->format('d/m/Y') : null,
                'fecha_entrega' => $tarea->fecha_entrega ? \Carbon\Carbon::parse($tarea->fecha_entrega)->format('Y-m-d') : null,
                'esta_vencida' => $tarea->esta_vencida,
                'esta_completada' => $tarea->esta_completada
            ]);

        } catch (\Exception $e) {
            Log::error('Error en show actividad', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['error' => 'Error al cargar la actividad'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'meta_id' => 'required|integer|exists:metas,id',
            'estado_id' => 'required|integer|exists:estados,id',
            'fecha_entrega' => 'nullable|date'
        ]);

        try {
            // Verificar permisos
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->tareaRepositorio->tareaPerteneceeACoordinadorGeneral($id, $trabajador->id)) {
                return response()->json(['error' => 'No tienes permisos para editar esta actividad'], 403);
            }

            // Verificar que la meta pertenece a las áreas del coordinador general
            if (!$this->metaRepositorio->metaPerteneceeACoordinadorGeneral($request->meta_id, $trabajador->id)) {
                return response()->json(['error' => 'La meta seleccionada no está bajo tu coordinación'], 400);
            }

            // Verificar que la meta es válida (pertenece a un equipo válido)
            $meta = $this->metaRepositorio->getById($request->meta_id);
            if (!$meta) {
                return response()->json(['error' => 'La meta seleccionada no es válida o no tiene equipo con coordinador válido'], 400);
            }

            // Obtener la tarea actual para saber si cambió la meta
            $tareaActual = $this->tareaRepositorio->getById($id);
            $metaAnteriorId = $tareaActual ? $tareaActual->meta_id : null;

            $actualizado = $this->tareaRepositorio->update($id, [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'meta_id' => $request->meta_id,
                'estado_id' => $request->estado_id,
                'fecha_entrega' => $request->fecha_entrega
            ]);

            if (!$actualizado) {
                return response()->json(['error' => 'Actividad no encontrada'], 404);
            }

            // Actualizar el estado de la meta actual
            $this->actualizarEstadoMeta($request->meta_id);

            // Si cambió la meta, actualizar también el estado de la meta anterior
            if ($metaAnteriorId && $metaAnteriorId != $request->meta_id) {
                $this->actualizarEstadoMeta($metaAnteriorId);
            }

            return response()->json([
                'success' => true,
                'message' => 'Actividad actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en update actividad', [
                'error' => $e->getMessage(),
                'id' => $id,
                'data' => $request->all()
            ]);
            
            return response()->json(['error' => 'Error al actualizar la actividad: ' . $e->getMessage()], 500);
        }
    }

    public function updateEstado(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:tareas,id',
            'estado_id' => 'required|integer|exists:estados,id'
        ]);

        try {
            // Verificar permisos
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->tareaRepositorio->tareaPerteneceeACoordinadorGeneral($request->id, $trabajador->id)) {
                return response()->json(['error' => 'No tienes permisos para modificar esta actividad'], 403);
            }

            // Obtener la tarea para conocer su meta_id
            $tarea = $this->tareaRepositorio->getById($request->id);
            if (!$tarea) {
                return response()->json(['error' => 'Actividad no encontrada'], 404);
            }

            $metaId = $tarea->meta_id;

            $actualizado = $this->tareaRepositorio->update($request->id, [
                'estado_id' => $request->estado_id
            ]);

            if (!$actualizado) {
                return response()->json(['error' => 'Actividad no encontrada'], 404);
            }

            // Actualizar el estado de la meta asociada
            $resultadoMeta = $this->actualizarEstadoMeta($metaId);

            return response()->json([
                'success' => true,
                'message' => 'Estado de actividad actualizado exitosamente',
                'meta_actualizada' => $resultadoMeta['actualizado'],
                'meta_estado' => $resultadoMeta['estado']
            ]);

        } catch (\Exception $e) {
            Log::error('Error en updateEstado', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'id' => $request->id,
                'estado_id' => $request->estado_id
            ]);
            
            return response()->json(['error' => 'Error al actualizar el estado: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Verificar permisos
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->tareaRepositorio->tareaPerteneceeACoordinadorGeneral($id, $trabajador->id)) {
                return response()->json(['error' => 'No tienes permisos para eliminar esta actividad'], 403);
            }

            // Obtener la tarea para conocer su meta_id antes de eliminarla
            $tarea = $this->tareaRepositorio->getById($id);
            if (!$tarea) {
                return response()->json(['error' => 'Actividad no encontrada'], 404);
            }

            $metaId = $tarea->meta_id;

            $eliminado = $this->tareaRepositorio->delete($id);

            if (!$eliminado) {
                return response()->json(['error' => 'Actividad no encontrada'], 404);
            }

            // Actualizar el estado de la meta después de eliminar la tarea
            $this->actualizarEstadoMeta($metaId);

            return response()->json(['success' => true, 'message' => 'Actividad eliminada exitosamente']);

        } catch (\Exception $e) {
            Log::error('Error en destroy actividad', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'id' => $id]);
            return response()->json(['error' => 'Error al eliminar la actividad: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualiza automáticamente el estado de una meta basado en el estado de sus tareas
     * 
     * @param int $metaId ID de la meta a actualizar
     * @return array Resultado de la actualización
     */
    private function actualizarEstadoMeta(int $metaId): array
    {
        try {
            // Obtener la meta con sus tareas
            $meta = $this->metaRepositorio->getById($metaId);
            
            if (!$meta) {
                Log::warning('Meta no encontrada para actualizar estado', ['meta_id' => $metaId]);
                return [
                    'actualizado' => false,
                    'estado' => null,
                    'error' => 'Meta no encontrada'
                ];
            }
            
            // Guardar el estado anterior para saber si cambió
            $estadoAnterior = $meta->estado ? $meta->estado->nombre : null;
            
            // Verificar si todas las tareas están completadas
            $todasCompletadas = $this->tareaRepositorio->todasLasTareasCompletadas($metaId);
            
            // Determinar el nuevo estado
            $estadoNombre = $todasCompletadas ? 'Completo' : 'En proceso';
            $estado = $this->estadoRepositorio->findOneBy('nombre', $estadoNombre);
            
            if (!$estado) {
                Log::error('Estado no encontrado para actualizar meta', [
                    'meta_id' => $metaId,
                    'estado_nombre' => $estadoNombre
                ]);
                return [
                    'actualizado' => false,
                    'estado' => $estadoAnterior,
                    'error' => 'Estado no encontrado: ' . $estadoNombre
                ];
            }
            
            // Solo actualizar si el estado es diferente
            $actualizado = false;
            $estadoActualizado = false;
            
            if ($meta->estado_id != $estado->id) {
                $actualizado = $this->metaRepositorio->update($metaId, ['estado_id' => $estado->id]);
                $estadoActualizado = true;
                
                Log::info('Estado de meta actualizado automáticamente', [
                    'meta_id' => $metaId,
                    'meta_nombre' => $meta->nombre,
                    'estado_anterior' => $estadoAnterior,
                    'nuevo_estado' => $estadoNombre,
                    'todas_tareas_completadas' => $todasCompletadas
                ]);
            } else {
                // No hubo cambio de estado, pero consideramos exitosa la operación
                $actualizado = true;
                
                Log::info('Estado de meta verificado (sin cambios)', [
                    'meta_id' => $metaId,
                    'meta_nombre' => $meta->nombre,
                    'estado' => $estadoNombre,
                    'todas_tareas_completadas' => $todasCompletadas
                ]);
            }
            
            return [
                'actualizado' => $actualizado,
                'estado_actualizado' => $estadoActualizado,
                'estado' => $estadoNombre,
                'estado_anterior' => $estadoAnterior,
                'meta_nombre' => $meta->nombre
            ];
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de meta automáticamente', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'meta_id' => $metaId
            ]);
            
            return [
                'actualizado' => false,
                'estado' => null,
                'error' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
