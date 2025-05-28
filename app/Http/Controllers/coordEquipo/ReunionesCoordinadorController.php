<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class ReunionesCoordinadorController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth', 'role:coordinador-grupo']);
    }

    public function index()
    {
        // Datos de ejemplo - reemplazar con datos reales de la base de datos
        $stats = [
            'reuniones_programadas' => 8,
            'reuniones_completadas' => 15,
            'participacion_promedio' => 87,
            'duracion_promedio' => 45
        ];

        $reuniones = [
            [
                'id' => 1,
                'titulo' => 'Revisión Semanal de Metas',
                'descripcion' => 'Revisión del progreso de las metas del equipo y planificación de la próxima semana',
                'fecha' => '2024-01-15',
                'hora' => '10:00',
                'duracion' => 60,
                'estado' => 'programada',
                'tipo' => 'recurrente',
                'participantes' => [
                    ['name' => 'Ana García', 'avatar' => '/placeholder-40x40.png'],
                    ['name' => 'Carlos López', 'avatar' => '/placeholder-40x40.png'],
                    ['name' => 'María Rodríguez', 'avatar' => '/placeholder-40x40.png'],
                ],
                'link_reunion' => 'https://meet.google.com/abc-defg-hij',
                'grabacion' => null
            ],
            [
                'id' => 2,
                'titulo' => 'Planificación Mensual',
                'descripcion' => 'Definición de objetivos y estrategias para el próximo mes',
                'fecha' => '2024-01-12',
                'hora' => '14:30',
                'duracion' => 90,
                'estado' => 'completada',
                'tipo' => 'planificacion',
                'participantes' => [
                    ['name' => 'Ana García', 'avatar' => '/placeholder-40x40.png'],
                    ['name' => 'Carlos López', 'avatar' => '/placeholder-40x40.png'],
                    ['name' => 'María Rodríguez', 'avatar' => '/placeholder-40x40.png'],
                    ['name' => 'Luis Martín', 'avatar' => '/placeholder-40x40.png'],
                ],
                'link_reunion' => null,
                'grabacion' => 'https://drive.google.com/file/d/1234567890'
            ],
            [
                'id' => 3,
                'titulo' => 'Seguimiento de Proyecto Alpha',
                'descripcion' => 'Revisión del avance del proyecto Alpha y resolución de bloqueos',
                'fecha' => '2024-01-18',
                'hora' => '09:00',
                'duracion' => 45,
                'estado' => 'programada',
                'tipo' => 'seguimiento',
                'participantes' => [
                    ['name' => 'Carlos López', 'avatar' => '/placeholder-40x40.png'],
                    ['name' => 'María Rodríguez', 'avatar' => '/placeholder-40x40.png'],
                ],
                'link_reunion' => 'https://zoom.us/j/123456789',
                'grabacion' => null
            ]
        ];

        $miembros_equipo = [
            ['id' => 1, 'name' => 'Ana García', 'email' => 'ana.garcia@empresa.com'],
            ['id' => 2, 'name' => 'Carlos López', 'email' => 'carlos.lopez@empresa.com'],
            ['id' => 3, 'name' => 'María Rodríguez', 'email' => 'maria.rodriguez@empresa.com'],
            ['id' => 4, 'name' => 'Luis Martín', 'email' => 'luis.martin@empresa.com'],
            ['id' => 5, 'name' => 'Elena Fernández', 'email' => 'elena.fernandez@empresa.com']
        ];

        return view('private.coord-equipo.reuniones', compact('stats', 'reuniones', 'miembros_equipo'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
            'duracion' => 'required|integer|min:15|max:480',
            'tipo' => 'required|in:reunion,planificacion,seguimiento,recurrente',
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'exists:users,id',
            'plataforma' => 'required|in:google-meet,zoom,teams',
            'es_recurrente' => 'boolean',
            'frecuencia' => 'required_if:es_recurrente,true|in:semanal,quincenal,mensual',
            'enviar_recordatorio' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Por favor corrige los errores en el formulario.');
        }

        // Aquí iría la lógica para crear la reunión
        // Reunion::create($validated);

        return redirect()->route('coord-equipo.reuniones')
            ->with('success', 'Reunión programada exitosamente.');
    }

    public function join($id)
    {
        // Lógica para unirse a una reunión
        // $reunion = Reunion::findOrFail($id);
        // return redirect($reunion->link_reunion);
        
        return redirect()->back()->with('info', 'Redirigiendo a la reunión...');
    }

    public function cancel($id)
    {
        // Lógica para cancelar una reunión
        // $reunion = Reunion::findOrFail($id);
        // $reunion->update(['estado' => 'cancelada']);

        return redirect()->back()->with('success', 'Reunión cancelada exitosamente.');
    }

    public function reschedule(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nueva_fecha' => 'required|date|after_or_equal:today',
            'nueva_hora' => 'required|date_format:H:i',
            'motivo' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Lógica para reprogramar reunión
        // $reunion = Reunion::findOrFail($id);
        // $reunion->update($validated);

        return redirect()->back()->with('success', 'Reunión reprogramada exitosamente.');
    }
}
