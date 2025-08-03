<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MiembroEquipo extends Model
{
    use SoftDeletes, LogsActivity;
    public $timestamps = false;
    protected $table = 'miembros_equipo';

    protected $fillable = ['equipo_id', 'trabajador_id', 'fecha_union', 'activo'];

    protected $dates = ['fecha_union', 'deleted_at'];

    protected $casts = [
        'activo' => 'boolean'

    ];
    // Relación inversa al Trabajador
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id');
    }

    // Relación a Equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }
     // Accessor para verificar si es coordinador
    public function getEsCoordinadorAttribute()
    {
        return $this->equipo && $this->equipo->coordinador_id === $this->trabajador_id;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Miembro de equipo fue {$eventName}");
    }
}
