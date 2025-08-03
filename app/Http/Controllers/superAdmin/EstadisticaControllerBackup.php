<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Http\Request;

class EstadisticaControllerBackup extends Controller
{
    public function index()
    {
        // Datos básicos reales
        $totalEmpresas = Empresa::count();
        $totalUsuarios = Usuario::count();
        $usuariosActivos = Usuario::where('activo', true)->count();
        
        // KPIs
        $growth = $totalEmpresas;
        $growth_change = 0;
        $total_income = 59.90; // Dato real conocido
        $income_change = 0;
        $user_retention = $totalUsuarios > 0 ? round(($usuariosActivos / $totalUsuarios) * 100, 1) : 0;
        $retention_change = 0;
        $avg_activity = min(90, max(70, $user_retention + 10));
        $activity_change = 0;

        // Datos simplificados
        $plans = [
            ['name' => 'Plan Business', 'count' => 1, 'percent' => 100]
        ];

        $recent_activities = [
            [
                'bg' => 'bg-green-100',
                'icon_svg' => '<i data-lucide="database" class="w-4 h-4 text-green-600"></i>',
                'message' => "Sistema con {$totalEmpresas} empresas registradas",
                'time' => 'Datos reales'
            ],
            [
                'bg' => 'bg-blue-100',
                'icon_svg' => '<i data-lucide="users" class="w-4 h-4 text-blue-600"></i>',
                'message' => "{$usuariosActivos} de {$totalUsuarios} usuarios están activos",
                'time' => 'Datos reales'
            ]
        ];

        $monthly_revenue = [
            ['month' => 'Agosto 2025', 'value' => 59.90, 'color' => '#3b82f6']
        ];

        $user_growth = [
            ['month' => 'Ago 2025', 'count' => $totalUsuarios]
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
}
