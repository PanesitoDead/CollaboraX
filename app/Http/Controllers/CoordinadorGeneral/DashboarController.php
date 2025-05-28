<?php
// app/Http/Controllers/CoordinadorGeneral/DashboardController.php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboarController extends Controller
{
    public function index()
    {
        // Datos simulados para el dashboard
        $metricas = [
            'total_actividades' => 47,
            'actividades_completadas' => 32,
            'actividades_pendientes' => 15,
            'equipos_activos' => 8,
            'reuniones_semana' => 12,
            'progreso_general' => 68
        ];

        $actividades_recientes = [
            [
                'id' => 1,
                'titulo' => 'Implementar sistema de autenticación',
                'equipo' => 'Desarrollo',
                'estado' => 'completada',
                'fecha' => '2024-01-15',
                'asignado' => 'Carlos Mendoza'
            ],
            [
                'id' => 2,
                'titulo' => 'Diseñar landing page',
                'equipo' => 'Marketing',
                'estado' => 'en_progreso',
                'fecha' => '2024-01-14',
                'asignado' => 'Ana García'
            ],
            [
                'id' => 3,
                'titulo' => 'Análisis de competencia',
                'equipo' => 'Ventas',
                'estado' => 'pendiente',
                'fecha' => '2024-01-13',
                'asignado' => 'Luis Rodríguez'
            ],
            [
                'id' => 4,
                'titulo' => 'Optimización de base de datos',
                'equipo' => 'Desarrollo',
                'estado' => 'en_progreso',
                'fecha' => '2024-01-12',
                'asignado' => 'María López'
            ],
            [
                'id' => 5,
                'titulo' => 'Campaña redes sociales',
                'equipo' => 'Marketing',
                'estado' => 'completada',
                'fecha' => '2024-01-11',
                'asignado' => 'Pedro Sánchez'
            ]
        ];

        $reuniones_proximas = [
            [
                'id' => 1,
                'titulo' => 'Revisión Sprint Semanal',
                'fecha' => '2024-01-16',
                'hora' => '09:00',
                'equipo' => 'Desarrollo',
                'participantes' => 6
            ],
            [
                'id' => 2,
                'titulo' => 'Planificación Marketing Q1',
                'fecha' => '2024-01-16',
                'hora' => '14:30',
                'equipo' => 'Marketing',
                'participantes' => 4
            ],
            [
                'id' => 3,
                'titulo' => 'Seguimiento Ventas',
                'fecha' => '2024-01-17',
                'hora' => '10:00',
                'equipo' => 'Ventas',
                'participantes' => 5
            ],
            [
                'id' => 4,
                'titulo' => 'Reunión General Mensual',
                'fecha' => '2024-01-18',
                'hora' => '16:00',
                'equipo' => 'Todos',
                'participantes' => 15
            ]
        ];

        $equipos_performance = [
            [
                'nombre' => 'Desarrollo',
                'total_actividades' => 18,
                'completadas' => 12,
                'en_progreso' => 4,
                'pendientes' => 2,
                'progreso' => 67
            ],
            [
                'nombre' => 'Marketing',
                'total_actividades' => 14,
                'completadas' => 10,
                'en_progreso' => 3,
                'pendientes' => 1,
                'progreso' => 71
            ],
            [
                'nombre' => 'Ventas',
                'total_actividades' => 10,
                'completadas' => 7,
                'en_progreso' => 2,
                'pendientes' => 1,
                'progreso' => 70
            ],
            [
                'nombre' => 'Soporte',
                'total_actividades' => 5,
                'completadas' => 3,
                'en_progreso' => 1,
                'pendientes' => 1,
                'progreso' => 60
            ]
        ];

        return view('coordinador-general.dashboard.index', compact(
            'metricas',
            'actividades_recientes',
            'reuniones_proximas',
            'equipos_performance'
        ));
    }
}