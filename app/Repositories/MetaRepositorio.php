<?php

 namespace App\Repositories;

 use App\Models\Meta;



class MetaRepositorio extends RepositorioBase
 {
    public function __construct(Meta $model)
    {
        parent::__construct($model);
    }
 
    public function getMetasActivas()
    {
        return Meta::where('estado_id', '!=', 1)->get();
    }

    public function countMetasActivas()
    {
        return Meta::where('estado_id', '!=', 1)->count();
    }
    
 }
 