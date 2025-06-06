<?php

 namespace App\Repositories;

 use App\Models\Empresa;
use Illuminate\Database\Eloquent\Builder;

class EmpresaRepositorio extends RepositorioBase
{
    public function __construct(Empresa $model)
    {
        parent::__construct($model);
    }

    
    public function getCoordinadoresGenerales(int $empresaId)
    {
        $empresa = $this->model->find($empresaId);
        if (!$empresa) {
            return collect(); // Retorna una colección vacía si no se encuentra la empresa
        }

        return $empresa->trabajadores()
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles',    'usuarios.rol_id',       '=', 'roles.id')
            ->where('roles.id', 3)                 // rol coordinador general
            ->select('trabajadores.*')
            ->get();
    }

    
    public function update(int $id, array $data): bool
    {
        // se actualiza del usuario asociado a la empresa
        $empresa = $this->getById($id);
        if (!$empresa) {
            return false;
        }
        $usuario = $empresa->usuario;
        if (!$usuario) {
            return false;
        }
        $usuario->fill($data);
        if (!$usuario->save()) {
            return false;
        }
        // se actualiza la empresa
        return parent::update($id, $data);
    }

    public function cambiarEstado(int $id, bool $estado): bool
    {
        // se cambia el estado del usuario asociado a la empresa
        $empresa = $this->getById($id);
        if (!$empresa) {
            return false;
        }
        $usuario = $empresa->usuario;
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
                    $consulta->where('empresas.id', $value);
                    break;
                case 'plan_servicio_id':
                    $consulta->where('empresas.plan_servicio_id', $value);
                    break;
                case 'estado':
                    $this->aplicarJoinCondicional($consulta, 'usuarios', 'usuario_id', '=', 'usuarios.id');
                    $consulta->select('empresas.*');
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

}