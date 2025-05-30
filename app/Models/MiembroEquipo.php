<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiembroEquipo extends Model
{
    use SoftDeletes;

    protected $table = 'miembros_equipo';

    protected $fillable = ['equipo_id', 'trabajador_id', 'fecha_union', 'activo'];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }
}
