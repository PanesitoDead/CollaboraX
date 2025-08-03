<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AreaCoordinador extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'areas_coordinador';

    public $timestamps = false;

    protected $fillable = ['area_id', 'trabajador_id', 'fecha_inicio', 'fecha_fin'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Coordinador de área fue {$eventName}");
    }
}
