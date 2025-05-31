<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanServicio extends Model
{
    use SoftDeletes;

    protected $table = 'plan_servicios';

    public $timestamps = false;
    
    protected $fillable = ['nombre', 'beneficios', 'costo_soles', 'cant_usuarios'];

    public function empresas()
    {
        return $this->hasMany(Empresa::class);
    }
}
