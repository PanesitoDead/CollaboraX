<?php

 namespace App\Repositories;

 use App\Models\MiembroEquipo;
 use App\Models\Trabajador;


class TrabajadorRepositorio extends RepositorioBase
 {
    public function __construct(Trabajador $model)
    {
        parent::__construct($model);
    }

    public function getMiembrosEquipo($idEquipo)
    {
        return MiembroEquipo::where('equipo_id', $idEquipo)->get();
    }

    public function countMiembrosEquipo($idEquipo)
    {
        return MiembroEquipo::where('equipo_id', $idEquipo)->count();
    }

    
 
 }
 