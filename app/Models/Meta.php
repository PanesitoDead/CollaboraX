<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meta extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'metas';

    protected $fillable = ['equipo_id', 'estado_id', 'nombre', 'descripcion', 'fecha_creacion', 'fecha_entrega'];

    protected $dates = ['fecha_creacion', 'fecha_entrega', 'deleted_at'];
    
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }

    // Accessor para calcular progreso basado en tareas
    public function getProgresoAttribute()
    {
        $totalTareas = $this->tareas->count();
        if ($totalTareas === 0) {
            return 0;
        }

        $tareasCompletadas = $this->tareas->filter(function($tarea) {
            return $tarea->estado && $tarea->estado->nombre === 'Completo';
        })->count();

        return round(($tareasCompletadas / $totalTareas) * 100);
    }

    // Accessor para contar tareas completadas
    public function getTareasCompletadasCountAttribute()
    {
        return $this->tareas->filter(function($tarea) {
            return $tarea->estado && $tarea->estado->nombre === 'Completo';
        })->count();
    }

    // Accessor para contar total de tareas
    public function getTotalTareasCountAttribute()
    {
        return $this->tareas->count();
    }
}
