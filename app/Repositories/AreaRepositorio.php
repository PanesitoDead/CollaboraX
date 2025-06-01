<?php

 namespace App\Repositories;

 use App\Models\Area;
use App\Models\AreaCoordinador;
use App\Models\Trabajador;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AreaRepositorio extends RepositorioBase
{
    protected AreaCoordinador $areaCoordinador;
    protected Trabajador $trabajador;
    protected Usuario $usuario;
    public function __construct(Area $model)
    {
        parent::__construct($model);
    }

    public function asignarCoordinador(int $areaId, int $coordinadorId): bool
    {
        // Asignar un coordinador a un área
        $area = $this->getById($areaId);
        if (!$area) {
            return false;
        }
        $coordinador = AreaCoordinador::where('area_id', $areaId)
            ->where('trabajador_id', $coordinadorId)
            ->first();
        if ($coordinador) {
            return true; // Ya está asignado
        }
        AreaCoordinador::create([
            'area_id' => $areaId,
            'trabajador_id' => $coordinadorId,
            'fecha_inicio' => now(),
            'fecha_fin' => null, // No tiene fecha de fin al asignar
        ]);
        $this->asignarRolCoordinador($coordinadorId);
        return true;
    }

    public function actualizarCoordinador(int $areaId, int $coordinadorId): bool
    {
        // Actualizar el coordinador de un área
        $area = $this->getById($areaId);
        if (!$area) {
            return false;
        }
        $coordinador = AreaCoordinador::where('area_id', $areaId)->first();
        if ($coordinador) {
            $coordinador->trabajador_id = $coordinadorId;
            $coordinador->save();
            return $this->asignarRolCoordinador($coordinadorId);
        } else {
            return $this->asignarCoordinador($areaId, $coordinadorId);
        }
    }

    private function asignarRolCoordinador(int $trabajadorId): bool
    {
        // 1) Buscar al trabajador
        $trabajador = Trabajador::find($trabajadorId);
        if (!$trabajador) {
            return false;
        }

        // 2) Buscar al usuario asociado al trabajador
        $usuario = Usuario::find($trabajador->usuario_id);
        if (!$usuario) {
            return false;
        }

        // 3) Asignar rol “Coordinador” (rol_id = 3) y guardar
        $usuario->rol_id = 3;
        return $usuario->save();
    }

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
                case 'estado':
                    $consulta->where('areas.activo', $value);
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
                    $consulta->orderBy('areas.id', $sortOrder);
                    break;
                case 'nombre':
                    $consulta->orderBy('areas.nombre', $sortOrder);
                    break;
                default:
                    $consulta->orderBy($sortField, $sortOrder);
                    break;
            }
        }
    }

}