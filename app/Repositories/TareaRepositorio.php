<?php

namespace App\Repositories;

use App\Models\Tarea;
use App\Models\Trabajador;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Estado;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Str;

class TareaRepositorio extends RepositorioBase
{
    public function __construct(Tarea $model)
    {
        parent::__construct($model);
    }

    public function getTareasPorEquipo(int $equipoId)
    {
        return $this->model->with(['meta.estado', 'estado'])
            ->whereHas('meta', function ($query) use ($equipoId) {
                $query->where('equipo_id', $equipoId);
            })
            ->get();
    }

    public function todasLasTareasCompletadas(int $metaId): bool
    {
        return $this->model->where('meta_id', $metaId)
            ->whereHas('estado', function ($query) {
                $query->where('nombre', '!=', 'Completo');
            })
            ->doesntExist();
    }

    public function getTareasPorEquipoCustom($equipoId)
    {
        return $this->model->with(['meta', 'estado'])
            ->whereHas('meta', function ($q) use ($equipoId) {
                $q->where('equipo_id', $equipoId);
            })
            ->get()
            ->map(function ($tarea) {
                return [
                    'id' => $tarea->id,
                    'titulo' => $tarea->nombre,
                    'descripcion' => $tarea->descripcion,
                    'fecha_limite' => $tarea->fecha_entrega,
                    'estado_slug' => Str::slug($tarea->estado->nombre),
                    'meta' => [
                        'id' => $tarea->meta->id,
                        'titulo' => $tarea->meta->nombre,
                    ],
                ];
            })
            ->values(); // ← limpia keys si las hay
    }

