<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Invitacion extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'invitaciones';
    public $timestamps = false;

    protected $fillable = ['equipo_id', 'trabajador_id', 'fecha_invitacion', 'fecha_expiracion', 'fecha_respuesta', 'estado'];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function area()
{
    return $this->hasOneThrough(
        Area::class,     // modelo final
        Equipo::class,   // modelo intermedio
        'id',            // llave en Equipo (intermedio)
        'id',            // llave en Area (final)
        'equipo_id',     // llave en Invitacion (local)
        'area_id'        // llave en Equipo (hacia Area)
    );
}

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Invitación para trabajador ID {$this->trabajador_id} fue {$eventName}");
    }

}
