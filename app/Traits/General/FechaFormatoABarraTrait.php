<?php

namespace App\Traits\General;

trait FechaFormatoABarraTrait
{
    public function convertirFecha($fecha)
    {
        // Verifica si la fecha no es nula y si contiene guiones
        if ($fecha && str_contains($fecha, '-')) {
            // Convierte la fecha al formato con barras
            return str_replace('-', '/', $fecha);
        }
        // Si la fecha es nula o no contiene guiones, la devuelve tal cual
        return $fecha;
    }
}
