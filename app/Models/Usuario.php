<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = ['correo', 'clave', 'rol_id', 'activo', 'en_linea', 'ultima_conexion', 'foto'];

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function trabajador()
    {
        return $this->hasOne(Trabajador::class);
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class);
    }

    public function fotoPerfil()
    {
        return $this->belongsTo(Archivo::class, 'foto');
    }
}
