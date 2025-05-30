<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meta extends Model
{
    use SoftDeletes;

    protected $table = 'metas';

    protected $fillable = ['equipo_id', 'estado_id', 'nombre', 'descripcion', 'fecha_creacion', 'fecha_entrega'];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }
}
