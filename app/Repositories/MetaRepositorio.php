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
        return $this->model->where('equipo_id', $equipo)->with(['tareas.estado', 'estado'])->get();
    }

    public function getMetasPorEquipoCustom($equipoId)
    {
        return $this->model->where('equipo_id', $equipoId)
            ->get()
            ->map(function ($meta) {
                return [
                    'id' => $meta->id,
                    'titulo' => $meta->nombre,
                ];
            });
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
            $meta->tareas_totales = $tareasTotales;
            $meta->tareas_completadas = $tareasCompletadas;

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
     * Obtener metas por áreas específicas
     * SOLO metas de equipos que tengan al menos un coordinador de equipo válido
     */
    public function getMetasByAreas(array $areaIds): Collection
    {
        return $this->model->with([
            'equipo.coordinador.usuario.rol',
            'equipo.area',
            'equipo.miembros' => function($query) {
                $query->where('activo', true);
            },
            'equipo.miembros.trabajador.usuario.rol',
            'estado',
            'tareas.estado'
        ])
        // FILTRO 1: El equipo debe pertenecer a las áreas especificadas
        ->whereHas('equipo', function($query) use ($areaIds) {
            $query->whereIn('area_id', $areaIds)
                  ->whereNull('deleted_at');
        })
        // FILTRO 2: El equipo debe tener al menos un coordinador de equipo válido
        ->whereHas('equipo.miembros', function($query) {
            $query->where('activo', true)
                  ->whereHas('trabajador.usuario.rol', function($rolQuery) {
                      $rolQuery->where('nombre', 'Coord. Equipo');
                  });
        })
        ->whereNull('deleted_at')
        ->orderBy('fecha_creacion', 'desc')
        ->get();
    }

    /**
     * Obtener metas por equipo (mejorado con validaciones)
     */
    public function getByEquipo(int $equipoId): Collection
    {
        try {
            Log::info('MetaRepositorio::getByEquipo iniciado', ['equipo_id' => $equipoId]);
        
            $metas = $this->model->with(['estado', 'tareas.estado'])
                ->where('equipo_id', $equipoId)
                ->whereNull('deleted_at')
                ->orderBy('fecha_creacion', 'desc')
                ->get();
            
            Log::info('MetaRepositorio::getByEquipo completado', [
                'equipo_id' => $equipoId,
                'metas_encontradas' => $metas->count(),
                'metas_ids' => $metas->pluck('id')->toArray()
            ]);
        
            return $metas;
        
        } catch (\Exception $e) {
            Log::error('Error en MetaRepositorio::getByEquipo', [
                'equipo_id' => $equipoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]); // Devolver colección vacía en caso de error
        }
    }

    /**
     * Verificar si una meta pertenece a las áreas de un coordinador general
     */
    public function metaPerteneceeACoordinadorGeneral(int $metaId, int $trabajadorId): bool
    {
        try {
            // Primero verificamos si el trabajador existe
            $trabajador = Trabajador::find($trabajadorId);
            if (!$trabajador) {
                return false;
            }

            // Obtenemos la meta
            $meta = $this->model->with(['equipo.area'])->find($metaId);
            if (!$meta || !$meta->equipo || !$meta->equipo->area) {
                return false;
            }

            // Verificar que el equipo tenga al menos un coordinador de equipo
            $tieneCoordinadorEquipo = $this->equipoTieneCoordinadorEquipo($meta->equipo_id);
            if (!$tieneCoordinadorEquipo) {
                return false;
            }

            // Verificamos si hay registros en areas_coordinador
            $areasCoordinador = DB::table('areas_coordinador')
                ->where('trabajador_id', $trabajadorId)
                ->whereNull('deleted_at')
                ->count();

            // Si no hay registros, verificamos si la meta pertenece a la empresa
            if ($areasCoordinador === 0) {
                return $meta->equipo->area->empresa_id === $trabajador->empresa_id;
            }

            // Si hay registros, verificamos si la meta está en un área asignada al coordinador
            return DB::table('areas_coordinador')
                ->where('area_id', $meta->equipo->area_id)
                ->where('trabajador_id', $trabajadorId)
                ->whereNull('deleted_at')
                ->where(function($query) {
                    $query->whereNull('fecha_fin')
                      ->orWhere('fecha_fin', '>', now());
            })
                ->exists();
        } catch (\Exception $e) {
            Log::error('Error al verificar si la meta pertenece al coordinador', [
                'meta_id' => $metaId,
                'trabajador_id' => $trabajadorId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar si un equipo tiene al menos un coordinador de equipo
     */
    private function equipoTieneCoordinadorEquipo(int $equipoId): bool
    {
        return DB::table('miembros_equipo')
            ->join('trabajadores', 'miembros_equipo.trabajador_id', '=', 'trabajadores.id')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('miembros_equipo.equipo_id', $equipoId)
            ->where('miembros_equipo.activo', true)
            ->where('roles.nombre', 'Coord. Equipo')
            ->whereNull('miembros_equipo.deleted_at')
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
            ->exists();
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
            'equipo.miembros' => function($query) {
                $query->where('activo', true);
            },
            'equipo.miembros.trabajador.usuario.rol',
            'estado',
            'tareas.estado'
        ])
        ->whereNull('deleted_at')
        ->find($id);

        // Verificar que el equipo tenga coordinador válido
        if ($meta && $meta->equipo && !$this->equipoTieneCoordinadorEquipo($meta->equipo_id)) {
            Log::warning('Meta con equipo sin coordinador de equipo válido', ['meta_id' => $id]);
            return null;
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

            // Verificar que el equipo tenga coordinador válido antes de actualizar
            if (isset($data['equipo_id']) && !$this->equipoTieneCoordinadorEquipo($data['equipo_id'])) {
                Log::error('No se puede asignar meta a equipo sin coordinador de equipo', ['equipo_id' => $data['equipo_id']]);
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
     * Buscar metas por nombre en las áreas del coordinador
     * SOLO metas de equipos con coordinadores de equipo válidos
     */
    public function buscarPorNombre(string $nombre, array $areaIds): Collection
    {
        return $this->model->with([
            'equipo.coordinador.usuario.rol',
            'equipo.area',
            'estado',
            'tareas.estado'
        ])
        ->where('nombre', 'LIKE', "%{$nombre}%")
        ->whereHas('equipo', function($query) use ($areaIds) {
            $query->whereIn('area_id', $areaIds)
                  ->whereNull('deleted_at');
        })
        // FILTRO: Solo metas de equipos que tengan al menos un coordinador de equipo
        ->whereHas('equipo.miembros', function($query) {
            $query->where('activo', true)
                  ->whereHas('trabajador.usuario.rol', function($rolQuery) {
                      $rolQuery->where('nombre', 'Coord. Equipo');
                  });
        })
        ->whereNull('deleted_at')
        ->get();
    }

    /**
     * Obtener estadísticas de metas por áreas
     * SOLO metas de equipos con coordinadores de equipo válidos
     */
    public function getEstadisticasPorAreas(array $areaIds): array
    {
        $metas = $this->getMetasByAreas($areaIds);
        
        $total = $metas->count();
        $completadas = $metas->where('estado.nombre', 'Completo')->count();
        $enProceso = $metas->where('estado.nombre', 'En proceso')->count();
        $pendientes = $metas->where('estado.nombre', 'Incompleta')->count();

        return [
            'total' => $total,
            'completadas' => $completadas,
            'en_proceso' => $enProceso,
            'pendientes' => $pendientes,
            'promedio_progreso' => $total > 0 ? round($metas->avg('progreso'), 1) : 0
        ];
    }

    /**
     * Obtener metas sin equipos válidos (para limpieza)
     */
    public function getMetasSinEquipoValido(int $empresaId): Collection
    {
        return $this->model->select('metas.*')
            ->join('equipos', 'metas.equipo_id', '=', 'equipos.id')
            ->join('areas', 'equipos.area_id', '=', 'areas.id')
            ->where('areas.empresa_id', $empresaId)
            ->whereNull('metas.deleted_at')
            ->whereDoesntHave('equipo.miembros', function($query) {
                $query->where('activo', true)
                      ->whereHas('trabajador.usuario.rol', function($rolQuery) {
                          $rolQuery->where('nombre', 'Coord. Equipo');
                      });
            })
            ->get();
    }
    
 }
 