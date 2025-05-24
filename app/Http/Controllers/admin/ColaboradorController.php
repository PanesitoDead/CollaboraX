<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ColaboradorController extends Controller
{
    public function index()
    {
        $colaboradores = collect([
            [
                'name' => 'Ana García',
                'email' => 'ana.garcia@empresa.com',
                'area' => 'Marketing',
                'equipo' => 'Marketing Digital',
                'estado' => 'activo',
                'ultimo_acceso' => 'Hace 2 horas',
                'initials' => 'AG',
            ],
            [
                'name' => 'Carlos López',
                'email' => 'carlos.lopez@empresa.com',
                'area' => 'Ventas',
                'equipo' => 'Ventas Corporativas',
                'estado' => 'activo',
                'ultimo_acceso' => 'Hace 1 día',
                'initials' => 'CL',
            ],
            // Más colaboradores...
        ]);

        // $areas = Area::all();
        $areas = collect([
            ['id' => 1, 'name' => 'Marketing'],
            ['id' => 2, 'name' => 'Ventas'],
            ['id' => 3, 'name' => 'Operaciones'],
            ['id' => 4, 'name' => 'Recursos Humanos'],
            ['id' => 5, 'name' => 'Finanzas'],
        ]);
        
        $stats = [
            'total' => 45,
            'nuevos' => 3,
            'activos' => 42,
            'pendientes' => 2,
            'productividad' => 78,
        ];

        return view('private.admin.colaboradores', compact('colaboradores', 'areas', 'stats'));
    }

    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'nombre' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'mensaje' => 'nullable|string',
        ]);

        // Lógica para enviar invitación
        // Mail::to($request->email)->send(new InvitacionColaborador($request->all()));

        return redirect()->route('admin.colaboradores.index')
            ->with('success', 'Invitación enviada correctamente.');
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
