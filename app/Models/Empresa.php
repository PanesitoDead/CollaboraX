<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Empresa extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'empresas';
    public $timestamps = false;

    protected $fillable = ['usuario_id', 'nombre', 'descripcion', 'ruc', 'telefono'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class);
    }
    
    public function totalMetasActivas(): int
    {
        // 1) Cargamos en memoria (eager loading) las áreas con sus metasActivas
        $this->loadMissing('areas.metasActivas');

        // 2) Sumamos la cantidad de metas activas en cada área
        $total = 0;
        foreach ($this->areas as $area) {
            $total += $area->metasActivas->count();
        }

        return $total;
    }

    public function progresoTotalPorPromedioAreas(): float
    {
        // 1. Cargamos las áreas en memoria (eager load)
        $this->loadMissing('areas');

        // Si no hay áreas, devolvemos 0
        if ($this->areas->isEmpty()) {
            return 0.0;
        }

        // 2. Sumamos el atributo porcentajeProgreso de cada área
        //    Laravel invocará automáticamente el getPorcentajeProgresoAttribute()
        $sumaPorcentajes = $this->areas->sum(function(Area $area) {
            return $area->porcentajeProgreso;
        });

        // 3. Calculamos el promedio dividiendo por la cantidad de áreas
        $cantidadAreas = $this->areas->count();
        $promedio = $sumaPorcentajes / $cantidadAreas;

        // 4. Redondeamos a 2 decimales
        return round($promedio, 2);
    }


    public function nro_usuarios()
    {
        return DB::table('empresas as e')
            ->join('areas as a', 'a.empresa_id','=', 'e.id')
            ->join('equipos as eq', 'eq.area_id', '=', 'a.id')
            ->join('miembros_equipo as et', 'et.equipo_id', '=', 'eq.id')
            ->join('trabajadores as t', 't.id', '=', 'et.trabajador_id')
            // Si quieres contar cuentas de usuario distintas:
            ->where('e.id', $this->id)
            ->distinct('t.usuario_id')
            ->count('t.usuario_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Empresa {$this->nombre} fue {$eventName}");
    }

    /**
     * Obtiene información del plan desde el microservicio de suscripciones
     */
    public function getPlanInfo()
    {
        try {
            $client = new \GuzzleHttp\Client([
                'timeout' => 10,
                'verify' => false
            ]);
            
            $baseUrl = env('PAGOS_MICROSERVICE_URL', 'http://34.173.216.37:3000');
            
            // Obtener resumen de suscripción del usuario
            $response = $client->get($baseUrl . "/api/suscripciones/usuario/{$this->usuario_id}/resumen", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            
            if (isset($responseData['success']) && $responseData['success'] && isset($responseData['data'])) {
                $data = $responseData['data'];
                
                if ($data['tiene_suscripcion_activa'] && isset($data['suscripcion_activa'])) {
                    $suscripcion = $data['suscripcion_activa'];
                    
                    // Obtener información detallada del plan
                    $planInfo = $this->obtenerDetallesPlan($suscripcion['plan_id']);
                    
                    return [
                        'nombre' => $suscripcion['plan_nombre'] ?? 'Sin nombre',
                        'estado' => 'activa',
                        'color_estado' => 'bg-green-500',
                        'fecha_vencimiento' => $suscripcion['fecha_fin'] ?? null,
                        'renovacion_automatica' => $suscripcion['renovacion_automatica'] ?? false,
                        'dias_restantes' => $data['dias_restantes'] ?? 0,
                        'plan' => $planInfo,
                        'limites' => [
                            'trabajadores' => $planInfo['cant_usuarios'] ?? 1,
                        ],
                        'funciones_avanzadas' => $this->determinarFuncionesAvanzadas($planInfo),
                    ];
                } else {
                    return [
                        'nombre' => 'Sin Suscripción',
                        'estado' => 'Sin suscripción',
                        'color_estado' => 'bg-gray-500',
                        'fecha_vencimiento' => null,
                        'renovacion_automatica' => false,
                        'dias_restantes' => 0,
                        'plan' => null,
                        'limites' => [
                            'trabajadores' => 0,
                        ],
                        'funciones_avanzadas' => false,
                    ];
                }
            }
            
            // Si no hay datos válidos
            return [
                'nombre' => 'Sin plan',
                'estado' => 'Sin suscripción',
                'color_estado' => 'bg-gray-500',
                'fecha_vencimiento' => null,
                'renovacion_automatica' => false,
                'dias_restantes' => 0,
                'plan' => null,
                'limites' => ['trabajadores' => 0, 'equipos' => 0, 'areas' => 0],
                'funciones_avanzadas' => false,
            ];
            
        } catch (\Exception $e) {
            Log::error("Error obteniendo plan de empresa {$this->id}: " . $e->getMessage());
            
            return [
                'nombre' => 'Error de consulta',
                'estado' => 'Error',
                'color_estado' => 'bg-red-500',
                'fecha_vencimiento' => null,
                'renovacion_automatica' => false,
                'dias_restantes' => 0,
                'plan' => null,
                'limites' => ['trabajadores' => 0, 'equipos' => 0, 'areas' => 0],
                'funciones_avanzadas' => false,
            ];
        }
    }

    /**
     * Obtiene los detalles completos del plan desde la API
     */
    private function obtenerDetallesPlan($planId)
    {
        if (!$planId) {
            return null;
        }

        try {
            $client = new \GuzzleHttp\Client([
                'timeout' => 10,
                'verify' => false
            ]);
            
            $baseUrl = env('PAGOS_MICROSERVICE_URL', 'http://34.173.216.37:3000');
            
            $response = $client->get($baseUrl . "/api/planes/{$planId}", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            
            if (isset($responseData['success']) && $responseData['success'] && isset($responseData['data'])) {
                return $responseData['data'];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error("Error obteniendo detalles del plan {$planId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Determina si el plan tiene funciones avanzadas
     */
    private function determinarFuncionesAvanzadas($planInfo)
    {
        if (!$planInfo) return false;

        $nombrePlan = strtolower($planInfo['nombre'] ?? '');
        $cantUsuarios = $planInfo['cant_usuarios'] ?? 1;
        $precio = $planInfo['precio'] ?? 0;

        // Si es ilimitado o tiene más de 5 usuarios, asumimos funciones avanzadas
        if ($cantUsuarios == -1 || $cantUsuarios > 5) {
            return true;
        }

        // Si el precio es mayor a $50, asumimos funciones avanzadas
        if ($precio > 50) {
            return true;
        }

        // Detectar por nombre del plan
        $planesAvanzados = ['premium', 'business', 'professional', 'enterprise', 'pro', 'empresarial'];
        foreach ($planesAvanzados as $planAvanzado) {
            if (strpos($nombrePlan, $planAvanzado) !== false) {
                return true;
            }
        }

        return false;
    }
}
