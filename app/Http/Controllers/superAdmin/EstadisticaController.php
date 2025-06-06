<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EstadisticaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Simulación de datos (sin conexión a BD)
        $growth = 120;            // Empresas nuevas este mes
        $growth_change = 15.2;     // Cambio porcentual vs mes anterior

        $total_income = 1247890;   // Ingresos en dólares este mes
        $income_change = 15.3;     // Cambio porcentual vs mes anterior

        $user_retention = 94.2;    // Retención de usuarios (%)
        $retention_change = 1.2;   // Cambio porcentual vs mes anterior

        $avg_activity = 87.3;      // Actividad promedio (%)
        $activity_change = -0.8;   // Cambio porcentual vs mes anterior

        // Distribución de planes
        $plans = [
            ['name' => 'Basic', 'count' => 65, 'percent' => 42],
            ['name' => 'Professional', 'count' => 54, 'percent' => 35],
            ['name' => 'Enterprise', 'count' => 37, 'percent' => 23],
        ];

        // Actividad reciente simulada
        $recent_activities = [
            ['bg' => 'bg-green-100', 'icon_svg' => '<svg>...</svg>', 'message' => 'Nueva empresa registrada: TechCorp Solutions', 'time' => 'Hace 2 horas'],
            ['bg' => 'bg-blue-100', 'icon_svg' => '<svg>...</svg>', 'message' => 'Pago recibido de Digital Innovations ($299/mes)', 'time' => 'Hace 4 horas'],
            ['bg' => 'bg-purple-100', 'icon_svg' => '<svg>...</svg>', 'message' => 'StartUp Hub actualizó a plan Professional', 'time' => 'Hace 6 horas'],
            ['bg' => 'bg-yellow-100', 'icon_svg' => '<svg>...</svg>', 'message' => 'Ticket de soporte resuelto para Global Systems', 'time' => 'Hace 8 horas'],
        ];

        // Datos de ingresos mensuales simulados para gráfica
        $monthly_revenue = [
            ['month' => 'Ene 2025', 'value' => 900000, 'color' => '#3b82f6'],
            ['month' => 'Feb 2025', 'value' => 1100000, 'color' => '#3b82f6'],
            ['month' => 'Mar 2025', 'value' => 1050000, 'color' => '#3b82f6'],
            ['month' => 'Abr 2025', 'value' => 1247890, 'color' => '#3b82f6'],
        ];

        // Datos de crecimiento de usuarios simulados para gráfica
        $user_growth = [
            ['month' => 'Ene 2025', 'count' => 50],
            ['month' => 'Feb 2025', 'count' => 75],
            ['month' => 'Mar 2025', 'count' => 90],
            ['month' => 'Abr 2025', 'count' => 120],
        ];

        return view('super-admin.estadisticas', compact(
            'growth', 'growth_change',
            'total_income', 'income_change',
            'user_retention', 'retention_change',
            'avg_activity', 'activity_change',
            'plans', 'recent_activities',
            'monthly_revenue', 'user_growth'
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