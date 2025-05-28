<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReunionesController extends Controller
{
    public function index()
    {
        // Datos de ejemplo para reuniones con las claves correctas
        $allMeetings = [
            [
                'id' => 1,
                'title' => 'Reunión de Planificación Q1',
                'description' => 'Planificación estratégica para el primer trimestre del año',
                'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'time' => '10:00',
                'duration' => 60,
                'location' => 'Sala de Conferencias A',
                'type' => 'Virtual',
                'attendees' => 8,
                'group' => 'Planificación Estratégica',
                'created_at' => Carbon::now()->subDays(1)
            ],
            [
                'id' => 2,
                'title' => 'Revisión de Proyecto Alpha',
                'description' => 'Revisión del progreso y próximos pasos del proyecto Alpha',
                'date' => Carbon::now()->format('Y-m-d'),
                'time' => '14:30',
                'duration' => 45,
                'location' => 'Sala Virtual 2',
                'type' => 'Virtual',
                'attendees' => 6,
                'group' => 'Desarrollo',
                'created_at' => Carbon::now()->subDays(3)
            ],
            [
                'id' => 3,
                'title' => 'Standup Diario',
                'description' => 'Reunión diaria del equipo de desarrollo para sincronización',
                'date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'time' => '09:00',
                'duration' => 15,
                'location' => 'Sala Virtual Principal',
                'type' => 'Virtual',
                'attendees' => 5,
                'group' => 'Desarrollo',
                'created_at' => Carbon::now()->subDays(5)
            ],
            [
                'id' => 4,
                'title' => 'Presentación a Cliente',
                'description' => 'Presentación de resultados y avances al cliente principal',
                'date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'time' => '16:00',
                'duration' => 90,
                'location' => 'Sala de Juntas',
                'type' => 'Presencial',
                'attendees' => 12,
                'group' => 'Ventas',
                'created_at' => Carbon::now()->subDays(2)
            ],
            [
                'id' => 5,
                'title' => 'Retrospectiva Sprint 12',
                'description' => 'Retrospectiva del sprint 12 - mejoras y lecciones aprendidas',
                'date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'time' => '11:00',
                'duration' => 60,
                'location' => 'Sala Virtual 3',
                'type' => 'Virtual',
                'attendees' => 7,
                'group' => 'Desarrollo',
                'created_at' => Carbon::now()->subDays(7)
            ],
            [
                'id' => 6,
                'title' => 'Análisis de Métricas',
                'description' => 'Revisión de métricas de rendimiento y KPIs del mes',
                'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'time' => '13:00',
                'duration' => 75,
                'location' => 'Sala de Análisis',
                'type' => 'Presencial',
                'attendees' => 4,
                'group' => 'Analytics',
                'created_at' => Carbon::now()->subDays(1)
            ]
        ];

        // Filtrar reuniones por categorías usando la clave correcta 'date'
        $today = Carbon::now()->format('Y-m-d');
        
        $upcomingMeetings = collect($allMeetings)->filter(function ($meeting) use ($today) {
            return $meeting['date'] > $today;
        })->values()->all();

        $todayMeetings = collect($allMeetings)->filter(function ($meeting) use ($today) {
            return $meeting['date'] === $today;
        })->values()->all();

        $pastMeetings = collect($allMeetings)->filter(function ($meeting) use ($today) {
            return $meeting['date'] < $today;
        })->values()->all();

        // Datos para el calendario
        $calendarEvents = collect($allMeetings)->map(function ($meeting) {
            return [
                'id' => $meeting['id'],
                'title' => $meeting['title'],
                'date' => $meeting['date'],
                'time' => $meeting['time'],
                'type' => $meeting['type'],
                'attendees' => $meeting['attendees'],
                'group' => $meeting['group']
            ];
        })->all();

        // Datos para dropdowns
        $coordinadores = [
            ['id' => 1, 'nombre' => 'Ana García'],
            ['id' => 2, 'nombre' => 'Carlos López'],
            ['id' => 3, 'nombre' => 'María Rodríguez'],
            ['id' => 4, 'nombre' => 'Luis Martín'],
            ['id' => 5, 'nombre' => 'Sofia Chen']
        ];

        $colaboradores = [
            ['id' => 1, 'nombre' => 'Pedro Sánchez'],
            ['id' => 2, 'nombre' => 'Laura Kim'],
            ['id' => 3, 'nombre' => 'Roberto Silva'],
            ['id' => 4, 'nombre' => 'Carmen Vega'],
            ['id' => 5, 'nombre' => 'Andrés Torres'],
            ['id' => 6, 'nombre' => 'Isabel Moreno'],
            ['id' => 7, 'nombre' => 'Miguel Herrera'],
            ['id' => 8, 'nombre' => 'Elena Castillo'],
            ['id' => 9, 'nombre' => 'Javier Ramos'],
            ['id' => 10, 'nombre' => 'Diego Ruiz']
        ];

        // Datos para participantes de videollamada (si es necesario)
        $participants = [
            [
                'id' => 'p1',
                'name' => 'Ana García',
                'avatar' => '/placeholder.svg?height=96&width=96',
                'isSpeaking' => true,
                'isMuted' => false,
                'isVideoOff' => false,
            ],
            [
                'id' => 'p2',
                'name' => 'Carlos Rodríguez',
                'avatar' => '/placeholder.svg?height=96&width=96',
                'isSpeaking' => false,
                'isMuted' => true,
                'isVideoOff' => false,
            ],
            [
                'id' => 'p3',
                'name' => 'Laura Martínez',
                'avatar' => '/placeholder.svg?height=96&width=96',
                'isSpeaking' => false,
                'isMuted' => false,
                'isVideoOff' => true,
            ],
            [
                'id' => 'p4',
                'name' => 'Miguel López',
                'avatar' => '/placeholder.svg?height=96&width=96',
                'isSpeaking' => false,
                'isMuted' => false,
                'isVideoOff' => false,
                'isHost' => true,
            ],
        ];

        return view('coordinador-general.reuniones.index', compact(
            'upcomingMeetings',
            'todayMeetings', 
            'pastMeetings',
            'calendarEvents',
            'coordinadores',
            'colaboradores',
            'participants'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|integer|min:15|max:480',
            'type' => 'required|string',
            'group' => 'required|string',
            'location' => 'required|string',
            'attendees' => 'required|array|min:1'
        ]);

        // Aquí iría la lógica para guardar en base de datos
        // Meeting::create($request->all());

        return redirect()->route('coordinador-general.reuniones')
            ->with('success', 'Reunión creada exitosamente');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|integer|min:15|max:480',
            'type' => 'required|string',
            'group' => 'required|string',
            'location' => 'required|string'
        ]);

        // Aquí iría la lógica para actualizar en base de datos
        // $meeting = Meeting::findOrFail($id);
        // $meeting->update($request->all());

        return redirect()->route('coordinador-general.reuniones')
            ->with('success', 'Reunión actualizada exitosamente');
    }

    public function destroy($id)
    {
        // Aquí iría la lógica para eliminar de base de datos
        // Meeting::findOrFail($id)->delete();

        return redirect()->route('coordinador-general.reuniones')
            ->with('success', 'Reunión eliminada exitosamente');
    }

    public function join($id)
    {
        // Aquí iría la lógica para unirse a la reunión
        // Podría redirigir al enlace de la reunión o marcar asistencia

        return redirect()->route('coordinador-general.reuniones')
            ->with('success', 'Te has unido a la reunión exitosamente');
    }
}