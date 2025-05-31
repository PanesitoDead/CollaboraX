<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiembroEquipo extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'miembros_equipo';

    protected $fillable = ['equipo_id', 'trabajador_id', 'fecha_union', 'activo'];

    protected $dates = ['fecha_union', 'deleted_at'];

    protected $casts = [
        'activo' => 'boolean'

    ];
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }
     // Accessor para verificar si es coordinador
    public function getEsCoordinadorAttribute()
    {
        return $this->equipo && $this->equipo->coordinador_id === $this->trabajador_id;
    }
}
