<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipo extends Model
{
    use SoftDeletes;

    protected $table = 'equipos';
    public $timestamps = false;
    protected $fillable = [
        'coordinador_id', 
        'area_id', 
        'nombre', 
        'descripcion', 
        'fecha_creacion'
    ];

    protected $dates = ['fecha_creacion', 'deleted_at'];

    public function coordinador()
    {
        return $this->belongsTo(Trabajador::class, 'coordinador_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function miembros()
    {
        return $this->hasMany(MiembroEquipo::class, 'equipo_id');
    }

    public function metas()
    {
        return $this->hasMany(Meta::class, 'equipo_id');
    }

    public function reuniones()
    {
        return $this->hasMany(Reunion::class, 'equipo_id');
    }

    public function invitaciones()
    {
        return $this->hasMany(Invitacion::class, 'equipo_id');
    }

    // Accessor para obtener el estado del equipo
    public function getEstadoAttribute()
    {
        if ($this->deleted_at) {
            return 'inactivo';
        }
        
        // Verificar si tiene metas pausadas
        $metasSuspendidas = $this->metas()->whereHas('estado', function($query) {
            $query->where('nombre', 'Suspendida');
        })->count();
        
        if ($metasSuspendidas > 0) {
            return 'pausado';
        }
        
        return 'activo';
    }

    // Accessor para obtener el progreso promedio
    public function getProgresoPromedioAttribute()
    {
        $metas = $this->metas;
        if ($metas->count() === 0) {
            return 0;
        }

        $totalMetas = $metas->count();
        $metasCompletadas = $metas->filter(function($meta) {
            return $meta->estado && $meta->estado->nombre === 'Completo';
        })->count();

        return round(($metasCompletadas / $totalMetas) * 100);
    }

    // Accessor para contar miembros activos
    public function getMiembrosActivosCountAttribute()
    {
        return $this->miembros()->where('activo', true)->count();
    }

    // Accessor para contar metas activas
    public function getMetasActivasCountAttribute()
    {
        return $this->metas()->whereHas('estado', function($query) {
            $query->whereIn('nombre', ['Incompleta', 'En proceso']);
        })->count();
    }
}
