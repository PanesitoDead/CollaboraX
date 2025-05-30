<?php

 namespace App\Repositories;

 use App\Models\PlanServicio;


class PlanRepositorio extends RepositorioBase
{
    public function __construct(PlanServicio $model)
    {
        parent::__construct($model);
    }

}
 