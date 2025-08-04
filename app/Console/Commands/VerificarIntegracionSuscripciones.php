<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SuscripcionService;
use App\Models\Usuario;

class VerificarIntegracionSuscripciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suscripciones:verificar {usuarioId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar la integración con el microservicio de suscripciones';

    protected $suscripcionService;

    public function __construct(SuscripcionService $suscripcionService)
    {
        parent::__construct();
        $this->suscripcionService = $suscripcionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usuarioId = $this->argument('usuarioId');
        
        if (!$usuarioId) {
            // Buscar el primer usuario admin
            $usuario = Usuario::where('rol_id', 1)->first();
            if (!$usuario) {
                $this->error('No se encontró ningún usuario admin en la base de datos.');
                return 1;
            }
            $usuarioId = $usuario->id;
            $this->info("Usando usuario admin: {$usuario->correo} (ID: {$usuarioId})");
        }

        $this->info("=== VERIFICACIÓN DE INTEGRACIÓN CON MICROSERVICIO ===");
        $this->newLine();

        // Verificar configuración
        $this->info("📋 CONFIGURACIÓN:");
        $baseUrl = config('services.suscripciones.url');
        $apiKey = config('services.suscripciones.api_key');
        $timeout = config('services.suscripciones.timeout');
        
        $this->line("URL Base: {$baseUrl}");
        $this->line("API Key: " . (strlen($apiKey) > 10 ? substr($apiKey, 0, 10) . '...' : $apiKey));
        $this->line("Timeout: {$timeout}s");
        $this->newLine();

        // Probar conexión
        $this->info("🔌 PROBANDO CONEXIÓN...");
        try {
            $suscripcionActual = $this->suscripcionService->obtenerSuscripcionActual($usuarioId);
            
            if ($suscripcionActual) {
                $this->info("✅ Conexión exitosa!");
                $this->newLine();
                
                $this->info("📦 INFORMACIÓN DE SUSCRIPCIÓN:");
                $this->line("Estado: " . ($suscripcionActual['estado'] ?? 'Desconocido'));
                
                if (isset($suscripcionActual['plan'])) {
                    $plan = $suscripcionActual['plan'];
                    $this->line("Plan: " . ($plan['nombre'] ?? 'Desconocido'));
                    $this->line("Tipo: " . ($plan['tipo'] ?? 'Desconocido'));
                    $this->line("Precio: $" . ($plan['precio'] ?? '0'));
                }
                
                if (isset($suscripcionActual['fecha_inicio'])) {
                    $this->line("Fecha inicio: " . $suscripcionActual['fecha_inicio']);
                }
                
                if (isset($suscripcionActual['fecha_fin'])) {
                    $this->line("Fecha fin: " . $suscripcionActual['fecha_fin']);
                }
                
                $this->line("Renovación automática: " . ($suscripcionActual['renovacion_automatica'] ? 'Sí' : 'No'));
                $this->newLine();
                
                // Probar procesamiento de límites
                $this->info("🎯 LÍMITES PROCESADOS:");
                $limites = $this->obtenerLimitesPorTipoPlan($suscripcionActual['plan'] ?? null);
                
                foreach ($limites as $tipo => $limite) {
                    $valor = $limite == -1 ? 'Ilimitado' : $limite;
                    $this->line(ucfirst(str_replace('_', ' ', $tipo)) . ": {$valor}");
                }
                
            } else {
                $this->warn("⚠️  No se obtuvo información de suscripción.");
                $this->line("Esto puede ser normal si el usuario no tiene suscripción activa.");
                $this->newLine();
                
                $this->info("🆓 USANDO PLAN GRATUITO POR DEFECTO:");
                $limitesGratuito = $this->obtenerLimitesPorTipoPlan(null);
                foreach ($limitesGratuito as $tipo => $limite) {
                    $valor = $limite == -1 ? 'Ilimitado' : $limite;
                    $this->line(ucfirst(str_replace('_', ' ', $tipo)) . ": {$valor}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error en la conexión:");
            $this->error($e->getMessage());
            $this->newLine();
            
            $this->warn("🔧 RECOMENDACIONES:");
            $this->line("1. Verificar que el microservicio esté funcionando");
            $this->line("2. Comprobar las variables de entorno en .env");
            $this->line("3. Verificar conectividad de red");
            $this->line("4. Revisar los logs de Laravel para más detalles");
            
            return 1;
        }

        $this->newLine();
        $this->info("✅ Verificación completada!");
        
        return 0;
    }

    private function obtenerLimitesPorTipoPlan($plan)
    {
        if (!$plan) {
            return [
                'trabajadores' => 5,
                'equipos' => 2,
                'areas' => 2,
                'metas_por_mes' => 10,
                'funciones_avanzadas' => false
            ];
        }

        $tipoPlan = strtolower($plan['tipo'] ?? $plan['nombre'] ?? '');

        return match($tipoPlan) {
            'standard', 'básico' => [
                'trabajadores' => 15,
                'equipos' => 5,
                'areas' => 3,
                'metas_por_mes' => 50,
                'funciones_avanzadas' => false
            ],
            'business', 'profesional' => [
                'trabajadores' => 50,
                'equipos' => 20,
                'areas' => 10,
                'metas_por_mes' => 200,
                'funciones_avanzadas' => true
            ],
            'enterprise', 'empresarial' => [
                'trabajadores' => -1,
                'equipos' => -1,
                'areas' => -1,
                'metas_por_mes' => -1,
                'funciones_avanzadas' => true
            ],
            default => [
                'trabajadores' => 5,
                'equipos' => 2,
                'areas' => 2,
                'metas_por_mes' => 10,
                'funciones_avanzadas' => false
            ]
        };
    }
}
