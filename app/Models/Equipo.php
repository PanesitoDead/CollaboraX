<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipo extends Model
{
    use SoftDeletes;

    protected $table = 'equipos';

    protected $fillable = ['coordinador_id', 'area_id', 'nombre', 'descripcion', 'fecha_creacion'];

    public function coordinador()
    {
        return $this->belongsTo(Trabajador::class, 'coordinador_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function miembros()
    {
        return $this->hasMany(MiembroEquipo::class);
    }

    public function metas()
    {
        return $this->hasMany(Meta::class);
    }

    public function reuniones()
    {
        return $this->hasMany(Reunion::class);
    }

    public function invitaciones()
    {
        return $this->hasMany(Invitacion::class);
    }
}
