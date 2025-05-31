<?php

namespace App\Repositories;
use App\Models\Area;
use App\Models\Trabajador;
use Illuminate\Support\Facades\Log;
use App\Models\Equipo;
use Illuminate\Support\Facades\DB;
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
     * SOLO equipos que tengan al menos un coordinador de equipo válido
     */
    public function getAllByEmpresa(int $empresaId): Collection
    {
        return $this->model->with([
            'coordinador.usuario.rol',
            'area',
            'miembros' => function($query) {
                $query->where('activo', true);
            },
            'miembros.trabajador.usuario.rol',
            'metas.estado'
        ])
        ->whereHas('area', function($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })
        // FILTRO CLAVE: Solo equipos donde el coordinador sea realmente un coordinador de equipo de esta empresa
        ->whereHas('coordinador', function($query) use ($empresaId) {
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
        ->whereNull('deleted_at')
        ->orderBy('fecha_creacion', 'desc')
        ->get();
    }

    /**
     * Obtener equipos por área
     * SOLO equipos que tengan coordinadores válidos
     */
    public function getByArea(int $areaId): Collection
    {
        // Primero obtenemos la empresa del área
        $area = Area::find($areaId);
        if (!$area) {
            return collect([]);
        }

        return $this->model->with([
            'coordinador.usuario.rol',
            'area',
            'miembros' => function($query) {
                $query->where('activo', true);
            },
            'miembros.trabajador.usuario.rol',
            'metas.estado'
        ])
        ->where('area_id', $areaId)
        // FILTRO: Solo equipos con coordinadores válidos de esta empresa
        ->whereHas('coordinador', function($query) use ($area) {
            $query->whereHas('miembrosEquipo', function($miembrosQuery) use ($area) {
                $miembrosQuery->where('activo', true)
                    ->whereHas('equipo.area', function($areaQuery) use ($area) {
                        $areaQuery->where('empresa_id', $area->empresa_id);
                    });
            })
            ->whereHas('usuario.rol', function($rolQuery) {
                $rolQuery->whereIn('nombre', ['Coord. Equipo', 'Coordinador de Equipo']);
            });
        })
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
     * SOLO si tiene coordinador válido
     */
    public function getById(String $id): ?Equipo
    {
        $equipo = $this->model->with([
            'coordinador.usuario.rol',
            'area.empresa',
            'miembros' => function($query) {
                $query->where('activo', true);
            },
            'miembros.trabajador.usuario.rol',
            'metas.estado',
            'reuniones'
        ])->find($id);

        // Verificar que el coordinador sea válido para la empresa
        if ($equipo && $equipo->area && $equipo->coordinador) {
            $esCoordinadorValido = $this->esCoordinadorDeEquipo($equipo->coordinador_id, $equipo->area->empresa_id);
            if (!$esCoordinadorValido) {
                return null; // No mostrar el equipo si el coordinador no es válido
            }
        }

        return $equipo;
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
     * Eliminar equipo (eliminación permanente con manejo de dependencias)
     */
    public function delete(int $id): bool
    {
        try {
            $equipo = $this->model->with(['metas', 'miembros', 'reuniones', 'invitaciones'])->find($id);
            if (!$equipo) {
                Log::warning('Equipo no encontrado para eliminar', ['id' => $id]);
                return false;
            }

            Log::info('Iniciando eliminación de equipo', [
                'id' => $id,
                'nombre' => $equipo->nombre,
                'metas_count' => $equipo->metas->count(),
                'miembros_count' => $equipo->miembros->count()
            ]);

            // Usar transacción para asegurar consistencia
            return DB::transaction(function () use ($equipo) {
                
                // 1. Eliminar todas las tareas asociadas a las metas del equipo
                if ($equipo->metas->count() > 0) {
                    foreach ($equipo->metas as $meta) {
                        // Si hay tareas asociadas a las metas, eliminarlas primero
                        if (method_exists($meta, 'tareas')) {
                            $meta->tareas()->forceDelete();
                        }
                    }
                    
                    // 2. Eliminar todas las metas del equipo
                    $equipo->metas()->forceDelete();
                    Log::info('Metas eliminadas', ['equipo_id' => $equipo->id]);
                }

                // 3. Eliminar todas las invitaciones del equipo
                if (method_exists($equipo, 'invitaciones') && $equipo->invitaciones->count() > 0) {
                    $equipo->invitaciones()->forceDelete();
                    Log::info('Invitaciones eliminadas', ['equipo_id' => $equipo->id]);
                }

                // 4. Eliminar todas las reuniones del equipo
                if (method_exists($equipo, 'reuniones') && $equipo->reuniones->count() > 0) {
                    $equipo->reuniones()->forceDelete();
                    Log::info('Reuniones eliminadas', ['equipo_id' => $equipo->id]);
                }

                // 5. Eliminar todos los miembros del equipo
                if ($equipo->miembros->count() > 0) {
                    $equipo->miembros()->forceDelete();
                    Log::info('Miembros eliminados', ['equipo_id' => $equipo->id]);
                }

                // 6. Finalmente eliminar el equipo
                $resultado = $equipo->forceDelete();
                Log::info('Equipo eliminado', ['equipo_id' => $equipo->id, 'resultado' => $resultado]);

                return $resultado;
            });

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
     * Obtener SOLO coordinadores de equipo disponibles para una empresa
     * Solo trabajadores con rol "Coordinador de Equipo" o "Coord. Equipo"
     */
    public function getCoordinadoresDisponibles(int $empresaId): Collection
    {
        return Trabajador::select('trabajadores.*')
            ->join('miembros_equipo', 'trabajadores.id', '=', 'miembros_equipo.trabajador_id')
            ->join('equipos', 'miembros_equipo.equipo_id', '=', 'equipos.id')
            ->join('areas', 'equipos.area_id', '=', 'areas.id')
            ->join('empresas', 'areas.empresa_id', '=', 'empresas.id')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('empresas.id', $empresaId)
            ->where('usuarios.activo', true)
            ->where('miembros_equipo.activo', true)
            // SOLO coordinadores de equipo
            ->whereIn('roles.nombre', ['Coord. Equipo', 'Coordinador de Equipo'])
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
            ->whereNull('equipos.deleted_at')
            ->whereNull('areas.deleted_at')
            ->with(['usuario.rol'])
            ->distinct()
            ->get();
    }

    /**
     * Obtener SOLO colaboradores disponibles para una empresa
     * Solo trabajadores con rol "Colaborador"
     */
    public function getColaboradoresDisponibles(int $empresaId): Collection
    {
        return Trabajador::select('trabajadores.*')
            ->join('miembros_equipo', 'trabajadores.id', '=', 'miembros_equipo.trabajador_id')
            ->join('equipos', 'miembros_equipo.equipo_id', '=', 'equipos.id')
            ->join('areas', 'equipos.area_id', '=', 'areas.id')
            ->join('empresas', 'areas.empresa_id', '=', 'empresas.id')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('empresas.id', $empresaId)
            ->where('usuarios.activo', true)
            ->where('miembros_equipo.activo', true)
            // SOLO colaboradores
            ->where('roles.nombre', 'Colaborador')
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
            ->whereNull('equipos.deleted_at')
            ->whereNull('areas.deleted_at')
            ->with(['usuario.rol'])
            ->distinct()
            ->get();
    }

    /**
     * Buscar equipos por nombre
     * SOLO equipos con coordinadores válidos
     */
    public function buscarPorNombre(string $nombre, int $empresaId): Collection
    {
        return $this->model->with([
            'coordinador.usuario.rol',
            'area',
            'miembros' => function($query) {
                $query->where('activo', true);
            }
        ])
        ->where('nombre', 'LIKE', "%{$nombre}%")
        ->whereHas('area', function($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })
        // FILTRO: Solo equipos con coordinadores válidos
        ->whereHas('coordinador', function($query) use ($empresaId) {
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
        ->whereNull('deleted_at')
        ->get();
    }

    /**
     * Obtener estadísticas de equipos por empresa
     * SOLO equipos válidos
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
     * Verificar si un trabajador pertenece a una empresa específica
     */
    public function trabajadorPerteneceAEmpresa(int $trabajadorId, int $empresaId): bool
    {
        return Trabajador::select('trabajadores.id')
            ->join('miembros_equipo', 'trabajadores.id', '=', 'miembros_equipo.trabajador_id')
            ->join('equipos', 'miembros_equipo.equipo_id', '=', 'equipos.id')
            ->join('areas', 'equipos.area_id', '=', 'areas.id')
            ->join('empresas', 'areas.empresa_id', '=', 'empresas.id')
            ->where('trabajadores.id', $trabajadorId)
            ->where('empresas.id', $empresaId)
            ->where('miembros_equipo.activo', true)
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('equipos.deleted_at')
            ->whereNull('areas.deleted_at')
            ->exists();
    }

    /**
     * Verificar si un trabajador es coordinador de equipo
     */
    public function esCoordinadorDeEquipo(int $trabajadorId, int $empresaId): bool
    {
        return Trabajador::select('trabajadores.id')
            ->join('miembros_equipo', 'trabajadores.id', '=', 'miembros_equipo.trabajador_id')
            ->join('equipos', 'miembros_equipo.equipo_id', '=', 'equipos.id')
            ->join('areas', 'equipos.area_id', '=', 'areas.id')
            ->join('empresas', 'areas.empresa_id', '=', 'empresas.id')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('trabajadores.id', $trabajadorId)
            ->where('empresas.id', $empresaId)
            ->where('miembros_equipo.activo', true)
            ->whereIn('roles.nombre', ['Coord. Equipo', 'Coordinador de Equipo'])
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('equipos.deleted_at')
            ->whereNull('areas.deleted_at')
            ->exists();
    }

    /**
     * Verificar si un trabajador es colaborador
     */
    public function esColaborador(int $trabajadorId, int $empresaId): bool
    {
        return Trabajador::select('trabajadores.id')
            ->join('miembros_equipo', 'trabajadores.id', '=', 'miembros_equipo.trabajador_id')
            ->join('equipos', 'miembros_equipo.equipo_id', '=', 'equipos.id')
            ->join('areas', 'equipos.area_id', '=', 'areas.id')
            ->join('empresas', 'areas.empresa_id', '=', 'empresas.id')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('trabajadores.id', $trabajadorId)
            ->where('empresas.id', $empresaId)
            ->where('miembros_equipo.activo', true)
            ->where('roles.nombre', 'Colaborador')
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('equipos.deleted_at')
            ->whereNull('areas.deleted_at')
            ->exists();
    }

    /**
     * Obtener trabajadores de una empresa con sus roles (para debug)
     */
    public function getTrabajadoresPorEmpresa(int $empresaId): Collection
    {
        return DB::table('trabajadores')
            ->select([
                'trabajadores.id',
                'trabajadores.nombres',
                'trabajadores.apellido_paterno',
                'trabajadores.apellido_materno',
                'usuarios.correo',
                'roles.nombre as rol_nombre',
                'empresas.nombre as empresa_nombre'
            ])
            ->join('miembros_equipo', 'trabajadores.id', '=', 'miembros_equipo.trabajador_id')
            ->join('equipos', 'miembros_equipo.equipo_id', '=', 'equipos.id')
            ->join('areas', 'equipos.area_id', '=', 'areas.id')
            ->join('empresas', 'areas.empresa_id', '=', 'empresas.id')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('empresas.id', $empresaId)
            ->where('usuarios.activo', true)
            ->where('miembros_equipo.activo', true)
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
            ->whereNull('equipos.deleted_at')
            ->whereNull('areas.deleted_at')
            ->distinct()
            ->get()
            ->map(function($trabajador) {
                return (object)[
                    'id' => $trabajador->id,
                    'nombres' => $trabajador->nombres,
                    'apellido_paterno' => $trabajador->apellido_paterno,
                    'apellido_materno' => $trabajador->apellido_materno,
                    'nombre_completo' => $trabajador->nombres . ' ' . $trabajador->apellido_paterno . ' ' . $trabajador->apellido_materno,
                    'correo' => $trabajador->correo,
                    'rol_nombre' => $trabajador->rol_nombre,
                    'empresa_nombre' => $trabajador->empresa_nombre
                ];
            });
    }

    /**
     * Obtener equipos que NO tienen metas asignadas
     */
    public function getEquiposSinMetas(int $empresaId): Collection
    {
        return $this->model->with([
            'coordinador.usuario.rol',
            'area'
        ])
        ->whereHas('area', function($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })
        // FILTRO: Solo equipos con coordinadores válidos
        ->whereHas('coordinador', function($query) use ($empresaId) {
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
        // FILTRO CLAVE: Solo equipos SIN metas asignadas
        ->whereDoesntHave('metas', function($query) {
            $query->whereNull('deleted_at');
        })
        ->whereNull('deleted_at')
        ->orderBy('nombre')
        ->get();
    }

    


}
