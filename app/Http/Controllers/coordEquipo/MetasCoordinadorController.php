<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use App\Repositories\EquipoRepositorio;
use App\Repositories\EstadoRepositorio;
use App\Repositories\MetaRepositorio;
use App\Repositories\TareaRepositorio;
use App\Repositories\TrabajadorRepositorio;
use Auth;
use Illuminate\Http\Request;

class MetasCoordinadorController extends Controller
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
        $metas = $this->metaRepositorio->getMetasConProgresoPorEquipo($equipo->id);

        return view('private.coord-equipo.metas', compact('metas', 'estados'));
    }

    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'titulo' => 'required|string|max:255',
        //     'descripcion' => 'required|string',
        //     'prioridad' => 'required|in:baja,media,alta',
        //     'fecha_limite' => 'required|date|after:today',
        //     'categoria' => 'required|string|max:100',
        //     'asignados' => 'required|array|min:1',
        //     'asignados.*' => 'exists:users,id'
        // ]);

        // if ($validator->fails()) {
        //     return redirect()->back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        // Aquí iría la lógica para crear la meta en la base de datos
        // Meta::create($request->validated());

        return redirect()->route('coordinador-grupo.metas')
            ->with('success', 'Meta creada exitosamente');
    }

    public function update(Request $request, $id)
    {
        // $validator = Validator::make($request->all(), [
        //     'titulo' => 'required|string|max:255',
        //     'descripcion' => 'required|string',
        //     'prioridad' => 'required|in:baja,media,alta',
        //     'fecha_limite' => 'required|date',
        //     'categoria' => 'required|string|max:100',
        //     'estado' => 'required|in:pendiente,en_progreso,completada,cancelada',
        //     'progreso' => 'required|integer|min:0|max:100'
        // ]);

        // if ($validator->fails()) {
        //     return redirect()->back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        // // Aquí iría la lógica para actualizar la meta
        // // $meta = Meta::findOrFail($id);
        // // $meta->update($request->validated());

        // return redirect()->route('coordinador-grupo.metas')
        //     ->with('success', 'Meta actualizada exitosamente');
    }

    public function destroy($id)
    {
        // Aquí iría la lógica para eliminar la meta
        // Meta::findOrFail($id)->delete();

        return redirect()->route('coordinador-grupo.metas')
            ->with('success', 'Meta eliminada exitosamente');
    }
}
