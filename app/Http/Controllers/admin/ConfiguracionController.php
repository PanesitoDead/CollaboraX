<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    // empresa y logo dentro
        // Simulación de datos de la empresa
        $empresa = new \stdClass();
        $empresa->nombre = 'Mi Empresa S.A.C.';
        $empresa->ruc = '12345678901';
        $empresa->email = 'contacto@miempresa.com';
        $empresa->logo = null; // o puedes usar una imagen ficticia: 'logos/demo.png'

        // Simulación de configuración general
        $config = new \stdClass();
        $config->registro_abierto = true;
        return view('private.admin.configuracion', compact('empresa'));
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
