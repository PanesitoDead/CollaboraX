<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use App\Repositories\EquipoRepositorio;
use App\Repositories\MetaRepositorio;
use App\Repositories\TareaRepositorio;
use App\Repositories\TrabajadorRepositorio;
use Auth;
use Illuminate\Http\Request;
use Validator;

class CoordEquipoController extends Controller
{
    protected TrabajadorRepositorio $trabajadorRepositorio;
    protected EquipoRepositorio $equipoRepositorio;
    protected MetaRepositorio $metaRepositorio;
    protected TareaRepositorio $tareaRepositorio;

    public function __construct(TrabajadorRepositorio $trabajadorRepositorio, EquipoRepositorio $equipoRepositorio, MetaRepositorio $metaRepositorio, TareaRepositorio $tareaRepositorio)
    {
        $this->trabajadorRepositorio = $trabajadorRepositorio;
        $this->equipoRepositorio = $equipoRepositorio;
        $this->metaRepositorio = $metaRepositorio;
        $this->tareaRepositorio = $tareaRepositorio;
    }

    public function dashboard()
    {
        $usuario = Auth::user();
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
        $equipo = $this->equipoRepositorio->findOneBy('coordinador_id', $trabajador->id);

        $colaboradores = $this->trabajadorRepositorio->getMiembrosEquipo($equipo->id);
        $metas = $this->metaRepositorio->getMetasConProgresoPorEquipo($equipo->id);
        $metas_completadas = $this->metaRepositorio->findByFields(['equipo_id' => $equipo->id, 'estado_id' => 3]);
        $actividades = $this->tareaRepositorio->getTareasPorEquipo($equipo->id);
        $actividades_completadas = $this->tareaRepositorio->getTareasCompletadasPorEquipo($equipo->id);
                
        $stats = [
            'total_colaboradores' => $colaboradores->count(),
            'metas_totales' => $metas->count(),
            'metas_completadas' => $metas_completadas->count(),
            'actividades_totales' => $actividades->count(),
            'actividades_completadas' => $actividades_completadas->count(),
            'cumplimiento' => $actividades_completadas->count() / $actividades->count() * 100
        ];

        return view('private.coord-equipo.dashboard', compact('stats', 'metas', 'actividades', 'colaboradores', 'equipo'));
    }

    public function crearActividad(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_limite' => 'required|date|after:today',
            'prioridad' => 'required|in:baja,media,alta',
            'asignado_a' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lógica para crear actividad
        // Activity::create($request->validated());

        return back()->with('success', 'Actividad creada exitosamente');
    }

    public function crearReunion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha' => 'required|date|after:now',
            'duracion' => 'required|integer|min:15|max:480',
            'participantes' => 'required|array|min:1'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Lógica para crear reunión
        // Meeting::create($request->validated());

        return back()->with('success', 'Reunión programada exitosamente');
    }
}
