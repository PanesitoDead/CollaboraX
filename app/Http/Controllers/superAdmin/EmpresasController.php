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
            $empresa->correo = $empresa->usuario->correo ?? 'No disponible';
            $empresa->activo = $empresa->usuario->activo;
            // Parseamos los campos de fecha a un formato legible
            $empresa->fecha_registro = $empresa->usuario->fecha_registro
                ? Carbon::parse(time: $empresa->usuario->fecha_registro)->format('d/m/Y H:i')
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
        $empresa = $this->empresaRepositorio->getById($id);
        if (!$empresa) {
            return redirect()->route('super-admin.empresas.index')->with('error', 'Empresa no encontrada.');
        }

        // Agregamos el campo plan_servicio
        $empresa->plan_servicio = $empresa->planServicio ? $empresa->planServicio->nombre : 'No asignado';
        // Agregamos el campo usuarios
        $empresa->nro_usuarios = $empresa->nro_usuarios();
        $empresa->correo = $empresa->usuario->correo ?? 'No disponible';
        $empresa->activo = $empresa->usuario->activo;
        // Parseamos los campos de fecha a un formato legible
        $empresa->fecha_registro = $empresa->usuario->fecha_registro
            ? Carbon::parse($empresa->usuario->fecha_registro)->format('d/m/Y H:i')
            : 'No disponible';

        return response()->json($empresa);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Cambiar de estado una empresa.
     */
    public function cambiarEstado(Request $request, string $id)
    {
        $success = $this->empresaRepositorio->cambiarEstado($id, $request->input('activo'));
        if (!$success) {
            return redirect()->route('super-admin.empresas.index')->with('error', 'Error al cambiar el estado de la empresa.');
        }
        return redirect()->route('super-admin.empresas.index')->with('success', 'Estado de la empresa actualizado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $empresa = $this->empresaRepositorio->update($id, $request->all());
        if (!$empresa) {
            return redirect()->route('super-admin.empresas.index')->with('error', 'Error al actualizar la empresa.');
        }
        return redirect()->route('super-admin.empresas.index')->with('success', 'Empresa actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
