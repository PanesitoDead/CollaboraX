<?php

 namespace App\Repositories;

 use App\Models\Usuario;



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

}
 