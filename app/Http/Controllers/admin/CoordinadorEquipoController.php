<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CoordinadorEquipoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coordinadoresData = collect([
            (object)[
                'id' => 1,
                'name' => 'Ana López',
                'email' => 'ana.lopez@example.com',
                'area' => (object)['nombre' => 'Recursos Humanos'],
                'equipo' => (object)['nombre' => 'Equipo A'],
                'asignadoPor' => (object)['name' => 'Carlos Pérez'],
                'estado' => 'activo',
                'created_at' => Carbon::parse('2024-01-15'),
            ],
            (object)[
                'id' => 2,
                'name' => 'Luis Torres',
                'email' => 'luis.torres@example.com',
                'area' => null,
                'equipo' => (object)['nombre' => 'Equipo A'],
                'asignadoPor' => null,
                'estado' => 'inactivo',
                'created_at' => Carbon::parse('2024-03-22'),
            ],
            (object)[
                'id' => 3,
                'name' => 'María Díaz',
                'email' => 'maria.diaz@example.com',
                'area' => (object)['nombre' => 'Logística'],
                'equipo' => (object)['nombre' => 'Equipo A'],
                'asignadoPor' => (object)['name' => 'Ana López'],
                'estado' => 'activo',
                'created_at' => Carbon::parse('2024-04-10'),
            ],
            (object)[
                'id' => 4,
                'name' => 'Jorge Ramírez',
                'email' => 'jorge.ramirez@example.com',
                'area' => (object)['nombre' => 'Sistemas'],
                'equipo' => (object)['nombre' => 'Equipo A'],
                'asignadoPor' => (object)['name' => 'Luis Torres'],
                'estado' => 'inactivo',
                'created_at' => Carbon::parse('2024-05-01'),
            ],
            (object)[
                'id' => 5,
                'name' => 'Elena Vargas',
                'email' => 'elena.vargas@example.com',
                'area' => (object)['nombre' => 'Marketing'],
                'equipo' => (object)['nombre' => 'Equipo A'],
                'asignadoPor' => null,
                'estado' => 'activo',
                'created_at' => Carbon::parse('2024-05-20'),
            ],
        ]);


        // Simular paginación (ej: 2 elementos por página, página actual = 1)
        $page = request()->get('page', 1);
        $perPage = 2;
        $coordinadores = new LengthAwarePaginator(
            $coordinadoresData->forPage($page, $perPage),
            $coordinadoresData->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Simulación de datos de coordinadores

        $areas = [
            (object)[ 'id' => 1, 'nombre' => 'Recursos Humanos' ],
            (object)[ 'id' => 2, 'nombre' => 'Logística' ],
            (object)[ 'id' => 3, 'nombre' => 'Sistemas' ],
            (object)[ 'id' => 4, 'nombre' => 'Marketing' ],
            (object)[ 'id' => 5, 'nombre' => 'Administración' ],
        ];

        return view('private.admin.coordinadores-equipos', compact('coordinadores', 'areas'));
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
