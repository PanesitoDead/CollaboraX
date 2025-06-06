<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;

    protected $table = 'areas';

    public $timestamps = false;

    protected $fillable = ['empresa_id', 'nombre', 'descripcion', 'codigo', 'color', 'activo', 'fecha_creacion'];
    
    protected $casts = ['fecha_creacion', 'deleted_at'];
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

     public function trabajadores(): BelongsToMany
    {
        return $this->belongsToMany(
            Trabajador::class,      // Modelo final
            'miembros_equipo',      // Tabla pivote
            'equipo_id',            // Foreign key del pivote que apunta a equipos.id
            'trabajador_id'         // Foreign key del pivote que apunta a trabajadores.id
        )
        // Hacemos join con equipos para obligar que el equipo pertenezca a esta área:
        ->join('equipos', 'miembros_equipo.equipo_id', '=', 'equipos.id')
        ->where('equipos.area_id', $this->id)
        // Para que Eloquent retorne sólo columnas de la tabla trabajadores:
        ->select('trabajadores.*');
    }

    public function coordinadores()
    {
        return $this->hasMany(AreaCoordinador::class);
    }

    public function coordinador()
    {
        return $this->hasOne(AreaCoordinador::class)->latestOfMany();
    }

    public function getNombreCompletoCoordinadorAttribute(): ?string
    {
        $coordinador = $this->coordinador()->first();
        if (!$coordinador) {
            return null;
        }

        $trabajador = $coordinador->trabajador;
        if (!$trabajador) {
            return 'Sin coordinador asignado';
        }

        return trim("{$trabajador->nombres} {$trabajador->apellido_paterno} {$trabajador->apellido_materno}");
    }

    public function metas(): HasManyThrough
    {
        return $this->hasManyThrough(
            \App\Models\Meta::class,
            \App\Models\Equipo::class,
            'area_id',     // FK de Equipo → Area
            'equipo_id',   // FK de Meta   → Equipo
            'id',          // PK de Area (opcional, Laravel lo asume automáticamente)
            'id'           // PK de Equipo (opcional, Laravel lo asume automáticamente)
        );
    }

    public function metasActivas(): HasManyThrough
    {
        return $this->metas()
                    ->whereIn('estado_id', [1, 2]);
    }

    public function getPorcentajeProgresoAttribute(): float
    {
        $metasConContadores = $this->metas()
            ->withCount([
                // Total de tareas por meta
                'tareas as tareas_count',

                // Solo tareas con estado_id = 3 (Completado)
                'tareas as tareas_finalizadas_count' => function ($query) {
                    $query->where('estado_id', 3);
                },
            ])
            ->get();

        $totalTareas = $metasConContadores->sum('tareas_count');
        $tareasFinalizadas = $metasConContadores->sum('tareas_finalizadas_count');

        if ($totalTareas === 0) {
            return 0.0;
        }

        return round(($tareasFinalizadas / $totalTareas) * 100, 2);
    }    
}
