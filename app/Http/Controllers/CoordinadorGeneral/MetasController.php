<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MetasController extends Controller
{
    public function index()
    {
        // Datos de metas (simulados - en producción vendrían de la base de datos)
        $metas = [
            [
                'id' => 1,
                'titulo' => 'Aumentar ventas Q1',
                'descripcion' => 'Incrementar las ventas en un 25% durante el primer trimestre del año',
                'tipo' => 'Trimestral',
                'prioridad' => 'Alta',
                'responsable' => 'Equipo Ventas',
                'fecha_inicio' => '2024-01-01',
                'fecha_limite' => '2024-03-31',
                'estado' => 'En Progreso',
                'progreso' => 65,
                'equipos' => ['Equipo Ventas', 'Equipo Marketing']
            ],
            [
                'id' => 2,
                'titulo' => 'Implementar nuevo CRM',
                'descripcion' => 'Migrar todos los procesos de ventas al nuevo sistema CRM',
                'tipo' => 'Semestral',
                'prioridad' => 'Alta',
                'responsable' => 'Equipo IT',
                'fecha_inicio' => '2024-01-15',
                'fecha_limite' => '2024-06-30',
                'estado' => 'En Progreso',
                'progreso' => 40,
                'equipos' => ['Equipo IT', 'Equipo Ventas']
            ],
            [
                'id' => 3,
                'titulo' => 'Campaña de marketing digital',
                'descripcion' => 'Lanzar campaña integral en redes sociales y Google Ads',
                'tipo' => 'Mensual',
                'prioridad' => 'Media',
                'responsable' => 'Equipo Marketing',
                'fecha_inicio' => '2024-02-01',
                'fecha_limite' => '2024-02-29',
                'estado' => 'Completada',
                'progreso' => 100,
                'equipos' => ['Equipo Marketing']
            ],
            [
                'id' => 4,
                'titulo' => 'Optimización de procesos',
                'descripcion' => 'Reducir tiempos de entrega en un 30% mediante automatización',
                'tipo' => 'Semestral',
                'prioridad' => 'Media',
                'responsable' => 'Equipo Operaciones',
                'fecha_inicio' => '2024-01-10',
                'fecha_limite' => '2024-07-10',
                'estado' => 'En Progreso',
                'progreso' => 25,
                'equipos' => ['Equipo Operaciones', 'Equipo IT']
            ],
            [
                'id' => 5,
                'titulo' => 'Capacitación del personal',
                'descripcion' => 'Programa de formación continua para todos los empleados',
                'tipo' => 'Anual',
                'prioridad' => 'Baja',
                'responsable' => 'Equipo RRHH',
                'fecha_inicio' => '2024-01-01',
                'fecha_limite' => '2024-12-31',
                'estado' => 'Pendiente',
                'progreso' => 10,
                'equipos' => ['Equipo RRHH']
            ]
        ];

        // Equipos disponibles
        $equiposDisponibles = [
            'Equipo Desarrollo',
            'Equipo Marketing', 
            'Equipo Ventas',
            'Equipo Operaciones'
        ];

        return view('coordinador-general.metas.index', compact('metas', 'equiposDisponibles'));
    }

}
