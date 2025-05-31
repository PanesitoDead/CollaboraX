<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trabajador extends Model
{
    use SoftDeletes;

    protected $table = 'trabajadores';

    protected $fillable = ['usuario_id', 'nombres', 'apellido_paterno', 'apellido_materno', 'doc_identidad', 'fecha_nacimiento', 'telefono'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function miembrosEquipo()
    {
        return $this->hasMany(MiembroEquipo::class);
    }

    public function coordinaciones()
    {
        return $this->hasMany(AreaCoordinador::class);
    }

    public function invitaciones()
    {
        return $this->hasMany(Invitacion::class);
    }

    public function mensajesEnviados()
    {
        return $this->hasMany(Mensaje::class, 'remitente_id');
    }

    public function mensajesRecibidos()
    {
        return $this->hasMany(Mensaje::class, 'destinatario_id');
    }

    public function equiposCoordinados()
    {
        return $this->hasMany(Equipo::class, 'coordinador_id');
    }

    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}");
    }
}
