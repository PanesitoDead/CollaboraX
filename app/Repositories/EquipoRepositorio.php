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
     * Obtener áreas asignadas a un coordinador general
     */
    public function getAreasCoordinadorGeneral(int $trabajadorId): Collection
    {
        try {
            // Primero verificamos si el trabajador existe
            $trabajador = Trabajador::find($trabajadorId);
            if (!$trabajador) {
                Log::error('Trabajador no encontrado', ['trabajador_id' => $trabajadorId]);
                return collect([]);
            }

            // Verificamos si hay registros en areas_coordinador
            $areasCoordinador = DB::table('areas_coordinador')
                ->where('trabajador_id', $trabajadorId)
                ->whereNull('deleted_at')
                ->count();

            // Si no hay registros, devolvemos todas las áreas de la empresa
            if ($areasCoordinador === 0) {
                Log::info('No se encontraron áreas asignadas, devolviendo todas las áreas de la empresa', [
                    'trabajador_id' => $trabajadorId,
                    'empresa_id' => $trabajador->empresa_id
                ]);
            
                return Area::where('empresa_id', $trabajador->empresa_id)
                    ->where('activo', true)
                    ->whereNull('deleted_at')
                    ->orderBy('nombre')
                    ->get();
            }

            // Si hay registros, devolvemos las áreas asignadas
            return Area::select('areas.*')
                ->join('areas_coordinador', 'areas.id', '=', 'areas_coordinador.area_id')
                ->where('areas_coordinador.trabajador_id', $trabajadorId)
                ->where('areas.activo', true)
                ->whereNull('areas.deleted_at')
                ->whereNull('areas_coordinador.deleted_at')
                ->where(function($query) {
                    $query->whereNull('areas_coordinador.fecha_fin')
                      ->orWhere('areas_coordinador.fecha_fin', '>', now());
            })
                ->orderBy('areas.nombre')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error al obtener áreas del coordinador general', [
                'trabajador_id' => $trabajadorId,
                'error' => $e->getMessage()
            ]);
            return collect([]);
        }
    }

    /**
     * Obtener equipos por áreas específicas
     * SOLO equipos que tengan al menos un coordinador de equipo
     */
    public function getEquiposByAreas(array $areaIds): Collection
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
        ->whereIn('area_id', $areaIds)
        ->whereNull('deleted_at') // Excluir soft deleted
        // FILTRO CLAVE: Solo equipos que tengan al menos un coordinador de equipo
        ->whereHas('miembros', function($query) {
            $query->where('activo', true)
                  ->whereHas('trabajador.usuario.rol', function($rolQuery) {
                      $rolQuery->where('nombre', 'Coord. Equipo');
                  });
        })
        ->orderBy('fecha_creacion', 'desc')
        ->get();
    }

    /**
     * Obtener colaboradores disponibles para convertir en coordinadores de equipo
     */
    public function getColaboradoresParaCoordinacion(int $empresaId): Collection
    {
        return Trabajador::select('trabajadores.*')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('trabajadores.empresa_id', $empresaId)
            ->where('usuarios.activo', true)
            ->where('roles.nombre', 'Colaborador')
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
            ->with(['usuario.rol'])
            ->orderBy('trabajadores.nombres')
            ->get();
    }

    /**
     * Buscar colaboradores por nombre o email
     */
    public function buscarColaboradores(string $termino, int $empresaId): Collection
    {
        return Trabajador::select('trabajadores.*')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('trabajadores.empresa_id', $empresaId)
            ->where('usuarios.activo', true)
            ->where('roles.nombre', 'Colaborador')
            ->where(function($query) use ($termino) {
                $query->where('trabajadores.nombres', 'LIKE', "%{$termino}%")
                      ->orWhere('trabajadores.apellido_paterno', 'LIKE', "%{$termino}%")
                      ->orWhere('trabajadores.apellido_materno', 'LIKE', "%{$termino}%")
                      ->orWhere('usuarios.correo', 'LIKE', "%{$termino}%");
            })
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
            ->with(['usuario.rol'])
            ->limit(10)
            ->get();
    }

    /**
     * Verificar si un área pertenece a un coordinador general
     */
    public function areaPerteneceeACoordinadorGeneral(int $areaId, int $trabajadorId): bool
    {
        try {
            // Primero verificamos si el trabajador existe
            $trabajador = Trabajador::find($trabajadorId);
            if (!$trabajador) {
                return false;
            }

            // Verificamos si hay registros en areas_coordinador
            $areasCoordinador = DB::table('areas_coordinador')
                ->where('trabajador_id', $trabajadorId)
                ->whereNull('deleted_at')
                ->count();

            // Si no hay registros, verificamos si el área pertenece a la empresa
            if ($areasCoordinador === 0) {
                return DB::table('areas')
                    ->where('id', $areaId)
                    ->where('empresa_id', $trabajador->empresa_id)
                    ->whereNull('deleted_at')
                    ->exists();
            }

            // Si hay registros, verificamos si el área está asignada al coordinador
            return DB::table('areas_coordinador')
                ->where('area_id', $areaId)
                ->where('trabajador_id', $trabajadorId)
                ->whereNull('deleted_at')
                ->where(function($query) {
                    $query->whereNull('fecha_fin')
                      ->orWhere('fecha_fin', '>', now());
            })
                ->exists();
        } catch (\Exception $e) {
            Log::error('Error al verificar si el área pertenece al coordinador', [
                'area_id' => $areaId,
                'trabajador_id' => $trabajadorId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar si un equipo pertenece a las áreas de un coordinador general
     * Y que tenga al menos un coordinador de equipo
     */
    public function equipoPerteneceeACoordinadorGeneral(int $equipoId, int $trabajadorId): bool
    {
        try {
            // Primero verificamos si el trabajador existe
            $trabajador = Trabajador::find($trabajadorId);
            if (!$trabajador) {
                return false;
            }

            // Obtenemos el equipo
            $equipo = $this->model->find($equipoId);
            if (!$equipo) {
                return false;
            }

            // Verificar que el equipo tenga al menos un coordinador de equipo
            $tieneCoordinadorEquipo = $this->equipoTieneCoordinadorEquipo($equipoId);
            if (!$tieneCoordinadorEquipo) {
                return false;
            }

            // Verificamos si hay registros en areas_coordinador
            $areasCoordinador = DB::table('areas_coordinador')
                ->where('trabajador_id', $trabajadorId)
                ->whereNull('deleted_at')
                ->count();

            // Si no hay registros, verificamos si el equipo pertenece a la empresa
            if ($areasCoordinador === 0) {
                return DB::table('areas')
                    ->join('equipos', 'areas.id', '=', 'equipos.area_id')
                    ->where('equipos.id', $equipoId)
                    ->where('areas.empresa_id', $trabajador->empresa_id)
                    ->whereNull('areas.deleted_at')
                    ->whereNull('equipos.deleted_at')
                    ->exists();
            }

            // Si hay registros, verificamos si el equipo está en un área asignada al coordinador
            return $this->model->select('equipos.id')
                ->join('areas', 'equipos.area_id', '=', 'areas.id')
                ->join('areas_coordinador', 'areas.id', '=', 'areas_coordinador.area_id')
                ->where('equipos.id', $equipoId)
                ->where('areas_coordinador.trabajador_id', $trabajadorId)
                ->whereNull('equipos.deleted_at')
                ->whereNull('areas_coordinador.deleted_at')
                ->where(function($query) {
                    $query->whereNull('areas_coordinador.fecha_fin')
                      ->orWhere('areas_coordinador.fecha_fin', '>', now());
            })
                ->exists();
        } catch (\Exception $e) {
            Log::error('Error al verificar si el equipo pertenece al coordinador', [
                'equipo_id' => $equipoId,
                'trabajador_id' => $trabajadorId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar si un equipo tiene al menos un coordinador de equipo
     */
    public function equipoTieneCoordinadorEquipo(int $equipoId): bool
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
     * Verificar si un trabajador es colaborador activo
     */
    public function esColaboradorActivo(int $trabajadorId, int $empresaId): bool
    {
        return Trabajador::select('trabajadores.id')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('trabajadores.id', $trabajadorId)
            ->where('trabajadores.empresa_id', $empresaId)
            ->where('usuarios.activo', true)
            ->where('roles.nombre', 'Colaborador')
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
            ->exists();
    }

    /**
     * Cambiar rol de colaborador a coordinador de equipo
     */
    public function cambiarRolACoordinadorEquipo(int $trabajadorId): bool
    {
        try {
            $trabajador = Trabajador::with('usuario')->find($trabajadorId);
            if (!$trabajador || !$trabajador->usuario) {
                return false;
            }

            // Buscar el rol "Coord. Equipo"
            $rolCoordinador = DB::table('roles')
                ->where('nombre', 'Coord. Equipo')
                ->first();

            if (!$rolCoordinador) {
                Log::error('Rol Coord. Equipo no encontrado');
                return false;
            }

            // Actualizar el rol del usuario
            $trabajador->usuario->update([
                'rol_id' => $rolCoordinador->id
            ]);

            Log::info('Rol cambiado exitosamente', [
                'trabajador_id' => $trabajadorId,
                'nuevo_rol' => 'Coord. Equipo'
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al cambiar rol', [
                'trabajador_id' => $trabajadorId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Cambiar rol de coordinador de equipo a colaborador
     */
    public function cambiarRolAColaborador(int $trabajadorId): bool
    {
        try {
            $trabajador = Trabajador::with('usuario')->find($trabajadorId);
            if (!$trabajador || !$trabajador->usuario) {
                return false;
            }

            // Buscar el rol "Colaborador"
            $rolColaborador = DB::table('roles')
                ->where('nombre', 'Colaborador')
                ->first();

            if (!$rolColaborador) {
                Log::error('Rol Colaborador no encontrado');
                return false;
            }

            // Actualizar el rol del usuario
            $trabajador->usuario->update([
                'rol_id' => $rolColaborador->id
            ]);

            Log::info('Rol cambiado exitosamente a Colaborador', [
                'trabajador_id' => $trabajadorId
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al cambiar rol a Colaborador', [
                'trabajador_id' => $trabajadorId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener todos los coordinadores de equipo de un equipo específico
     */
    public function getCoordinadoresEquipoDelEquipo(int $equipoId): Collection
    {
        return Trabajador::select('trabajadores.*')
            ->join('miembros_equipo', 'trabajadores.id', '=', 'miembros_equipo.trabajador_id')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('miembros_equipo.equipo_id', $equipoId)
            ->where('miembros_equipo.activo', true)
            ->where('roles.nombre', 'Coord. Equipo')
            ->whereNull('miembros_equipo.deleted_at')
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
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
     * SOLO si tiene al menos un coordinador de equipo
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
        ])
        ->whereNull('deleted_at') // Excluir soft deleted
        ->find($id);

        // Verificar que el equipo tenga al menos un coordinador de equipo
        if ($equipo && !$this->equipoTieneCoordinadorEquipo($equipo->id)) {
            Log::warning('Equipo sin coordinador de equipo válido', ['equipo_id' => $id]);
            return null;
        }

        return $equipo;
    }

    /**
     * Eliminar equipo (soft delete con cambio automático de roles)
     * FUNCIONALIDAD: Cambiar TODOS los coordinadores de equipo a Colaborador automáticamente
     */
    public function delete(int $id): bool
    {
        try {
            $equipo = $this->model->find($id);
            if (!$equipo) {
                Log::warning('Equipo no encontrado para eliminar', ['id' => $id]);
                return false;
            }

            // Obtener todos los coordinadores de equipo antes de eliminar
            $coordinadoresEquipo = $this->getCoordinadoresEquipoDelEquipo($id);

            Log::info('Iniciando eliminación (soft delete) de equipo', [
                'id' => $id,
                'nombre' => $equipo->nombre,
                'coordinadores_equipo' => $coordinadoresEquipo->pluck('id')->toArray()
            ]);

            return DB::transaction(function () use ($equipo, $coordinadoresEquipo) {
            
                // 1. Soft delete del equipo
                $resultado = $equipo->delete(); // Esto hace soft delete
                Log::info('Equipo marcado como eliminado (soft delete)', ['equipo_id' => $equipo->id, 'resultado' => $resultado]);

                // 2. Desactivar miembros del equipo
                $equipo->miembros()->update(['activo' => false]);
                Log::info('Miembros desactivados', ['equipo_id' => $equipo->id]);

                // 3. FUNCIONALIDAD PRINCIPAL: Cambiar TODOS los coordinadores de equipo a Colaborador automáticamente
                foreach ($coordinadoresEquipo as $coordinador) {
                    $rolCambiado = $this->cambiarRolAColaborador($coordinador->id);
                    Log::info('Cambio automático de rol a Colaborador', [
                        'trabajador_id' => $coordinador->id,
                        'nombre' => $coordinador->nombres . ' ' . $coordinador->apellido_paterno,
                        'rol_cambiado' => $rolCambiado
                    ]);
                }

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
     * VALIDACIÓN: No permitir remover el último coordinador de equipo
     * FUNCIONALIDAD: Cambiar rol a Colaborador si se remueve coordinador
     */
    public function removerMiembro(int $equipoId, int $trabajadorId): bool
    {
        $equipo = $this->model->find($equipoId);
        if (!$equipo) {
            return false;
        }

        $miembro = $equipo->miembros()
            ->where('trabajador_id', $trabajadorId)
            ->where('activo', true)
            ->first();

        if (!$miembro) {
            return false;
        }

        // Verificar si el trabajador es coordinador de equipo
        $esCoordinadorEquipo = DB::table('trabajadores')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('trabajadores.id', $trabajadorId)
            ->where('roles.nombre', 'Coord. Equipo')
            ->exists();

        if ($esCoordinadorEquipo) {
            // Contar cuántos coordinadores de equipo quedarían después de remover este
            $coordinadoresRestantes = DB::table('miembros_equipo')
                ->join('trabajadores', 'miembros_equipo.trabajador_id', '=', 'trabajadores.id')
                ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
                ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
                ->where('miembros_equipo.equipo_id', $equipoId)
                ->where('miembros_equipo.activo', true)
                ->where('roles.nombre', 'Coord. Equipo')
                ->where('trabajadores.id', '!=', $trabajadorId) // Excluir el que se va a remover
                ->count();

            // Si no quedarían coordinadores de equipo, no permitir la remoción
            if ($coordinadoresRestantes === 0) {
                Log::warning('No se puede remover el último coordinador de equipo', [
                    'equipo_id' => $equipoId,
                    'trabajador_id' => $trabajadorId
                ]);
                return false;
            }
        }

        $miembro->activo = false;
        $miembro->save();

        // Si se removió un coordinador de equipo, cambiar su rol a Colaborador automáticamente
        if ($esCoordinadorEquipo) {
            $this->cambiarRolAColaborador($trabajadorId);
        }

        return true;
    }

    /**
     * Obtener estadísticas por áreas
     * SOLO equipos con coordinadores de equipo válidos
     */
    public function getEstadisticasPorAreas(array $areaIds): array
    {
        $equipos = $this->getEquiposByAreas($areaIds);
        
        $total = $equipos->count();
        $activos = $equipos->where('deleted_at', null)->count();
        
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
     * Buscar equipos por nombre en las áreas del coordinador
     * SOLO equipos con coordinadores de equipo válidos
     */
    public function buscarPorNombre(string $nombre, array $areaIds): Collection
    {
        return $this->model->with([
            'coordinador.usuario.rol',
            'area',
            'miembros' => function($query) {
                $query->where('activo', true);
            }
        ])
        ->where('nombre', 'LIKE', "%{$nombre}%")
        ->whereIn('area_id', $areaIds)
        ->whereNull('deleted_at')
        // FILTRO: Solo equipos que tengan al menos un coordinador de equipo
        ->whereHas('miembros', function($query) {
            $query->where('activo', true)
                  ->whereHas('trabajador.usuario.rol', function($rolQuery) {
                      $rolQuery->where('nombre', 'Coord. Equipo');
                  });
        })
        ->get();
    }

    /**
     * Validar que un equipo puede ser creado/actualizado
     * Debe tener al menos un coordinador de equipo
     */
    public function validarEquipoParaOperacion(int $equipoId): bool
    {
        return $this->equipoTieneCoordinadorEquipo($equipoId);
    }

    /**
     * Obtener equipos sin coordinadores de equipo válidos (para limpieza)
     */
    public function getEquiposSinCoordinadorEquipo(int $empresaId): Collection
    {
        return $this->model->select('equipos.*')
            ->join('areas', 'equipos.area_id', '=', 'areas.id')
            ->where('areas.empresa_id', $empresaId)
            ->whereNull('equipos.deleted_at')
            ->whereDoesntHave('miembros', function($query) {
                $query->where('activo', true)
                      ->whereHas('trabajador.usuario.rol', function($rolQuery) {
                          $rolQuery->where('nombre', 'Coord. Equipo');
                      });
            })
            ->get();
    }

}
