<?php

namespace App\Repositories;
use App\Models\Area;
use App\Models\Trabajador;
use Illuminate\Support\Facades\Log;
use App\Models\Equipo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
class EquipoRepositorio extends RepositorioBase
{
    
    public function __construct(Equipo $model)
    {
        parent::__construct($model);
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
     * Obtener todos los equipos de una empresa específica con sus relaciones
     */
    public function getAllByEmpresa(int $empresaId): Collection
    {
        return $this->model->with([
            'coordinador.usuario',
            'area',
            'miembros' => function($query) {
                $query->where('activo', true);
            },
            'miembros.trabajador.usuario',
            'metas.estado'
        ])
        ->whereHas('area', function($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })
        ->whereNull('deleted_at')
        ->orderBy('fecha_creacion', 'desc')
        ->get();
    }

    /**
     * Obtener equipos por área
     */
    public function getByArea(int $areaId): Collection
    {
        return $this->model->with([
            'coordinador.usuario',
            'area',
            'miembros' => function($query) {
                $query->where('activo', true);
            },
            'miembros.trabajador.usuario',
            'metas.estado'
        ])
        ->where('area_id', $areaId)
        ->whereNull('deleted_at')
        ->get();
    }

    /**
     * Crear un nuevo equipo
     */
    public function create(array $data): Equipo
    {
        return $this->model->create([
            'coordinador_id' => $data['coordinador_id'],
            'area_id' => $data['area_id'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'fecha_creacion' => now()
        ]);
    }

    /**
     * Obtener equipo por ID con relaciones
     */
    public function getById(String $id): ?Equipo
    {
        return $this->model->with([
            'coordinador.usuario',
            'area.empresa',
            'miembros' => function($query) {
                $query->where('activo', true);
            },
            'miembros.trabajador.usuario',
            'metas.estado',
            'reuniones'
        ])->find($id);
    }

    /**
     * Actualizar equipo
     */
    public function update(int $id, array $data): bool
    {
        try {
            Log::info('Intentando actualizar equipo', ['id' => $id, 'data' => $data]);
            
            $equipo = $this->model->find($id);
            if (!$equipo) {
                Log::error('Equipo no encontrado', ['id' => $id]);
                return false;
            }

            Log::info('Equipo encontrado', ['equipo' => $equipo->toArray()]);

            $updateData = [
                'coordinador_id' => $data['coordinador_id'] ?? $equipo->coordinador_id,
                'area_id' => $data['area_id'] ?? $equipo->area_id,
                'nombre' => $data['nombre'] ?? $equipo->nombre,
                'descripcion' => $data['descripcion'] ?? $equipo->descripcion
            ];

            Log::info('Datos para actualizar', ['updateData' => $updateData]);

            $result = $equipo->update($updateData);
            
            Log::info('Resultado de actualización', ['result' => $result]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Error al actualizar equipo', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Eliminar equipo (eliminación permanente)
     */
    public function delete(int $id): bool
    {
        try {
            $equipo = $this->model->find($id);
            if (!$equipo) {
                return false;
            }

            // Primero eliminamos los miembros del equipo
            $equipo->miembros()->forceDelete();
            
            // Luego eliminamos el equipo permanentemente
            return $equipo->forceDelete();
        } catch (\Exception $e) {
            Log::error('Error al eliminar equipo', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Obtener áreas disponibles para una empresa
     */
    public function getAreasDisponibles(int $empresaId): Collection
    {
        return Area::where('empresa_id', $empresaId)
                  ->where('activo', true)
                  ->whereNull('deleted_at')
                  ->orderBy('nombre')
                  ->get();
    }

    /**
     * Obtener coordinadores disponibles para una empresa
     */
    public function getCoordinadoresDisponibles(int $empresaId): Collection
    {
        return Trabajador::with('usuario.rol')
            ->whereHas('usuario', function($query) {
                $query->whereHas('rol', function($roleQuery) {
                    $roleQuery->whereIn('nombre', ['Coord. General', 'Coord. Equipo', 'Coordinador General', 'Coordinador de Equipo']);
                })
                ->where('activo', true)
                ->whereNull('deleted_at');
            })
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * Buscar equipos por nombre
     */
    public function buscarPorNombre(string $nombre, int $empresaId): Collection
    {
        return $this->model->with([
            'coordinador.usuario',
            'area',
            'miembros' => function($query) {
                $query->where('activo', true);
            }
        ])
        ->where('nombre', 'LIKE', "%{$nombre}%")
        ->whereHas('area', function($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })
        ->whereNull('deleted_at')
        ->get();
    }

    /**
     * Obtener estadísticas de equipos por empresa
     */
    public function getEstadisticas(int $empresaId): array
    {
        $equipos = $this->getAllByEmpresa($empresaId);
        
        $total = $equipos->count();
        $activos = $equipos->where('deleted_at', null)->count();
        
        // Calcular equipos con metas en progreso
        $conMetasActivas = $equipos->filter(function($equipo) {
            return $equipo->metas->where('estado.nombre', '!=', 'Completo')->count() > 0;
        })->count();

        return [
            'total' => $total,
            'activos' => $activos,
            'con_metas_activas' => $conMetasActivas,
            'promedio_miembros' => $total > 0 ? round($equipos->sum(function($equipo) {
                return $equipo->miembros->count();
            }) / $total, 1) : 0
        ];
    }

    /**
     * Agregar miembro a equipo
     */
    public function agregarMiembro(int $equipoId, int $trabajadorId): bool
    {
        $equipo = $this->model->find($equipoId);
        if (!$equipo) {
            return false;
        }

        // Verificar si ya es miembro
        $yaEsMiembro = $equipo->miembros()
            ->where('trabajador_id', $trabajadorId)
            ->where('activo', true)
            ->exists();

        if ($yaEsMiembro) {
            return false;
        }

        $equipo->miembros()->create([
            'trabajador_id' => $trabajadorId,
            'activo' => true,
            'fecha_union' => now()
        ]);

        return true;
    }

    /**
     * Remover miembro de equipo
     */
    public function removerMiembro(int $equipoId, int $trabajadorId): bool
    {
        $equipo = $this->model->find($equipoId);
        if (!$equipo) {
            return false;
        }

        return $equipo->miembros()
            ->where('trabajador_id', $trabajadorId)
            ->update(['activo' => false]);
    }

    


}
