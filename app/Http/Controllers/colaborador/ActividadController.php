<?php

namespace App\Http\Controllers\colaborador;

use App\Http\Controllers\Controller;
use App\Repositories\TareaRepositorio;
use App\Repositories\TrabajadorRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Laravel\Prompts\error;

class ActividadController extends Controller
{
    protected TrabajadorRepositorio $trabajadorRepositorio;

    protected TareaRepositorio $tareaRepositorio;

    public function __construct(TrabajadorRepositorio $trabajadorRepositorio, TareaRepositorio $tareaRepositorio)
    {
        $this->tareaRepositorio = $tareaRepositorio;
        $this->trabajadorRepositorio = $trabajadorRepositorio;
    }
    public function index(Request $request)
    {
        $trabajador = $this->getTrabajador();
        $equipo = $trabajador->equipoFromColab;
        $actividades = $equipo ? $this->tareaRepositorio->getTareasPorEquipo($equipo->id) : collect();

        $searchQuery = $request->get('search', '');

        $actividades = $actividades->map(function ($actividad) {
            return [
                'id' => $actividad->id,
                'nombre' => $actividad->nombre,
                'descripcion' => $actividad->descripcion,
                'fecha_entrega' => $actividad->fecha_entrega?? 'Sin fecha de entrega',
                'estado_id' => $actividad->estado->id ?? null,
                'estado' => $actividad->estado ? $actividad->estado->nombre : 'Desconocido',
                // 'prioridad' => $actividad->prioridad,
                'equipo' => $actividad->meta->equipo ? $actividad->meta->equipo->nombre : 'Sin equipo',
                'equipo_id' => $actividad->meta->equipo ? $actividad->meta->equipo->id : null,
                'meta' => $actividad->meta ? $actividad->meta->nombre : 'Sin meta',
                'meta_id' => $actividad->meta ? $actividad->meta->id : null,
                'asignado_por' => $actividad->meta->equipo ? $actividad->meta->equipo->coordinador->nombreCompleto : 'Sin asignar',
            ];
        });

        // Filtrar actividades por búsqueda
        if ($searchQuery) {
            $actividades = $actividades->filter(function ($actividad) use ($searchQuery) {
                return str_contains(strtolower($actividad['nombre']), strtolower($searchQuery)) ||
                       str_contains(strtolower($actividad['descripcion']), strtolower($searchQuery)) ||
                       str_contains(strtolower($actividad['equipo']), strtolower($searchQuery));
            });
        }

        // Agrupar por estado para el kanban
        $kanbanColumns = [
            [
                'id' => '',
                'titulo' => 'Incompletas',
                'estado_id' => '1',
                'color' => 'yellow',
                'items' => $actividades->where('estado_id', '1')->values(),
            ],
            [
                'id' => 'en-proceso',
                'titulo' => 'En Proceso',
                'estado_id' => '2',
                'color' => 'blue',
                'items' => $actividades->where('estado_id', '2')->values(),
            ],
            [
                'id' => 'completas',
                'titulo' => 'Completas',
                'estado_id' => '3',
                'color' => 'green',
                'items' => $actividades->where('estado_id', '3')->values(),
            ],
            [
                'id' => 'suspendidas',
                'titulo' => 'Suspendidas',
                'estado_id' => '4',
                'color' => 'red',
                'items' => $actividades->where('estado_id', '4')->values(),
            ],
        ];

        return view('private.colaborador.actividades', compact('kanbanColumns', 'searchQuery', 'actividades'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $actividad = $this->tareaRepositorio->getById($id);
        if (!$actividad) {
            return response()->json([
                'status' => 'error',
                'message' => 'Actividad no encontrada.'
            ]);
        }
        return response()->json([
            'id' => $actividad->id,
            'nombre' => $actividad->nombre,
            'descripcion' => $actividad->descripcion,
            'fecha_entrega' => $actividad->fecha_entrega?? 'Sin fecha de entrega',
            'estado_id' => $actividad->estado->id ?? null,
            'estado' => $actividad->estado ? $actividad->estado->nombre : 'Desconocido',
            // 'prioridad' => $actividad->prioridad,
            'equipo' => $actividad->meta->equipo ? $actividad->meta->equipo->nombre : 'Sin equipo',
            'equipo_id' => $actividad->meta->equipo ? $actividad->meta->equipo->id : null,
            'meta' => $actividad->meta ? $actividad->meta->nombre : 'Sin meta',
            'meta_id' => $actividad->meta ? $actividad->meta->id : null,
            'asignado_por' => $actividad->meta->equipo ? $actividad->meta->equipo->coordinador->nombreCompleto : 'Sin asignar',
        ]);
    }

    public function getTrabajador()
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return redirect()->route('colaborador.actividades')->with('error', 'Usuario no autenticado.');
        }
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
        if (!$trabajador) {
            return redirect()->route('colaborador.actividades')->with('error', 'Trabajador no encontrado.');
        }
        return $trabajador;
    }
}
