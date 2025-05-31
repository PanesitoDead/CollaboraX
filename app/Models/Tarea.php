<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarea extends Model
{
    use SoftDeletes;
    public $timestamps = false;
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



   // Accessor para verificar si está completada
    public function getEstaCompletadaAttribute()
    {
        return $this->estado && $this->estado->nombre === 'Completo';
    }

    // Accessor para verificar si está vencida
    public function getEstaVencidaAttribute()
    {
        if (!$this->fecha_entrega) {
            return false;
        }
        
        return $this->fecha_entrega < now() && !$this->esta_completada;
    }
}
