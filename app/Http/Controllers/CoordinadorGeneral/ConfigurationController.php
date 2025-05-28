<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    /**
     * Display the configuration page.
     */
    public function index()
    {
        return view('coordinador-general.configuracion.index');
    }

    /**
     * Update profile information (simulado - solo frontend).
     */
    public function updateProfile(Request $request)
    {
        // En un entorno real, aquí validarías y actualizarías los datos del usuario
        // Por ahora solo retornamos una respuesta exitosa para el frontend
        
        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente'
        ]);
    }

    /**
     * Update notification preferences (simulado - solo frontend).
     */
    public function updateNotifications(Request $request)
    {
        // En un entorno real, aquí guardarías las preferencias de notificaciones
        // Por ahora solo retornamos una respuesta exitosa para el frontend
        
        return response()->json([
            'success' => true,
            'message' => 'Preferencias de notificaciones guardadas'
        ]);
    }

    /**
     * Update security settings (simulado - solo frontend).
     */
    public function updateSecurity(Request $request)
    {
        // En un entorno real, aquí validarías la contraseña actual y actualizarías la nueva
        // También manejarías la configuración de 2FA
        // Por ahora solo retornamos una respuesta exitosa para el frontend
        
        return response()->json([
            'success' => true,
            'message' => 'Configuración de seguridad actualizada'
        ]);
    }
}
