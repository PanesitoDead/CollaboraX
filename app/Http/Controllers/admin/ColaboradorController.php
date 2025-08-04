<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CrearColaboradorRequest;
use App\Http\Requests\Admin\EditarColaboradorRequest;
use App\Models\Usuario;
use App\Repositories\AreaRepositorio;
use App\Repositories\EmpresaRepositorio;
use App\Repositories\TrabajadorRepositorio;
use App\Repositories\UsuarioRepositorio;
use App\Traits\Http\Controllers\CriterioTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ColaboradorController extends Controller
{
    use CriterioTrait;

    protected UsuarioRepositorio $usuarioRepositorio;
    protected EmpresaRepositorio $empresaRepositorio;

    protected TrabajadorRepositorio $trabajadorRepositorio;

    protected AreaRepositorio $areaRepositorio;

    public function __construct(UsuarioRepositorio $usuarioRepositorio, EmpresaRepositorio $empresaRepositorio, TrabajadorRepositorio $trabajadorRepositorio, AreaRepositorio $areaRepositorio)
    {
        $this->usuarioRepositorio = $usuarioRepositorio;
        $this->empresaRepositorio = $empresaRepositorio;
        $this->trabajadorRepositorio = $trabajadorRepositorio;
        $this->areaRepositorio = $areaRepositorio;
    }

    public function index(Request $request)
    {
        $empresa = $this->getEmpresa();
        $areas = $this->areaRepositorio->findBy('empresa_id', $empresa->id);

        $coordinadores = $this->getPaginado($request);
        return view('private.admin.colaboradores', [
            'criterios' => $this->obtenerCriterios($request),
            'areas' => $areas,
            'coordinadores' => $coordinadores,
            'empresa' => $empresa,
        ]);
    }

    public function getPaginado(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();
        $empresa = $this->empresaRepositorio->findOneBy('usuario_id', $usuario->id);

        $criterios = $this->obtenerCriterios($request);
        // Creamos el query builder para las áreas
        $query = $this->trabajadorRepositorio->getModel()->newQuery();
        // Unimos las tablas necesarias
        $query->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id');
        // Filtramos por la empresa del usuario autenticado
        $query->where('trabajadores.empresa_id', $empresa->id);
        // // Filtramos por el rol de colaborador id=5
        $query->where('usuarios.rol_id', 5);

        // Aplicamos los criterios de búsqueda
        $trabajadoresPag = $this->trabajadorRepositorio->obtenerPaginado($criterios, $query);
        $trabajadoresParse = $trabajadoresPag->getCollection()->map(function ($trabajador) {
            $trabajador->correo = $trabajador->usuario->correo ?? 'No disponible';
            $trabajador->fecha_registro = Carbon::parse($trabajador->usuario->fecha_registro)->format('d/m/Y');
            return $trabajador;
        });
        return $trabajadoresPag->setCollection($trabajadoresParse);
    }

    /**
     * Cambiar de estado un colaborador.
     */
    public function cambiarEstado(Request $request, string $id)
    {
        $success = $this->trabajadorRepositorio->cambiarEstado($id, $request->input('activo'));
        if (!$success) {
            return redirect()->route('admin.colaboradores.index')->with('error', 'Error al cambiar el estado del colaborador.');
        }
        return redirect()->route('admin.colaboradores.index')->with('success', 'Estado del colaborador actualizado correctamente.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CrearColaboradorRequest $request)
    {
        // Agregar al correo el dominio de la empresa
        $empresa = $this->getEmpresa();
        $correoCompleto = $request->input('correo') . '@' . strtolower($empresa->nombre) . '.cx.com';

        // Verificar que el correo corporativo no exista
        $existeCorreo = $this->usuarioRepositorio->findOneBy('correo', $correoCompleto);
        if ($existeCorreo) {
            return back()->withErrors(['correo' => 'Este correo corporativo ya está en uso.'])->withInput();
        }

        try {
            // Creamos el usuario primero
            $usuario = $this->usuarioRepositorio->create([
                'correo' => $correoCompleto,
                'correo_personal' => $request->input('correo_personal'),
                'clave' => bcrypt($request->input('clave')),
                'clave_mostrar' => $request->input('clave'),
                'rol_id' => 5, // Rol de colaborador
                'activo' => true,
                'en_linea' => false,
                'foto' => null, // Asignar foto si es necesario
                'ultima_conexion' => Carbon::now(),
                'fecha_registro' => Carbon::now(),
            ]);

            // Crear el colaborador
            $trabajador = $this->trabajadorRepositorio->create([
                'nombres' => $request->input('nombres'),
                'apellido_paterno' => $request->input('apellido_paterno'),
                'apellido_materno' => $request->input('apellido_materno'),
                'usuario_id' => $usuario->id,
                'empresa_id' => $empresa->id,
            ]);

            return redirect()->route('admin.colaboradores.index')->with('success', 'Colaborador creado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Error al crear el colaborador: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Validar correo personal en tiempo real
     */
    public function validarCorreoPersonal(Request $request)
    {
        $correoPersonal = $request->input('correo_personal');
        
        if (!$correoPersonal) {
            return response()->json(['valido' => false, 'mensaje' => 'El correo personal es obligatorio.']);
        }

        if (!filter_var($correoPersonal, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['valido' => false, 'mensaje' => 'El correo personal debe ser una dirección válida.']);
        }

        $existe = $this->usuarioRepositorio->findOneBy('correo_personal', $correoPersonal);
        
        if ($existe) {
            return response()->json(['valido' => false, 'mensaje' => 'Este correo personal ya está registrado.']);
        }

        return response()->json(['valido' => true, 'mensaje' => 'Correo personal disponible.']);
    }

    /**
     * Validar correo corporativo en tiempo real
     */
    public function validarCorreoCorporativo(Request $request)
    {
        $correo = $request->input('correo');
        
        if (!$correo) {
            return response()->json(['valido' => false, 'mensaje' => 'El correo corporativo es obligatorio.']);
        }

        try {
            $empresa = $this->getEmpresa();
            $correoCompleto = $correo . '@' . strtolower($empresa->nombre) . '.cx.com';
            
            $existe = $this->usuarioRepositorio->findOneBy('correo', $correoCompleto);
            
            if ($existe) {
                return response()->json(['valido' => false, 'mensaje' => 'Este correo corporativo ya está en uso.']);
            }

            return response()->json(['valido' => true, 'mensaje' => 'Correo corporativo disponible.']);
        } catch (\Exception $e) {
            return response()->json(['valido' => false, 'mensaje' => 'Error al validar el correo corporativo.']);
        }
    }

    /**
     * Validar campo individual
     */
    public function validarCampo(Request $request)
    {
        $campo = $request->input('campo');
        $valor = $request->input('valor');
        
        // Debug log
        Log::info('Validando campo', ['campo' => $campo, 'valor' => $valor]);
        
        $rules = [];
        $messages = [];
        
        switch ($campo) {
            case 'nombres':
                $rules = ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'];
                $messages = [
                    'required' => 'El nombre es obligatorio.',
                    'string' => 'El nombre debe ser una cadena de texto.',
                    'max' => 'El nombre no puede superar los 255 caracteres.',
                    'regex' => 'El nombre solo puede contener letras y espacios.',
                ];
                break;
            case 'apellido_paterno':
                $rules = ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'];
                $messages = [
                    'required' => 'El apellido paterno es obligatorio.',
                    'string' => 'El apellido paterno debe ser una cadena de texto.',
                    'max' => 'El apellido paterno no puede superar los 255 caracteres.',
                    'regex' => 'El apellido paterno solo puede contener letras y espacios.',
                ];
                break;
            case 'apellido_materno':
                $rules = ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'];
                $messages = [
                    'required' => 'El apellido materno es obligatorio.',
                    'string' => 'El apellido materno debe ser una cadena de texto.',
                    'max' => 'El apellido materno no puede superar los 255 caracteres.',
                    'regex' => 'El apellido materno solo puede contener letras y espacios.',
                ];
                break;
            default:
                Log::warning('Campo no reconocido para validación', ['campo' => $campo]);
                return response()->json(['valido' => false, 'mensaje' => 'Campo no válido para validación.']);
        }
        
        $validator = Validator::make([$campo => $valor], [$campo => $rules], $messages);
        
        if ($validator->fails()) {
            return response()->json(['valido' => false, 'mensaje' => $validator->errors()->first($campo)]);
        }
        
        return response()->json(['valido' => true, 'mensaje' => 'Campo válido.']);
    }

    /**
     * Validar correo personal en tiempo real para edición
     */
    public function validarCorreoPersonalEdicion(Request $request)
    {
        $correoPersonal = $request->input('correo_personal');
        $colaboradorId = $request->input('colaborador_id');
        
        if (!$correoPersonal) {
            return response()->json(['valido' => false, 'mensaje' => 'El correo personal es obligatorio.']);
        }

        if (!filter_var($correoPersonal, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['valido' => false, 'mensaje' => 'El correo personal debe ser una dirección válida.']);
        }

        // Verificar que no exista otro usuario con este correo (excluyendo el actual)
        $trabajador = $this->trabajadorRepositorio->getById($colaboradorId);
        if (!$trabajador) {
            return response()->json(['valido' => false, 'mensaje' => 'Colaborador no encontrado.']);
        }

        $usuarioExistente = $this->usuarioRepositorio->findOneBy('correo_personal', $correoPersonal);
        
        if ($usuarioExistente && $usuarioExistente->id !== $trabajador->usuario_id) {
            return response()->json(['valido' => false, 'mensaje' => 'Este correo personal ya está registrado por otro usuario.']);
        }

        return response()->json(['valido' => true, 'mensaje' => 'Correo personal válido.']);
    }

    /**
     * Validar campo individual para edición
     */
    public function validarCampoEdicion(Request $request)
    {
        $campo = $request->input('campo');
        $valor = $request->input('valor');
        
        Log::info('Validando campo edición', ['campo' => $campo, 'valor' => $valor]);
        
        $rules = [];
        $messages = [];
        
        switch ($campo) {
            case 'nombres':
                $rules = ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'];
                $messages = [
                    'required' => 'El nombre es obligatorio.',
                    'string' => 'El nombre debe ser una cadena de texto.',
                    'max' => 'El nombre no puede superar los 255 caracteres.',
                    'regex' => 'El nombre solo puede contener letras y espacios.',
                ];
                break;
            case 'apellido_paterno':
                $rules = ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'];
                $messages = [
                    'required' => 'El apellido paterno es obligatorio.',
                    'string' => 'El apellido paterno debe ser una cadena de texto.',
                    'max' => 'El apellido paterno no puede superar los 255 caracteres.',
                    'regex' => 'El apellido paterno solo puede contener letras y espacios.',
                ];
                break;
            case 'apellido_materno':
                $rules = ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/'];
                $messages = [
                    'required' => 'El apellido materno es obligatorio.',
                    'string' => 'El apellido materno debe ser una cadena de texto.',
                    'max' => 'El apellido materno no puede superar los 255 caracteres.',
                    'regex' => 'El apellido materno solo puede contener letras y espacios.',
                ];
                break;
            case 'doc_identidad':
                if (empty($valor)) {
                    return response()->json(['valido' => true, 'mensaje' => 'Documento de identidad válido (opcional).']);
                }
                $rules = ['string', 'max:8', 'regex:/^[0-9]+$/'];
                $messages = [
                    'string' => 'El documento de identidad debe ser una cadena de texto.',
                    'max' => 'El documento de identidad no puede superar los 8 caracteres.',
                    'regex' => 'El documento de identidad solo puede contener números.',
                ];
                break;
            case 'telefono':
                if (empty($valor)) {
                    return response()->json(['valido' => true, 'mensaje' => 'Teléfono válido (opcional).']);
                }
                $rules = ['digits:9'];
                $messages = [
                    'digits' => 'El teléfono debe contener exactamente 9 dígitos.',
                ];
                break;
            case 'fecha_nacimiento':
                if (empty($valor)) {
                    return response()->json(['valido' => true, 'mensaje' => 'Fecha de nacimiento válida (opcional).']);
                }
                $rules = ['date', 'before:today'];
                $messages = [
                    'date' => 'La fecha de nacimiento debe ser una fecha válida.',
                    'before' => 'La fecha de nacimiento debe ser anterior a hoy.',
                ];
                break;
            default:
                Log::warning('Campo no reconocido para validación de edición', ['campo' => $campo]);
                return response()->json(['valido' => false, 'mensaje' => 'Campo no válido para validación.']);
        }
        
        $validator = Validator::make([$campo => $valor], [$campo => $rules], $messages);
        
        if ($validator->fails()) {
            return response()->json(['valido' => false, 'mensaje' => $validator->errors()->first($campo)]);
        }
        
        return response()->json(['valido' => true, 'mensaje' => 'Campo válido.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $trabajador = $this->trabajadorRepositorio->getById($id);
        if (!$trabajador) {
            return redirect()->route('admin.colaboradores.index')->with('error', 'Colaborador no encontrado.');
        }

        // Agregamos el campo correo
        $trabajador->correo = $trabajador->usuario->correo ?? 'No disponible';
        $trabajador->correo_personal = $trabajador->usuario->correo_personal ?? 'No disponible';
        $trabajador->clave_mostrar = $trabajador->usuario->clave_mostrar ?? 'No disponible';
        $trabajador->estado = $trabajador->usuario->activo;
        $trabajador->nro_metas = $trabajador->metas()->count();
        $trabajador->nro_tareas = $trabajador->tareas()->count();
        $trabajador->nro_reuniones = $trabajador->reuniones()->count();
        $trabajador->fecha_nacimiento = Carbon::parse($trabajador->fecha_nacimiento)->format('d/m/Y');
        $trabajador->fecha_registro = Carbon::parse($trabajador->usuario->fecha_registro)->format('d/m/Y');
        return response()->json($trabajador);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditarColaboradorRequest $request, string $id)
    {
        $trabajador = $this->trabajadorRepositorio->getById($id);
        if (!$trabajador) {
            return redirect()->route('admin.colaboradores.index')->with('error', 'Colaborador no encontrado.');
        }

        try {
            // Actualizar el usuario
            $usuario = $trabajador->usuario;
            $usuario->correo_personal = $request->input('correo_personal');
            $usuario->save();

            // Actualizar el colaborador
            $trabajador->nombres = $request->input('nombres');
            $trabajador->apellido_paterno = $request->input('apellido_paterno');
            $trabajador->apellido_materno = $request->input('apellido_materno');
            $trabajador->telefono = $request->input('telefono');
            $trabajador->doc_identidad = $request->input('doc_identidad');
            $trabajador->fecha_nacimiento = $request->input('fecha_nacimiento');
            $trabajador->save();

            return redirect()->route('admin.colaboradores.index')->with('success', 'Colaborador actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Error al actualizar el colaborador: ' . $e->getMessage()])->withInput();
        }
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
        /** @var Usuario $usuario */
        $usuario = Auth::user();
        if (!$usuario) {
            abort(401, 'Usuario no autenticado.');
        }
        $empresa = $this->empresaRepositorio->findOneBy('usuario_id', $usuario->id);
        if (!$empresa) {
            abort(404, 'No se encontró la empresa asociada al usuario.');
        }
        return $empresa;
    }
}
