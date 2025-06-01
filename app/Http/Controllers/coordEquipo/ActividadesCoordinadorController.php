<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoordEquipo\ActualizarEstadoActividadRequest;
use App\Repositories\EquipoRepositorio;
use App\Repositories\EstadoRepositorio;
use App\Repositories\MetaRepositorio;
use App\Repositories\TareaRepositorio;
use App\Repositories\TrabajadorRepositorio;
use Auth;
use Illuminate\Http\Request;

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
        $estados = $this->estadoRepositorio->getAll();
        return view('private.coord-equipo.actividades', compact('estados'));
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

            $tareas = $this->tareaRepositorio->getTareasPorEquipo($equipo->id);

            return response()->json(['tareas' => $tareas], 200);
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

            $metas = $this->metaRepositorio->getMetasPorEquipo($equipo->id);

            return response()->json($metas, 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al obtener metas.', 'detalle' => $e->getMessage()], 500);
        }
    }


    public function estados()
    {
        try {
            $estados = $this->estadoRepositorio->getAll();
            return response()->json($estados, 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al obtener estados.', 'detalle' => $e->getMessage()], 500);
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
}
