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

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
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

    // Accessor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombres . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno;
    }

    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'miembros_equipo');
    }

    // Accessor para iniciales
    public function getInicialesAttribute()
    {
        $nombres = explode(' ', $this->nombres);
        $iniciales = substr($nombres[0], 0, 1);
        if (isset($nombres[1])) {
            $iniciales .= substr($nombres[1], 0, 1);
        } else {
            $iniciales .= substr($this->apellido_paterno, 0, 1);
        }
        return strtoupper($iniciales);
    }

    
}
