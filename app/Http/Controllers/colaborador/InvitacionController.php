<?php

namespace App\Http\Controllers\colaborador;

use App\Http\Controllers\Controller;
use App\Repositories\InvitacionRepositorio;
use App\Repositories\TrabajadorRepositorio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\Http\Controllers\CriterioTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\In;

class InvitacionController extends Controller
{
    use CriterioTrait;

    protected InvitacionRepositorio $invitacionRepositorio;

    protected TrabajadorRepositorio $trabajadorRepositorio;


    public function __construct(InvitacionRepositorio $invitacionRepositorio, TrabajadorRepositorio $trabajadorRepositorio)
    {
        $this->invitacionRepositorio = $invitacionRepositorio;
        $this->trabajadorRepositorio = $trabajadorRepositorio;
    }

    public function index(Request $request)
    {
        $usuario = Auth::user();
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
        $criterios = $this->obtenerCriterios($request);
        // Creamos el query builder para las invitaciones
        $query = $this->invitacionRepositorio->getModel()->newQuery();
        // Filtramos por el trabajador del usuario autenticado
        $query->where('trabajador_id', $trabajador->id);
        // Aplicamos los criterios de búsqueda
        $invitacionesPag = $this->invitacionRepositorio->obtenerPaginado($criterios, $query);
        $invitacionesParse = $invitacionesPag->getCollection()->map(function ($invitacion) {
            // Formateamos la fecha de invitación
            $invitacion->fecha_invitacion = Carbon::parse($invitacion->fecha_invitacion)->format('d/m/Y');
            $invitacion->fecha_expiracion = Carbon::parse($invitacion->fecha_expiracion)->format('d/m/Y');
            $invitacion->fecha_respuesta = $invitacion->fecha_respuesta ? Carbon::parse($invitacion->fecha_respuesta)->format('d/m/Y')
                : 'No disponible';
            // Si existe un equipo, extraemos su nombre
            if ($invitacion->equipo) {
                $invitacion->equipo_nombre = $invitacion->equipo->nombre;
                $invitacion->equipo_avatar = $invitacion->equipo->avatar ?? '/placeholder-40x40.png';
            } else {
                $invitacion->equipo_nombre = null;
                $invitacion->equipo_avatar = '/placeholder-40x40.png';
            }
            // Si existe un coordinador activo, extraemos nombre y correo
            if ($invitacion->equipo && $invitacion->equipo->trabajador) {
                $invitacion->coordinador_nombres = $invitacion->equipo->coordinador->nombres;
                $invitacion->coordinador_apellido_paterno = $invitacion->equipo->coordinador->apellido_paterno;
                $invitacion->coordinador_apellido_materno = $invitacion->equipo->coordinador->apellido_materno;
                $invitacion->coordinador_correo = $invitacion->coordinador->equipo->coordinador->usuario->correo; 
            } else {
                $invitacion->coordinador_nombre = null;
                $invitacion->coordinador_correo = null;
            }
            return $invitacion;
        });
        $invitacionesPag->setCollection($invitacionesParse);

        $nro_invitacionesPendientes = $invitacionesPag->where('estado', 'PENDIENTE')->count();
        return view('private.colaborador.invitaciones', [
            'invitaciones' => $invitacionesPag,
            'nro_invitacionesPendientes' => $nro_invitacionesPendientes,
            'criterios' => $criterios,
        ]);
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
