<?php

namespace App\Repositories;

use App\Models\Meta;
use App\Models\Estado;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\Equipo;
use App\Models\Area;
use App\Models\Trabajador;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class MetaRepositorio extends RepositorioBase
 {
    public function __construct(Meta $model)
    {
        parent::__construct($model);
    }

    public function getMetasPorEquipo($equipo)
    {
        return $this->model->where('equipo_id', $equipo)->with('tareas')->get();
    }

    public function getMetasConProgresoPorEquipo(int $equipoId)
    {
        $metas = $this->getMetasPorEquipo($equipoId);

        return $metas->map(function ($meta) {
            $tareasTotales = $meta->tareas()->count();
            $tareasCompletadas = $meta->tareas()
                ->whereHas('estado', function ($q) {
                    $q->where('nombre', 'Completo');
                })->count();

            $porcentaje = $tareasTotales > 0 
                ? round(($tareasCompletadas / $tareasTotales) * 100) 
                : 0;

            $meta->porcentaje = $porcentaje;

            return $meta;
        });
    }
    
    protected function aplicarRango(Builder $consulta, ?array $range): void
    {
        if ($range['field'] && $range['values']) {
            if ($range['values']['start'] === $range['values']['end']) {
                $consulta->where($range['field'], $range['values']['start']);
            } else {
                $consulta->whereBetween($range['field'], [$range['values']['start'], $range['values']['end']]);
            }
        }
    }
    protected function aplicarFiltros(Builder $consulta, array $filtros): void
    {
        // Quitamos todos los valores nulos o cadenas vacías
        $filtros = array_filter(
            $filtros,
            fn($value) => !is_null($value) && $value !== ''
        );
        foreach ($filtros as $key => $value) {
            switch ($key) {
                case 'id':
                    $consulta->where('id', $value);
                    break;
                default:
                    $consulta->where($key, $value);
                    break;
            }
        }
    }
    protected function aplicarBusqueda(Builder $consulta, ?string $searchTerm, ?string $searchColumn): void
    {
        if ($searchTerm && $searchColumn) {
            switch ($searchColumn) {
                case 'id':
                    $consulta->where('id', 'like', $searchTerm);
                    break;
                default:
                    $consulta->where($searchColumn, 'like', '%' . $searchTerm . '%');
                    break;
            }
        }
    }

    protected function aplicarOrdenamiento(Builder $consulta, ?string $sortField, ?string $sortOrder): void
    {
        if ($sortField && $sortOrder) {
            switch ($sortField) {
                case 'id':
                default:
                    $consulta->orderBy($sortField, $sortOrder);
                    break;
            }
        }
    }





    
    /**
     * Obtener todas las metas de una empresa específica
     * SOLO metas de equipos que tengan coordinadores de equipo válidos de esta empresa
     */
    public function getAllByEmpresa(int $empresaId): Collection
    {
        return $this->model->with([
            'equipo.coordinador.usuario.rol',
            'equipo.area',
            'estado',
            'tareas.estado'
        ])
        // FILTRO 1: El equipo debe pertenecer a un área de esta empresa
        ->whereHas('equipo.area', function($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })
        // FILTRO 2: El equipo debe tener un coordinador válido de esta empresa
        ->whereHas('equipo.coordinador', function($query) use ($empresaId) {
            $query->whereHas('miembrosEquipo', function($miembrosQuery) use ($empresaId) {
                $miembrosQuery->where('activo', true)
                    ->whereHas('equipo.area', function($areaQuery) use ($empresaId) {
                        $areaQuery->where('empresa_id', $empresaId);
                    });
            })
            ->whereHas('usuario.rol', function($rolQuery) {
                $rolQuery->whereIn('nombre', ['Coord. Equipo', 'Coordinador de Equipo']);
            });
        })
        // FILTRO 3: El equipo no debe estar eliminado
        ->whereHas('equipo', function($query) {
            $query->whereNull('deleted_at');
        })
        ->whereNull('deleted_at')
        ->orderBy('fecha_creacion', 'desc')
        ->get();
    }

    /**
     * Crear una nueva meta
     */
    public function create(array $data): Meta
    {
        return $this->model->create([
            'equipo_id' => $data['equipo_id'],
            'estado_id' => $data['estado_id'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'fecha_creacion' => $data['fecha_creacion'] ?? now(),
            'fecha_entrega' => $data['fecha_entrega'] ?? null
        ]);
    }

    /**
     * Obtener meta por ID con relaciones
     * SOLO si pertenece a un equipo válido
     */
    public function getById(String $id): ?Meta
    {
        $meta = $this->model->with([
            'equipo.coordinador.usuario.rol',
            'equipo.area.empresa',
            'estado',
            'tareas.estado'
        ])->find($id);

        // Verificar que el equipo tenga coordinador válido para la empresa
        if ($meta && $meta->equipo && $meta->equipo->area && $meta->equipo->coordinador) {
            $empresaId = $meta->equipo->area->empresa_id;
            $esCoordinadorValido = $this->equipoTieneCoordinadorValido($meta->equipo_id, $empresaId);
            if (!$esCoordinadorValido) {
                return null; // No mostrar la meta si el equipo no tiene coordinador válido
            }
        }

        return $meta;
    }

    /**
     * Actualizar meta
     */
    public function update(int $id, array $data): bool
    {
        try {
            $meta = $this->model->find($id);
            if (!$meta) {
                return false;
            }

            $updateData = [
                'equipo_id' => $data['equipo_id'] ?? $meta->equipo_id,
                'estado_id' => $data['estado_id'] ?? $meta->estado_id,
                'nombre' => $data['nombre'] ?? $meta->nombre,
                'descripcion' => $data['descripcion'] ?? $meta->descripcion,
                'fecha_entrega' => $data['fecha_entrega'] ?? $meta->fecha_entrega
            ];

            return $meta->update($updateData);

        } catch (\Exception $e) {
            Log::error('Error al actualizar meta', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Eliminar meta (SoftDelete - solo marca como eliminada)
     */
    public function delete(int $id): bool
    {
        try {
            $meta = $this->model->with(['tareas'])->find($id);
            if (!$meta) {
                Log::warning('Meta no encontrada para eliminar', ['id' => $id]);
                return false;
            }

            Log::info('Iniciando eliminación suave de meta', [
                'id' => $id,
                'nombre' => $meta->nombre,
                'tareas_count' => $meta->tareas->count()
            ]);

            // Usar transacción para asegurar consistencia
            return DB::transaction(function () use ($meta) {
                
                // 1. Eliminar (SoftDelete) todas las tareas asociadas a la meta
                if ($meta->tareas->count() > 0) {
                    foreach ($meta->tareas as $tarea) {
                        $tarea->delete(); // SoftDelete para tareas
                    }
                    Log::info('Tareas eliminadas (SoftDelete)', ['meta_id' => $meta->id]);
                }

                // 2. Eliminar (SoftDelete) la meta
                $resultado = $meta->delete(); // SoftDelete para la meta
                Log::info('Meta eliminada (SoftDelete)', ['meta_id' => $meta->id, 'resultado' => $resultado]);

                return $resultado;
            });

        } catch (\Exception $e) {
            Log::error('Error al eliminar meta', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Obtener estados disponibles para metas
     */
    public function getEstadosDisponibles(): Collection
    {
        return Estado::whereNull('deleted_at')
                    ->orderBy('nombre')
                    ->get();
    }

    /**
     * Verificar si un equipo tiene coordinador válido para una empresa
     */
    private function equipoTieneCoordinadorValido(int $equipoId, int $empresaId): bool
    {
        return DB::table('equipos')
            ->join('areas', 'equipos.area_id', '=', 'areas.id')
            ->join('trabajadores', 'equipos.coordinador_id', '=', 'trabajadores.id')
            ->join('miembros_equipo', 'trabajadores.id', '=', 'miembros_equipo.trabajador_id')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('equipos.id', $equipoId)
            ->where('areas.empresa_id', $empresaId)
            ->where('miembros_equipo.activo', true)
            ->whereIn('roles.nombre', ['Coord. Equipo', 'Coordinador de Equipo'])
            ->whereNull('equipos.deleted_at')
            ->whereNull('areas.deleted_at')
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
            ->exists();
    }
    
 }
 