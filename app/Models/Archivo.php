<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archivo extends Model
{
    use SoftDeletes;

    protected $table = 'archivos';

    public $timestamps = false;

    protected $fillable = ['nombre', 'ruta'];


    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'archivo_id','id');
    }

    // Accessor para formatear el tamaño
    public function getTamañoFormateadoAttribute()
    {
        $bytes = $this->tamaño;
        if ($bytes === 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}