    public function getTareasCompletadasPorEquipo(int $equipoId)
    {
        return $this->model
            ->whereHas('estado', function ($query) {
                $query->where('nombre', 'Completo');
            })
            ->whereHas('meta', function ($query) use ($equipoId) {
                $query->where('equipo_id', $equipoId);
            })
            ->get();
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
                    $consulta->where('empresas.id', $value);
                    break;
                case 'plan_servicio_id':
                    $consulta->where('empresas.plan_servicio_id', $value);
                    break;
                case 'estado':
                    $this->aplicarJoinCondicional($consulta, 'usuarios', 'usuario_id', '=', 'usuarios.id');
                    $consulta->where('usuarios.activo', $value);
                    break;
                default:
                    $consulta->where($key, $value);
                    break;
            }
        }
    }

    protected function aplicarBusqueda(Builder $consulta, ?string $searchTerm, ?string $searchColumn): void
    {
        // Si no hay columna de búsqueda, ponemos la columna por defecto
        if (!$searchColumn) {
            $searchColumn = 'nombre'; // Columna por defecto para búsqueda
        }
        if ($searchTerm && $searchColumn) {
            switch ($searchColumn) {
                case 'id':
                    $consulta->where('empresas.id', 'like', $searchTerm);
                    break;
                case 'nombre':
                    $consulta->where('empresas.nombre', 'like', '%' . $searchTerm . '%');
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
                    $consulta->orderBy('empresa.id', $sortOrder);
                    break;
                case 'nombre':
                    $consulta->orderBy('empresa.nombre', $sortOrder);
                    break;
                default:
                    $consulta->orderBy($sortField, $sortOrder);
                    break;
            }
        }
    }






    /**
     * Obtener tareas por áreas específicas
     * SOLO tareas de metas que pertenezcan a equipos válidos (con coordinador de equipo)
     */
    public function getTareasByAreas(array $areaIds): Collection
    {
        return $this->model->with([
            'meta.equipo.coordinador.usuario.rol',
            'meta.equipo.area',
            'meta.equipo.miembros' => function($query) {
                $query->where('activo', true);
            },
            'meta.equipo.miembros.trabajador.usuario.rol',
            'meta.estado',
            'estado'
        ])
        // FILTRO 1: La meta debe pertenecer a un equipo de las áreas especificadas
        ->whereHas('meta.equipo', function($query) use ($areaIds) {
            $query->whereIn('area_id', $areaIds)
                  ->whereNull('deleted_at');
        })
        // FILTRO 2: El equipo debe tener al menos un coordinador de equipo válido
        ->whereHas('meta.equipo.miembros', function($query) {
            $query->where('activo', true)
                  ->whereHas('trabajador.usuario.rol', function($rolQuery) {
                      $rolQuery->where('nombre', 'Coord. Equipo');
                  });
        })
        // FILTRO 3: La meta no debe estar eliminada
        ->whereHas('meta', function($query) {
            $query->whereNull('deleted_at');
        })
        ->whereNull('deleted_at')
        ->orderBy('fecha_creacion', 'desc')
        ->get();
    }

    /**
     * Verificar si una tarea pertenece a las áreas de un coordinador general
     */
    public function tareaPerteneceeACoordinadorGeneral(int $tareaId, int $trabajadorId): bool
    {
        try {
            // Primero verificamos si el trabajador existe
            $trabajador = Trabajador::find($trabajadorId);
            if (!$trabajador) {
                return false;
            }

            // Obtenemos la tarea
            $tarea = $this->model->with(['meta.equipo.area'])->find($tareaId);
            if (!$tarea || !$tarea->meta || !$tarea->meta->equipo || !$tarea->meta->equipo->area) {
                return false;
            }

            // Verificar que el equipo tenga al menos un coordinador de equipo
            $tieneCoordinadorEquipo = $this->equipoTieneCoordinadorEquipo($tarea->meta->equipo_id);
            if (!$tieneCoordinadorEquipo) {
                return false;
            }

            // Verificamos si hay registros en areas_coordinador
            $areasCoordinador = DB::table('areas_coordinador')
                ->where('trabajador_id', $trabajadorId)
                ->whereNull('deleted_at')
                ->count();

            // Si no hay registros, verificamos si la tarea pertenece a la empresa
            if ($areasCoordinador === 0) {
                return $tarea->meta->equipo->area->empresa_id === $trabajador->empresa_id;
            }

            // Si hay registros, verificamos si la tarea está en un área asignada al coordinador
            return DB::table('areas_coordinador')
                ->where('area_id', $tarea->meta->equipo->area_id)
                ->where('trabajador_id', $trabajadorId)
                ->whereNull('deleted_at')
                ->where(function($query) {
                    $query->whereNull('fecha_fin')
                      ->orWhere('fecha_fin', '>', now());
            })
                ->exists();
        } catch (\Exception $e) {
            Log::error('Error al verificar si la tarea pertenece al coordinador', [
                'tarea_id' => $tareaId,
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
     * Crear una nueva tarea
     */
    public function create(array $data): Tarea
    {
        return $this->model->create([
            'meta_id' => $data['meta_id'],
            'estado_id' => $data['estado_id'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'fecha_creacion' => $data['fecha_creacion'] ?? now(),
            'fecha_entrega' => $data['fecha_entrega'] ?? null
        ]);
    }

    /**
     * Obtener tarea por ID con relaciones
     * SOLO si pertenece a un equipo válido
     */
    public function getById(String $id): ?Tarea
    {
        $tarea = $this->model->with([
            'meta.equipo.coordinador.usuario.rol',
            'meta.equipo.area.empresa',
            'meta.equipo.miembros' => function($query) {
                $query->where('activo', true);
            },
            'meta.equipo.miembros.trabajador.usuario.rol',
            'meta.estado',
            'estado'
        ])
        ->whereNull('deleted_at')
        ->find($id);

        // Verificar que el equipo tenga coordinador válido
        if ($tarea && $tarea->meta && $tarea->meta->equipo && !$this->equipoTieneCoordinadorEquipo($tarea->meta->equipo_id)) {
            Log::warning('Tarea con equipo sin coordinador de equipo válido', ['tarea_id' => $id]);
            return null;
        }

        return $tarea;
    }

    /**
     * Actualizar tarea
     */
    public function update(int $id, array $data): bool
    {
        try {
            $tarea = $this->model->find($id);
            if (!$tarea) {
                return false;
            }

            // Verificar que la meta tenga equipo válido antes de actualizar
            if (isset($data['meta_id'])) {
                $meta = DB::table('metas')
                    ->join('equipos', 'metas.equipo_id', '=', 'equipos.id')
                    ->where('metas.id', $data['meta_id'])
                    ->whereNull('metas.deleted_at')
                    ->whereNull('equipos.deleted_at')
                    ->first();
                
                if (!$meta || !$this->equipoTieneCoordinadorEquipo($meta->equipo_id)) {
                    Log::error('No se puede asignar tarea a meta con equipo sin coordinador de equipo', ['meta_id' => $data['meta_id']]);
                    return false;
                }
            }

            $updateData = [
                'meta_id' => $data['meta_id'] ?? $tarea->meta_id,
                'estado_id' => $data['estado_id'] ?? $tarea->estado_id,
                'nombre' => $data['nombre'] ?? $tarea->nombre,
                'descripcion' => $data['descripcion'] ?? $tarea->descripcion,
                'fecha_entrega' => $data['fecha_entrega'] ?? $tarea->fecha_entrega
            ];

            return $tarea->update($updateData);

        } catch (\Exception $e) {
            Log::error('Error al actualizar tarea', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Eliminar tarea (SoftDelete)
     */
    public function delete(int $id): bool
    {
        try {
            $tarea = $this->model->find($id);
            if (!$tarea) {
                Log::warning('Tarea no encontrada para eliminar', ['id' => $id]);
                return false;
            }

            Log::info('Eliminando tarea (SoftDelete)', [
                'id' => $id,
                'nombre' => $tarea->nombre
            ]);

            return $tarea->delete(); // SoftDelete

        } catch (\Exception $e) {
            Log::error('Error al eliminar tarea', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Obtener estados disponibles para tareas
     */
    public function getEstadosDisponibles(): Collection
    {
        return Estado::whereNull('deleted_at')
                    ->orderBy('nombre')
                    ->get();
    }

    /**
     * Buscar tareas por nombre en las áreas del coordinador
     * SOLO tareas de equipos con coordinadores de equipo válidos
     */
    public function buscarPorNombre(string $nombre, array $areaIds): Collection
    {
        return $this->model->with([
            'meta.equipo.coordinador.usuario.rol',
            'meta.equipo.area',
            'meta.estado',
            'estado'
        ])
        ->where('nombre', 'LIKE', "%{$nombre}%")
        ->whereHas('meta.equipo', function($query) use ($areaIds) {
            $query->whereIn('area_id', $areaIds)
                  ->whereNull('deleted_at');
        })
        // FILTRO: Solo tareas de equipos que tengan al menos un coordinador de equipo
        ->whereHas('meta.equipo.miembros', function($query) {
            $query->where('activo', true)
                  ->whereHas('trabajador.usuario.rol', function($rolQuery) {
                      $rolQuery->where('nombre', 'Coord. Equipo');
                  });
        })
        ->whereHas('meta', function($query) {
            $query->whereNull('deleted_at');
        })
        ->whereNull('deleted_at')
        ->get();
    }

    /**
     * Obtener estadísticas de tareas por áreas
     * SOLO tareas de equipos con coordinadores de equipo válidos
     */
    public function getEstadisticasPorAreas(array $areaIds): array
    {
        $tareas = $this->getTareasByAreas($areaIds);
        
        $total = $tareas->count();
        $completadas = $tareas->where('estado.nombre', 'Completo')->count();
        $enProceso = $tareas->where('estado.nombre', 'En proceso')->count();
        $pendientes = $tareas->where('estado.nombre', 'Incompleta')->count();
        $suspendidas = $tareas->where('estado.nombre', 'Suspendida')->count();

        return [
            'total' => $total,
            'completadas' => $completadas,
            'en_proceso' => $enProceso,
            'pendientes' => $pendientes,
            'suspendidas' => $suspendidas,
            'vencidas' => $tareas->where('esta_vencida', true)->count()
        ];
    }

    /**
     * Obtener tareas sin equipos válidos (para limpieza)
     */
    public function getTareasSinEquipoValido(int $empresaId): Collection
    {
        return $this->model->select('tareas.*')
            ->join('metas', 'tareas.meta_id', '=', 'metas.id')
            ->join('equipos', 'metas.equipo_id', '=', 'equipos.id')
            ->join('areas', 'equipos.area_id', '=', 'areas.id')
            ->where('areas.empresa_id', $empresaId)
            ->whereNull('tareas.deleted_at')
            ->whereDoesntHave('meta.equipo.miembros', function($query) {
                $query->where('activo', true)
                      ->whereHas('trabajador.usuario.rol', function($rolQuery) {
                          $rolQuery->where('nombre', 'Coord. Equipo');
                      });
            })
            ->get();
    }

}