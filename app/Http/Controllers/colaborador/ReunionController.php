<?php

namespace App\Http\Controllers\Colaborador;

use App\Http\Controllers\Controller;
use App\Repositories\EquipoRepositorio;
use App\Repositories\ReunionRepositorio;
use App\Repositories\TrabajadorRepositorio;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Faker\Factory as Faker;

class ReunionController extends Controller
{
    
    protected TrabajadorRepositorio $trabajadorRepositorio;
    protected EquipoRepositorio $equipoRepositorio;
    protected ReunionRepositorio $reunionRepositorio;

    public function __construct(TrabajadorRepositorio $trabajadorRepositorio, EquipoRepositorio $equipoRepositorio, ReunionRepositorio $reunionRepositorio)
    {
        $this->trabajadorRepositorio = $trabajadorRepositorio;
        $this->equipoRepositorio = $equipoRepositorio;
        $this->reunionRepositorio = $reunionRepositorio;
    }

    public function index()
    {
        $usuario = Auth::user();
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
        $equipo = $trabajador->getEquipoFromColabAttribute();

        $meetingsPaginator = $this->reunionRepositorio->obtenerReunionesPorEquipo($equipo->id, 'PROGRAMADA', 5);

        foreach ($meetingsPaginator as $reunion) {
            $this->reunionRepositorio->actualizarEstadoSiFinalizada($reunion->id);
        }

        $meetingsPaginator = $this->reunionRepositorio->obtenerReunionesPorEquipo($equipo->id, 'PROGRAMADA', 5);

        $meetings = collect($meetingsPaginator->items());

        $todayMeetings = $meetings->filter(fn($m) =>
            Carbon::parse("{$m['date']} {$m['time']}")->isToday()
        )->values();

        $upcomingMeetings = $meetings->filter(fn($m) =>
            Carbon::parse("{$m['date']} {$m['time']}")->isFuture() &&
            !Carbon::parse("{$m['date']} {$m['time']}")->isToday()
        )->values();

        $pastMeetings = $this->reunionRepositorio->obtenerReunionesPorEquipo($equipo->id, 'COMPLETADA', 5);

        return view('private.colaborador.reuniones', [
            'meetingsPaginator' => $meetingsPaginator, 
            'todayMeetings'     => $todayMeetings,
            'upcomingMeetings'  => $upcomingMeetings,
            'pastMeetings'      => $pastMeetings,
        ]);
    }

    public function join(Request $request, $id)
    {
        $meeting = collect($this->getMeetingsData())->firstWhere('id', $id);

        if (! $meeting) {
            return response()->json(['error' => 'Reunión no encontrada'], 404);
        }

        $participants = $this->getParticipantsData();

        return view('colaborador.reuniones.video-call', compact('meeting', 'participants'));
    }

    public function viewDetails(Request $request, $id)
    {
        $all = array_merge(
            $this->getMeetingsData()->toArray(),
            $this->getPastMeetingsData()
        );

        $meeting = collect($all)->firstWhere('id', $id);

        if (! $meeting) {
            return response()->json(['error' => 'Reunión no encontrada'], 404);
        }

        if ($request->ajax()) {
            return response()->json(['meeting' => $meeting]);
        }

        return view('colaborador.reuniones.details', compact('meeting'));
    }

    public function endCall(Request $request)
    {
        return response()->json(['success' => true]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:1000',
            'meeting_id' => 'required|string',
        ]);

        // Simular envío de mensaje en el chat de la reunión
        $chatMessage = [
            'id'     => uniqid(),
            'sender' => 'Tú',
            'text'   => $request->message,
            'time'   => now()->format('H:i'),
        ];

        return response()->json(['message' => $chatMessage]);
    }

    public function toggleMic(Request $request)
    {
        return response()->json(['success' => true]);
    }

    public function toggleVideo(Request $request)
    {
        return response()->json(['success' => true]);
    }

    public function toggleScreenShare(Request $request)
    {
        return response()->json(['success' => true]);
    }

    /**
     * Genera reuniones y las devuelve paginadas.
     */
    private function getMeetingsData(int $perPage = 5): LengthAwarePaginator
    {
        $faker    = Faker::create('es_PE');
        $meetings = [];

        for ($i = 1; $i <= 20; $i++) {
            $dt = $faker->dateTimeBetween('now', '+1 month');
            $meetings[] = [
                'id'          => (string) $i,
                'title'       => ucfirst(rtrim($faker->sentence(3), '.')),
                'description' => $faker->paragraph(),
                'date'        => $dt->format('Y-m-d'),
                'time'        => $dt->format('H:i'),
                'duration'    => $faker->numberBetween(30, 120),
                'location'    => $faker->randomElement([
                    'Sala Virtual 1',
                    'Sala Virtual 2',
                    'Sala de conferencias principal',
                    'Sala de capacitación',
                ]),
                'type'      => $faker->randomElement(['Virtual', 'Presencial']),
                'attendees' => $faker->numberBetween(2, 15),
            ];
        }

        // Página actual desde ?page=
        $page = request()->input('page', 1);

        $offset = ($page - 1) * $perPage;
        $items  = array_slice($meetings, $offset, $perPage);

        return new LengthAwarePaginator(
            $items,
            count($meetings),
            $perPage,
            $page,
            [
                'path'     => request()->url(),
                'pageName' => 'page',
            ]
        );
    }

    /**
     * Genera reuniones pasadas (sin paginar).
     */
    private function getPastMeetingsData(): array
    {
        $faker = Faker::create('es_PE');
        $past  = [];

        for ($i = 101; $i <= 105; $i++) {
            $dt = $faker->dateTimeBetween('-1 month', 'now');
            $past[] = [
                'id'          => (string) $i,
                'title'       => ucfirst(rtrim($faker->sentence(4), '.')),
                'description' => $faker->paragraph(),
                'date'        => $dt->format('Y-m-d'),
                'time'        => $dt->format('H:i'),
                'duration'    => $faker->numberBetween(30, 90),
                'location'    => $faker->randomElement([
                    'Sala Virtual 2',
                    'Sala Virtual 3',
                    'Sala de capacitación',
                ]),
                'type'      => $faker->randomElement(['Virtual', 'Presencial']),
                'attendees' => $faker->numberBetween(5, 20),
            ];
        }

        return $past;
    }

    /**
     * Genera participantes de ejemplo.
     */
    private function getParticipantsData(): array
    {
        $faker        = Faker::create('es_PE');
        $participants = [];

        for ($i = 1; $i <= 5; $i++) {
            $participants[] = [
                'id'           => 'p' . $i,
                'name'         => $faker->name(),
                'avatar'       => '/placeholder.svg?height=96&width=96',
                'is_speaking'  => $faker->boolean(20),
                'is_muted'     => $faker->boolean(30),
                'is_video_off' => $faker->boolean(40),
                'is_host'      => $i === 1,
            ];
        }

        return $participants;
    }

    // Resto de métodos create/store/show/edit/update/destroy...
}
