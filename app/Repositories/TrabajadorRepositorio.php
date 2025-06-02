<?php

namespace App\Repositories;

use App\Models\MiembroEquipo;
use App\Models\Trabajador;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TrabajadorRepositorio extends RepositorioBase
 {
    public function __construct(Trabajador $model)
    {
        parent::__construct($model);
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function cambiarEstado(int $id, bool $estado): bool
    {
        // se cambia el estado del usuario asociado al trabajador
        $trabajador = $this->getById($id);
        if (!$trabajador) {
            return false;
        }
        $usuario = $trabajador->usuario;
        if (!$usuario) {
            return false;
        }
        $usuario->activo = $estado;
        return $usuario->save();
    }

    public function getMiembrosEquipo($idEquipo)
    {
        return MiembroEquipo::with(['trabajador.usuario.rol'])->where('equipo_id', $idEquipo)->get();
    }

    public function countMiembrosEquipo($idEquipo)
    {
        return MiembroEquipo::where('equipo_id', $idEquipo)->count();
    }

    // public function getColaboradoresDisponibles()
    // {
    //     return Trabajador::whereHas('usuario.rol', function ($query) {
    //             $query->where('nombre', 'Colaborador');
    //         })
    //         ->whereNotIn('id', function ($query) {
    //             $query->select('trabajador_id')->from('miembros_equipo');
    //         })
    //         ->get();
    // }

    public function getColaboradoresDisponibles()
    {
        return Trabajador::all();
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
                // case 'area_id':
                //     if ((int) $value === 0) {
                //         // 1) Trabajadores que NO estén en ningún miembro_equipo
                //         //    (es decir, no tienen equipo y por ende no tienen área).
                //         $this->aplicarJoinCondicional(
                //             $consulta,
                //             'miembros_equipo',
                //             'trabajadores.id',
                //             '=',
                //             'miembros_equipo.trabajador_id',
                //             'left'   // LEFT JOIN para detectar los NULL
                //         );
                //         // Filtramos donde no exista registro en miembros_equipo
                //         $consulta->whereNull('miembros_equipo.trabajador_id');
                //     } else {
                //         // 2) Trabajadores que estén en un equipo cuya área sea $value
                //         $this->aplicarJoinCondicional(
                //             $consulta,
                //             'miembros_equipo',
                //             'trabajadores.id',
                //             '=',
                //             'miembros_equipo.trabajador_id'
                //         );
                //         $this->aplicarJoinCondicional(
                //             $consulta,
                //             'equipos',
                //             'miembros_equipo.equipo_id',
                //             '=',
                //             'equipos.id'
                //         );
                //         // Ahora filtramos directamente por el área del equipo
                //         $consulta->where('equipos.area_id', $value);
                //     }
                //     break;
                case 'estado':
                    $this->aplicarJoinCondicional($consulta, 'usuarios', 'usuario_id', '=', 'usuarios.id');
                    $consulta->where('usuarios.activo', $value);
                    break;
                case 'rol_id':
                    $this->aplicarJoinCondicional($consulta, 'usuarios', 'usuario_id', '=', 'usuarios.id');
                    $consulta->select('trabajadores.*');
                    $consulta->where('usuarios.rol_id', $value);
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
            $searchColumn = 'nombres'; // Columna por defecto para búsqueda
        }
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
 
 }
 