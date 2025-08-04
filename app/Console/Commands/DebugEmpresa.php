<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Empresa;
use Illuminate\Support\Facades\Log;

class DebugEmpresa extends Command
{
    protected $signature = 'debug:empresa {email}';
    protected $description = 'Debug empresa data for a specific user email';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Buscando usuario con email: {$email}");
        
        $usuario = Usuario::where('correo', $email)->first();
        
        if (!$usuario) {
            $this->error("Usuario no encontrado con email: {$email}");
            return;
        }
        
        $this->info("Usuario encontrado:");
        $this->info("ID: {$usuario->id}");
        $this->info("Email: {$usuario->correo}");
        $this->info("Email personal: {$usuario->correo_personal}");
        $this->info("Activo: " . ($usuario->activo ? 'SÍ' : 'NO'));
        
        $empresa = Empresa::where('usuario_id', $usuario->id)->first();
        
        if (!$empresa) {
            $this->error("No se encontró empresa para este usuario");
            return;
        }
        
        $this->info("Empresa encontrada:");
        $this->info("ID: {$empresa->id}");
        $this->info("Nombre: {$empresa->nombre}");
        $this->info("RUC: {$empresa->ruc}");
        $this->info("Teléfono: {$empresa->telefono}");
        $this->info("Descripción: {$empresa->descripcion}");
        
        $this->info("✅ Todo parece estar bien configurado");
    }
}
