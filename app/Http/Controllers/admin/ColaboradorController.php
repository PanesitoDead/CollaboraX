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
    public function store(Request $request)
    {
        // Agregar al correo el dominio de la empresa
        $empresa = $this->getEmpresa();
        $request->merge([
            'correo' => $request->input('correo') . '@' . strtolower($empresa->nombre) . '.cx.com',
        ]);

        // Creamos el usuario primero
        $usuario = $this->usuarioRepositorio->create([
            'correo' => $request->input('correo'),
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
        if (!$trabajador) {
            return redirect()->route('admin.colaboradores.index')->with('error', 'Error al crear el colaborador.');
        }

        return redirect()->route('admin.colaboradores.index')->with('success', 'Colaborador creado correctamente.');
       
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
    public function update(Request $request, string $id)
    {
        $trabajador = $this->trabajadorRepositorio->getById($id);
        if (!$trabajador) {
            return redirect()->route('admin.colaboradores.index')->with('error', 'Colaborador no encontrado.');
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

        return redirect()->route('admin.colaboradores.index')->with('success', 'Colaborador actualizado correctamente.');
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
            return redirect()->route('admin.dashboard')->with('error', 'No se encontró la empresa asociada al usuario.');
        }
        return $empresa;
    }
}
