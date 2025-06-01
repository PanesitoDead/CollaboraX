<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Repositories\EmpresaRepositorio;
use App\Repositories\TrabajadorRepositorio;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Traits\Http\Controllers\CriterioTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ColaboradorController extends Controller
{
    use CriterioTrait;
    protected EmpresaRepositorio $empresaRepositorio;

    protected TrabajadorRepositorio $trabajadorRepositorio;

    public function __construct(EmpresaRepositorio $empresaRepositorio, TrabajadorRepositorio $trabajadorRepositorio)
    {
        $this->empresaRepositorio = $empresaRepositorio;
        $this->trabajadorRepositorio = $trabajadorRepositorio;
    }
    public function index(Request $request)
    {
        $coordinadores = $this->getPaginado($request);
        return view('private.admin.colaboradores', [
            'coordinadores' => $coordinadores,
        ]);
    }

    public function getPaginado(Request $request)
    {
        $usuario = Auth::user();
        $empresa = $this->empresaRepositorio->findOneBy('usuario_id', $usuario->id);
        // dd($empresa);
        $criterios = $this->obtenerCriterios($request);
        // Creamos el query builder para las áreas
        $query = $this->trabajadorRepositorio->getModel()->newQuery();
        // Filtramos por la empresa del usuario autenticado
        $query->where('empresa_id', $empresa->id);
        // Aplicamos los criterios de búsqueda
        $trabajadoresPag = $this->trabajadorRepositorio->obtenerPaginado($criterios, $query);
        $trabajadoresParse = $trabajadoresPag->getCollection()->map(function ($trabajador) {
            $trabajador->correo = $trabajador->usuario->correo ?? 'No disponible';
            return $trabajador;
        });
        return $trabajadoresPag->setCollection($trabajadoresParse);
    }

    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'nombre' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'mensaje' => 'nullable|string',
        ]);

        // Lógica para enviar invitación
        // Mail::to($request->email)->send(new InvitacionColaborador($request->all()));

        return redirect()->route('admin.colaboradores.index')
            ->with('success', 'Invitación enviada correctamente.');
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
