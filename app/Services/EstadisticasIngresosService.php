<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class EstadisticasIngresosService
{
    private $client;
    private $baseUrl;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'verify' => false
        ]);
        
        $this->baseUrl = env('PAGOS_MICROSERVICE_URL', 'http://34.173.216.37:3000');
    }

    /**
     * Obtener ingresos por mes (últimos 12 meses)
     */
    public function getIngresosPorMes()
    {
        try {
            $response = $this->client->get($this->baseUrl . '/api/estadisticas/ingresos/por-mes', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            
            if (isset($responseData['success']) && $responseData['success']) {
                return $responseData['data'];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error("Error obteniendo ingresos por mes: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener ingresos del mes actual
     */
    public function getIngresosMesActual()
    {
        try {
            $response = $this->client->get($this->baseUrl . '/api/estadisticas/ingresos/mes-actual', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            
            if (isset($responseData['success']) && $responseData['success']) {
                return $responseData['data'];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error("Error obteniendo ingresos del mes actual: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener estadísticas generales de ingresos
     */
    public function getEstadisticasGenerales()
    {
        try {
            $response = $this->client->get($this->baseUrl . '/api/estadisticas/ingresos/generales', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            
            if (isset($responseData['success']) && $responseData['success']) {
                return $responseData['data'];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error("Error obteniendo estadísticas generales: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener estadísticas por planes
     */
    public function getEstadisticasPorPlanes()
    {
        try {
            $response = $this->client->get($this->baseUrl . '/api/estadisticas/ingresos/por-planes', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            
            if (isset($responseData['success']) && $responseData['success']) {
                return $responseData['data'];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error("Error obteniendo estadísticas por planes: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener ingresos por rango de fechas
     */
    public function getIngresosPorRango($fechaInicio, $fechaFin)
    {
        try {
            $response = $this->client->get($this->baseUrl . '/api/estadisticas/ingresos/rango', [
                'query' => [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            
            if (isset($responseData['success']) && $responseData['success']) {
                return $responseData['data'];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error("Error obteniendo ingresos por rango: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Formatear datos de ingresos para gráficos
     */
    public function formatearParaGraficos($datos)
    {
        if (!$datos || !isset($datos['ingresos_por_mes'])) {
            return [
                'labels' => [],
                'datasets' => []
            ];
        }

        $labels = [];
        $ingresos = [];

        foreach ($datos['ingresos_por_mes'] as $mes) {
            $labels[] = $mes['nombre_mes'] . ' ' . $mes['año'];
            $ingresos[] = $mes['ingresos_totales'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ingresos Mensuales',
                    'data' => $ingresos,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true
                ]
            ]
        ];
    }

    /**
     * Obtener resumen completo de estadísticas
     */
    public function getResumenCompleto()
    {
        $ingresosMes = $this->getIngresosMesActual();
        $ingresosPorMes = $this->getIngresosPorMes();
        $estadisticasGenerales = $this->getEstadisticasGenerales();
        $estadisticasPorPlanes = $this->getEstadisticasPorPlanes();

        return [
            'mes_actual' => $ingresosMes,
            'por_mes' => $ingresosPorMes,
            'generales' => $estadisticasGenerales,
            'por_planes' => $estadisticasPorPlanes
        ];
    }
}
