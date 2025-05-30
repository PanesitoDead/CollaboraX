<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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

    public function coordinador()
    {
        return $this->hasOne(AreaCoordinador::class)->latestOfMany();
    }

    public function metas(): HasManyThrough
    {
        return $this->hasManyThrough(
            \App\Models\Meta::class,
            \App\Models\Equipo::class,
            'area_id',     // FK de Equipo → Area
            'equipo_id',   // FK de Meta   → Equipo
            'id',          // PK de Area (opcional, Laravel lo asume automáticamente)
            'id'           // PK de Equipo (opcional, Laravel lo asume automáticamente)
        );
    }

    public function metasActivas(): HasManyThrough
    {
        return $this->metas()
                    ->whereIn('estado_id', [1, 2]);
    }
}
