<?php

 namespace App\Repositories;

use App\Models\Rol;

class RolRepositorio extends RepositorioBase
 {
    public function __construct(Rol $model)
    {
        parent::__construct($model);
    }
 
    // métodos específicos para el repositorio de Rol pueden ser añadidos aquí
 }
 