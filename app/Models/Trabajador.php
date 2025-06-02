<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trabajador extends Model
{
    use SoftDeletes;

    protected $table = 'trabajadores';

    public $timestamps = false;

    protected $fillable = ['usuario_id', 'nombres', 'apellido_paterno', 'apellido_materno', 'doc_identidad', 'fecha_nacimiento', 'telefono', 'empresa_id'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function miembrosEquipo()
    {
        return $this->hasMany(MiembroEquipo::class);
    }

    public function getEquipoAttribute()
    {
        // 1) Primero obtenemos el primer “MiembroEquipo” de este trabajador
        $miembro = $this->miembrosEquipo()->first();
        if (!$miembro) {
            // No hay fila en miembros_equipo → no tiene equipo → no tiene área
            return null;
        }

        // 2) A partir de ese MiembroEquipo, obtenemos el Equipo
        //    (asumimos que MiembroEquipo tiene relación belongsTo hacia Equipo,
        //     o que podemos buscarlo así:)
        $equipo = Equipo::find($miembro->equipo_id);

        if (! $equipo) {
            // Por si hubiera algún dato inconsistente
            return null;
        }
        // 3) Finalmente, devolvemos el equipo asociado a ese MiembroEquipo
        return $equipo; // puede ser null si somehow el equipo no tuviera area_id
    }

    public function getAreaAttribute(): ?Area
    {
        // 1) Primero obtenemos el primer “MiembroEquipo” de este trabajador
        $miembro = $this->miembrosEquipo()->first();
        if (!$miembro) {
            // No hay fila en miembros_equipo → no tiene equipo → no tiene área
            return null;
        }

        // 2) A partir de ese MiembroEquipo, obtenemos el Equipo
        //    (asumimos que MiembroEquipo tiene relación belongsTo hacia Equipo,
        //     o que podemos buscarlo así:)
        $equipo = Equipo::find($miembro->equipo_id);

        if (! $equipo) {
            // Por si hubiera algún dato inconsistente
            return null;
        }

        // 3) Finalmente, devolvemos el área asociada a ese Equipo
        return $equipo->area; // puede ser null si somehow el equipo no tuviera area_id
    }
    public function metas()
    {
        return $this->hasManyThrough(
            Meta::class,
            MiembroEquipo::class,
            'trabajador_id', // Clave foránea en MiembroEquipo
            'equipo_id',     // Clave foránea en Meta
            'id',            // Clave local en Trabajador
            'equipo_id'      // Clave local en MiembroEquipo que relaciona con Meta
        );
    }

    public function tareas()
    {
        return Tarea::whereIn('meta_id', function ($query) {
            $query->select('id')
                ->from('metas')
                ->whereIn('equipo_id', function ($query2) {
                    $query2->select('equipo_id')
                            ->from('miembros_equipo')
                            ->where('trabajador_id', $this->id);
                });
        })->get();
    }

    public function reuniones()
    {
        return Reunion::whereIn('equipo_id', function ($query) {
            $query->select('equipo_id')
                ->from('miembros_equipo')
                ->where('trabajador_id', $this->id);
        })->get();
    }

    public function coordinaciones()
    {
        return $this->hasMany(AreaCoordinador::class);
    }

    public function invitaciones()
    {
        return $this->hasMany(Invitacion::class);
    }

    public function mensajesEnviados()
    {
        return $this->hasMany(Mensaje::class, 'remitente_id');
    }

    public function mensajesRecibidos()
    {
        return $this->hasMany(Mensaje::class, 'destinatario_id');
    }

    public function equiposCoordinados()
    {
        return $this->hasMany(Equipo::class, 'coordinador_id');
    }

    // Accessor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombres . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno;
    }

    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'miembros_equipo');
    }

    // Accessor para iniciales
    public function getInicialesAttribute()
    {
        $nombres = explode(' ', $this->nombres);
        $iniciales = substr($nombres[0], 0, 1);
        if (isset($nombres[1])) {
            $iniciales .= substr($nombres[1], 0, 1);
        } else {
            $iniciales .= substr($this->apellido_paterno, 0, 1);
        }
        return strtoupper($iniciales);
    }

    // Accessor para obtener la foto de perfil a través del usuario
    public function getFotoUrlAttribute()
    {
        if ($this->usuario && $this->usuario->fotoArchivo) {
            return asset('storage/' . $this->usuario->fotoArchivo->ruta);
        }
        return null;
    }

    
}
