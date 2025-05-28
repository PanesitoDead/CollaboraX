<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class EquipoCoordinadorController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth', 'role:coordinador-grupo']);
    }

    public function index()
    {
        // Estadísticas del equipo
        $stats = [
            'miembros' => 8,
            'miembros_nuevos' => 2,
            'metas_activas' => 4,
            'metas_completadas' => 1,
            'actividades_total' => 18,
            'actividades_progreso' => 12,
            'actividades_completadas' => 6,
            'rendimiento' => 92,
            'rendimiento_cambio' => 5
        ];

        // Miembros del equipo
        $miembros = [
            [
                'id' => 1,
                'nombre' => 'Ana Martínez',
                'email' => 'ana.martinez@empresa.cx.com',
                'rol' => 'Coordinador',
                'actividades_completadas' => 5,
                'actividades_totales' => 6,
                'rendimiento' => 95,
                'avatar' => '/placeholder-32px.png'
            ],
            [
                'id' => 2,
                'nombre' => 'Carlos López',
                'email' => 'carlos.lopez@empresa.cx.com',
                'rol' => 'Desarrollador Senior',
                'actividades_completadas' => 4,
                'actividades_totales' => 5,
                'rendimiento' => 92,
                'avatar' => '/placeholder-32px.png'
            ],
            [
                'id' => 3,
                'nombre' => 'María Rodríguez',
                'email' => 'maria.rodriguez@empresa.cx.com',
                'rol' => 'Desarrollador',
                'actividades_completadas' => 3,
                'actividades_totales' => 4,
                'rendimiento' => 88,
                'avatar' => '/placeholder-32px.png'
            ],
            [
                'id' => 4,
                'nombre' => 'Juan Pérez',
                'email' => 'juan.perez@empresa.cx.com',
                'rol' => 'Diseñador UI',
                'actividades_completadas' => 2,
                'actividades_totales' => 3,
                'rendimiento' => 85,
                'avatar' => '/placeholder-32px.png'
            ],
            [
                'id' => 5,
                'nombre' => 'Laura Sánchez',
                'email' => 'laura.sanchez@empresa.cx.com',
                'rol' => 'QA',
                'actividades_completadas' => 3,
                'actividades_totales' => 3,
                'rendimiento' => 100,
                'avatar' => '/placeholder-32px.png'
            ],
            [
                'id' => 6,
                'nombre' => 'Roberto Fernández',
                'email' => 'roberto.fernandez@empresa.cx.com',
                'rol' => 'Desarrollador',
                'actividades_completadas' => 2,
                'actividades_totales' => 3,
                'rendimiento' => 90,
                'avatar' => '/placeholder-32px.png'
            ]
        ];

        // Invitaciones
        $invitaciones = [
            [
                'id' => 1,
                'colaborador' => [
                    'id' => 201,
                    'nombre' => 'Lucía Ramírez',
                    'email' => 'lucia.ramirez@empresa.cx.com',
                    'rol' => 'Diseñador UX'
                ],
                'fecha' => '2025-05-18 14:30:00',
                'estado' => 'pendiente'
            ],
            [
                'id' => 2,
                'colaborador' => [
                    'id' => 202,
                    'nombre' => 'Gabriel Herrera',
                    'email' => 'gabriel.herrera@empresa.cx.com',
                    'rol' => 'Desarrollador Backend'
                ],
                'fecha' => '2025-05-17 10:15:00',
                'estado' => 'aceptada'
            ],
            [
                'id' => 3,
                'colaborador' => [
                    'id' => 203,
                    'nombre' => 'Daniela Vargas',
                    'email' => 'daniela.vargas@empresa.cx.com',
                    'rol' => 'QA Engineer'
                ],
                'fecha' => '2025-05-15 16:45:00',
                'estado' => 'rechazada'
            ]
        ];

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
