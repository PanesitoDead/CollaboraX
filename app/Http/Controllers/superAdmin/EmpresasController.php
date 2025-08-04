<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Repositories\EmpresaRepositorio;
use App\Traits\Http\Controllers\CriterioTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
     * Validar campo individual para edición de empresas
     */
    public function validarCampo(Request $request)
    {
        $campo = $request->input('campo');
        $valor = $request->input('valor');
        $empresaId = $request->input('empresa_id');
        
        Log::info('Validando campo empresa', ['campo' => $campo, 'valor' => $valor, 'empresa_id' => $empresaId]);
        
        $rules = [];
        $messages = [];
        
        switch ($campo) {
            case 'nombre':
                $rules = ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 .,&-]+$/'];
                $messages = [
                    'required' => 'El nombre de la empresa es obligatorio.',
                    'string' => 'El nombre debe ser una cadena de texto.',
                    'max' => 'El nombre no puede superar los 255 caracteres.',
                    'regex' => 'El nombre solo puede contener letras, números, espacios y algunos caracteres especiales (.,&-).',
                ];
                break;
            case 'descripcion':
                if (empty($valor)) {
                    return response()->json(['valido' => true, 'mensaje' => 'Descripción válida (opcional).']);
                }
                $rules = ['string', 'max:500'];
                $messages = [
                    'string' => 'La descripción debe ser una cadena de texto.',
                    'max' => 'La descripción no puede superar los 500 caracteres.',
                ];
                break;
            case 'ruc':
                $rules = ['required', 'string', 'size:11', 'regex:/^[0-9]+$/'];
                
                // Si estamos editando, excluir el RUC actual de la validación de unicidad
                if ($empresaId) {
                    $rules[] = "unique:empresas,ruc,{$empresaId}";
                } else {
                    $rules[] = 'unique:empresas,ruc';
                }
                
                $messages = [
                    'required' => 'El RUC es obligatorio.',
                    'string' => 'El RUC debe ser una cadena de texto.',
                    'size' => 'El RUC debe tener exactamente 11 dígitos.',
                    'regex' => 'El RUC solo puede contener números.',
                    'unique' => 'Este RUC ya está registrado por otra empresa.',
                ];
                break;
            case 'telefono':
                if (empty($valor)) {
                    return response()->json(['valido' => true, 'mensaje' => 'Teléfono válido (opcional).']);
                }
                $rules = ['string', 'regex:/^[0-9+\-\s()]+$/', 'max:20'];
                $messages = [
                    'string' => 'El teléfono debe ser una cadena de texto.',
                    'regex' => 'El teléfono solo puede contener números, espacios y los caracteres +, -, ( ).',
                    'max' => 'El teléfono no puede superar los 20 caracteres.',
                ];
                break;
            default:
                Log::warning('Campo no reconocido para validación de empresa', ['campo' => $campo]);
                return response()->json(['valido' => false, 'mensaje' => 'Campo no válido para validación.']);
        }
        
        $validator = Validator::make([$campo => $valor], [$campo => $rules], $messages);
        
        if ($validator->fails()) {
            return response()->json(['valido' => false, 'mensaje' => $validator->errors()->first($campo)]);
        }
        
        return response()->json(['valido' => true, 'mensaje' => 'Campo válido.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
