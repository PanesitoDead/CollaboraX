<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\EmpresaRepositorio;
use App\Traits\Http\Controllers\CriterioTrait;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use CriterioTrait;

    protected EmpresaRepositorio $empresaRepositorio;

    public function __construct(EmpresaRepositorio $empresaRepositorio)
    {
        $this->empresaRepositorio = $empresaRepositorio;
    }
    
    public function index()
    {
        $empresa = $this->getEmpresa();

        // Simulación de estadísticas generales
        $stats = [
            'areas' => $empresa->areas->count(),
            'usuarios_totales' => $empresa->trabajadores->count(),
            'metas_activas' => $empresa->totalMetasActivas(),
            'cumplimiento' => $empresa->progresoTotalPorPromedioAreas(),
        ];

        // Coordinadores generales
        $coordinadores = $this->empresaRepositorio->getCoordinadoresGenerales($empresa->id);
        // Áreas
        $areas = $empresa->areas;
      

        return view('private.admin.dashboard', [
            'empresa' => $empresa,
            'stats' => $stats,
            'coordinadores' => $coordinadores,
            'areas' => $areas,
        ]);
    }

    public function getEmpresa()
    {
        $usuario = Auth::user();
        $empresa = $this->empresaRepositorio->findOneBy('usuario_id', $usuario->id);
        if (!$empresa) {
            return redirect()->route('admin.dashboard.index')->with('error', 'No se encontró la empresa asociada al usuario.');
        }
        return $empresa;
    }
}
