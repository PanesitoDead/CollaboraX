<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarea extends Model
{
    use SoftDeletes;

    protected $table = 'tareas';

    protected $fillable = ['meta_id', 'estado_id', 'nombre', 'descripcion', 'fecha_creacion', 'fecha_entrega'];

    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
}
