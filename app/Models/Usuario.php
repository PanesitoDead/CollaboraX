<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usuarios';
    public $timestamps = false;

    protected $fillable = ['correo', 'clave', 'rol_id', 'activo', 'en_linea', 'ultima_conexion', 'foto'];

    protected $casts = [
        'activo' => 'boolean',
        'en_linea' => 'boolean'
    ];
    protected $hidden = [
        'clave',
        'remember_token',
    ];

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

    public function getAuthPassword()
    {
        return $this->clave;
    }

    public function getAuthIdentifierName()
    {
        return 'correo';
    }
    
}
