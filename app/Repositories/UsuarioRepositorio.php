<?php

 namespace App\Repositories;

 use App\Models\Usuario;
 use Illuminate\Database\Eloquent\Builder;


class UsuarioRepositorio extends RepositorioBase
{
    public function __construct(Usuario $model)
    {
        parent::__construct($model);
    }

    public function existeCorreo($correo)
    {
        return Usuario::where('correo', $correo)->exists();
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
                default:
                    $consulta->where($key, $value);
                    break;
            }
        }
    }
    protected function aplicarBusqueda(Builder $consulta, ?string $searchTerm, ?string $searchColumn): void
    {
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
 