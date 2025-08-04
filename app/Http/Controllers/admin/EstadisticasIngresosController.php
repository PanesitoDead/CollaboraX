<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EstadisticasIngresosService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EstadisticasIngresosController extends Controller
{
    private $estadisticasService;

    public function __construct(EstadisticasIngresosService $estadisticasService)
    {
        $this->estadisticasService = $estadisticasService;
    }

    /**
     * Mostrar vista de estadísticas de ingresos
     */
    public function index()
    {
        $resumen = $this->estadisticasService->getResumenCompleto();
        
        return view('private.admin.estadisticas-ingresos', compact('resumen'));
    }

    /**
     * API: Obtener ingresos por mes
     */
    public function getIngresosPorMes(): JsonResponse
    {
        $datos = $this->estadisticasService->getIngresosPorMes();
        
        if ($datos) {
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No se pudieron obtener los datos de ingresos'
        ], 500);
    }

    /**
     * API: Obtener ingresos del mes actual
     */
    public function getIngresosMesActual(): JsonResponse
    {
        $datos = $this->estadisticasService->getIngresosMesActual();
        
        if ($datos) {
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No se pudieron obtener los datos del mes actual'
        ], 500);
    }

    /**
     * API: Obtener estadísticas generales
     */
    public function getEstadisticasGenerales(): JsonResponse
    {
        $datos = $this->estadisticasService->getEstadisticasGenerales();
        
        if ($datos) {
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No se pudieron obtener las estadísticas generales'
        ], 500);
    }

    /**
     * API: Obtener estadísticas por planes
     */
    public function getEstadisticasPorPlanes(): JsonResponse
    {
        $datos = $this->estadisticasService->getEstadisticasPorPlanes();
        
        if ($datos) {
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No se pudieron obtener las estadísticas por planes'
        ], 500);
    }

    /**
     * API: Obtener ingresos por rango de fechas
     */
    public function getIngresosPorRango(Request $request): JsonResponse
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        $datos = $this->estadisticasService->getIngresosPorRango(
            $request->fecha_inicio,
            $request->fecha_fin
        );
        
        if ($datos) {
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No se pudieron obtener los datos para el rango especificado'
        ], 500);
    }

    /**
     * API: Obtener datos formateados para gráficos
     */
    public function getDatosParaGraficos(): JsonResponse
    {
        $ingresosPorMes = $this->estadisticasService->getIngresosPorMes();
        
        if ($ingresosPorMes) {
            $datosFormateados = $this->estadisticasService->formatearParaGraficos($ingresosPorMes);
            
            return response()->json([
                'success' => true,
                'data' => $datosFormateados
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No se pudieron obtener los datos para gráficos'
        ], 500);
    }

    /**
     * API: Obtener resumen completo
     */
    public function getResumenCompleto(): JsonResponse
    {
        $resumen = $this->estadisticasService->getResumenCompleto();
        
        return response()->json([
            'success' => true,
            'data' => $resumen
        ]);
    }
}
