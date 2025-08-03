<?php

namespace App\Repositories;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AuditoriaRepositorio extends RepositorioBase
{
    public function __construct(Activity $model)
    {
        parent::__construct($model);
    }

    /**
     * Aplica filtros específicos de auditoría
     */
    protected function aplicarFiltros(\Illuminate\Database\Eloquent\Builder $consulta, array $filtros): void
    {
        if (!empty($filtros['subject_type'])) {
            $consulta->where('subject_type', $filtros['subject_type']);
        }

        if (!empty($filtros['event'])) {
            $consulta->where('event', $filtros['event']);
        }

        if (!empty($filtros['fecha_desde'])) {
            $consulta->whereDate('created_at', '>=', $filtros['fecha_desde']);
        }

        if (!empty($filtros['fecha_hasta'])) {
            $consulta->whereDate('created_at', '<=', $filtros['fecha_hasta']);
        }

        if (!empty($filtros['causer_id'])) {
            $consulta->where('causer_id', $filtros['causer_id']);
        }
    }

    /**
     * Aplica búsqueda en auditorías
     */
    protected function aplicarBusqueda(\Illuminate\Database\Eloquent\Builder $consulta, ?string $searchTerm, ?string $searchColumn): void
    {
        if (!empty($searchTerm)) {
            $consulta->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                  ->orWhere('subject_type', 'like', "%{$searchTerm}%")
                  ->orWhere('event', 'like', "%{$searchTerm}%")
                  ->orWhereHas('causer', function ($subQuery) use ($searchTerm) {
                      $subQuery->where('correo', 'like', "%{$searchTerm}%");
                  });
            });
        }
    }

    /**
     * Aplica ordenamiento
     */
    protected function aplicarOrdenamiento(\Illuminate\Database\Eloquent\Builder $consulta, ?string $sortField, ?string $sortOrder): void
    {
        $sortField = $sortField ?? 'created_at';
        $sortOrder = $sortOrder ?? 'desc';
        
        $consulta->orderBy($sortField, $sortOrder);
    }

    /**
     * Aplica rango de fechas
     */
    protected function aplicarRango(\Illuminate\Database\Eloquent\Builder $consulta, array $range): void
    {
        if (!empty($range['start'])) {
            $consulta->whereDate('created_at', '>=', $range['start']);
        }

        if (!empty($range['end'])) {
            $consulta->whereDate('created_at', '<=', $range['end']);
        }
    }

    /**
     * Obtener auditorías con relaciones cargadas
     */
    public function obtenerConRelaciones(array $criterios): LengthAwarePaginator
    {
        $query = $this->model->with(['subject', 'causer']);
        return $this->obtenerPaginado($criterios, $query);
    }

    /**
     * Obtener estadísticas de auditoría
     */
    public function obtenerEstadisticas(): array
    {
        $hoy = Carbon::today();
        $inicioSemana = Carbon::now()->startOfWeek();
        $inicioMes = Carbon::now()->startOfMonth();

        return [
            'total_registros' => $this->model->count(),
            'registros_hoy' => $this->model->whereDate('created_at', $hoy)->count(),
            'registros_semana' => $this->model->where('created_at', '>=', $inicioSemana)->count(),
            'registros_mes' => $this->model->where('created_at', '>=', $inicioMes)->count(),
        ];
    }

    /**
     * Obtener actividades por modelo
     */
    public function obtenerPorModelo(int $limit = 10): Collection
    {
        return $this->model->selectRaw('subject_type, COUNT(*) as total')
            ->whereNotNull('subject_type')
            ->groupBy('subject_type')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener actividades por evento
     */
    public function obtenerPorEvento(): Collection
    {
        return $this->model->selectRaw('event, COUNT(*) as total')
            ->groupBy('event')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Obtener modelos únicos disponibles
     */
    public function obtenerModelosDisponibles(): SupportCollection
    {
        return $this->model->select('subject_type')
            ->whereNotNull('subject_type')
            ->distinct()
            ->orderBy('subject_type')
            ->get()
            ->pluck('subject_type')
            ->map(function ($modelo) {
                return [
                    'value' => $modelo,
                    'label' => class_basename($modelo)
                ];
            });
    }

    /**
     * Obtener eventos únicos disponibles
     */
    public function obtenerEventosDisponibles(): SupportCollection
    {
        return $this->model->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');
    }

    /**
     * Obtener usuarios que han generado actividades
     */
    public function obtenerUsuariosConActividades(): SupportCollection
    {
        return $this->model->with('causer')
            ->whereNotNull('causer_id')
            ->select('causer_id')
            ->distinct()
            ->get()
            ->pluck('causer')
            ->filter()
            ->unique('id')
            ->values();
    }

    /**
     * Limpiar registros antiguos
     */
    public function limpiarRegistrosAntiguos(int $dias): int
    {
        $fechaLimite = Carbon::now()->subDays($dias);
        return $this->model->where('created_at', '<', $fechaLimite)->delete();
    }
}
