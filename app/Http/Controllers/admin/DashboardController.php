<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Estadísticas generales
        // $stats = [
        //     'areas' => Area::count(),
        //     'usuarios_totales' => User::count(),
        //     'usuarios_nuevos' => User::where('created_at', '>=', now()->subWeek())->count(),
        //     'metas_activas' => Meta::where('estado', 'activa')->count(),
        //     'metas_progreso' => Meta::where('estado', 'en_progreso')->count(),
        //     'metas_pendientes' => Meta::where('estado', 'pendiente')->count(),
        //     'cumplimiento' => 76, // Calcular según lógica de negocio
        // ];

        // Simulación de estadísticas generales
        $stats = [
            'areas' => 5,
            'usuarios_totales' => 50,
            'usuarios_nuevos' => 5,
            'metas_activas' => 10,
            'metas_progreso' => 3,
            'metas_pendientes' => 2,
            'cumplimiento' => 76,
        ];

        // Coordinadores generales
        $coordinadores = [
            [
                'name' => 'María González',
                'email' => 'maria.gonzalez@empresa.com',
                'area' => 'Marketing',
                'last_active' => 'Hace 2 horas',
                'initials' => 'MG',
            ],
            [
                'name' => 'Carlos Méndez',
                'email' => 'carlos.mendez@empresa.com',
                'area' => 'Ventas',
                'last_active' => 'Hace 30 minutos',
                'initials' => 'CM',
            ],
            [
                'name' => 'Ana Pérez',
                'email' => 'ana.perez@empresa.com',
                'area' => 'Operaciones',
                'last_active' => 'Hace 1 día',
                'initials' => 'AP',
            ],
            [
                'name' => 'Roberto García',
                'email' => 'roberto.garcia@empresa.com',
                'area' => 'Finanzas',
                'last_active' => 'Hace 4 horas',
                'initials' => 'RG',
            ],
            [
                'name' => 'Javier López',
                'email' => 'javier.lopez@empresa.com',
                'area' => 'TI',
                'last_active' => 'Hace 1 hora',
                'initials' => 'JL',
            ],
        ];

        // Áreas
        $areas = [
            [
                'name' => 'Marketing',
                'coordinator' => 'María González',
                'groups' => 2,
                'users' => 8,
                'progress' => 82,
            ],
            [
                'name' => 'Ventas',
                'coordinator' => 'Carlos Méndez',
                'groups' => 3,
                'users' => 12,
                'progress' => 75,
            ],
            [
                'name' => 'Operaciones',
                'coordinator' => 'Ana Pérez',
                'groups' => 2,
                'users' => 10,
                'progress' => 68,
            ],
            [
                'name' => 'Finanzas',
                'coordinator' => 'Roberto García',
                'groups' => 1,
                'users' => 6,
                'progress' => 90,
            ],
            [
                'name' => 'TI',
                'coordinator' => 'Javier López',
                'groups' => 2,
                'users' => 9,
                'progress' => 72,
            ],
        ];

        // Usuarios disponibles para asignar como coordinadores
        // $usuarios_disponibles = User::where('rol', 'colaborador')
        //     ->where('estado', 'activo')
        //     ->get();

        // Simulación de usuarios disponibles
        $usuarios_disponibles = [
            [
                'id' => 1,
                'name' => 'Laura Torres',
                'email' => 'laura@empresa.cx.com',
                'area' => 'Marketing',
                'initials' => 'LT',
            ],
            [
                'id' => 2,
                'name' => 'Fernando Ruiz',
                'email' => 'fernan@empresa.cx.com',
                'area' => 'Ventas',
                'initials' => 'FR',
            ],
        ];

        // Áreas disponibles
        // $areas_disponibles = Area::where('estado', 'activa')->get();

        // Simulación de áreas disponibles
        $areas_disponibles = [
            [
                'id' => 1,
                'name' => 'Recursos Humanos',
                'description' => 'Gestión del talento humano',
            ],
            [
                'id' => 2,
                'name' => 'Investigación y Desarrollo',
                'description' => 'Innovación y desarrollo de productos',
            ],
        ];

        return view('private.admin.dashboard', compact(
            'stats',
            'coordinadores',
            'areas',
            'usuarios_disponibles',
            'areas_disponibles'
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
