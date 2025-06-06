<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EstadisticaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'actividades_terminadas' => 78,
            'actividades_nuevas' => 5.2,
            'metas_completadas' => 24,
            'metas_total' => 32,
            'asistencias_totales' => 45,
            'asistencias_semana' => 28,
            'porcentaje_avance' => 7.5,
        ];

        $roles_dist = [
            [
                'nombre'      => 'Coordinadores Generales',
                'porcentaje'  => 25,
                'color'       => '#3B82F6', // azul
            ],
            [
                'nombre'      => 'Gerentes de Equipo',
                'porcentaje'  => 20,
                'color'       => '#F97316', // naranja
            ],
            [
                'nombre'      => 'Colaboradores',
                'porcentaje'  => 55,
                'color'       => '#10B981', // verde
            ],
        ];

    // Actividad Semanal
    $actividad_semanal = [
        [
            'dia'          => 'Lun',
            'metas'        => 5,
            'actividades'  => 8,
        ],
        [
            'dia'          => 'Mar',
            'metas'        => 6,
            'actividades'  => 7,
        ],
        [
            'dia'          => 'Mié',
            'metas'        => 4,
            'actividades'  => 6,
        ],
        [
            'dia'          => 'Jue',
            'metas'        => 7,
            'actividades'  => 5,
        ],
        [
            'dia'          => 'Vie',
            'metas'        => 8,
            'actividades'  => 9,
        ],
    ];

    // Mejores Colaboradores
    $top_performers = [
        [
            'area'        => 'Ventas',
            'puntuacion'  => 92,
        ],
        [
            'area'        => 'Marketing',
            'puntuacion'  => 88,
        ],
        [
            'area'        => 'Desarrollo',
            'puntuacion'  => 85,
        ],
        [
            'area'        => 'Atención al Cliente',
            'puntuacion'  => 80,
        ],
    ];

    // Estado de Metas
    $metas_estado = [
        [
            'nombre'      => 'Completadas',
            'cantidad'    => 40,
            'porcentaje'  => 50,
            'color'       => '#10B981', // verde
        ],
        [
            'nombre'      => 'En Progreso',
            'cantidad'    => 20,
            'porcentaje'  => 25,
            'color'       => '#F59E0B', // amarillo
        ],
        [
            'nombre'      => 'Pendientes',
            'cantidad'    => 15,
            'porcentaje'  => 18.75,
            'color'       => '#EF4444', // rojo
        ],
        [
            'nombre'      => 'Canceladas',
            'cantidad'    => 5,
            'porcentaje'  => 6.25,
            'color'       => '#9CA3AF', // gris
        ],
    ];

    // Reuniones por Semana
    $reuniones_semana = [
        [
            'dia'      => 'Lun',
            'conteo'   => 3,
        ],
        [
            'dia'      => 'Mar',
            'conteo'   => 2,
        ],
        [
            'dia'      => 'Mié',
            'conteo'   => 4,
        ],
        [
            'dia'      => 'Jue',
            'conteo'   => 1,
        ],
        [
            'dia'      => 'Vie',
            'conteo'   => 5,
        ],
    ];
        return view('private.admin.estadisticas', compact(
            'stats',
            'roles_dist',
        'actividad_semanal',
        'top_performers',
        'metas_estado',
        'reuniones_semana'
            ));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
