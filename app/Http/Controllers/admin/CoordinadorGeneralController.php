<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CoordinadorGeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Simulamos los coordinadores generales
    $coordinadoresData = collect([
        (object)[
            'id' => 1,
            'name' => 'Ana López',
            'email' => 'ana.lopez@example.com',
            'area' => (object)['nombre' => 'Recursos Humanos'],
            'equipos_count' => 3,
            'coordinadores_count' => 5,
            'estado' => 'activo',
        ],
        (object)[
            'id' => 2,
            'name' => 'Luis Torres',
            'email' => 'luis.torres@example.com',
            'area' => null,
            'equipos_count' => 1,
            'coordinadores_count' => 2,
            'estado' => 'inactivo',
        ],
        (object)[
            'id' => 3,
            'name' => 'María Díaz',
            'email' => 'maria.diaz@example.com',
            'area' => (object)['nombre' => 'Logística'],
            'equipos_count' => 2,
            'coordinadores_count' => 4,
            'estado' => 'activo',
        ],
        (object)[
            'id' => 4,
            'name' => 'Jorge Ramírez',
            'email' => 'jorge.ramirez@example.com',
            'area' => (object)['nombre' => 'Sistemas'],
            'equipos_count' => 0,
            'coordinadores_count' => 1,
            'estado' => 'inactivo',
        ],
        (object)[
            'id' => 5,
            'name' => 'Elena Vargas',
            'email' => 'elena.vargas@example.com',
            'area' => (object)['nombre' => 'Marketing'],
            'equipos_count' => 4,
            'coordinadores_count' => 3,
            'estado' => 'activo',
        ],
    ]);

    // Simular paginación (2 por página)
    $page     = request()->get('page', 1);
    $perPage  = 2;
    $items    = $coordinadoresData->forPage($page, $perPage);
    $paginator = new LengthAwarePaginator(
        $items,
        $coordinadoresData->count(),
        $perPage,
        $page,
        [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]
    );

    // Simular las áreas para el filtro y el select de asignar
    $areas = collect([
        (object)['id' => 1, 'nombre' => 'Recursos Humanos'],
        (object)['id' => 2, 'nombre' => 'Logística'],
        (object)['id' => 3, 'nombre' => 'Sistemas'],
        (object)['id' => 4, 'nombre' => 'Marketing'],
        (object)['id' => 5, 'nombre' => 'Administración'],
    ]);

    // Simular usuarios disponibles (rol Colaborador y estado Activo)
    $usuariosDisponibles = collect([
        (object)['id' => 10, 'name' => 'Carlos Pérez', 'email' => 'carlos.perez@example.com'],
        (object)['id' => 11, 'name' => 'Lucía Gómez',   'email' => 'lucia.gomez@example.com'],
        (object)['id' => 12, 'name' => 'Miguel Salas',   'email' => 'miguel.salas@example.com'],
    ]);

    return view('private.admin.coordinadores-generales', [
        'coordinadores'         => $paginator,
        'areas'                 => $areas,
        'usuariosDisponibles'   => $usuariosDisponibles,
    ]);
}

    public function asignarEquipo(Request $request)
    {
        $request->validate([
            'coordinador_id' => 'required|numeric',
            'equipo' => 'required|string',
            'notificar' => 'boolean'
        ]);

        // Lógica para asignar equipo
        
        return redirect()->route('coordinadores-equipo')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Equipo asignado',
                'message' => 'Se ha asignado el equipo correctamente.'
            ]);
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
