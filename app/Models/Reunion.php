<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Reunion extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'reuniones';
    public $timestamps = false;

    protected $fillable = ['equipo_id', 'fecha', 'hora', 'duracion', 'descripcion', 'asunto', 'modalidad_id', 'sala', 'estado', 'observacion', 'link_moderador', 'link_participante', 'meeting_id'];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function modalidad()
    {
        return $this->belongsTo(Modalidad::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Reunión '{$this->asunto}' fue {$eventName}");
    }
}
