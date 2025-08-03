<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\Trabajador;
use App\Models\Rol;
use App\Models\Area;
use App\Models\Equipo;
use App\Models\Meta;
use App\Models\Tarea;
use App\Models\Estado;
use App\Models\Reunion;
use App\Models\Modalidad;

class AuditoriaTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Este seeder crea algunos registros de prueba para generar logs de auditoría
        
        $this->command->info('Creando datos de prueba para auditoría...');
        
        // Verificar si existe un rol básico
        $rol = Rol::first();
        if (!$rol) {
            $rol = Rol::create([
                'nombre' => 'Administrador',
                'descripcion' => 'Rol de administrador del sistema'
            ]);
        }
        
        // Crear un usuario de prueba
        $usuario = Usuario::create([
            'correo' => 'test.auditoria@collaborax.com',
            'correo_personal' => 'test.personal@gmail.com',
            'clave' => bcrypt('password123'),
            'clave_mostrar' => 'password123',
            'rol_id' => $rol->id,
            'activo' => true,
            'en_linea' => false,
            'cambio_clave' => false
        ]);
        
        $this->command->info('Usuario creado: ' . $usuario->correo);
        
        // Crear una empresa de prueba
        $empresa = Empresa::create([
            'usuario_id' => $usuario->id,
            'plan_servicio_id' => 1, // Asumiendo que existe
            'nombre' => 'Empresa de Prueba Auditoría',
            'descripcion' => 'Empresa creada para probar el sistema de auditoría',
            'ruc' => '20123456789',
            'telefono' => '987654321'
        ]);
        
        $this->command->info('Empresa creada: ' . $empresa->nombre);
        
        // Crear un trabajador
        $trabajador = Trabajador::create([
            'usuario_id' => $usuario->id,
            'empresa_id' => $empresa->id,
            'nombres' => 'Juan Carlos',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'García',
            'doc_identidad' => '12345678',
            'fecha_nacimiento' => '1990-01-15',
            'telefono' => '987654321'
        ]);
        
        $this->command->info('Trabajador creado: ' . $trabajador->nombres . ' ' . $trabajador->apellido_paterno);
        
        // Realizar algunas actualizaciones para generar logs de auditoría
        sleep(1); // Pequeña pausa para diferencias en timestamps
        
        $usuario->update([
            'correo_personal' => 'nuevo.email@gmail.com'
        ]);
        
        sleep(1);
        
        $empresa->update([
            'descripcion' => 'Descripción actualizada para probar auditoría',
            'telefono' => '999888777'
        ]);
        
        sleep(1);
        
        $trabajador->update([
            'telefono' => '966555444'
        ]);
        
        // Crear un área de prueba
        $area = Area::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Área de Desarrollo',
            'descripcion' => 'Área destinada al desarrollo de software',
            'codigo' => 'DEV',
            'color' => '#3498db',
            'activo' => true,
            'fecha_creacion' => now()
        ]);
        
        $this->command->info('Área creada: ' . $area->nombre);
        
        sleep(1);
        
        // Crear un equipo
        $equipo = Equipo::create([
            'coordinador_id' => $trabajador->id,
            'area_id' => $area->id,
            'nombre' => 'Equipo Frontend',
            'descripcion' => 'Equipo encargado del desarrollo frontend',
            'fecha_creacion' => now()
        ]);
        
        $this->command->info('Equipo creado: ' . $equipo->nombre);
        
        sleep(1);
        
        // Actualizar el área
        $area->update([
            'descripcion' => 'Área de desarrollo actualizada para auditoría',
            'color' => '#2ecc71'
        ]);
        
        sleep(1);
        
        // Actualizar el equipo
        $equipo->update([
            'descripcion' => 'Descripción del equipo actualizada'
        ]);
        
        $this->command->info('Se han realizado varias operaciones que generarán logs de auditoría.');
        $this->command->info('Modelos auditados: Usuario, Empresa, Trabajador, Área, Equipo');
        $this->command->info('Puedes ver los logs visitando /auditoria en tu aplicación.');
    }
}
