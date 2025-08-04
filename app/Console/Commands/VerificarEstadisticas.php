<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empresa;
use App\Models\Trabajador;
use App\Models\Equipo;
use App\Models\MiembroEquipo;

class VerificarEstadisticas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'estadisticas:verificar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar datos de estadísticas y distribución de roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener la empresa creada
        $empresa = Empresa::first();
        $this->info("=== ANÁLISIS DE DATOS DE LA EMPRESA ===");
        $this->info("Empresa: {$empresa->nombre} (ID: {$empresa->id})");
        $this->newLine();

        // Total de trabajadores
        $totalTrabajadores = Trabajador::where('empresa_id', $empresa->id)->count();
        $this->info("📊 TOTAL TRABAJADORES: {$totalTrabajadores}");
        $this->newLine();

        // Coordinadores
        $coordinadoresIds = Equipo::whereNotNull('coordinador_id')
            ->whereHas('area', function($query) use ($empresa) {
                $query->where('empresa_id', $empresa->id);
            })
            ->pluck('coordinador_id')
            ->unique();

        $this->info("👨‍💼 COORDINADORES: {$coordinadoresIds->count()}");
        $this->info("IDs: " . $coordinadoresIds->implode(', '));
        $this->newLine();

        // Miembros de equipos
        $miembrosIds = MiembroEquipo::whereHas('equipo.area', function($query) use ($empresa) {
                $query->where('empresa_id', $empresa->id);
            })
            ->pluck('trabajador_id')
            ->unique();

        $this->info("👥 MIEMBROS DE EQUIPOS: {$miembrosIds->count()}");
        $this->info("IDs: " . $miembrosIds->implode(', '));
        $this->newLine();

        // Sin asignar (trabajadores que no son ni coordinadores ni miembros)
        $todosConRol = $coordinadoresIds->merge($miembrosIds)->unique();
        $trabajadoresEmpresa = Trabajador::where('empresa_id', $empresa->id)->pluck('id');
        $sinAsignar = $trabajadoresEmpresa->diff($todosConRol);

        $this->info("❓ SIN ASIGNAR: {$sinAsignar->count()}");
        $this->info("IDs: " . $sinAsignar->implode(', '));
        $this->newLine();

        // Verificar roles únicos (lógica del controlador)
        $coordinadores = count($coordinadoresIds);
        $soloMiembros = count(array_diff($miembrosIds->toArray(), $coordinadoresIds->toArray()));
        $sinAsignarFinal = count(array_diff($trabajadoresEmpresa->toArray(), $todosConRol->toArray()));

        $this->info("=== DISTRIBUCIÓN FINAL (LÓGICA DEL CONTROLADOR) ===");
        $this->info("Coordinadores: {$coordinadores}");
        $this->info("Solo Miembros: {$soloMiembros}");
        $this->info("Sin Asignar: {$sinAsignarFinal}");
        $this->info("TOTAL: " . ($coordinadores + $soloMiembros + $sinAsignarFinal));
        $this->newLine();

        // Porcentajes
        if ($totalTrabajadores > 0) {
            $this->info("=== PORCENTAJES ===");
            $this->info("Coordinadores: " . round(($coordinadores / $totalTrabajadores) * 100) . "%");
            $this->info("Solo Miembros: " . round(($soloMiembros / $totalTrabajadores) * 100) . "%");
            $this->info("Sin Asignar: " . round(($sinAsignarFinal / $totalTrabajadores) * 100) . "%");
            $this->info("TOTAL: " . round((($coordinadores + $soloMiembros + $sinAsignarFinal) / $totalTrabajadores) * 100) . "%");
            $this->newLine();
        }

        // Detalle de equipos
        $this->info("=== DETALLE DE EQUIPOS ===");
        $equipos = Equipo::whereHas('area', function($query) use ($empresa) {
            $query->where('empresa_id', $empresa->id);
        })->with(['area', 'coordinador', 'miembros'])->get();

        foreach ($equipos as $equipo) {
            $this->info("Equipo: {$equipo->nombre} (Área: {$equipo->area->nombre})");
            $this->info("  Coordinador: ID {$equipo->coordinador_id} - {$equipo->coordinador->nombres}");
            $this->info("  Miembros: {$equipo->miembros->count()}");
            foreach ($equipo->miembros as $miembro) {
                $this->info("    - ID {$miembro->trabajador_id}: {$miembro->trabajador->nombres}");
            }
            $this->newLine();
        }
    }
}
