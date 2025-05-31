<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use App\Repositories\TareaRepositorio;
use App\Repositories\EquipoRepositorio;
use App\Repositories\MetaRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActividadesController extends Controller
{
    protected $tareaRepositorio;
    protected $equipoRepositorio;
    protected $metaRepositorio;

    public function __construct(
        TareaRepositorio $tareaRepositorio,
        EquipoRepositorio $equipoRepositorio,
        MetaRepositorio $metaRepositorio
    ) {
        $this->tareaRepositorio = $tareaRepositorio;
        $this->equipoRepositorio = $equipoRepositorio;
        $this->metaRepositorio = $metaRepositorio;
    }

    public function index()
    {
        try {
            // Por ahora usaremos empresa ID = 1 para pruebas
            $empresaId = 1;
            
            // Obtener todas las tareas de equipos válidos de la empresa
            $tareas = $this->tareaRepositorio->getAllByEmpresa($empresaId);
            $equipos = $this->equipoRepositorio->getAllByEmpresa($empresaId);
            $estados = $this->metaRepositorio->getEstadosDisponibles();

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

            // Transformar estados para la vista
            $estadosTransformados = $estados->map(function($estado) {
                return [
                    'id' => $estado->id,
                    'nombre' => $estado->nombre,
                    'descripcion' => $estado->descripcion
                ];
            });

            return view('coordinador-general.actividades.index', [
                'tareas' => $tareasTransformadas,
                'equipos' => $equiposTransformados,
                'estados' => $estadosTransformados
            ]);

        } catch (\Exception $e) {
            Log::error('Error en actividades index', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Error al cargar las actividades: ' . $e->getMessage());
        }
    }

    public function getActividades()
    {
        try {
            $empresaId = 1;
            $tareas = $this->tareaRepositorio->getAllByEmpresa($empresaId);

            $tareasTransformadas = $tareas->map(function($tarea) {
                return [
                    'id' => $tarea->id,
                    'titulo' => $tarea->nombre,
                    'descripcion' => $tarea->descripcion,
                    'estado' => $tarea->estado ? strtolower(str_replace(' ', '-', $tarea->estado->nombre)) : 'pendiente',
                    'estado_nombre' => $tarea->estado ? $tarea->estado->nombre : 'Pendiente',
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

            return response()->json($tareasTransformadas);

        } catch (\Exception $e) {
            Log::error('Error en getActividades', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al cargar actividades'], 500);
        }
    }

    public function getEquipos()
    {
        try {
            $empresaId = 1;
            $equipos = $this->equipoRepositorio->getAllByEmpresa($empresaId);

            $equiposTransformados = $equipos->map(function($equipo) {
                return [
                    'id' => $equipo->id,
                    'nombre' => $equipo->nombre
                ];
            });

            return response()->json($equiposTransformados);

        } catch (\Exception $e) {
            Log::error('Error en getEquipos', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al cargar equipos'], 500);
        }
    }

    public function getMetasPorEquipo($equipoId)
    {
        // LOGGING MEJORADO PARA DEBUGGING
        Log::info('=== INICIO getMetasPorEquipo ===', [
            'equipo_id' => $equipoId,
            'request_url' => request()->url(),
            'request_method' => request()->method(),
            'request_headers' => request()->headers->all(),
            'user_agent' => request()->userAgent()
        ]);
        
        try {
            // Validar que el equipoId sea un número válido
            if (!is_numeric($equipoId) || $equipoId <= 0) {
                Log::error('ID de equipo inválido', ['equipo_id' => $equipoId]);
                return response()->json(['error' => 'ID de equipo inválido'], 400);
            }

            // Verificar que el equipo existe
            $equipo = $this->equipoRepositorio->getById($equipoId);
            Log::info('Resultado búsqueda equipo', ['equipo_encontrado' => $equipo ? true : false]);
            
            if (!$equipo) {
                Log::error('Equipo no encontrado', ['equipo_id' => $equipoId]);
                return response()->json(['error' => 'Equipo no encontrado'], 404);
            }

            Log::info('Equipo encontrado', ['equipo_id' => $equipoId, 'equipo_nombre' => $equipo->nombre]);

            // Obtener las metas del equipo
            $metas = $this->metaRepositorio->getByEquipo($equipoId);
            Log::info('Metas obtenidas', ['equipo_id' => $equipoId, 'metas_count' => $metas->count()]);

            // Transformar las metas para la respuesta
            $metasTransformadas = $metas->map(function($meta) {
                return [
                    'id' => $meta->id,
                    'nombre' => $meta->nombre,
                    'descripcion' => $meta->descripcion ?? '',
                    'estado' => $meta->estado ? $meta->estado->nombre : 'Sin estado'
                ];
            });

            Log::info('=== FIN getMetasPorEquipo EXITOSO ===', [
                'equipo_id' => $equipoId, 
                'metas_transformadas' => $metasTransformadas->toArray()
            ]);

            return response()->json($metasTransformadas);

        } catch (\Exception $e) {
            Log::error('=== ERROR en getMetasPorEquipo ===', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'equipo_id' => $equipoId,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
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
            $tarea = $this->tareaRepositorio->create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'meta_id' => $request->meta_id,
                'estado_id' => $request->estado_id,
                'fecha_creacion' => now(),
                'fecha_entrega' => $request->fecha_entrega
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Actividad creada exitosamente',
                'tarea' => [
                    'id' => $tarea->id,
                    'titulo' => $tarea->nombre,
                    'descripcion' => $tarea->descripcion,
                    'estado' => $tarea->estado ? strtolower(str_replace(' ', '-', $tarea->estado->nombre)) : 'pendiente',
                    'estado_nombre' => $tarea->estado ? $tarea->estado->nombre : 'Pendiente',
                    'meta' => $tarea->meta ? $tarea->meta->nombre : 'Sin meta',
                    'equipo' => $tarea->meta && $tarea->meta->equipo ? $tarea->meta->equipo->nombre : 'Sin equipo',
                    'fecha_entrega' => $tarea->fecha_entrega ? \Carbon\Carbon::parse($tarea->fecha_entrega)->format('Y-m-d') : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en store actividad', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error al crear la actividad: ' . $e->getMessage()], 500);
        }
    }

    public function updateEstado(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:tareas,id',
            'estado_id' => 'required|integer|exists:estados,id'
        ]);

        try {
            $actualizado = $this->tareaRepositorio->update($request->id, [
                'estado_id' => $request->estado_id
            ]);

            if (!$actualizado) {
                return response()->json(['error' => 'Actividad no encontrada'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Estado de actividad actualizado exitosamente'
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
            $eliminado = $this->tareaRepositorio->delete($id);

            if (!$eliminado) {
                return response()->json(['error' => 'Actividad no encontrada'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Actividad eliminada exitosamente']);

        } catch (\Exception $e) {
            Log::error('Error en destroy actividad', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'id' => $id]);
            return response()->json(['error' => 'Error al eliminar la actividad: ' . $e->getMessage()], 500);
        }
    }
}
