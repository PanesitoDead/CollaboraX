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

    public function actividades()
    {
        return $this->hasManyThrough(Tarea::class, Meta::class, 'equipo_id', 'meta_id');
    }

    public function tareasCompletadas()
    {
        return $this->hasManyThrough(Tarea::class, Meta::class, 'equipo_id', 'meta_id')
                    ->whereHas('estado', function($query) {
                        $query->where('nombre', 'Completo');
                    });
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

    // Accessor para nombre completo del coordinador
    public function getCoordinadorNombreCompletoAttribute()
    {
        if ($this->coordinador) {
            return $this->coordinador->nombres . ' ' . $this->coordinador->apellido_paterno . ' ' . $this->coordinador->apellido_materno;
        }
        return '';
    }

    // Accessor para iniciales del coordinador
    public function getCoordinadorInicialesAttribute()
    {
        if ($this->coordinador) {
            $nombres = explode(' ', $this->coordinador->nombres);
            $iniciales = substr($nombres[0], 0, 1);
            if (isset($nombres[1])) {
                $iniciales .= substr($nombres[1], 0, 1);
            } else {
                $iniciales .= substr($this->coordinador->apellido_paterno, 0, 1);
            }
            return strtoupper($iniciales);
        }
        return '';
    }
    


    


}
