<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoordEquipo\ProgramarReunionRequest;
use App\Http\Requests\CoordEquipo\ReprogramarReunionRequest;
use App\Repositories\EquipoRepositorio;
use App\Repositories\ModalidadRepositorio;
use App\Repositories\ReunionRepositorio;
use App\Repositories\TrabajadorRepositorio;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;
use Validator;

class ReunionesCoordinadorController extends Controller
{
    protected TrabajadorRepositorio $trabajadorRepositorio;
    protected EquipoRepositorio $equipoRepositorio;
    protected ReunionRepositorio $reunionRepositorio;
    protected ModalidadRepositorio $modalidadRepositorio;

    public function __construct(TrabajadorRepositorio $trabajadorRepositorio, EquipoRepositorio $equipoRepositorio, ReunionRepositorio $reunionRepositorio, ModalidadRepositorio $modalidadRepositorio)
    {
        $this->trabajadorRepositorio = $trabajadorRepositorio;
        $this->equipoRepositorio = $equipoRepositorio;
        $this->reunionRepositorio = $reunionRepositorio;
        $this->modalidadRepositorio = $modalidadRepositorio;
    }

    public function index()
    {

        $usuario = Auth::user();
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
        $equipo = $this->equipoRepositorio->findOneBy('coordinador_id', $trabajador->id);
        $modalidades = $this->modalidadRepositorio->getAll();

        if (!$equipo) {
            return redirect()->back()->withErrors('No se encontró el equipo asociado al usuario.');
        }

        $reunionesProgramadas = $this->reunionRepositorio->findByFields(['equipo_id' => $equipo->id, 'estado' => 'PROGRAMADA']);

        foreach ($reunionesProgramadas as $reunion) {
            $this->reunionRepositorio->actualizarEstadoSiFinalizada($reunion->id);
        }

        $reunionesProgramadas = $this->reunionRepositorio->findByFields(['equipo_id' => $equipo->id, 'estado' => 'PROGRAMADA']);

        $reunionesCompletadas = $this->reunionRepositorio->findByFields(['equipo_id' => $equipo->id, 'estado' => 'COMPLETADA']);
        $duracionPromedio = $reunionesCompletadas->avg('duracion');

        $stats = [
            'reuniones_programadas' => $reunionesProgramadas->count(),
            'reuniones_completadas' => $reunionesCompletadas->count(),
            'participacion_promedio' => 87,
            'duracion_promedio' => $duracionPromedio ? round($duracionPromedio, 1) : 0
        ];

        return view('private.coord-equipo.reuniones', compact('stats', 'reunionesProgramadas', 'reunionesCompletadas', 'modalidades'));
    }

    public function store(ProgramarReunionRequest $request)
    {

        $usuario = Auth::user();
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
        $equipo = $this->equipoRepositorio->findOneBy('coordinador_id', $trabajador->id);

        if (!$equipo) {
            return redirect()->back()->withErrors('No se encontró el equipo del coordinador.');
        }

        $fechaHora = Carbon::parse($request->input('fecha'));
        $fecha = $fechaHora->toDateString();
        $hora = $fechaHora->toTimeString();

        try {
            $response = Http::post(config('bbb.url') . '/create', [
                'name'         => $request->input('titulo'),
                'meetingID'    => 'eq-' . $equipo->id . '-' . now()->timestamp,
                'moderatorPW'  => 'mod123',
                'attendeePW'   => 'att456',
                'duration'     => $request->input('duracion'),
            ]);

            if (!$response->ok() || $response['status'] !== 'created') {
                return redirect()->back()->withErrors('No se pudo crear la sala en BBB.');
            }

            $links = $response->json();
        } catch (\Exception $e) {
            Log::error('Error al conectar con el microservicio BBB: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error al crear la sala virtual.');
        }

        $datos = [
            'equipo_id'     => $equipo->id,
            'fecha'         => $fecha,
            'hora'          => $hora,
            'duracion'      => $request->input('duracion'),
            'descripcion'   => $request->input('descripcion'),
            'asunto'        => $request->input('titulo'),
            'modalidad_id'  => $request->input('modalidad_id'),
            'sala'          => $request->input('sala'),
            'link_moderador' => $links['moderator_url'],
            'link_participante' => $links['attendee_url'],
            'meeting_id' => $links['meetingID'],
        ];

        $this->reunionRepositorio->create($datos);

        return redirect()->back()->with('success', 'Reunión programada exitosamente.');
    }

    public function join($id)
    {
        return redirect()->back()->with('info', 'Redirigiendo a la reunión...');
    }

    public function cancel($id)
    {
        try {
            $resultado = $this->reunionRepositorio->cancelarReunion($id);

            if (!$resultado) {
                return redirect()->back()->with('warning', 'No se pudo cancelar la reunión. Verifica su estado.');
            }

            return redirect()->back()->with('success', 'Reunión cancelada correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al cancelar reunión: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al cancelar la reunión.');
        }
    }

    public function reschedule(ReprogramarReunionRequest $request, $id)
    {
        try {
            $reunion = $this->reunionRepositorio->getById($id);

            if (!$reunion) {
                return redirect()->back()->with('error', 'Reunión no encontrada.');
            }

            $reunion->fecha = $request->input('nueva_fecha');
            $reunion->hora = $request->input('nueva_hora');
            $reunion->estado = 'PROGRAMADA';
            $reunion->observacion = $request->input('motivo');

            $reunion->save();

            return redirect()->back()->with('success', 'Reunión reprogramada correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al reprogramar reunión: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al reprogramar la reunión.');
        }
    }

    public function verificarEstadoReunion($reunionId)
    {
        $reunion = $this->reunionRepositorio->getById($reunionId);

        if (!$reunion || !$reunion->meeting_id) {
            return;
        }

        try {
            $response = Http::timeout(5)->get(config('bbb.url') . '/info/' . $reunion->meeting_id);

            if ($response->ok()) {
                $info = $response->json();

                if (!$info['running'] && !empty($info['end_time'])) {
                    if ($reunion->estado !== 'COMPLETADA') {
                        $this->reunionRepositorio->update($reunionId, [
                            'estado' => 'COMPLETADA',
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('No se pudo verificar el estado de la reunión: ' . $e->getMessage());
        }
    }



}
