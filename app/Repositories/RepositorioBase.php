<?php

 namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

 abstract class RepositorioBase
 {
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }


    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function getActives()
    {
        return $this->model->where('active', 1)->get();
    }

    public function getById(string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findBy(string $field, $value, array $relations = []): Collection
    {
        return $this->model->where($field, $value)->with($relations)->get();
    }

    public function findOneBy($field, $value)
    {
        return $this->model->where($field, $value)->first();
    }

    public function findByFields(array $conditions, array $relations = []): Collection
    {
        $query = $this->model->newQuery();

        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->get();
    }

    public function findByMultipleConditions(array $conditions, array $relations = []): Collection
    {
        $query = $this->model->newQuery();

        foreach ($conditions as $condition) {
            if (count($condition) === 3) {
                [$field, $operator, $value] = $condition;

                if ($operator === 'IN') {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $operator, $value);
                }
            }
        }

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->get();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $entity = $this->getById($id);
        if ($entity) {
            return $entity->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $entity = $this->getById($id);

        if ($entity) {
            return $entity->delete();
        }
        return false;
    }

    public function count(): int
    {
        return $this->model->count();
    }

    abstract protected function aplicarRango(Builder $consulta, array $range): void;


    /**
     * Aplica los filtros a la consulta.
     *
     * @param Builder $consulta
     * @param array $filtros
     */
    abstract protected function aplicarFiltros(Builder $consulta, array $filtros): void;

    /**
     * Aplica la búsqueda a la consulta.
     *
     * @param Builder $consulta
     * @param string|null $searchTerm
     * @param string|null $searchColumn
     */
    abstract protected function aplicarBusqueda(Builder $consulta, ?string $searchTerm, ?string $searchColumn): void;

    /**
     * Aplica el ordenamiento a la consulta.
     *
     * @param Builder $consulta
     * @param string|null $sortField
     * @param string|null $sortOrder
     */
    abstract protected function aplicarOrdenamiento(Builder $consulta, ?string $sortField, ?string $sortOrder): void;

    /**
     * Obtiene los registros del modelo paginados.
     *
     * @param array $criterios
     * @param Builder|null $query
     * @return LengthAwarePaginator
     */
    public function obtenerPaginado(array $criterios, Builder $query = null): LengthAwarePaginator
    {
        $pageIndex = $criterios['pageIndex'] ?? 1;
        $pageSize = $criterios['pageSize'] ?? 5;
        $sortField = $criterios['sortField'];
        $sortOrder = $criterios['sortOrder'];
        $range = $criterios['range'] ?? null;
        $filters = $criterios['filters'];
        $searchTerm = $criterios['searchTerm'];
        $searchColumn = $criterios['searchColumn'];
        $query = $query ?? $this->model->query();

        // Aplicar rango
        $this->aplicarRango($query, $range);

        // Aplicar filtros
        $this->aplicarFiltros($query, $filters);

        // Aplicar búsqueda
        $this->aplicarBusqueda($query, $searchTerm, $searchColumn);

        // Aplicar ordenamiento
        $this->aplicarOrdenamiento($query, $sortField, $sortOrder);

        
        
        // Paginar los resultados
        $paginados = $query->paginate($pageSize, ['*'], 'page', $pageIndex);
        if ($paginados->isEmpty()) {
            return $query->paginate($pageSize, ['*'], 'page', 1);
        }
        return $paginados;
    }

    /**
     * Verifica si es necesario hacer un join con una tabla.
     * Si no hay joins en la consulta, se asume que se necesita hacer un join.
     *
     * @param Builder $query
     * @param string $table
     * @return bool
     */
    protected function necesitaJoin(Builder $query, string $table): bool
    {
        if (!$query->getQuery()->joins) {
            return true;
        }
        foreach ($query->getQuery()->joins as $join) {
            if ($join->table == $table) {
                return false;
            }
        }

        return true;
    }

    /**
     * Une una tabla a la consulta.
     *
     * @param Builder $query
     * @param string $table
     * @param mixed ...$conditions
     */
    protected function unirTabla(Builder $query, string $table, ...$conditions): void
    {
        $query->join($table, ...$conditions);

        // // Si la tabla tiene columna 'estado', aplicar filtro
        // if ($this->tablaTieneEstado($table)) {
        //     $query->where("$table.estado", '!=', 'ELIMINADO');
        // }
    }

    /**
     * Aplica un join condicional a la consulta.
     *
     * @param Builder $query
     * @param string $table
     * @param mixed ...$conditions
     */
    protected function aplicarJoinCondicional(Builder $query, string $table, ...$conditions): void
    {
        if ($this->necesitaJoin($query, $table)) {
            $this->unirTabla($query, $table, ...$conditions);
        }
    }

 }
 