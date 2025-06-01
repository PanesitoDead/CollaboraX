<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoordEquipo\ActualizarEstadoActividadRequest;
use App\Http\Requests\CoordEquipo\CrearTareaEquipoRequest;
use App\Repositories\EquipoRepositorio;
use App\Repositories\EstadoRepositorio;
use App\Repositories\MetaRepositorio;
use App\Repositories\TareaRepositorio;
use App\Repositories\TrabajadorRepositorio;
use Auth;
use Illuminate\Http\Request;
use Str;

class ActividadesCoordinadorController extends Controller
{

    protected EstadoRepositorio $estadoRepositorio;
    protected TareaRepositorio $tareaRepositorio;
    protected MetaRepositorio $metaRepositorio;
    protected TrabajadorRepositorio $trabajadorRepositorio;
    protected EquipoRepositorio $equipoRepositorio;

    public function __construct(EstadoRepositorio $estadoRepositorio, TareaRepositorio $tareaRepositorio, MetaRepositorio $metaRepositorio, TrabajadorRepositorio $trabajadorRepositorio, EquipoRepositorio $equipoRepositorio) {
        $this->estadoRepositorio = $estadoRepositorio;
        $this->tareaRepositorio = $tareaRepositorio;
        $this->metaRepositorio = $metaRepositorio;
        $this->trabajadorRepositorio = $trabajadorRepositorio;
        $this->equipoRepositorio = $equipoRepositorio;
    }

    public function index()
    {
        $usuario = Auth::user();
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
        $equipo = $this->equipoRepositorio->findOneBy('coordinador_id', $trabajador->id);

        $estados = $this->estadoRepositorio->getAll();
        $metas = $this->metaRepositorio->getMetasPorEquipo($equipo->id);
        return view('private.coord-equipo.actividades', compact('estados', 'metas'));
    }

    public function actividadesPorEquipo()
    {
        try {
            $usuario = Auth::user();
            $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
            $equipo = $this->equipoRepositorio->findOneBy('coordinador_id', $trabajador->id);

            if (!$equipo) {
                return response()->json(['error' => 'Equipo no encontrado.'], 404);
            }

            $tareas = $this->tareaRepositorio->getTareasPorEquipoCustom($equipo->id);

            return response()->json($tareas, 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al obtener actividades.', 'detalle' => $e->getMessage()], 500);
        }
    }


    public function metasPorEquipo()
    {
        try {
            $usuario = Auth::user();
            $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
            $equipo = $this->equipoRepositorio->findOneBy('coordinador_id', $trabajador->id);

            if (!$equipo) {
                return response()->json(['error' => 'Equipo no encontrado.'], 404);
            }

            $metas = $this->metaRepositorio->getMetasPorEquipoCustom($equipo->id);

            return response()->json($metas, 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al obtener metas.', 'detalle' => $e->getMessage()], 500);
        }
    }


    public function estados()
    {
        try {
            $estados = $this->estadoRepositorio->getAll();

            $estadosFormateados = $estados->map(function ($estado) {
                return [
                    'id' => $estado->id,
                    'nombre' => $estado->nombre,
                    'slug' => Str::slug($estado->nombre),
                ];
            });

            return response()->json($estadosFormateados, 200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Error al obtener estados.',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }


    public function actualizarEstadoActividad(ActualizarEstadoActividadRequest $request, $id)
    {
        try {
            $actualizado = $this->tareaRepositorio->update($id, ['estado_id' => $request->estado_id]);

            if (!$actualizado) {
                return response()->json(['error' => 'No se pudo actualizar el estado de la actividad.'], 400);
            }

            return response()->json(['success' => true], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al actualizar estado.', 'detalle' => $e->getMessage()], 500);
        }
    }

    public function storeActividad (CrearTareaEquipoRequest $request)
    {
        try {
            $data = $request->only(['nombre', 'descripcion', 'meta_id', 'fecha_entrega']);
            $data['estado_id'] = $this->estadoRepositorio->findOneBy('nombre', 'En proceso')->id;

            $tarea = $this->tareaRepositorio->create($data);

            if (!$tarea) {
                return redirect()->back()->withErrors('No se pudo crear la actividad.');
            }

            return redirect()->back()->with('success', 'Actividad creada correctamente.');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear la actividad: ' . $e->getMessage()]);
        }
    }

    public function crearActividad(CrearTareaEquipoRequest $request)
    {
        try {
            $data = $request->only(['nombre', 'descripcion', 'meta_id', 'fecha_entrega']);
            $data['estado_id'] = $this->estadoRepositorio->findOneBy('nombre', 'En proceso')->id; 

            $tarea = $this->tareaRepositorio->create($data);

            if (!$tarea) {
                return response()->json(['error' => 'No se pudo crear la actividad.'], 400);
            }

            $tarea->load('meta', 'estado');

            $tareaFormateada = [
                'id' => $tarea->id,
                'titulo' => $tarea->nombre,
                'descripcion' => $tarea->descripcion,
                'fecha_limite' => $tarea->fecha_entrega,
                'estado_slug' => Str::slug($tarea->estado->nombre),
                'meta' => [
                    'id' => $tarea->meta->id,
                    'titulo' => $tarea->meta->nombre,
                ],
            ];

            return response()->json([
                'success' => true,
                'tarea' => $tareaFormateada,
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Error al crear la actividad.',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}
