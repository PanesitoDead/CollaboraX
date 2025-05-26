<?php

namespace App\Http\Controllers\colaborador;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvitacionController extends Controller
{
    public function index()
    {
        // Datos simulados - reemplazar con consultas reales a la base de datos
        $invitacionesPendientes = [
            [
                'id' => 1,
                'equipo' => 'Desarrollo Frontend',
                'coordinador' => 'Ana García',
                'coordinador_avatar' => '/placeholder-40x40.png',
                'fecha_invitacion' => '2024-01-15',
                'mensaje' => 'Te invitamos a unirte a nuestro equipo de desarrollo frontend. Creemos que tus habilidades serían una gran adición.',
                'tipo' => 'equipo',
                'urgencia' => 'alta'
            ],
            [
                'id' => 2,
                'equipo' => 'Marketing Digital',
                'coordinador' => 'Carlos López',
                'coordinador_avatar' => '/placeholder-40x40.png',
                'fecha_invitacion' => '2024-01-14',
                'mensaje' => 'Nos gustaría que formes parte de nuestro equipo de marketing digital para el próximo proyecto.',
                'tipo' => 'proyecto',
                'urgencia' => 'media'
            ],
            [
                'id' => 3,
                'equipo' => 'Análisis de Datos',
                'coordinador' => 'María Rodríguez',
                'coordinador_avatar' => '/placeholder-40x40.png',
                'fecha_invitacion' => '2024-01-13',
                'mensaje' => 'Tu experiencia en análisis sería muy valiosa para nuestro equipo. ¿Te interesa unirte?',
                'tipo' => 'equipo',
                'urgencia' => 'baja'
            ]
        ];

        $historialInvitaciones = [
            [
                'id' => 4,
                'equipo' => 'Diseño UX/UI',
                'coordinador' => 'Laura Martín',
                'coordinador_avatar' => '/placeholder-40x40.png',
                'fecha_invitacion' => '2024-01-10',
                'fecha_respuesta' => '2024-01-11',
                'estado' => 'aceptada',
                'mensaje' => 'Te invitamos a colaborar en el rediseño de nuestra plataforma principal.',
                'tipo' => 'proyecto',
                'motivo_rechazo' => null
            ],
            [
                'id' => 5,
                'equipo' => 'Backend Development',
                'coordinador' => 'Pedro Sánchez',
                'coordinador_avatar' => '/placeholder-40x40.png',
                'fecha_invitacion' => '2024-01-08',
                'fecha_respuesta' => '2024-01-09',
                'estado' => 'rechazada',
                'mensaje' => 'Buscamos un desarrollador backend para nuestro nuevo microservicio.',
                'tipo' => 'equipo',
                'motivo_rechazo' => 'Conflicto de horarios con proyecto actual'
            ],
            [
                'id' => 6,
                'equipo' => 'Quality Assurance',
                'coordinador' => 'Isabel Torres',
                'coordinador_avatar' => '/placeholder-40x40.png',
                'fecha_invitacion' => '2024-01-05',
                'fecha_respuesta' => '2024-01-06',
                'estado' => 'aceptada',
                'mensaje' => 'Necesitamos tu expertise en testing para asegurar la calidad del producto.',
                'tipo' => 'proyecto',
                'motivo_rechazo' => null
            ],
            [
                'id' => 7,
                'equipo' => 'DevOps',
                'coordinador' => 'Roberto Díaz',
                'coordinador_avatar' => '/placeholder-40x40.png',
                'fecha_invitacion' => '2024-01-03',
                'fecha_respuesta' => '2024-01-04',
                'estado' => 'rechazada',
                'mensaje' => 'Te invitamos a formar parte del equipo de infraestructura y despliegue.',
                'tipo' => 'equipo',
                'motivo_rechazo' => 'Ya comprometido con otro equipo'
            ]
        ];

        $estadisticas = [
            'total_pendientes' => count($invitacionesPendientes),
            'total_aceptadas' => count(array_filter($historialInvitaciones, fn($inv) => $inv['estado'] === 'aceptada')),
            'total_rechazadas' => count(array_filter($historialInvitaciones, fn($inv) => $inv['estado'] === 'rechazada')),
            'tasa_aceptacion' => count($historialInvitaciones) > 0 ? 
                round((count(array_filter($historialInvitaciones, fn($inv) => $inv['estado'] === 'aceptada')) / count($historialInvitaciones)) * 100) : 0
        ];

        return view('private.colaborador.invitaciones', compact(
            'invitacionesPendientes',
            'historialInvitaciones',
            'estadisticas'
        ));
    }

    public function aceptar(Request $request, $id): JsonResponse
    {
        // Validar la invitación
        $request->validate([
            'mensaje' => 'nullable|string|max:500'
        ]);

        // Aquí iría la lógica para aceptar la invitación en la base de datos
        // Por ahora simulamos la respuesta

        return response()->json([
            'success' => true,
            'message' => 'Invitación aceptada correctamente'
        ]);
    }

    public function rechazar(Request $request, $id): JsonResponse
    {
        // Validar el motivo del rechazo
        $request->validate([
            'motivo' => 'required|string|max:500'
        ]);

        // Aquí iría la lógica para rechazar la invitación en la base de datos
        // Por ahora simulamos la respuesta

        return response()->json([
            'success' => true,
            'message' => 'Invitación rechazada correctamente'
        ]);
    }

    public function verDetalles($id): JsonResponse
    {
        // Aquí iría la lógica para obtener los detalles completos de la invitación
        // Por ahora simulamos los datos

        $detalles = [
            'id' => $id,
            'equipo' => 'Desarrollo Frontend',
            'coordinador' => 'Ana García',
            'coordinador_avatar' => '/placeholder-40x40.png',
            'fecha_invitacion' => '2024-01-15',
            'mensaje' => 'Te invitamos a unirte a nuestro equipo de desarrollo frontend. Creemos que tus habilidades serían una gran adición.',
            'descripcion_equipo' => 'Equipo encargado del desarrollo de interfaces de usuario modernas y responsivas.',
            'responsabilidades' => [
                'Desarrollo de componentes React',
                'Implementación de diseños responsivos',
                'Optimización de rendimiento frontend',
                'Colaboración con el equipo de diseño'
            ],
            'requisitos' => [
                'Experiencia en React y TypeScript',
                'Conocimientos de Tailwind CSS',
                'Experiencia con Git y metodologías ágiles'
            ],
            'beneficios' => [
                'Horario flexible',
                'Trabajo remoto',
                'Capacitación continua',
                'Ambiente colaborativo'
            ],
            'tipo' => 'equipo',
            'urgencia' => 'alta',
            'fecha_limite' => '2024-01-20'
        ];

        return response()->json($detalles);
    }
}
