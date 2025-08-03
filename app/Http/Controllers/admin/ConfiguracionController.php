<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\EmpresaRepositorio;
use App\Services\SuscripcionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConfiguracionController extends Controller
{
    protected EmpresaRepositorio $empresaRepositorio;
    protected SuscripcionService $suscripcionService;
    
    public function __construct(EmpresaRepositorio $empresaRepositorio, SuscripcionService $suscripcionService)
    {
        $this->empresaRepositorio = $empresaRepositorio;
        $this->suscripcionService = $suscripcionService;
    }

    public function index()
    {
        $empresa = $this->getEmpresa();
        
        // Si getEmpresa() retorna un redirect, retornarlo directamente
        if ($empresa instanceof \Illuminate\Http\RedirectResponse) {
            return $empresa;
        }
        
        // Obtener datos de suscripción con manejo de errores
        $usuario = Auth::user();
        $usuarioId = $usuario->id;
        
        try {
            // Usar el nuevo endpoint de resumen para obtener datos completos
            $resumenCompleto = $this->suscripcionService->obtenerResumenUsuario($usuarioId);
            
            if ($resumenCompleto) {
                // Usar datos del resumen
                $planesDisponibles = $this->suscripcionService->obtenerPlanes();
                $suscripcionActual = $resumenCompleto['suscripcion_activa'] ?? null;
                $historialPagos = $resumenCompleto['historial_pagos'] ?? [];
                $historialSuscripciones = $resumenCompleto['historial_suscripciones'] ?? [];
                $tieneSuscripcionActiva = $resumenCompleto['tiene_suscripcion_activa'] ?? false;
                $diasRestantes = $resumenCompleto['dias_restantes'] ?? 0;
                
                // Formatear paginación para el historial
                $paginacion = [
                    'pagina_actual' => 1,
                    'total_paginas' => 1,
                    'total_elementos' => count($historialPagos),
                    'elementos_por_pagina' => count($historialPagos)
                ];
            } else {
                // Fallback al método anterior si el resumen no está disponible
                $planesDisponibles = $this->suscripcionService->obtenerPlanes();
                $suscripcionActual = $this->suscripcionService->obtenerSuscripcionActual($usuarioId);
                $historialResult = $this->suscripcionService->obtenerHistorialPagos($usuarioId, 1, 10);
                $historialPagos = $historialResult['pagos'] ?? [];
                $paginacion = $historialResult['paginacion'] ?? [];
                $historialSuscripciones = [];
                $tieneSuscripcionActiva = !empty($suscripcionActual);
                $diasRestantes = 0;
            }
        } catch (\Exception $e) {
            // Si hay error con el servicio de suscripciones, usar valores por defecto
            Log::error('Error obteniendo datos de suscripción: ' . $e->getMessage());
            $planesDisponibles = [];
            $suscripcionActual = null;
            $historialPagos = [];
            $paginacion = [];
            $historialSuscripciones = [];
            $tieneSuscripcionActiva = false;
            $diasRestantes = 0;
        }
        
        return view('private.admin.configuracion', [
            'empresa' => $empresa,
            'planesDisponibles' => $planesDisponibles,
            'suscripcionActual' => $suscripcionActual,
            'historialPagos' => $historialPagos,
            'paginacion' => $paginacion,
            'historialSuscripciones' => $historialSuscripciones,
            'tieneSuscripcionActiva' => $tieneSuscripcionActiva,
            'diasRestantes' => $diasRestantes
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

    public function getEmpresa()
    {
        $usuarioId = Auth::id();
        if (!$usuarioId) {
            return redirect()->route('admin.dashboard.index')->with('error', 'Usuario no autenticado.');
        }
        
        $empresa = $this->empresaRepositorio->findOneBy('usuario_id', $usuarioId);
        return $empresa;
    }
}
