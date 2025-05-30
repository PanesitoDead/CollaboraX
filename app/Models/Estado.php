<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estado extends Model
{
    use SoftDeletes;

    protected $table = 'estados';

    protected $fillable = ['nombre', 'descripcion'];

    public function metas()
    {
        return $this->hasMany(Meta::class);
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }
}
