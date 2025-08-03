<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['subject', 'causer'])
            ->latest();

        // Filtros
        if ($request->filled('modelo')) {
            $query->where('subject_type', $request->modelo);
        }

        if ($request->filled('evento')) {
            $query->where('event', $request->evento);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('usuario')) {
            $query->where('causer_id', $request->usuario);
        }

        $auditorias = $query->paginate(20);

        return view('auditoria.index', compact('auditorias'));
    }

    public function show($id)
    {
        $auditoria = Activity::with(['subject', 'causer'])->findOrFail($id);
        return view('auditoria.show', compact('auditoria'));
    }

    public function getModelosDisponibles()
    {
        $modelos = Activity::select('subject_type')
            ->distinct()
            ->pluck('subject_type')
            ->filter()
            ->map(function ($modelo) {
                return [
                    'value' => $modelo,
                    'label' => class_basename($modelo)
                ];
            });

        return response()->json($modelos);
    }

    public function getEventosDisponibles()
    {
        $eventos = Activity::select('event')
            ->distinct()
            ->pluck('event')
            ->filter();

        return response()->json($eventos);
    }

    public function estadisticas()
    {
        $stats = [
            'total_actividades' => Activity::count(),
            'actividades_hoy' => Activity::whereDate('created_at', Carbon::today())->count(),
            'actividades_semana' => Activity::where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
            'actividades_mes' => Activity::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
        ];

        // Actividades por modelo
        $por_modelo = Activity::select('subject_type', DB::raw('count(*) as total'))
            ->groupBy('subject_type')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'modelo' => class_basename($item->subject_type),
                    'total' => $item->total
                ];
            });

        // Actividades por evento
        $por_evento = Activity::select('event', DB::raw('count(*) as total'))
            ->groupBy('event')
            ->orderBy('total', 'desc')
            ->get();

        // Actividades por día (últimos 7 días)
        $por_dia = Activity::select(DB::raw('DATE(created_at) as fecha'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return response()->json([
            'estadisticas' => $stats,
            'por_modelo' => $por_modelo,
            'por_evento' => $por_evento,
            'por_dia' => $por_dia
        ]);
    }
}
