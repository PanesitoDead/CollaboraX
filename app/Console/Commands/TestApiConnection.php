<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EstadisticasIngresosService;
use App\Models\Empresa;

class TestApiConnection extends Command
{
    protected $signature = 'test:api-connection {--empresa-id=1}';
    protected $description = 'Testear la conexión con la API de microservicios de pagos';

    public function handle()
    {
        $empresaId = $this->option('empresa-id');
        
        $this->info("🔍 Testeando conexión con API de pagos...");
        $this->info("📊 Empresa ID: {$empresaId}");
        $this->newLine();

        // Test 1: Información de plan de empresa
        $this->info("1️⃣ Probando getPlanInfo() de empresa...");
        try {
            $empresa = Empresa::find($empresaId);
            if (!$empresa) {
                $this->error("❌ Empresa no encontrada con ID: {$empresaId}");
                return 1;
            }

            $planInfo = $empresa->getPlanInfo();
            $this->info("✅ Plan info obtenido:");
            $this->line("   Nombre: " . ($planInfo['nombre'] ?? 'N/A'));
            $this->line("   Estado: " . ($planInfo['estado'] ?? 'N/A'));
            $this->line("   Usuarios permitidos: " . ($planInfo['limites']['trabajadores'] ?? 'N/A'));
            $this->line("   Funciones avanzadas: " . (($planInfo['funciones_avanzadas'] ?? false) ? 'Sí' : 'No'));
            
            // Mostrar información del plan real desde la API
            if (isset($planInfo['plan']) && $planInfo['plan']) {
                $this->newLine();
                $this->info("📋 Información del plan desde API:");
                $plan = $planInfo['plan'];
                $this->line("   Nombre: " . ($plan['nombre'] ?? 'N/A'));
                $this->line("   Precio: $" . number_format($plan['precio'] ?? 0, 2));
                $this->line("   Frecuencia: " . ($plan['frecuencia'] ?? 'N/A'));
                $this->line("   Cant. Usuarios: " . ($plan['cant_usuarios'] ?? 'N/A'));
                $this->line("   Descripción: " . ($plan['descripcion'] ?? 'N/A'));
                
                if (isset($plan['beneficios']) && is_array($plan['beneficios']) && !empty($plan['beneficios']) && $plan !== null) {
                    $this->line("   Beneficios: " . implode(', ', $plan['beneficios']));
                }
            }
        } catch (\Exception $e) {
            $this->error("❌ Error en getPlanInfo(): " . $e->getMessage());
        }
        
        $this->newLine();

        // Test 2: Estadísticas de ingresos
        $this->info("2️⃣ Probando EstadisticasIngresosService...");
        $estadisticasService = new EstadisticasIngresosService();
        
        try {
            $resumen = $estadisticasService->getResumenCompleto();
            $this->info("✅ Resumen completo obtenido:");
            
            if (isset($resumen['mes_actual']['resumen_mes_actual'])) {
                $mesActual = $resumen['mes_actual']['resumen_mes_actual'];
                $this->line("   Mes actual: " . ($mesActual['nombre_mes'] ?? 'N/A') . " " . ($mesActual['año'] ?? 'N/A'));
                $this->line("   Ingresos aprobados: $" . number_format($mesActual['ingresos_aprobados'] ?? 0, 2));
                $this->line("   Total pagos: " . ($mesActual['total_pagos'] ?? 0));
            }
            
            if (isset($resumen['por_planes']['resumen']['plan_mas_rentable'])) {
                $planRentable = $resumen['por_planes']['resumen']['plan_mas_rentable'];
                $this->line("   Plan más rentable: " . ($planRentable['plan_nombre'] ?? 'N/A'));
                $this->line("   Ingresos del plan: $" . number_format($planRentable['ingresos_totales'] ?? 0, 2));
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error en estadísticas de ingresos: " . $e->getMessage());
        }
        
        $this->newLine();

        // Test 3: URLs y configuración
        $this->info("3️⃣ Verificando configuración...");
        $baseUrl = env('PAGOS_MICROSERVICE_URL', 'http://34.173.216.37:3000');
        $this->line("   URL base configurada: {$baseUrl}");
        
        // Test simple de conectividad - probemos sin /api primero
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 5, 'verify' => false]);
            
            // Probar endpoint de salud (sin /api)
            $healthUrl = str_replace('/api', '', $baseUrl) . '/health';
            $this->line("   Probando URL: {$healthUrl}");
            $response = $client->get($healthUrl);
            $this->info("✅ Conectividad OK - Status: " . $response->getStatusCode());
            
        } catch (\Exception $e) {
            $this->error("❌ Error de conectividad: " . $e->getMessage());
            
            // Intentar con la URL alternativa
            try {
                $alternativeUrl = 'http://34.173.216.37:3000/health';
                $this->line("   Probando URL alternativa: {$alternativeUrl}");
                $response = $client->get($alternativeUrl);
                $this->info("✅ Conectividad OK con URL alternativa - Status: " . $response->getStatusCode());
            } catch (\Exception $e2) {
                $this->error("❌ Error también con URL alternativa: " . $e2->getMessage());
            }
        }

        $this->newLine();
        $this->info("🎉 Test completado!");
        
        return 0;
    }
}
