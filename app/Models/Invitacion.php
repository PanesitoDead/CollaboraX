<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invitacion extends Model
{
    use SoftDeletes;

    protected $table = 'invitaciones';

    protected $fillable = ['equipo_id', 'trabajador_id', 'fecha_invitacion', 'fecha_expiracion', 'fecha_respuesta', 'estado'];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }
}
