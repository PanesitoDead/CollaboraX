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

    public function index(Request $request, string $estado)
    {
        $map = [
            'pendiente' => ['PENDIENTE'],
            'historial' => ['ACEPTADA', 'RECHAZADA'],
        ];
        $filtroEstados = $map[$estado] ?? $map['pendiente'];

        $usuario = Auth::user();
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
        $criterios = $this->obtenerCriterios($request);
        // Creamos el query builder para las invitaciones
        $query = $this->invitacionRepositorio->getModel()->newQuery();
        // Filtramos por el trabajador del usuario autenticado
        $query->where('trabajador_id', $trabajador->id)->whereIn('estado', $filtroEstados);
        // Aplicamos los criterios de búsqueda
        $invitacionesPag = $this->invitacionRepositorio->obtenerPaginado($criterios, $query);
        $invitacionesParse = $invitacionesPag->getCollection()->map(function ($invitacion) {
            // Formateamos la fecha de invitación
            $invitacion->fecha_invitacion = Carbon::parse($invitacion->fecha_invitacion)->format('d/m/Y');
            $invitacion->fecha_expiracion = Carbon::parse($invitacion->fecha_expiracion)->format('d/m/Y');
            $invitacion->fecha_respuesta = $invitacion->fecha_respuesta ? Carbon::parse($invitacion->fecha_respuesta)->format('d/m/Y')
                : 'Sin respuesta';
            // Si existe un equipo, extraemos su nombre
            if ($invitacion->equipo) {
                $invitacion->equipo_nombre = $invitacion->equipo->nombre;
                $invitacion->equipo_avatar = $invitacion->equipo->avatar ?? '/placeholder-40x40.png';
            } else {
                $invitacion->equipo_nombre = null;
                $invitacion->equipo_avatar = '/placeholder-40x40.png';
            }
            // Si existe un coordinador activo, extraemos nombre y correo
            if ($invitacion->equipo && $invitacion->equipo->coordinador) {
                $invitacion->coordinador_iniciales = $invitacion->equipo->coordinador->iniciales;
                $invitacion->coordinador_nombres = $invitacion->equipo->coordinador->nombres;
                $invitacion->coordinador_nombre_completo = $invitacion->equipo->coordinador->nombres . ' ' .
                    $invitacion->equipo->coordinador->apellido_paterno . ' ' .
                    $invitacion->equipo->coordinador->apellido_materno;
                $invitacion->coordinador_correo = $invitacion->equipo->coordinador->usuario->correo; 
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
            'estadoSlug' => $estado,
        ]);
    }

    public function aceptar($id)
    {
       $success = $this->invitacionRepositorio->aceptarInvitacion($id);
       
       if (!$success) {
            return redirect()->route('colaborador.invitaciones.index')->with('error', 'Error al aceptar la invitación. Por favor, inténtalo de nuevo.');
        }
        return redirect()->route('colaborador.invitaciones.index')->with('success', 'Invitación aceptada correctamente.');
        
    }

    public function rechazar($id)
    {
        $success = $this->invitacionRepositorio->cancelarInivitacion($id);
        
        if (!$success) {
            return redirect()->route('colaborador.invitaciones.index')->with('error', 'Error al rechazar la invitación. Por favor, inténtalo de nuevo.');
        }
        return redirect()->route('colaborador.invitaciones.index')->with('success', 'Invitación rechazada correctamente.');        
    }
}
