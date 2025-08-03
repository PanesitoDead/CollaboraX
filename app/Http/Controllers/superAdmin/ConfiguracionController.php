<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\PlanApiService;
use App\Http\Requests\PlanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfiguracionController extends Controller
{

    protected PlanApiService $planApiService;

    public function __construct(PlanApiService $planApiService)
    {
        $this->planApiService = $planApiService;
    }


    public function index()
    {   
        $planes = $this->planApiService->getAllPlanes();

        // Si hay error con la API o no hay planes
        if (empty($planes)) {
            return view('super-admin.configuracion', [
                'planes' => [],
                'error' => 'No se pudieron cargar los planes desde la API'
            ]);
        }

        return view('super-admin.configuracion', [
            'planes' => $planes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('super-admin.planes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlanRequest $request)
    {
        $plan = $this->planApiService->createPlan($request->validated());
        
        if (!$plan) {
            return redirect()->route('super-admin.configuracion.index')
                           ->with('error', 'Error al crear el plan');
        }
        
        return redirect()->route('super-admin.configuracion.index')
                       ->with('success', 'Plan creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $plan = $this->planApiService->getPlanById($id);
        
        if (!$plan) {
            return redirect()->route('super-admin.configuracion.index')
                           ->with('error', 'Plan no encontrado');
        }
        
        return view('super-admin.planes.show', ['plan' => $plan]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $plan = $this->planApiService->getPlanById($id);
        
        if (!$plan) {
            return redirect()->route('super-admin.configuracion.index')
                           ->with('error', 'Plan no encontrado');
        }
        
        return view('super-admin.planes.edit', ['plan' => $plan]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(PlanRequest $request, string $id)
    {
        $plan = $this->planApiService->updatePlan($id, $request->validated());
        
        if (!$plan) {
            return redirect()->route('super-admin.configuracion.index')
                           ->with('error', 'Error al actualizar la configuración');
        }
        
        return redirect()->route('super-admin.configuracion.index')
                       ->with('success', 'Configuración actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = $this->planApiService->deletePlan($id);
        
        if (!$deleted) {
            return redirect()->route('super-admin.configuracion.index')
                           ->with('error', 'Error al eliminar el plan');
        }
        
        return redirect()->route('super-admin.configuracion.index')
                       ->with('success', 'Plan eliminado correctamente');
    }
}
