<?php

namespace App\Repositories;

use App\Models\Tarea;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Estado;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TareaRepositorio extends RepositorioBase
{
    public function __construct(Tarea $model)
    {
        parent::__construct($model);
    }

    public function getTareasPorEquipo(int $equipoId)
    {
        return $this->model
            ->whereHas('meta', function ($query) use ($equipoId) {
                $query->where('equipo_id', $equipoId);
            })
            ->get();
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
     * Obtener todas las tareas de una empresa específica
     * SOLO tareas de metas que pertenezcan a equipos válidos de esta empresa
     */
    public function getAllByEmpresa(int $empresaId): Collection
    {
        return $this->model->with([
            'meta.equipo.coordinador.usuario.rol',
            'meta.equipo.area',
            'estado'
        ])
        // FILTRO 1: La meta debe pertenecer a un equipo de un área de esta empresa
        ->whereHas('meta.equipo.area', function($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })
        // FILTRO 2: El equipo debe tener un coordinador válido de esta empresa
        ->whereHas('meta.equipo.coordinador', function($query) use ($empresaId) {
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
        // FILTRO 3: El equipo y la meta no deben estar eliminados
        ->whereHas('meta.equipo', function($query) {
            $query->whereNull('deleted_at');
        })
        ->whereHas('meta', function($query) {
            $query->whereNull('deleted_at');
        })
        ->whereNull('deleted_at')
        ->orderBy('fecha_creacion', 'desc')
        ->get();
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
            'estado'
        ])->find($id);

        // Verificar que el equipo tenga coordinador válido para la empresa
        if ($tarea && $tarea->meta && $tarea->meta->equipo && $tarea->meta->equipo->area && $tarea->meta->equipo->coordinador) {
            $empresaId = $tarea->meta->equipo->area->empresa_id;
            $esCoordinadorValido = $this->equipoTieneCoordinadorValido($tarea->meta->equipo->id, $empresaId);
            if (!$esCoordinadorValido) {
                return null; // No mostrar la tarea si el equipo no tiene coordinador válido
            }
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

            Log::info('Eliminando tarea', [
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