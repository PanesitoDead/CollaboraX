<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SuscripcionService
{
    private $client;
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false // Solo para desarrollo, en producción usar certificados válidos
        ]);
        
        // Configurar desde el archivo .env
        $this->baseUrl = env('PAGOS_MICROSERVICE_URL', 'http://34.173.216.37:3000');
        $this->apiKey = env('PAGOS_MICROSERVICE_API_KEY', 'default-api-key');
    }

    /**
     * Obtener todos los planes disponibles
     */
    public function obtenerPlanes()
    {
        try {
            $cacheKey = 'planes_disponibles';
            
            // Intentar usar cache, si falla obtener directamente
            try {
                return Cache::remember($cacheKey, 3600, function () { // Cache por 1 hora
                    $response = $this->client->get($this->baseUrl . '/api/planes', [
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json'
                        ]
                    ]);

                    $responseData = json_decode($response->getBody()->getContents(), true);
                    
                    // Extraer los datos según la estructura de la API documentada
                    $data = null;
                    if (isset($responseData['success']) && $responseData['success'] && isset($responseData['data'])) {
                        $data = $responseData['data'];
                    } elseif (is_array($responseData)) {
                        // Fallback para respuesta directa sin wrapper
                        $data = $responseData;
                    }
                    
                    // Validar que sea un array y que contenga elementos válidos
                    if (!is_array($data)) {
                        Log::warning('API devolvió datos no válidos para planes (cache), usando planes por defecto');
                        return $this->getPlanesDefault();
                    }
                    
                    // Filtrar solo elementos que sean arrays con las claves necesarias
                    $planesValidos = array_filter($data, function($plan) {
                        return is_array($plan) && 
                               isset($plan['nombre']) && 
                               isset($plan['precio']) &&
                               isset($plan['frecuencia']);
                    });
                    
                    return array_values($planesValidos);
                });
            } catch (\Exception $cacheException) {
                // Si el cache falla, obtener directamente sin cache
                Log::warning('Cache no disponible, obteniendo planes directamente: ' . $cacheException->getMessage());
                
                $response = $this->client->get($this->baseUrl . '/api/planes', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ]
                ]);

                $responseData = json_decode($response->getBody()->getContents(), true);
                
                // Extraer los datos según la estructura de la API documentada
                $data = null;
                if (isset($responseData['success']) && $responseData['success'] && isset($responseData['data'])) {
                    $data = $responseData['data'];
                } elseif (is_array($responseData)) {
                    // Fallback para respuesta directa sin wrapper
                    $data = $responseData;
                }
                
                // Validar que sea un array y que contenga elementos válidos
                if (!is_array($data)) {
                    Log::warning('API devolvió datos no válidos para planes, usando planes por defecto');
                    return $this->getPlanesDefault();
                }
                
                // Filtrar solo elementos que sean arrays con las claves necesarias
                $planesValidos = array_filter($data, function($plan) {
                    return is_array($plan) && 
                           isset($plan['nombre']) && 
                           isset($plan['precio']) &&
                           isset($plan['frecuencia']);
                });
                
                return array_values($planesValidos);
            }
        } catch (RequestException $e) {
            Log::error('Error al obtener planes: ' . $e->getMessage());
            return $this->getPlanesDefault();
        }
    }

    /**
     * Limpiar cache del usuario para forzar actualización de datos
     */
    public function limpiarCacheUsuario($usuarioId)
    {
        $cacheKey = "resumen_usuario_{$usuarioId}";
        Cache::forget($cacheKey);
        Log::info("Cache limpiado manualmente para usuario {$usuarioId}");
    }

    /**
     * Obtener resumen completo del usuario (nueva funcionalidad)
     * @param int $usuarioId ID del usuario
     * @param bool $forzarActualizacion Si es true, ignora el cache y obtiene datos frescos
     */
    public function obtenerResumenUsuario($usuarioId, $forzarActualizacion = false)
    {
        try {
            $cacheKey = "resumen_usuario_{$usuarioId}";
            
            if ($forzarActualizacion) {
                // Si se fuerza la actualización, eliminar el cache y obtener datos frescos
                Cache::forget($cacheKey);
                Log::info("Forzando actualización de datos para usuario {$usuarioId}");
            }
            
            return Cache::remember($cacheKey, 300, function () use ($usuarioId) { // Cache por 5 minutos
                $response = $this->client->get($this->baseUrl . "/api/suscripciones/usuario/{$usuarioId}/resumen", [
                    'headers' => [
                        'Accept' => 'application/json'
                    ],
                    'query' => [
                        'timestamp' => time() // Evitar cache del navegador/proxy
                    ]
                ]);

                $data = json_decode($response->getBody()->getContents(), true);
                
                if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
                    Log::info("Resumen actualizado para usuario {$usuarioId}", [
                        'tiene_suscripcion' => $data['data']['tiene_suscripcion_activa'] ?? false,
                        'plan' => $data['data']['suscripcion_activa']['plan_nombre'] ?? 'ninguno'
                    ]);
                    return $data['data'];
                }
                
                return null;
            });
        } catch (RequestException $e) {
            Log::error('Error al obtener resumen del usuario: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener suscripción activa del usuario (actualizado para usar resumen)
     */
    public function obtenerSuscripcionActual($usuarioId)
    {
        try {
            // Usar el nuevo endpoint de resumen que es más eficiente
            $resumen = $this->obtenerResumenUsuario($usuarioId);
            
            if ($resumen && isset($resumen['suscripcion_activa'])) {
                return $resumen['suscripcion_activa'];
            }
            
            // Fallback al endpoint original si el resumen no está disponible
            $response = $this->client->get($this->baseUrl . "/api/suscripciones/usuario/{$usuarioId}/activa", [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
                return $data['data'];
            }
            
            return null;
        } catch (RequestException $e) {
            Log::error('Error al obtener suscripción actual: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener historial de pagos del usuario (optimizado con resumen)
     */
    public function obtenerHistorialPagos($usuarioId, $pagina = 1, $limite = 10)
    {
        try {
            // Para la primera página con pocos elementos, usar el resumen que es más rápido
            if ($pagina === 1 && $limite <= 5) {
                $resumen = $this->obtenerResumenUsuario($usuarioId);
                
                if ($resumen && isset($resumen['historial_pagos'])) {
                    $historialCompleto = $resumen['historial_pagos'];
                    $pagosPaginados = array_slice($historialCompleto, 0, $limite);
                    
                    return [
                        'pagos' => $pagosPaginados,
                        'paginacion' => [
                            'pagina_actual' => 1,
                            'total_paginas' => ceil(count($historialCompleto) / $limite),
                            'total_elementos' => count($historialCompleto),
                            'elementos_por_pagina' => $limite
                        ]
                    ];
                }
            }
            
            // Fallback al endpoint específico de pagos para paginación completa
            $response = $this->client->get($this->baseUrl . "/api/pagos/usuario/{$usuarioId}", [
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'query' => [
                    'page' => $pagina,
                    'limit' => $limite
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
                $pagos = $data['data'];
                return [
                    'pagos' => $pagos ?? [],
                    'paginacion' => [
                        'pagina_actual' => $pagina,
                        'total_paginas' => 1,
                        'total' => count($pagos ?? []),
                        'desde' => 1,
                        'hasta' => count($pagos ?? [])
                    ]
                ];
            }
            
            return [
                'pagos' => [],
                'paginacion' => [
                    'pagina_actual' => $pagina,
                    'total_paginas' => 1,
                    'total' => 0,
                    'desde' => 0,
                    'hasta' => 0
                ]
            ];
        } catch (RequestException $e) {
            Log::error('Error al obtener historial de pagos: ' . $e->getMessage());
            return [
                'pagos' => [],
                'paginacion' => [
                    'pagina_actual' => 1,
                    'total_paginas' => 1,
                    'total' => 0,
                    'desde' => 0,
                    'hasta' => 0
                ]
            ];
        }
    }

    /**
     * Crear preferencia de pago en MercadoPago
     */
    public function crearPreferenciaPago($usuarioId, $planId, $datosAdicionales = [])
    {
        try {
            // Primero obtener información del plan
            $planes = $this->obtenerPlanes();
            $planSeleccionado = null;
            
            foreach ($planes as $plan) {
                if (isset($plan['id']) && $plan['id'] == $planId) {
                    $planSeleccionado = $plan;
                    break;
                }
            }
            
            if (!$planSeleccionado) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Plan no encontrado'
                ];
            }
            
            // Construir payload según el formato esperado por la API
            $payload = [
                'usuario_id' => (int) $usuarioId,
                'plan_id' => (int) $planId,
                'descripcion' => $planSeleccionado['nombre'] ?? 'Suscripción mensual',
                'monto' => (float) ($planSeleccionado['precio'] ?? 0)
            ];

            // Validar que todos los campos requeridos estén presentes
            if (!isset($payload['usuario_id']) || !isset($payload['plan_id']) || empty($payload['descripcion']) || $payload['monto'] <= 0) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Datos del plan inválidos'
                ];
            }

            $response = $this->client->post($this->baseUrl . '/api/mercadopago/preferencia', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => $payload
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            // La API de MercadoPago retorna directamente init_point e id_preferencia
            if (isset($data['init_point']) && isset($data['id_preferencia'])) {
                // Limpiar cache
                Cache::forget('planes_disponibles');
                Cache::forget("suscripcion_actual_{$usuarioId}");
                
                return [
                    'success' => true,
                    'data' => [
                        'init_point' => $data['init_point'],
                        'id_preferencia' => $data['id_preferencia']
                    ],
                    'message' => 'Preferencia de pago creada exitosamente'
                ];
            }
            
            // Si no tiene la estructura esperada, verificar si hay estructura con success
            if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
                // Limpiar cache
                Cache::forget('planes_disponibles');
                Cache::forget("suscripcion_actual_{$usuarioId}");
                
                return [
                    'success' => true,
                    'data' => $data['data'],
                    'message' => $data['message'] ?? 'Preferencia de pago creada exitosamente'
                ];
            }
            
            // Si la API retorna error
            return [
                'success' => false,
                'data' => null,
                'message' => $data['message'] ?? 'Error desconocido al crear preferencia de pago'
            ];
        } catch (RequestException $e) {
            Log::error('Error al crear preferencia de pago: ' . $e->getMessage());
            
            $errorData = null;
            if ($e->hasResponse()) {
                $errorData = json_decode($e->getResponse()->getBody()->getContents(), true);
            }
            
            return [
                'success' => false,
                'message' => $errorData['message'] ?? 'Error al crear la preferencia de pago',
                'errors' => $errorData['errors'] ?? []
            ];
        }
    }

    /**
     * Verificar estado de un pago específico
     */
    public function verificarEstadoPago($pagoId)
    {
        try {
            $response = $this->client->get($this->baseUrl . "/api/pagos/{$pagoId}", [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data ?? null;
        } catch (RequestException $e) {
            Log::error('Error al verificar estado del pago: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cancelar suscripción
     */
    public function cancelarSuscripcion($usuarioId, $suscripcionId)
    {
        try {
            Log::info('=== INICIANDO CANCELACIÓN DE SUSCRIPCIÓN ===', [
                'usuario_id' => $usuarioId,
                'suscripcion_id' => $suscripcionId,
                'endpoint' => $this->baseUrl . "/api/suscripciones/{$suscripcionId}/estado"
            ]);

            // Usar el endpoint correcto según la documentación: PATCH /api/suscripciones/:id/estado
            $response = $this->client->patch($this->baseUrl . "/api/suscripciones/{$suscripcionId}/estado", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'estado' => 'cancelada'
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            
            Log::info('Respuesta del microservicio', [
                'status_code' => $statusCode,
                'response_body' => $responseBody
            ]);

            $responseData = json_decode($responseBody, true);

            // Limpiar caches relacionados
            Cache::forget("suscripcion_actual_{$usuarioId}");
            Cache::forget("resumen_usuario_{$usuarioId}");
            
            // Verificar la respuesta según el formato de la API
            if (isset($responseData['success']) && $responseData['success']) {
                Log::info('Cancelación exitosa', ['response' => $responseData]);
                return [
                    'success' => true, 
                    'message' => $responseData['message'] ?? 'Suscripción cancelada exitosamente'
                ];
            } else {
                Log::warning('Cancelación falló según respuesta', ['response' => $responseData]);
                return [
                    'success' => false, 
                    'message' => $responseData['message'] ?? 'Error al cancelar la suscripción'
                ];
            }
        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'sin_respuesta';
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'sin_contenido';
            
            Log::error('Error RequestException al cancelar suscripción', [
                'message' => $e->getMessage(),
                'status_code' => $statusCode,
                'error_body' => $errorBody,
                'usuario_id' => $usuarioId,
                'suscripcion_id' => $suscripcionId
            ]);
            
            // Extraer mensaje específico del error si está disponible
            $errorMessage = 'Error al cancelar la suscripción';
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $errorData = json_decode($responseBody, true);
                if (isset($errorData['message'])) {
                    $errorMessage = $errorData['message'];
                } else if (isset($errorData['error'])) {
                    $errorMessage = $errorData['error'];
                }
            }
            
            return ['success' => false, 'message' => $errorMessage];
        } catch (\Exception $e) {
            Log::error('Error general al cancelar suscripción', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'usuario_id' => $usuarioId,
                'suscripcion_id' => $suscripcionId
            ]);
            
            return ['success' => false, 'message' => 'Error interno al cancelar la suscripción'];
        }
    }

    /**
     * Renovar manualmente una suscripción
     */
    public function renovarSuscripcion($suscripcionId)
    {
        try {
            $response = $this->client->post($this->baseUrl . "/api/suscripciones/{$suscripcionId}/renovar", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['success']) && $data['success'] === true) {
                return [
                    'success' => true,
                    'message' => 'Suscripción renovada exitosamente',
                    'data' => $data['data'] ?? null
                ];
            }
            
            return [
                'success' => false,
                'message' => $data['message'] ?? 'Error al renovar la suscripción'
            ];
        } catch (RequestException $e) {
            Log::error('Error al renovar suscripción: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al renovar la suscripción'];
        }
    }

    /**
     * Cancelar renovación automática de una suscripción
     * Actualizado según la documentación API v1.1.0
     */
    public function cancelarRenovacionAutomatica($suscripcionId)
    {
        try {
            $response = $this->client->patch($this->baseUrl . "/api/suscripciones/{$suscripcionId}/renovacion-automatica", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'renovacion_automatica' => false
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['success']) && $data['success'] === true) {
                return [
                    'success' => true,
                    'message' => 'Renovación automática cancelada exitosamente'
                ];
            }
            
            return [
                'success' => false,
                'message' => $data['message'] ?? 'Error al cancelar la renovación automática'
            ];
        } catch (RequestException $e) {
            Log::error('Error al cancelar renovación automática: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al cancelar la renovación automática'];
        }
    }

    /**
     * Activar renovación automática de una suscripción
     * Nuevo método según la documentación API v1.1.0
     */
    public function activarRenovacionAutomatica($suscripcionId)
    {
        try {
            $response = $this->client->patch($this->baseUrl . "/suscripciones/{$suscripcionId}/renovacion-automatica", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'renovacion_automatica' => true
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['success']) && $data['success'] === true) {
                return [
                    'success' => true,
                    'message' => 'Renovación automática activada exitosamente'
                ];
            }
            
            return [
                'success' => false,
                'message' => $data['message'] ?? 'Error al activar la renovación automática'
            ];
        } catch (RequestException $e) {
            Log::error('Error al activar renovación automática: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al activar la renovación automática'];
        }
    }

    /**
     * Verificar suscripciones vencidas y procesar renovaciones automáticas
     */
    public function verificarSuscripcionesVencidas()
    {
        try {
            $response = $this->client->post($this->baseUrl . "/suscripciones/check-expired", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['success']) && $data['success'] === true) {
                Log::info('Verificación de suscripciones vencidas completada', $data['data'] ?? []);
                return [
                    'success' => true,
                    'message' => 'Verificación completada exitosamente',
                    'data' => $data['data'] ?? null
                ];
            }
            
            return [
                'success' => false,
                'message' => $data['message'] ?? 'Error en la verificación'
            ];
        } catch (RequestException $e) {
            Log::error('Error al verificar suscripciones vencidas: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la verificación de suscripciones'];
        }
    }

    /**
     * Obtener estado detallado de una suscripción específica
     */
    public function obtenerSuscripcion($suscripcionId)
    {
        try {
            $response = $this->client->get($this->baseUrl . "/suscripciones/{$suscripcionId}", [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
                return $data['data'];
            }
            
            return null;
        } catch (RequestException $e) {
            Log::error('Error al obtener suscripción: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Planes por defecto en caso de error de conexión
     */
    private function getPlanesDefault()
    {
        return [
            [
                'id' => 1,
                'nombre' => 'Plan Standard',
                'precio' => 29.90,
                'frecuencia' => 'mensual',
                'descripcion' => 'Ideal para uso básico'
            ],
            [
                'id' => 2,
                'nombre' => 'Plan Business',
                'precio' => 59.90,
                'frecuencia' => 'mensual',
                'descripcion' => 'Para pequeñas empresas'
            ],
            [
                'id' => 3,
                'nombre' => 'Plan Enterprise',
                'precio' => 99.90,
                'frecuencia' => 'mensual',
                'descripcion' => 'Para organizaciones grandes'
            ]
        ];
    }
}
