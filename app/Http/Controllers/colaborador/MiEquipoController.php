<?php

namespace App\Http\Controllers\colaborador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MiEquipoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Datos del equipo (simulados - reemplazar con consultas reales)
        $equipoInfo = [
            'nombre' => 'Equipo Desarrollo Frontend',
            'area' => 'Tecnología',
            'progreso_general' => 92,
            'metas_completadas' => 4,
            'metas_totales' => 5,
            'actividades_completadas' => 18,
            'actividades_totales' => 24,
            'reuniones_pendientes' => 3
        ];

        $estadisticas = [
            'miembros' => 8,
            'miembros_nuevos' => 2,
            'metas_activas' => 4,
            'metas_completadas_mes' => 1,
            'actividades_total' => 18,
            'actividades_progreso' => 12,
            'actividades_completadas' => 6,
            'rendimiento' => 92,
            'mejora_rendimiento' => 5
        ];

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

        $reuniones = [
            [
                'id' => 1,
                'titulo' => 'Revisión semanal de Sprint',
                'fecha' => 'Mañana, 10:00 - 11:30',
                'tipo' => 'Videoconferencia'
            ],
            [
                'id' => 2,
                'titulo' => 'Planificación de tareas',
                'fecha' => 'Jueves, 15:00 - 16:00',
                'tipo' => 'Presencial'
            ],
            [
                'id' => 3,
                'titulo' => 'Retrospectiva de Sprint',
                'fecha' => 'Viernes, 14:00 - 15:30',
                'tipo' => 'Videoconferencia'
            ]
        ];

        $metas = [
            [
                'id' => 1,
                'titulo' => 'Lanzamiento de nueva interfaz',
                'descripcion' => 'Completar el rediseño y lanzamiento de la nueva interfaz de usuario',
                'fecha_vencimiento' => '30 Jun 2025',
                'progreso' => 75,
                'actividades_completadas' => 9,
                'actividades_totales' => 12
            ],
            [
                'id' => 2,
                'titulo' => 'Optimización de rendimiento',
                'descripcion' => 'Mejorar el tiempo de carga de la aplicación en un 30%',
                'fecha_vencimiento' => '15 Jul 2025',
                'progreso' => 45,
                'actividades_completadas' => 5,
                'actividades_totales' => 11
            ],
            [
                'id' => 3,
                'titulo' => 'Implementación de nuevas funcionalidades',
                'descripcion' => 'Desarrollar e implementar las funcionalidades solicitadas por los usuarios',
                'fecha_vencimiento' => '31 Jul 2025',
                'progreso' => 20,
                'actividades_completadas' => 2,
                'actividades_totales' => 10
            ]
        ];

        return view('private.colaborador.mi-equipo', compact(
            'equipoInfo',
            'estadisticas',
            'miembros',
            'reuniones',
            'metas'
        ));
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
