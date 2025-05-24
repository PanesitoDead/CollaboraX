<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        // if (!auth()->check() || auth()->user()->rol !== $role) {
        //     abort(403, 'No tienes permisos para acceder a esta sección.');
        // }

        return $next($request);
    }
}