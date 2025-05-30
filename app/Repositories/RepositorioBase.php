<?php

 namespace App\Repositories;

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

    public function getById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findBy(string $field, $value, array $relations = []): Collection
    {
        return $this->model->where($field, $value)->with($relations)->get();
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

 }
 