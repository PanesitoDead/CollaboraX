<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Exception;

class PlanApiService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('api.planes.base_url');
        $this->timeout = config('api.planes.timeout');
    }

    /**
     * Obtener todos los planes
     */
    public function getAllPlanes()
    {
        try {
            $response = Http::timeout($this->timeout)->get($this->baseUrl . '/planes');
            
            if ($response->successful()) {
                $data = $response->json();
                
                // La API puede devolver directamente un array de planes
                $planes = $data['data'] ?? $data;
                
                // Obtener datos actualizados de la sesión si existen
                $planesActualizados = Session::get('planes_actualizados', []);
                
                // Normalizar los datos y aplicar cambios de sesión
                $planesNormalizados = collect($planes)->map(function ($plan) use ($planesActualizados) {
                    $planNormalizado = [
                        'id' => $plan['id'],
                        'nombre' => $plan['nombre'],
                        'costo_soles' => $plan['precio'],
                        'cant_usuarios' => $plan['cant_usuarios'] ?? 1,
                        'beneficios' => is_array($plan['beneficios']) 
                            ? implode("\n", $plan['beneficios']) 
                            : $plan['beneficios'],
                        'descripcion' => $plan['descripcion'] ?? '',
                        'frecuencia' => $plan['frecuencia'] ?? 'mensual'
                    ];
                    
                    // Aplicar cambios de sesión si existen
                    if (isset($planesActualizados[$plan['id']])) {
                        $cambios = $planesActualizados[$plan['id']];
                        $planNormalizado['nombre'] = $cambios['nombre'];
                        $planNormalizado['costo_soles'] = $cambios['costo_soles'];
                        $planNormalizado['descripcion'] = $cambios['descripcion'];
                        $planNormalizado['beneficios'] = $cambios['beneficios'];
                        $planNormalizado['cant_usuarios'] = $cambios['cant_usuarios'];
                    }
                    
                    return (object) $planNormalizado;
                })->values()->all();
                
                return $planesNormalizados;
            }
            
            Log::error('Error al obtener planes de la API', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return [];
        } catch (Exception $e) {
            Log::error('Excepción al obtener planes de la API', [
                'message' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Obtener un plan por ID
     */
    public function getPlanById($id)
    {
        try {
            $response = Http::timeout($this->timeout)->get($this->baseUrl . "/planes/{$id}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                // La API puede devolver directamente los datos del plan
                $plan = $data['data'] ?? $data;
                
                // Normalizar los datos
                return (object) [
                    'id' => $plan['id'],
                    'nombre' => $plan['nombre'],
                    'costo_soles' => $plan['precio'],
                    'cant_usuarios' => $plan['cant_usuarios'] ?? 1,
                    'beneficios' => is_array($plan['beneficios']) 
                        ? implode("\n", $plan['beneficios']) 
                        : $plan['beneficios'],
                    'descripcion' => $plan['descripcion'] ?? '',
                    'frecuencia' => $plan['frecuencia'] ?? 'mensual'
                ];
            }
            
            Log::error('Error al obtener plan por ID de la API', [
                'id' => $id,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return null;
        } catch (Exception $e) {
            Log::error('Excepción al obtener plan por ID de la API', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Crear un nuevo plan
     */
    public function createPlan(array $data)
    {
        try {
            // Convertir los datos del formulario al formato esperado por la API
            $apiData = [
                'nombre' => $data['nombre'],
                'precio' => $data['costo_soles'],
                'descripcion' => $data['descripcion'] ?? 'Plan personalizado',
                'frecuencia' => 'mensual',
                'beneficios' => explode("\n", $data['beneficios']),
                'cant_usuarios' => (int) ($data['cant_usuarios'] ?? 1)
            ];
            
            $response = Http::timeout($this->timeout)->post($this->baseUrl . '/planes', $apiData);
            
            if ($response->successful()) {
                $responseData = $response->json();
                
                // La API puede devolver directamente los datos del plan creado
                $plan = $responseData['data'] ?? $responseData;
                
                return (object) [
                    'id' => $plan['id'],
                    'nombre' => $plan['nombre'],
                    'costo_soles' => $plan['precio'],
                    'cant_usuarios' => $plan['cant_usuarios'] ?? 1,
                    'beneficios' => is_array($plan['beneficios']) 
                        ? implode("\n", $plan['beneficios']) 
                        : $plan['beneficios'],
                    'descripcion' => $plan['descripcion'] ?? '',
                    'frecuencia' => $plan['frecuencia'] ?? 'mensual'
                ];
            }
            
            Log::error('Error al crear plan en la API', [
                'data' => $apiData,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return null;
        } catch (Exception $e) {
            Log::error('Excepción al crear plan en la API', [
                'data' => $data,
                'message' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Actualizar un plan
     */
    public function updatePlan($id, array $data)
    {
        // Guardar los cambios en la sesión para mantener la funcionalidad local
        $planesActualizados = Session::get('planes_actualizados', []);
        $planesActualizados[$id] = $data;
        Session::put('planes_actualizados', $planesActualizados);
        
        try {
            // Convertir los datos del formulario al formato esperado por la API
            $apiData = [
                'nombre' => $data['nombre'],
                'precio' => $data['costo_soles'],
                'descripcion' => $data['descripcion'] ?? 'Plan personalizado',
                'frecuencia' => 'mensual',
                'beneficios' => explode("\n", $data['beneficios']),
                'cant_usuarios' => (int) ($data['cant_usuarios'] ?? 1)
            ];
            
            $response = Http::timeout($this->timeout)->put($this->baseUrl . "/planes/{$id}", $apiData);
            
            if ($response->successful()) {
                $responseData = $response->json();
                
                // La API puede devolver directamente los datos del plan actualizado
                $plan = $responseData['data'] ?? $responseData;
                
                return (object) [
                    'id' => $plan['id'] ?? $id,
                    'nombre' => $plan['nombre'] ?? $data['nombre'],
                    'costo_soles' => $plan['precio'] ?? $data['costo_soles'],
                    'cant_usuarios' => $plan['cant_usuarios'] ?? $data['cant_usuarios'] ?? 1,
                    'beneficios' => isset($plan['beneficios']) 
                        ? (is_array($plan['beneficios']) 
                            ? implode("\n", $plan['beneficios']) 
                            : $plan['beneficios'])
                        : $data['beneficios'],
                    'descripcion' => $plan['descripcion'] ?? $data['descripcion'] ?? '',
                    'frecuencia' => $plan['frecuencia'] ?? 'mensual'
                ];
            }
            
            Log::error('Error al actualizar plan en la API', [
                'id' => $id,
                'data' => $apiData,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            // Si la API falla, devolver los datos locales
            return (object) [
                'id' => $id,
                'nombre' => $data['nombre'],
                'costo_soles' => $data['costo_soles'],
                'cant_usuarios' => $data['cant_usuarios'] ?? 1,
                'beneficios' => $data['beneficios'],
                'descripcion' => $data['descripcion'] ?? '',
                'frecuencia' => 'mensual'
            ];
            
        } catch (Exception $e) {
            Log::error('Excepción al actualizar plan en la API', [
                'id' => $id,
                'data' => $data,
                'message' => $e->getMessage()
            ]);
            
            // Si hay excepción, devolver los datos locales
            return (object) [
                'id' => $id,
                'nombre' => $data['nombre'],
                'costo_soles' => $data['costo_soles'],
                'cant_usuarios' => $data['cant_usuarios'] ?? 1,
                'beneficios' => $data['beneficios'],
                'descripcion' => $data['descripcion'] ?? '',
                'frecuencia' => 'mensual'
            ];
        }
    }

    /**
     * Eliminar un plan
     */
    public function deletePlan($id)
    {
        try {
            $response = Http::timeout($this->timeout)->delete($this->baseUrl . "/planes/{$id}");
            
            if ($response->successful()) {
                return true;
            }
            
            Log::error('Error al eliminar plan en la API', [
                'id' => $id,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return false;
        } catch (Exception $e) {
            Log::error('Excepción al eliminar plan en la API', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}
