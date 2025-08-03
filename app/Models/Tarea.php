<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Tarea extends Model
{
    use SoftDeletes, LogsActivity;
    public $timestamps = false;
    protected $table = 'tareas';

    protected $fillable = ['meta_id', 'estado_id', 'nombre', 'descripcion', 'fecha_creacion', 'fecha_entrega'];

    protected $dates = ['fecha_creacion', 'fecha_entrega', 'deleted_at'];
    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

   // Accessor para verificar si está vencida
    public function getEstaVencidaAttribute()
    {
        if (!$this->fecha_entrega) {
            return false;
        }

        return \Carbon\Carbon::parse($this->fecha_entrega)->isPast() && 
               (!$this->estado || $this->estado->nombre !== 'Completo');
    }

    // Accessor para verificar si está completada
    public function getEstaCompletadaAttribute()
    {
        return $this->estado && $this->estado->nombre === 'Completo';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Tarea '{$this->nombre}' fue {$eventName}");
    }
}
