<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PlanServicio extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'plan_servicios';

    public $timestamps = false;
    
    protected $fillable = ['nombre', 'beneficios', 'costo_soles', 'cant_usuarios'];

    public function empresas()
    {
        return $this->hasMany(Empresa::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Plan de servicio '{$this->nombre}' fue {$eventName}");
    }
}
