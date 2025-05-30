<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Validator;

class CoordEquipoController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth', 'role:coordinador-grupo']);
    }

    public function dashboard()
    {
        $user = Auth::user();

        
        
        // Datos simulados - reemplazar con datos reales de la base de datos
        $stats = [
            'total_colaboradores' => 12,
            'metas_completadas' => 8,
            'actividades_pendientes' => 15,
            'reuniones_programadas' => 3
        ];

        $metas = [
            [
                'id' => 1,
                'titulo' => 'Incrementar ventas Q1',
                'descripcion' => 'Aumentar las ventas en un 25% durante el primer trimestre',
                'progreso' => 75,
                'fecha_limite' => '2024-03-31',
                'estado' => 'en_progreso',
                'colaboradores_asignados' => 5
            ],
            [
                'id' => 2,
                'titulo' => 'Mejorar satisfacción cliente',
                'descripcion' => 'Alcanzar un 95% de satisfacción en encuestas',
                'progreso' => 60,
                'fecha_limite' => '2024-04-15',
                'estado' => 'en_progreso',
                'colaboradores_asignados' => 3
            ]
        ];

        $actividades = [
            [
                'id' => 1,
                'titulo' => 'Revisión de procesos',
                'descripcion' => 'Revisar y optimizar procesos actuales',
                'fecha_limite' => '2024-02-15',
                'prioridad' => 'alta',
                'estado' => 'pendiente',
                'asignado_a' => 'Juan Pérez'
            ],
            [
                'id' => 2,
                'titulo' => 'Capacitación equipo',
                'descripcion' => 'Sesión de capacitación para nuevas herramientas',
                'fecha_limite' => '2024-02-20',
                'prioridad' => 'media',
                'estado' => 'en_progreso',
                'asignado_a' => 'María García'
            ]
        ];

        $colaboradores = [
            [
                'id' => 1,
                'nombre' => 'Juan Pérez',
                'email' => 'juan@empresa.com',
                'area' => 'Ventas',
                'actividades_asignadas' => 3,
                'estado' => 'activo'
            ],
            [
                'id' => 2,
                'nombre' => 'María García',
                'email' => 'maria@empresa.com',
                'area' => 'Marketing',
                'actividades_asignadas' => 2,
                'estado' => 'activo'
            ]
        ];

        return view('private.coord-equipo.dashboard', compact('stats', 'metas', 'actividades', 'colaboradores'));
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
