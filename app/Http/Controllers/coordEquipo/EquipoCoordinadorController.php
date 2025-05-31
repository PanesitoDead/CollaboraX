<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoordEquipo\InvitarColaboradoresRequest;
use App\Http\Requests\CoordEquipo\ProgramarReunionRequest;
use App\Repositories\EquipoRepositorio;
use App\Repositories\InvitacionRepositorio;
use App\Repositories\MetaRepositorio;
use App\Repositories\ModalidadRepositorio;
use App\Repositories\ReunionRepositorio;
use App\Repositories\TrabajadorRepositorio;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class EquipoCoordinadorController extends Controller
{

    protected TrabajadorRepositorio $trabajadorRepositorio;
    protected EquipoRepositorio $equipoRepositorio;
    protected MetaRepositorio $metaRepositorio;
    protected ReunionRepositorio $reunionRepositorio;
    protected InvitacionRepositorio $invitacionRepositorio;
    protected ModalidadRepositorio $modalidadRepositorio;

    public function __construct(TrabajadorRepositorio $trabajadorRepositorio, EquipoRepositorio $equipoRepositorio, MetaRepositorio $metaRepositorio, ReunionRepositorio $reunionRepositorio, InvitacionRepositorio $invitacionRepositorio, ModalidadRepositorio $modalidadRepositorio)
    {
        $this->trabajadorRepositorio = $trabajadorRepositorio;
        $this->equipoRepositorio = $equipoRepositorio;
        $this->metaRepositorio = $metaRepositorio;
        $this->reunionRepositorio = $reunionRepositorio;
        $this->invitacionRepositorio = $invitacionRepositorio;
        $this->modalidadRepositorio = $modalidadRepositorio;
    }

    public function index()
    {

        $usuario = Auth::user();
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
        $equipo = $this->equipoRepositorio->findOneBy('coordinador_id', $trabajador->id);
        $miembros = $this->trabajadorRepositorio->getMiembrosEquipo($equipo->id);
        $cantidadMiembros = $this->trabajadorRepositorio->countMiembrosEquipo($equipo->id);
        $invitaciones = $this->invitacionRepositorio->getInvitacionesPorEquipo($equipo->id);
        $colaboradores_disponibles = $this->trabajadorRepositorio->getColaboradoresDisponibles();
        $modalidades = $this->modalidadRepositorio->getAll();

        $metas = $this->metaRepositorio->getMetasPorEquipo($equipo->id);
        $reunionesPendientes = $this->reunionRepositorio->countReunionesPendientesPorEquipo($equipo->id);

        // Cálculos
        $metasCompletadas = $metas->where('estado_id', 3)->count(); // estado_id 3 = completado
        $metasActivas = $metas->where('estado_id', '!=', 3)->count();


        $totalTareas = 0;
        $tareasCompletadas = 0;
        $tareasProgreso = 0;

        foreach ($metas as $meta) {
            $totalTareas += $meta->tareas->count();
            $tareasCompletadas += $meta->tareas->where('estado_id', 3)->count(); // estado_id 3 = completado
            $tareasProgreso += $meta->tareas->where('estado_id', 2)->count();
        }

        $rendimiento = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0;
        
        $stats = [
            'miembros' => $cantidadMiembros,
            'actividades_progreso' => $tareasProgreso,
            'metas_completadas' => $metasCompletadas,
            'metas_activas' => $metasActivas,
            'actividades_total' => $totalTareas,
            'actividades_completadas' => $tareasCompletadas,
            'rendimiento' => $rendimiento,
            'reuniones_pendientes' => $reunionesPendientes
        ];

        return view('private.coord-equipo.mi-equipo', compact(
            'stats', 
            'equipo',
            'miembros', 
            'invitaciones', 
            'colaboradores_disponibles',
            'modalidades'
        ));
    }

    public function invitarColaboradores(InvitarColaboradoresRequest $request)
    {
        $usuario = Auth::user();
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
        $equipo = $this->equipoRepositorio->findOneBy('coordinador_id', $trabajador->id);

        if (!$equipo) {
            return redirect()->back()->withErrors('No se encontró un equipo asociado al coordinador.');
        }

        $colaboradorIds = $request->input('colaboradores');
        $fechaExpiracion = Carbon::now()->addDays(5);

        foreach ($colaboradorIds as $colaboradorId) {
            $this->invitacionRepositorio->create([
                'equipo_id' => $equipo->id,
                'trabajador_id' => $colaboradorId,
                'fecha_expiracion' => $fechaExpiracion,
                'estado' => 'PENDIENTE',
            ]);
        }

        return redirect()->back()->with('success', 'Invitaciones enviadas correctamente.');
    }


    public function cancelarInvitacion($id)
    {
        $cancelado = $this->invitacionRepositorio->cancelarInivitacion($id);

        if (!$cancelado) {
            return redirect()->back()->withErrors('La invitación no existe o ya fue cancelada.');
        }

        return redirect()->back()->with('success', 'Invitación cancelada correctamente.');
    }

    // public function programarReunion(ProgramarReunionRequest $request)
    // {
    //     $usuario = Auth::user();
    //     $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
    //     $equipo = $this->equipoRepositorio->findOneBy('coordinador_id', $trabajador->id);

    //     if (!$equipo) {
    //         return redirect()->back()->withErrors('No se encontró el equipo del coordinador.');
    //     }

    //     $fechaHora = Carbon::parse($request->input('fecha'));
    //     $fecha = $fechaHora->toDateString();
    //     $hora = $fechaHora->toTimeString();

    //     $datos = [
    //         'equipo_id'     => $equipo->id,
    //         'fecha'         => $fecha,
    //         'hora'          => $hora,
    //         'duracion'      => $request->input('duracion'),
    //         'descripcion'   => $request->input('descripcion'),
    //         'asunto'        => $request->input('titulo'),
    //         'modalidad_id'  => $request->input('modalidad_id'),
    //         'sala'          => $request->input('sala'),
    //     ];

    //     $this->reunionRepositorio->create($datos);

    //     return redirect()->back()->with('success', 'Reunión programada exitosamente.');
    // }
}
