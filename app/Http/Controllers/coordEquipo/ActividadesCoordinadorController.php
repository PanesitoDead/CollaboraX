<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActividadesCoordinadorController extends Controller
{
    public function index()
    {
        return view('private.coord-equipo.actividades');
    }
}
