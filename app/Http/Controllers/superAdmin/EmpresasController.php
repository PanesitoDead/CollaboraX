<?php

namespace App\Http\Controllers\superAdmin;

use App\Http\Controllers\Controller;
use App\Repositories\EmpresaRepositorio;
use App\Traits\Http\Controllers\CriterioTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class EmpresasController extends Controller
{

    use CriterioTrait;
    protected EmpresaRepositorio $empresaRepositorio;

    public function __construct(EmpresaRepositorio $empresaRepositorio)
    {
        $this->empresaRepositorio = $empresaRepositorio;
    }


    public function index(Request $request)
    {   
        $criterios = $this->obtenerCriterios($request);
        $empresasPag = $this->empresaRepositorio->obtenerPaginado($criterios);
        $empresasParse = $empresasPag->getCollection()->map(function ($empresa) {
            // Agregamos el campo plan_servicio
            $empresa->plan_servicio = $empresa->planServicio ? $empresa->planServicio->nombre : 'No asignado';
            // Agregamos el campo usuarios
            $empresa->nro_usuarios =  0;
            $empresa->correo = $empresa->usuario->correo ?? 'No disponible';
            $empresa->activo = $empresa->usuario->activo;
            // Parseamos los campos de fecha a un formato legible
            $empresa->fecha_registro = $empresa->usuario->fecha_registro
                ? Carbon::parse($empresa->usuario->fecha_registro)->format('d/m/Y H:i')
                : 'No disponible';
            return $empresa;
        });
        $empresasPag->setCollection($empresasParse);
    
        return view('super-admin.empresas', [
            'empresas' => $empresasPag,
            'criterios' => $criterios,
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
