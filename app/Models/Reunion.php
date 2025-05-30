<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reunion extends Model
{
    use SoftDeletes;

    protected $table = 'reuniones';

    protected $fillable = ['equipo_id', 'fecha', 'hora', 'duracion', 'descripcion', 'asunto', 'modalidad_id', 'sala'];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function modalidad()
    {
        return $this->belongsTo(Modalidad::class);
    }
}
