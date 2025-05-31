<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use App\Repositories\EquipoRepositorio;
use App\Repositories\InvitacionRepositorio;
use App\Repositories\MetaRepositorio;
use App\Repositories\ReunionRepositorio;
use App\Repositories\TrabajadorRepositorio;
use Auth;
use Illuminate\Http\Request;
use Validator;

class EquipoCoordinadorController extends Controller
{

    protected TrabajadorRepositorio $trabajadorRepositorio;
    protected EquipoRepositorio $equipoRepositorio;
    protected MetaRepositorio $metaRepositorio;
    protected ReunionRepositorio $reunionRepositorio;
    protected InvitacionRepositorio $invitacionRepositorio;

    public function __construct(TrabajadorRepositorio $trabajadorRepositorio, EquipoRepositorio $equipoRepositorio, MetaRepositorio $metaRepositorio, ReunionRepositorio $reunionRepositorio, InvitacionRepositorio $invitacionRepositorio)
    {
        $this->trabajadorRepositorio = $trabajadorRepositorio;
        $this->equipoRepositorio = $equipoRepositorio;
        $this->metaRepositorio = $metaRepositorio;
        $this->reunionRepositorio = $reunionRepositorio;
        $this->invitacionRepositorio = $invitacionRepositorio;
    }

    public function index()
    {

        $usuario = Auth::user();
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
        $equipo = $this->equipoRepositorio->findOneBy('coordinador_id', $trabajador->id);
        $miembros = $this->trabajadorRepositorio->getMiembrosEquipo($equipo->id);
        $cantidadMiembros = $this->trabajadorRepositorio->countMiembrosEquipo($equipo->id);
        $invitaciones = $this->invitacionRepositorio->getInvitacionesPorEquipo($equipo->id);

        $metas = $this->metaRepositorio->getMetasPorEquipo($equipo->id);
        $reunionesPendientes = $this->reunionRepositorio->countReunionesPendientesPorEquipo($equipo->id);

        // Cálculos
        $metasCompletadas = $metas->where('estado_id', 3)->count(); // estado_id 3 = completado
        $metasActivas = $metas->where('estado_id', '!=', 3)->count();


        $totalTareas = 0;
        $tareasCompletadas = 0;
        $tareasProgreso = 0;

        foreach ($metas as $meta) {
            $totalTareas += $meta->tareas->count();
            $tareasCompletadas += $meta->tareas->where('estado_id', 3)->count(); // estado_id 3 = completado
            $tareasProgreso += $meta->tareas->where('estado_id', 2)->count();
        }

        $rendimiento = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0;
        
        $stats = [
            'miembros' => $cantidadMiembros,
            'actividades_progreso' => $tareasProgreso,
            'metas_completadas' => $metasCompletadas,
            'metas_activas' => $metasActivas,
            'actividades_total' => $totalTareas,
            'actividades_completadas' => $tareasCompletadas,
            'rendimiento' => $rendimiento,
            'reuniones_pendientes' => $reunionesPendientes
        ];

        // Invitaciones
        // $invitaciones = [
        //     [
        //         'id' => 1,
        //         'colaborador' => [
        //             'id' => 201,
        //             'nombre' => 'Lucía Ramírez',
        //             'email' => 'lucia.ramirez@empresa.cx.com',
        //             'rol' => 'Diseñador UX'
        //         ],
        //         'fecha' => '2025-05-18 14:30:00',
        //         'estado' => 'pendiente'
        //     ],
        //     [
        //         'id' => 2,
        //         'colaborador' => [
        //             'id' => 202,
        //             'nombre' => 'Gabriel Herrera',
        //             'email' => 'gabriel.herrera@empresa.cx.com',
        //             'rol' => 'Desarrollador Backend'
        //         ],
        //         'fecha' => '2025-05-17 10:15:00',
        //         'estado' => 'aceptada'
        //     ],
        //     [
        //         'id' => 3,
        //         'colaborador' => [
        //             'id' => 203,
        //             'nombre' => 'Daniela Vargas',
        //             'email' => 'daniela.vargas@empresa.cx.com',
        //             'rol' => 'QA Engineer'
        //         ],
        //         'fecha' => '2025-05-15 16:45:00',
        //         'estado' => 'rechazada'
        //     ]
        // ];

        // Colaboradores disponibles para invitar
        $colaboradores_disponibles = [
            [
                'id' => 101,
                'nombre' => 'Diego Morales',
                'email' => 'diego.morales@empresa.cx.com',
                'departamento' => 'Desarrollo',
                'rol' => 'Desarrollador Frontend',
                'avatar' => '/placeholder-32px.png'
            ],
            [
                'id' => 102,
                'nombre' => 'Sofía Gutiérrez',
                'email' => 'sofia.gutierrez@empresa.cx.com',
                'departamento' => 'Diseño',
                'rol' => 'Diseñador UX/UI',
                'avatar' => '/placeholder-32px.png'
            ],
            [
                'id' => 103,
                'nombre' => 'Alejandro Torres',
                'email' => 'alejandro.torres@empresa.cx.com',
                'departamento' => 'Desarrollo',
                'rol' => 'Desarrollador Backend',
                'avatar' => '/placeholder-32px.png'
            ],
            [
                'id' => 104,
                'nombre' => 'Valentina Ruiz',
                'email' => 'valentina.ruiz@empresa.cx.com',
                'departamento' => 'QA',
                'rol' => 'Tester',
                'avatar' => '/placeholder-32px.png'
            ],
            [
                'id' => 105,
                'nombre' => 'Javier Mendoza',
                'email' => 'javier.mendoza@empresa.cx.com',
                'departamento' => 'Desarrollo',
                'rol' => 'Desarrollador Full Stack',
                'avatar' => '/placeholder-32px.png'
            ]
        ];

        return view('private.coord-equipo.mi-equipo', compact(
            'stats', 
            'equipo',
            'miembros', 
            'invitaciones', 
            'colaboradores_disponibles'
        ));
    }

    public function invitarColaboradores(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'colaboradores' => 'required|array|min:1',
            'colaboradores.*' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Lógica para enviar invitaciones
        foreach ($request->colaboradores as $colaboradorId) {
            // Crear invitación en la base de datos
            // Enviar notificación al colaborador
        }

        return redirect()->back()
            ->with('success', 'Invitaciones enviadas correctamente');
    }

    public function cancelarInvitacion($id)
    {
        // Lógica para cancelar invitación
        return redirect()->back()
            ->with('success', 'Invitación cancelada correctamente');
    }

    public function programarReunion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'fecha' => 'required|date|after:now',
            'duracion' => 'required|integer|min:15|max:480',
            'participantes' => 'required|array|min:1',
            'descripcion' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Lógica para crear reunión
        return redirect()->back()
            ->with('success', 'Reunión programada correctamente');
    }
}
