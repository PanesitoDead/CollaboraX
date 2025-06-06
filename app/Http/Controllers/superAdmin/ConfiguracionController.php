<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Repositories\PlanRepositorio;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{

    protected PlanRepositorio $planRepositorio;

    public function __construct(PlanRepositorio $planRepositorio)
    {
        $this->planRepositorio = $planRepositorio;
    }


    public function index()
    {   
        $planes = $this->planRepositorio->getAll();

        return view('super-admin.configuracion', [
            'planes' => $planes,
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
       
    }
  
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $plan = $this->planRepositorio->update($id, $request->all());
        if (!$plan) {
            return redirect()->route('super-admin.configuracion.index')->with('error', 'Error al actualizar la configuración');
        }
        return redirect()->route('super-admin.configuracion.index')->with('success', 'Configuración actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
