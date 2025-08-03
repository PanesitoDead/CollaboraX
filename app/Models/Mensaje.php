<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Mensaje extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'mensajes';
    public $timestamps = false;
    protected $fillable = ['remitente_id', 'destinatario_id', 'contenido', 'fecha', 'hora', 'leido', 'archivo_id'];

    public function remitente()
    {
        return $this->belongsTo(Trabajador::class, 'remitente_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(Trabajador::class, 'destinatario_id');
    }

    public function archivo()
    {
        return $this->belongsTo(Archivo::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['leido']) // No registrar solo cambios de lectura
            ->setDescriptionForEvent(fn(string $eventName) => "Mensaje fue {$eventName}");
    }
}
