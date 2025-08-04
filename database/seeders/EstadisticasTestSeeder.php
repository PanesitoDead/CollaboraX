<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;
use App\Models\Equipo;
use App\Models\Meta;
use App\Models\Tarea;
use App\Models\Reunion;
use App\Models\Estado;
use App\Models\Trabajador;
use App\Models\MiembroEquipo;
use App\Models\Empresa;
use App\Models\Modalidad;
use Carbon\Carbon;

class EstadisticasTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario admin para la empresa
        $usuarioAdmin = \App\Models\Usuario::firstOrCreate([
            'correo' => 'admin@empresademo.com'
        ], [
            'correo_personal' => 'admin@empresademo.com',
            'clave' => bcrypt('password123'),
            'clave_mostrar' => 'password123',
            'rol_id' => 1, // Asumiendo que 1 es admin
            'activo' => true,
            'en_linea' => false,
        ]);

        // Crear empresa asociada al usuario admin
        $empresa = Empresa::firstOrCreate([
            'usuario_id' => $usuarioAdmin->id
        ], [
            'nombre' => 'Empresa Demo',
            'descripcion' => 'Empresa de demostración para estadísticas',
            'ruc' => '12345678901',
            'telefono' => '123456789'
        ]);

        // Crear estados si no existen
        $estados = [
            ['nombre' => 'Pendiente', 'descripcion' => 'Tarea o meta pendiente'],
            ['nombre' => 'En Progreso', 'descripcion' => 'Tarea o meta en progreso'],
            ['nombre' => 'Completo', 'descripcion' => 'Tarea o meta completada'],
            ['nombre' => 'Cancelado', 'descripcion' => 'Tarea o meta cancelada'],
        ];

        foreach ($estados as $estadoData) {
            Estado::firstOrCreate(['nombre' => $estadoData['nombre']], $estadoData);
        }

        // Crear modalidades si no existen
        $modalidades = [
            ['nombre' => 'Presencial', 'descripcion' => 'Reunión presencial'],
            ['nombre' => 'Virtual', 'descripcion' => 'Reunión virtual'],
            ['nombre' => 'Híbrida', 'descripcion' => 'Reunión híbrida'],
        ];

        foreach ($modalidades as $modalidadData) {
            Modalidad::firstOrCreate(['nombre' => $modalidadData['nombre']], $modalidadData);
        }

        // Crear trabajadores
        $trabajadores = [];
        for ($i = 1; $i <= 15; $i++) {
            // Crear usuario para cada trabajador
            $usuarioTrabajador = \App\Models\Usuario::firstOrCreate([
                'correo' => "trabajador{$i}@empresademo.com"
            ], [
                'correo_personal' => "trabajador{$i}@empresademo.com",
                'clave' => bcrypt('password123'),
                'clave_mostrar' => 'password123',
                'rol_id' => 2, // Asumiendo que 2 es trabajador
                'activo' => true,
                'en_linea' => false,
            ]);

            $trabajadores[] = Trabajador::firstOrCreate([
                'doc_identidad' => str_pad($i, 8, '1', STR_PAD_LEFT) // 8 dígitos: 11111111, 11111112, etc.
            ], [
                'usuario_id' => $usuarioTrabajador->id,
                'empresa_id' => $empresa->id,
                'nombres' => "Trabajador {$i}",
                'apellido_paterno' => "Apellido {$i}",
                'apellido_materno' => "Materno {$i}",
                'telefono' => str_pad($i, 8, '9', STR_PAD_LEFT),
                'fecha_nacimiento' => Carbon::now()->subYears(25 + $i)->format('Y-m-d'),
            ]);
        }

        // Crear áreas
        $areas = [
            ['nombre' => 'Ventas', 'descripcion' => 'Área de ventas'],
            ['nombre' => 'Marketing', 'descripcion' => 'Área de marketing'],
            ['nombre' => 'Desarrollo', 'descripcion' => 'Área de desarrollo'],
            ['nombre' => 'Recursos Humanos', 'descripcion' => 'Área de RRHH'],
        ];

        $areaIndex = 0;
        foreach ($areas as $areaData) {
            $area = Area::firstOrCreate([
                'nombre' => $areaData['nombre']
            ], [
                'empresa_id' => $empresa->id,
                'descripcion' => $areaData['descripcion'],
                'codigo' => strtoupper(substr($areaData['nombre'], 0, 3)),
                'color' => '#' . substr(md5($areaData['nombre']), 0, 6),
                'activo' => true,
                'fecha_creacion' => Carbon::now(),
            ]);

            // Crear equipos para cada área (2 equipos por área = 8 equipos total)
            for ($j = 1; $j <= 2; $j++) {
                // Calcular índice del coordinador de manera segura
                $coordinadorIndex = ($areaIndex * 2) + ($j - 1); // 0, 1, 2, 3, 4, 5, 6, 7
                $coordinador = $trabajadores[$coordinadorIndex];
                
                $equipo = Equipo::firstOrCreate([
                    'area_id' => $area->id,
                    'nombre' => "Equipo {$area->nombre} {$j}"
                ], [
                    'coordinador_id' => $coordinador->id,
                    'descripcion' => "Equipo de {$area->nombre} número {$j}",
                    'fecha_creacion' => Carbon::now(),
                ]);

                // Agregar miembros al equipo (empezar después de los coordinadores)
                $startMemberIndex = 8; // Después de los 8 coordinadores
                for ($k = 0; $k < 2; $k++) { // 2 miembros por equipo
                    $trabajadorIndex = $startMemberIndex + ($areaIndex * 4) + (($j - 1) * 2) + $k;
                    if (isset($trabajadores[$trabajadorIndex])) {
                        MiembroEquipo::firstOrCreate([
                            'equipo_id' => $equipo->id,
                            'trabajador_id' => $trabajadores[$trabajadorIndex]->id,
                        ], [
                            'fecha_union' => Carbon::now()->subDays(rand(30, 365)),
                            'activo' => true,
                        ]);
                    }
                }

                // Crear metas para cada equipo
                $estadosIds = Estado::pluck('id')->toArray();
                for ($m = 1; $m <= rand(3, 6); $m++) {
                    $meta = Meta::firstOrCreate([
                        'equipo_id' => $equipo->id,
                        'nombre' => "Meta {$area->nombre} {$j}.{$m}"
                    ], [
                        'estado_id' => $estadosIds[array_rand($estadosIds)],
                        'descripcion' => "Descripción de la meta {$m} del equipo {$equipo->nombre}",
                        'fecha_creacion' => Carbon::now()->subDays(rand(60, 120)),
                        'fecha_entrega' => Carbon::now()->addDays(rand(30, 90)),
                    ]);

                    // Crear tareas para cada meta
                    for ($t = 1; $t <= rand(2, 5); $t++) {
                        Tarea::firstOrCreate([
                            'meta_id' => $meta->id,
                            'nombre' => "Tarea {$m}.{$t}"
                        ], [
                            'estado_id' => $estadosIds[array_rand($estadosIds)],
                            'descripcion' => "Descripción de la tarea {$t} de la meta {$meta->nombre}",
                            'fecha_creacion' => Carbon::now()->subDays(rand(30, 90)),
                            'fecha_entrega' => Carbon::now()->addDays(rand(15, 60)),
                        ]);
                    }
                }

                // Crear reuniones para cada equipo
                $modalidadesIds = Modalidad::pluck('id')->toArray();
                for ($r = 1; $r <= rand(5, 10); $r++) {
                    Reunion::firstOrCreate([
                        'equipo_id' => $equipo->id,
                        'asunto' => "Reunión {$equipo->nombre} #{$r}"
                    ], [
                        'fecha' => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d'),
                        'hora' => Carbon::createFromTime(rand(8, 17), [0, 30][rand(0, 1)])->format('H:i:s'),
                        'duracion' => [60, 90, 120][rand(0, 2)],
                        'descripcion' => "Reunión semanal del equipo {$equipo->nombre}",
                        'modalidad_id' => $modalidadesIds[array_rand($modalidadesIds)],
                        'estado' => ['programada', 'realizada', 'cancelada'][rand(0, 2)],
                        'meeting_id' => 'MEET' . uniqid(),
                    ]);
                }
            }
            $areaIndex++;
        }

        $this->command->info('Datos de prueba para estadísticas creados exitosamente.');
    }
}
