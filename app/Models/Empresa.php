<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
