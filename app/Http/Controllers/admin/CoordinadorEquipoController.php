<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\AreaRepositorio;
use App\Repositories\EmpresaRepositorio;
use App\Repositories\TrabajadorRepositorio;
use App\Repositories\UsuarioRepositorio;
use App\Traits\Http\Controllers\CriterioTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CoordinadorEquipoController extends Controller
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
        return view('private.admin.coordinadores-equipos', [
            'criterios' => $this->obtenerCriterios($request),
            'areas' => $areas,
            'coordinadores' => $coordinadores,
            'empresa' => $empresa,
        ]);
    }

    public function getPaginado(Request $request)
    {
        /** @var \App\Models\Usuario $usuario */
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
        // Filtramos por el rol de coordinador de equipo id=4
        $query->where('usuarios.rol_id', 4);

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
            return redirect()->route('admin.coordinadores-equipos.index')->with('error', 'Error al cambiar el estado del colaborador.');
        }
        return redirect()->route('admin.coordinadores-equipos.index')->with('success', 'Estado del colaborador actualizado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $trabajador = $this->trabajadorRepositorio->getById($id);
        if (!$trabajador) {
            return redirect()->route('admin.coordinadores-equipos.index')->with('error', 'Colaborador no encontrado.');
        }

        // Agregamos el campo correo
        $trabajador->correo = $trabajador->usuario->correo ?? 'No disponible';
        $trabajador->correo_personal = $trabajador->usuario->correo_personal ?? 'No disponible';
        $trabajador->clave_mostrar = $trabajador->usuario->clave_mostrar ?? 'No disponible';
        $trabajador->estado = $trabajador->usuario->activo;
        // Formateamos la fecha de nacimiento
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
    public function update(Request $request, string $id)
    {
        $trabajador = $this->trabajadorRepositorio->getById($id);
        if (!$trabajador) {
            return redirect()->route('admin.coordinadores-equipos.index')->with('error', 'Colaborador no encontrado.');
        }

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

        return redirect()->route('admin.coordinadores-equipos.index')->with('success', 'Datos actualizados exitosamente');
    }

    /**
     * Valida campos específicos para edición de coordinadores de equipo
     */
    public function validarCampoEdicion(Request $request)
    {
        $campo = $request->input('campo');
        $valor = $request->input('valor');
        $coordinadorId = $request->input('coordinador_id');

        // Validaciones básicas según el campo
        switch ($campo) {
            case 'nombres':
                if (empty(trim($valor))) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'Los nombres son obligatorios.'
                    ]);
                }
                if (strlen($valor) < 2) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'Los nombres deben tener al menos 2 caracteres.'
                    ]);
                }
                if (strlen($valor) > 50) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'Los nombres no pueden exceder 50 caracteres.'
                    ]);
                }
                break;

            case 'apellido_paterno':
                if (empty(trim($valor))) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El apellido paterno es obligatorio.'
                    ]);
                }
                if (strlen($valor) < 2) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El apellido paterno debe tener al menos 2 caracteres.'
                    ]);
                }
                if (strlen($valor) > 50) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El apellido paterno no puede exceder 50 caracteres.'
                    ]);
                }
                break;

            case 'apellido_materno':
                if (empty(trim($valor))) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El apellido materno es obligatorio.'
                    ]);
                }
                if (strlen($valor) < 2) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El apellido materno debe tener al menos 2 caracteres.'
                    ]);
                }
                if (strlen($valor) > 50) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El apellido materno no puede exceder 50 caracteres.'
                    ]);
                }
                break;

            case 'documento':
                if (empty(trim($valor))) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El documento es obligatorio.'
                    ]);
                }
                if (strlen($valor) < 8) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El documento debe tener al menos 8 caracteres.'
                    ]);
                }
                
                // Verificar si ya existe otro coordinador con el mismo documento
                $coordinadorExistente = $this->trabajadorRepositorio->findOneBy('doc_identidad', $valor);
                if ($coordinadorExistente && $coordinadorExistente->id != $coordinadorId) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'Ya existe un coordinador con este documento.'
                    ]);
                }
                break;

            case 'telefono':
                if (empty(trim($valor))) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El teléfono es obligatorio.'
                    ]);
                }
                if (!preg_match('/^\d{9}$/', $valor)) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El teléfono debe tener exactamente 9 dígitos.'
                    ]);
                }
                break;

            case 'fecha_nacimiento':
                if (empty(trim($valor))) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'La fecha de nacimiento es obligatoria.'
                    ]);
                }
                
                $fechaNacimiento = Carbon::parse($valor);
                $edad = $fechaNacimiento->diffInYears(Carbon::now());
                
                if ($edad < 18) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El coordinador debe ser mayor de edad.'
                    ]);
                }
                if ($edad > 65) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'La edad no puede ser mayor a 65 años.'
                    ]);
                }
                break;

            default:
                return response()->json([
                    'valido' => false,
                    'mensaje' => 'Campo no válido para validación.'
                ]);
        }

        return response()->json([
            'valido' => true,
            'mensaje' => 'Campo válido.'
        ]);
    }

    /**
     * Valida correo personal específicamente para edición de coordinadores
     */
    public function validarCorreoPersonalEdicion(Request $request)
    {
        $correo = $request->input('correo_personal');
        $coordinadorId = $request->input('coordinador_id');

        // Validación básica de formato
        if (empty(trim($correo))) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'El correo personal es obligatorio.'
            ]);
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'El formato del correo no es válido.'
            ]);
        }

        // Verificar si ya existe otro coordinador con el mismo correo personal
        $usuarioExistente = $this->usuarioRepositorio->findOneBy('correo_personal', $correo);
        if ($usuarioExistente) {
            // Verificar si es un trabajador diferente al que se está editando
            $trabajadorExistente = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuarioExistente->id);
            if ($trabajadorExistente && $trabajadorExistente->id != $coordinadorId) {
                return response()->json([
                    'valido' => false,
                    'mensaje' => 'Ya existe un coordinador con este correo personal.'
                ]);
            }
        }

        return response()->json([
            'valido' => true,
            'mensaje' => 'Correo válido y disponible.'
        ]);
    }

    public function getEmpresa()
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();
        if (!$usuario) {
            return null;
        }
        $empresa = $this->empresaRepositorio->findOneBy('usuario_id', $usuario->id);
        if (!$empresa) {
            return null;
        }
        return $empresa;
    }
}
