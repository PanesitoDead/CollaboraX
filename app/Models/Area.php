<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;

    protected $table = 'areas';

    public $timestamps = false;

    protected $fillable = ['empresa_id', 'nombre', 'descripcion', 'codigo', 'color', 'activo', 'fecha_creacion'];
    
    protected $casts = ['fecha_creacion', 'deleted_at'];
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    public function coordinadores()
    {
        return $this->hasMany(AreaCoordinador::class);
    }
}
