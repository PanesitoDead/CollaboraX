<?php

 namespace App\Repositories;

 use App\Models\Empresa;


class EmpresaRepositorio extends RepositorioBase
{
    public function __construct(Empresa $model)
    {
        parent::__construct($model);
    }

}
 