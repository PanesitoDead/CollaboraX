<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mensaje extends Model
{
    use SoftDeletes;

    protected $table = 'mensajes';

    protected $fillable = ['remitente_id', 'destinatario_id', 'contenido', 'fecha', 'hora', 'leido', 'archivo_id'];

    public function remitente()
    {
        return $this->belongsTo(Trabajador::class, 'remitente_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(Trabajador::class, 'destinatario_id');
    }

    public function archivo()
    {
        return $this->belongsTo(Archivo::class);
    }
}
