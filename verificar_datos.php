<?php

use App\Models\Empresa;
use App\Models\Trabajador;
use App\Models\Equipo;
use App\Models\MiembroEquipo;

// Obtener la empresa creada
$empresa = Empresa::first();
echo "=== ANÁLISIS DE DATOS DE LA EMPRESA ===\n";
echo "Empresa: {$empresa->nombre} (ID: {$empresa->id})\n\n";

// Total de trabajadores
$totalTrabajadores = Trabajador::where('empresa_id', $empresa->id)->count();
echo "📊 TOTAL TRABAJADORES: {$totalTrabajadores}\n\n";

// Coordinadores
$coordinadoresIds = Equipo::whereNotNull('coordinador_id')
    ->whereHas('area', function($query) use ($empresa) {
        $query->where('empresa_id', $empresa->id);
    })
    ->pluck('coordinador_id')
    ->unique();

echo "👨‍💼 COORDINADORES: {$coordinadoresIds->count()}\n";
echo "IDs: " . $coordinadoresIds->implode(', ') . "\n\n";

// Miembros de equipos
$miembrosIds = MiembroEquipo::whereHas('equipo.area', function($query) use ($empresa) {
        $query->where('empresa_id', $empresa->id);
    })
    ->pluck('trabajador_id')
    ->unique();

echo "👥 MIEMBROS DE EQUIPOS: {$miembrosIds->count()}\n";
echo "IDs: " . $miembrosIds->implode(', ') . "\n\n";

// Sin asignar (trabajadores que no son ni coordinadores ni miembros)
$todosConRol = $coordinadoresIds->merge($miembrosIds)->unique();
$trabajadoresEmpresa = Trabajador::where('empresa_id', $empresa->id)->pluck('id');
$sinAsignar = $trabajadoresEmpresa->diff($todosConRol);

echo "❓ SIN ASIGNAR: {$sinAsignar->count()}\n";
echo "IDs: " . $sinAsignar->implode(', ') . "\n\n";

// Verificar roles únicos (lógica del controlador)
$coordinadores = count($coordinadoresIds);
$soloMiembros = count(array_diff($miembrosIds->toArray(), $coordinadoresIds->toArray()));
$sinAsignarFinal = count(array_diff($trabajadoresEmpresa->toArray(), $todosConRol->toArray()));

echo "=== DISTRIBUCIÓN FINAL (LÓGICA DEL CONTROLADOR) ===\n";
echo "Coordinadores: {$coordinadores}\n";
echo "Solo Miembros: {$soloMiembros}\n";
echo "Sin Asignar: {$sinAsignarFinal}\n";
echo "TOTAL: " . ($coordinadores + $soloMiembros + $sinAsignarFinal) . "\n\n";

// Porcentajes
if ($totalTrabajadores > 0) {
    echo "=== PORCENTAJES ===\n";
    echo "Coordinadores: " . round(($coordinadores / $totalTrabajadores) * 100) . "%\n";
    echo "Solo Miembros: " . round(($soloMiembros / $totalTrabajadores) * 100) . "%\n";
    echo "Sin Asignar: " . round(($sinAsignarFinal / $totalTrabajadores) * 100) . "%\n";
    echo "TOTAL: " . round((($coordinadores + $soloMiembros + $sinAsignarFinal) / $totalTrabajadores) * 100) . "%\n\n";
}

// Detalle de equipos
echo "=== DETALLE DE EQUIPOS ===\n";
$equipos = Equipo::whereHas('area', function($query) use ($empresa) {
    $query->where('empresa_id', $empresa->id);
})->with(['area', 'coordinador', 'miembros'])->get();

foreach ($equipos as $equipo) {
    echo "Equipo: {$equipo->nombre} (Área: {$equipo->area->nombre})\n";
    echo "  Coordinador: ID {$equipo->coordinador_id} - {$equipo->coordinador->nombres}\n";
    echo "  Miembros: {$equipo->miembros->count()}\n";
    foreach ($equipo->miembros as $miembro) {
        echo "    - ID {$miembro->trabajador_id}: {$miembro->trabajador->nombres}\n";
    }
    echo "\n";
}
