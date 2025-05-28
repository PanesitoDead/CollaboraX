<?php

namespace App\Http\Controllers\admin;

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

        $rendimiento_areas = [
            ['nombre' => 'Marketing', 'porcentaje' => 85, 'color' => '#8B5CF6'],
            ['nombre' => 'Ventas', 'porcentaje' => 78, 'color' => '#3B82F6'],
            ['nombre' => 'Operaciones', 'porcentaje' => 72, 'color' => '#10B981'],
            ['nombre' => 'Finanzas', 'porcentaje' => 90, 'color' => '#F59E0B'],
            ['nombre' => 'TI', 'porcentaje' => 68, 'color' => '#EF4444'],
        ];

        $actividad_semanal = [
            ['dia' => 'Lun', 'metas' => 8, 'actividades' => 15],
            ['dia' => 'Mar', 'metas' => 12, 'actividades' => 22],
            ['dia' => 'Mié', 'metas' => 10, 'actividades' => 18],
            ['dia' => 'Jue', 'metas' => 15, 'actividades' => 25],
            ['dia' => 'Vie', 'metas' => 9, 'actividades' => 16],
            ['dia' => 'Sáb', 'metas' => 5, 'actividades' => 8],
            ['dia' => 'Dom', 'metas' => 3, 'actividades' => 5],
        ];

        $top_performers = [
            ['nombre' => 'María González', 'area' => 'Marketing', 'puntuacion' => 95],
            ['nombre' => 'Carlos Méndez', 'area' => 'Ventas', 'puntuacion' => 92],
            ['nombre' => 'Ana Pérez', 'area' => 'Operaciones', 'puntuacion' => 88],
            ['nombre' => 'Roberto García', 'area' => 'Finanzas', 'puntuacion' => 85],
            ['nombre' => 'Javier López', 'area' => 'TI', 'puntuacion' => 82],
        ];

        $metas_estado = [
            ['nombre' => 'Completadas', 'cantidad' => 24, 'porcentaje' => 60, 'color' => '#10B981'],
            ['nombre' => 'En Progreso', 'cantidad' => 12, 'porcentaje' => 30, 'color' => '#F59E0B'],
            ['nombre' => 'Pendientes', 'cantidad' => 4, 'porcentaje' => 10, 'color' => '#EF4444'],
        ];

        $actividad_reciente = [
            [
                'descripcion' => 'Meta completada: Campaña Q1',
                'usuario' => 'María González',
                'tiempo' => 'Hace 15 min',
                'color' => 'green',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>',
            ],
            [
                'descripcion' => 'Nueva actividad asignada',
                'usuario' => 'Carlos Méndez',
                'tiempo' => 'Hace 30 min',
                'color' => 'blue',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>',
            ],
            // Más actividades...
        ];

        $rendimiento_detallado = [
            [
                'nombre' => 'Marketing',
                'coordinador' => 'María González',
                'equipos' => 2,
                'colaboradores' => 8,
                'metas_activas' => 6,
                'metas_completadas' => 4,
                'rendimiento' => 85,
                'color' => '#8B5CF6',
            ],
            // Más áreas...
        ];

        return view('private.admin.estadisticas', compact(
            'stats',
            'rendimiento_areas',
            'actividad_semanal',
            'top_performers',
            'metas_estado',
            'actividad_reciente',
            'rendimiento_detallado'
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
