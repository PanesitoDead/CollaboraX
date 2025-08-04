<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta sección.');
        }

        $user = Auth::user();
        
        try {
            $userRole = null;
            
            // Obtener el ID del usuario del email (ya que ese es el identificador)
            $userEmail = null;
            if (isset($user->correo)) {
                $userEmail = $user->correo;
            } elseif (isset($user->email)) {
                $userEmail = $user->email;
            }
            
            if (!$userEmail) {
                Log::error('No se pudo obtener el email del usuario');
                return $this->accessDeniedResponse($request);
            }
            
            // Buscar el rol del usuario en la base de datos directamente
            $userWithRole = DB::table('usuarios')
                ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
                ->where('usuarios.correo', $userEmail)
                ->select('roles.nombre as rol_nombre', 'usuarios.id as user_id')
                ->first();
            
            if ($userWithRole) {
                $userRole = $userWithRole->rol_nombre;
            }
            
            // Si no se encontró el rol, denegamos el acceso
            if (!$userRole) {
                Log::warning('Usuario sin rol definido: ' . $userEmail);
                return $this->accessDeniedResponse($request);
            }

            // Verificar si el rol del usuario está en la lista de roles permitidos
            if (!in_array($userRole, $roles)) {
                Log::warning('Usuario con rol no permitido', [
                    'user_email' => $userEmail,
                    'user_role' => $userRole,
                    'allowed_roles' => $roles,
                    'url' => $request->url()
                ]);
                return $this->accessDeniedResponse($request);
            }

            return $next($request);
            
        } catch (\Exception $e) {
            // En caso de error, denegamos el acceso
            Log::error('Error en CheckRole middleware: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'url' => $request->url()
            ]);
            return $this->accessDeniedResponse($request);
        }
    }

    /**
     * Respuesta cuando el acceso es denegado
     */
    private function accessDeniedResponse(Request $request)
    {
        // Si es una petición AJAX, devolver JSON
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'No tienes permisos para acceder a esta sección.',
                'code' => 403
            ], 403);
        }

        // Si es una petición normal, mostrar vista de acceso denegado
        return response()->view('errors.403-simple', [
            'message' => 'No tienes permisos para acceder a esta sección.'
        ], 403);
    }
}