<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class LimpiarAuditoriaAntigua extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auditoria:limpiar {--dias=90 : Número de días a mantener}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia registros de auditoría antiguos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dias = (int) $this->option('dias');
        
        $this->info("Limpiando registros de auditoría anteriores a {$dias} días...");
        
        $fechaLimite = Carbon::now()->subDays($dias);
        
        $count = Activity::where('created_at', '<', $fechaLimite)->count();
        
        if ($count === 0) {
            $this->info('No hay registros antiguos para eliminar.');
            return 0;
        }
        
        if ($this->confirm("Se eliminarán {$count} registros. ¿Continuar?")) {
            $deleted = Activity::where('created_at', '<', $fechaLimite)->delete();
            $this->info("Se eliminaron {$deleted} registros de auditoría.");
        } else {
            $this->info('Operación cancelada.');
        }
        
        return 0;
    }
}
