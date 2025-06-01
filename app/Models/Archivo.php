<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archivo extends Model
{
    use SoftDeletes;

    protected $table = 'archivos';

    public $timestamps = false;

    protected $fillable = ['nombre', 'ruta', 'tipo', 'tamaño', 'fecha_subida'];

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'archivo_id');
    }
}
