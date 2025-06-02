<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Repositories\AreaRepositorio;
use App\Repositories\EmpresaRepositorio;
use App\Repositories\TrabajadorRepositorio;
use App\Repositories\UsuarioRepositorio;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public function getEmpresa()
    {
        $usuario = Auth::user();
        $empresa = $this->empresaRepositorio->findOneBy('usuario_id', $usuario->id);
        if (!$empresa) {
            return redirect()->route('admin.dashboard')->with('error', 'No se encontró la empresa asociada al usuario.');
        }
        return $empresa;
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
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

        // // Actualizar el usuario
        // $usuario = $trabajador->usuario;
        // $usuario->correo = $request->input('correo');
        // if ($request->has('clave')) {
        //     $usuario->clave = bcrypt($request->input('clave'));
        // }
        // $usuario->save();

        // Actualizar el colaborador
        $trabajador->nombres = $request->input('nombres');
        $trabajador->apellido_paterno = $request->input('apellido_paterno');
        $trabajador->apellido_materno = $request->input('apellido_materno');
        $trabajador->telefono = $request->input('telefono');
        $trabajador->doc_identidad = $request->input('doc_identidad');
        $trabajador->fecha_nacimiento = $request->input('fecha_nacimiento');
        $trabajador->save();

        return redirect()->route('admin.coordinadores-equipos.index')->with('success', 'Colaborador actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
