<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Empresa extends Model
{
    use SoftDeletes;

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
}
