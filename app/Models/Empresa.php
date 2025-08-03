<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Empresa extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'empresas';
    public $timestamps = false;

    protected $fillable = ['usuario_id', 'plan_servicio_id', 'nombre', 'descripcion', 'ruc', 'telefono'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function planServicio()
    {
        return $this->belongsTo(PlanServicio::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class);
    }
    
    public function totalMetasActivas(): int
    {
        // 1) Cargamos en memoria (eager loading) las áreas con sus metasActivas
        $this->loadMissing('areas.metasActivas');

        // 2) Sumamos la cantidad de metas activas en cada área
        $total = 0;
        foreach ($this->areas as $area) {
            $total += $area->metasActivas->count();
        }

        return $total;
    }

    public function progresoTotalPorPromedioAreas(): float
    {
        // 1. Cargamos las áreas en memoria (eager load)
        $this->loadMissing('areas');

        // Si no hay áreas, devolvemos 0
        if ($this->areas->isEmpty()) {
            return 0.0;
        }

        // 2. Sumamos el atributo porcentajeProgreso de cada área
        //    Laravel invocará automáticamente el getPorcentajeProgresoAttribute()
        $sumaPorcentajes = $this->areas->sum(function(Area $area) {
            return $area->porcentajeProgreso;
        });

        // 3. Calculamos el promedio dividiendo por la cantidad de áreas
        $cantidadAreas = $this->areas->count();
        $promedio = $sumaPorcentajes / $cantidadAreas;

        // 4. Redondeamos a 2 decimales
        return round($promedio, 2);
    }


    public function nro_usuarios()
    {
        return DB::table('empresas as e')
            ->join('areas as a', 'a.empresa_id','=', 'e.id')
            ->join('equipos as eq', 'eq.area_id', '=', 'a.id')
            ->join('miembros_equipo as et', 'et.equipo_id', '=', 'eq.id')
            ->join('trabajadores as t', 't.id', '=', 'et.trabajador_id')
            // Si quieres contar cuentas de usuario distintas:
            ->where('e.id', $this->id)
            ->distinct('t.usuario_id')
            ->count('t.usuario_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Empresa {$this->nombre} fue {$eventName}");
    }
}
