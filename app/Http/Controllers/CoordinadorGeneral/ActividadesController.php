<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActividadesController extends Controller
{
    public function index()
    {
        return view('coordinador-general.actividades.index');
    }

    public function getActividades()
    {
        $actividades = [
            // Pendientes (6 actividades)
            [
                'id' => 1,
                'titulo' => 'Implementar autenticación',
                'descripcion' => 'Desarrollar sistema de login y registro de usuarios',
                'equipo' => 'Equipo Desarrollo',
                'prioridad' => 'Alta',
                'fechaLimite' => '2024-02-15',
                'asignadoA' => 'Carlos Ruiz',
                'estado' => 'pendiente'
            ],
            [
                'id' => 2,
                'titulo' => 'Diseñar landing page',
                'descripcion' => 'Crear diseño para página principal del sitio web',
                'equipo' => 'Equipo Marketing',
                'prioridad' => 'Media',
                'fechaLimite' => '2024-02-20',
                'asignadoA' => 'Ana García',
                'estado' => 'pendiente'
            ],
            [
                'id' => 3,
                'titulo' => 'Configurar base de datos',
                'descripcion' => 'Establecer estructura de base de datos principal',
                'equipo' => 'Equipo Desarrollo',
                'prioridad' => 'Alta',
                'fechaLimite' => '2024-02-10',
                'asignadoA' => 'Miguel Torres',
                'estado' => 'pendiente'
            ],
            [
                'id' => 4,
                'titulo' => 'Análisis de mercado',
                'descripcion' => 'Investigar competencia y tendencias del mercado',
                'equipo' => 'Equipo Ventas',
                'prioridad' => 'Media',
                'fechaLimite' => '2024-02-25',
                'asignadoA' => 'Laura Mendez',
                'estado' => 'pendiente'
            ],
            [
                'id' => 5,
                'titulo' => 'Preparar presentación',
                'descripcion' => 'Crear slides para reunión con cliente importante',
                'equipo' => 'Equipo Ventas',
                'prioridad' => 'Alta',
                'fechaLimite' => '2024-02-12',
                'asignadoA' => 'Roberto Silva',
                'estado' => 'pendiente'
            ],
            [
                'id' => 6,
                'titulo' => 'Documentar API',
                'descripcion' => 'Crear documentación técnica completa de la API',
                'equipo' => 'Equipo IT',
                'prioridad' => 'Baja',
                'fechaLimite' => '2024-03-01',
                'asignadoA' => 'Elena Vargas',
                'estado' => 'pendiente'
            ],
            
            // En Proceso (6 actividades)
            [
                'id' => 7,
                'titulo' => 'Desarrollar dashboard',
                'descripcion' => 'Panel de control administrativo para gestión',
                'equipo' => 'Equipo Desarrollo',
                'prioridad' => 'Alta',
                'fechaLimite' => '2024-02-18',
                'asignadoA' => 'Pedro López',
                'estado' => 'en-proceso'
            ],
            [
                'id' => 8,
                'titulo' => 'Campaña redes sociales',
                'descripcion' => 'Estrategia de marketing digital en redes sociales',
                'equipo' => 'Equipo Marketing',
                'prioridad' => 'Media',
                'fechaLimite' => '2024-02-22',
                'asignadoA' => 'Sofia Herrera',
                'estado' => 'en-proceso'
            ],
            [
                'id' => 9,
                'titulo' => 'Optimizar rendimiento',
                'descripcion' => 'Mejorar velocidad de carga de la aplicación',
                'equipo' => 'Equipo Desarrollo',
                'prioridad' => 'Media',
                'fechaLimite' => '2024-02-28',
                'asignadoA' => 'Diego Morales',
                'estado' => 'en-proceso'
            ],
            [
                'id' => 10,
                'titulo' => 'Seguimiento clientes',
                'descripcion' => 'Contactar y dar seguimiento a leads potenciales',
                'equipo' => 'Equipo Ventas',
                'prioridad' => 'Alta',
                'fechaLimite' => '2024-02-16',
                'asignadoA' => 'Carmen Jiménez',
                'estado' => 'en-proceso'
            ],
            [
                'id' => 11,
                'titulo' => 'Testing aplicación',
                'descripcion' => 'Pruebas de funcionalidad y usabilidad',
                'equipo' => 'Equipo Desarrollo',
                'prioridad' => 'Alta',
                'fechaLimite' => '2024-02-20',
                'asignadoA' => 'Andrés Castro',
                'estado' => 'en-proceso'
            ],
            [
                'id' => 12,
                'titulo' => 'Manual usuario',
                'descripcion' => 'Guía de uso completa para clientes finales',
                'equipo' => 'Equipo IT',
                'prioridad' => 'Media',
                'fechaLimite' => '2024-02-26',
                'asignadoA' => 'Valeria Ramos',
                'estado' => 'en-proceso'
            ],
            
            // Completadas (6 actividades)
            [
                'id' => 13,
                'titulo' => 'Configurar servidor',
                'descripcion' => 'Setup inicial del hosting y configuración',
                'equipo' => 'Equipo Desarrollo',
                'prioridad' => 'Alta',
                'fechaLimite' => '2024-02-05',
                'asignadoA' => 'Fernando Díaz',
                'estado' => 'completada'
            ],
            [
                'id' => 14,
                'titulo' => 'Crear logo empresa',
                'descripcion' => 'Diseño de identidad visual corporativa',
                'equipo' => 'Equipo Marketing',
                'prioridad' => 'Media',
                'fechaLimite' => '2024-02-08',
                'asignadoA' => 'Gabriela Soto',
                'estado' => 'completada'
            ],
            [
                'id' => 15,
                'titulo' => 'Definir arquitectura',
                'descripcion' => 'Estructura técnica del sistema completo',
                'equipo' => 'Equipo Desarrollo',
                'prioridad' => 'Alta',
                'fechaLimite' => '2024-02-03',
                'asignadoA' => 'Ricardo Peña',
                'estado' => 'completada'
            ],
            [
                'id' => 16,
                'titulo' => 'Estrategia contenido',
                'descripcion' => 'Plan de publicaciones y contenido digital',
                'equipo' => 'Equipo Marketing',
                'prioridad' => 'Media',
                'fechaLimite' => '2024-02-07',
                'asignadoA' => 'Mónica Reyes',
                'estado' => 'completada'
            ],
            [
                'id' => 17,
                'titulo' => 'Contactar proveedores',
                'descripcion' => 'Negociar precios y términos comerciales',
                'equipo' => 'Equipo Ventas',
                'prioridad' => 'Baja',
                'fechaLimite' => '2024-02-06',
                'asignadoA' => 'Javier Ortiz',
                'estado' => 'completada'
            ],
            [
                'id' => 18,
                'titulo' => 'Análisis competencia',
                'descripcion' => 'Estudio detallado de mercado y competidores',
                'equipo' => 'Equipo Marketing',
                'prioridad' => 'Media',
                'fechaLimite' => '2024-02-09',
                'asignadoA' => 'Patricia Luna',
                'estado' => 'completada'
            ],
            
            // Retrasadas (6 actividades)
            [
                'id' => 19,
                'titulo' => 'Integrar pasarela pago',
                'descripcion' => 'Conectar sistema de pagos en línea',
                'equipo' => 'Equipo Desarrollo',
                'prioridad' => 'Alta',
                'fechaLimite' => '2024-01-30',
                'asignadoA' => 'Alejandro Vega',
                'estado' => 'retrasada'
            ],
            [
                'id' => 20,
                'titulo' => 'Auditoría seguridad',
                'descripcion' => 'Revisar vulnerabilidades del sistema',
                'equipo' => 'Equipo IT',
                'prioridad' => 'Alta',
                'fechaLimite' => '2024-01-28',
                'asignadoA' => 'Cristina Flores',
                'estado' => 'retrasada'
            ],
            [
                'id' => 21,
                'titulo' => 'Capacitar equipo ventas',
                'descripcion' => 'Training sobre nuevo producto y procesos',
                'equipo' => 'Equipo Ventas',
                'prioridad' => 'Media',
                'fechaLimite' => '2024-01-25',
                'asignadoA' => 'Raúl Guerrero',
                'estado' => 'retrasada'
            ],
            [
                'id' => 22,
                'titulo' => 'Backup automático',
                'descripcion' => 'Sistema de respaldos automatizado',
                'equipo' => 'Equipo IT',
                'prioridad' => 'Alta',
                'fechaLimite' => '2024-01-20',
                'asignadoA' => 'Beatriz Campos',
                'estado' => 'retrasada'
            ],
            [
                'id' => 23,
                'titulo' => 'Optimizar SEO',
                'descripcion' => 'Mejorar posicionamiento en buscadores',
                'equipo' => 'Equipo Marketing',
                'prioridad' => 'Media',
                'fechaLimite' => '2024-01-31',
                'asignadoA' => 'Sergio Medina',
                'estado' => 'retrasada'
            ],
            [
                'id' => 24,
                'titulo' => 'Monitoreo sistema',
                'descripcion' => 'Implementar alertas y métricas de rendimiento',
                'equipo' => 'Equipo IT',
                'prioridad' => 'Alta',
                'fechaLimite' => '2024-01-22',
                'asignadoA' => 'Natalia Cruz',
                'estado' => 'retrasada'
            ]
        ];

        return response()->json($actividades);
    }

    public function getEquipos()
    {
        $equipos = [
            'Equipo Desarrollo',
            'Equipo Marketing',
            'Equipo Ventas',
            'Equipo Operaciones',
            'Equipo IT',
            'Equipo RRHH'
        ];

        return response()->json($equipos);
    }

   
}
