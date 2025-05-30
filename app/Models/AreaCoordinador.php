<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaCoordinador extends Model
{
    use SoftDeletes;

    protected $table = 'areas_coordinador';

    protected $fillable = ['area_id', 'trabajador_id', 'fecha_inicio', 'fecha_fin'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }
}
