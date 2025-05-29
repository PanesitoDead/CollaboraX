<?php

namespace App\Http\Controllers\superAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class EmpresasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Datos de ejemplo simulados
        $data = [
            ['id' => 1, 'nombre' => 'TechCorp Solutions',  'email' => 'admin@techcorp.com',  'plan' => 'Enterprise',   'usuarios_count' => 45, 'estado' => 'active',   'created_at' => '2024-01-15'],
            ['id' => 2, 'nombre' => 'Digital Innovations','email' => 'contact@digital.com', 'plan' => 'Professional','usuarios_count' => 23, 'estado' => 'active',   'created_at' => '2024-01-14'],
            ['id' => 3, 'nombre' => 'StartUp Hub',        'email' => 'info@startup.com',   'plan' => 'Basic',       'usuarios_count' => 8,  'estado' => 'inactive', 'created_at' => '2024-01-13'],
            ['id' => 4, 'nombre' => 'Global Systems',     'email' => 'admin@global.com',   'plan' => 'Enterprise',  'usuarios_count' => 67, 'estado' => 'active',   'created_at' => '2024-01-12'],
        ];

        // Convertimos cada arreglo en un objeto y parseamos created_at con Carbon
        $items = collect($data)
            ->map(function($item) {
                return (object) array_merge($item, [
                    'created_at' => Carbon::parse($item['created_at']),
                ]);
            });

        // Simulación de paginación (todos en una sola página)
        $perPage = 10;
        $page    = LengthAwarePaginator::resolveCurrentPage();
        $slice   = $items->slice(($page - 1) * $perPage, $perPage)->values();
        $empresas = new LengthAwarePaginator(
            $slice,
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Pasamos filtros actuales para reuso en la vista
        $filters = [
            'search' => $request->query('search', ''),
            'plan'   => $request->query('plan', ''),
            'estado' => $request->query('estado', ''),
        ];

        return view('super-admin.empresas', compact('empresas', 'filters'));
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
