<?php

namespace App\Http\Controllers\colaborador;

use App\Http\Controllers\Controller;
use App\Repositories\TrabajadorRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MiEquipoController extends Controller
{
    protected TrabajadorRepositorio $trabajadorRepositorio;
    public function __construct(TrabajadorRepositorio $trabajadorRepositorio)
    {
        $this->trabajadorRepositorio = $trabajadorRepositorio;
    }
    public function index()
    {
        $trabajador = $this->getTrabajador();
        $equipo = $trabajador->equipoFromColab;
        if (!$equipo) {
            return redirect()->route('colaborador.actividades')->with('error', 'No tienes un equipo asignado.');
        }
        // Datos del equipo (simulados - reemplazar con consultas reales)
        $equipoInfo = [
            'nombre' => $equipo->nombre ?? 'Sin equipo asignado',
            'area' => $equipo->area->nombre ?? 'Sin área asignada',
            'progreso_general' => $equipo->progresoPromedio ?? 0,
            'metas_completadas' => $equipo->metas->where('estado', 'Completo')->count() ?? 0,
            'metas_totales' => $equipo->metas->count() ?? 0,
            'actividades_completadas' => $equipo->actividades->where('estado', 'Completo')->count() ?? 0,
            'actividades_totales' => $equipo->actividades->count() ?? 0,
            'reuniones_pendientes' => 0
        ];

        $estadisticas = [
            'miembros' =>  $equipo->miembros->count() ?? 0,
            'miembros_nuevos' => $equipo->miembros->where('fecha_registro', '>=', now()->subMonth())->count() ?? 0,
            // metas activas son todas las que no estan completadas o suspendidas
            'metas_activas' => $equipo->metas->where('estado', '!=', 'Completo')->count() ?? 0,
            'metas_completadas_mes' => $equipo->metas->where('estado', 'Completo')->where('fecha_entrega', '>=', now()->subMonth())->count() ?? 0,
            'actividades_total' => $equipo->actividades->count() ?? 0,
            'actividades_progreso' => $equipo->actividades->where('estado', '!=', 'En proceso')->count() ?? 0,
            'actividades_completadas' => $equipo->actividades->where('estado', 'Completo')->count() ?? 0,
            'rendimiento' => $equipo->progresoPromedio ?? 0,
            'mejora_rendimiento' => 5
        ];

        $miembros = $equipo->miembros->map(function ($miembro) {
            return [
                'id' => $miembro->id,
                'nombre' => $miembro->trabajador->nombreCompleto,
                'email' => $miembro->trabajador->usuario->correo,
                'rol' => $miembro->trabajador->usuario->rol->nombre ?? 'Sin rol asignado',
                'actividades_completadas' => $miembro->actividadesCompletadasCount,
                'actividades_totales' => $miembro->actividadesTotalesCount,
                'rendimiento' => $miembro->rendimiento,
                'avatar' => $miembro->avatar ?? '/placeholder-32px.png',
                'last_seen' => $miembro->trabajador->usuario->ultima_conexion ?? now()->subDays(1)->format('Y-m-d H:i:s'),
            ];
        })->toArray();

        $metas = $equipo->metas->map(function ($meta) {
            return [
                'id' => $meta->id,
                'nombre' => $meta->nombre,
                'descripcion' => $meta->descripcion,
                'fecha_entrega' => $meta->fecha_entrega ?? 'Sin fecha de entrega',
                'estado' => $meta->estado ? $meta->estado->nombre : 'Desconocido',
                'progreso' => $meta->progreso,
                'tareas_completadas' => $meta->tareasCompletadasCount,
                'total_tareas' => $meta->totalTareasCount,
            ];
        })->toArray();

        return view('private.colaborador.mi-equipo', compact(
            'equipoInfo',
            'estadisticas',
            'miembros',
            'metas'
        ));
    }

    public function getTrabajador()
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return redirect()->route('colaborador.actividades')->with('error', 'Usuario no autenticado.');
        }
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id);
        if (!$trabajador) {
            return redirect()->route('colaborador.actividades')->with('error', 'Trabajador no encontrado.');
        }
        return $trabajador;
    }
}
