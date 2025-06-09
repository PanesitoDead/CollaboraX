<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\EmpresaRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfiguracionController extends Controller
{
    protected EmpresaRepositorio $empresaRepositorio;
    public function __construct(EmpresaRepositorio $empresaRepositorio)
    {
        $this->empresaRepositorio = $empresaRepositorio;
    }

    public function index()
    {
        $empresa = $this->getEmpresa();
        return view('private.admin.configuracion', 
            [
                'empresa' => $empresa,
            ]
        );
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

    public function getEmpresa()
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return redirect()->route('admin.dashboard')->with('error', 'Usuario no autenticado.');
        }
        $empresa = $this->empresaRepositorio->findOneBy('usuario_id', $usuario->id);
        if (!$empresa) {
            return redirect()->route('admin.dashboard.index')->with('error', 'No se encontró la empresa asociada al usuario.');
        }
        return $empresa;
    }
}
