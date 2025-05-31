<?php

 namespace App\Repositories;

 use App\Models\Area;
use App\Models\AreaCoordinador;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AreaRepositorio extends RepositorioBase
{
    protected AreaCoordinador $areaCoordinador;
    public function __construct(Area $model)
    {
        parent::__construct($model);
    }

    /*
      Sobreescribe el método update para incluir la actualización del usuario asociado a la area.
    */

    public function cambiarEstado(int $id, bool $estado): bool
    {
        // se cambia el estado del usuario asociado a la area
        $area = $this->getById($id);
        if (!$area) {
            return false;
        }
        $usuario = $area->usuario;
        if (!$usuario) {
            return false;
        }
        $usuario->activo = $estado;
        return $usuario->save();
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
                    $consulta->where('areas.id', $value);
                    break;
                case 'plan_servicio_id':
                    $consulta->where('areas.plan_servicio_id', $value);
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
                    $consulta->where('areas.id', 'like', $searchTerm);
                    break;
                case 'nombre':
                    $consulta->where('areas.nombre', 'like', '%' . $searchTerm . '%');
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
                    $consulta->orderBy('area.id', $sortOrder);
                    break;
                case 'nombre':
                    $consulta->orderBy('area.nombre', $sortOrder);
                    break;
                default:
                    $consulta->orderBy($sortField, $sortOrder);
                    break;
            }
        }
    }

}