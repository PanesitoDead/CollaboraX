<?php

namespace App\Repositories;

use App\Models\Equipo;
use App\Models\Area;
use App\Models\Trabajador;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class EquipoRepositorio
{
    protected $model;

    public function __construct(Equipo $model)
    {
        $this->model = $model;
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
    public function getById(int $id): ?Equipo
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

            if ($result) {
                Log::info('Equipo actualizado exitosamente', ['equipo_actualizado' => $equipo->fresh()->toArray()]);
            }

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
     * Eliminar equipo (soft delete)
     */
    public function delete(int $id): bool
    {
        $equipo = $this->model->find($id);
        if (!$equipo) {
            return false;
        }

        return $equipo->delete();
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
        return Trabajador::with('usuario')
            ->whereHas('usuario', function($query) {
                $query->whereHas('rol', function($roleQuery) {
                    $roleQuery->whereIn('nombre', ['Coord. General', 'Coord. Equipo']);
                })
                ->where('activo', true)
                ->whereNull('deleted_at');
            })
            ->whereHas('usuario.empresa', function($query) use ($empresaId) {
                $query->where('empresas.id', $empresaId);
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
