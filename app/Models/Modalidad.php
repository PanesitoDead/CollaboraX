<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modalidad extends Model
{
    use SoftDeletes;

    protected $table = 'modalidades';

    protected $fillable = ['nombre', 'descripcion'];

    public function reuniones()
    {
        return $this->hasMany(Reunion::class);
    }
}
