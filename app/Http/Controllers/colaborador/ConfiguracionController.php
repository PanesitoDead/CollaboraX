<?php

namespace App\Http\Controllers\colaborador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ConfiguracionController extends Controller
{
    public function index()
    {
        // Obtener datos simulados de sesión o valores por defecto
        $profileData = Session::get('profileData', [
            'name'       => 'Juan Pérez',
            'email'      => 'juan.perez@empresa.com',
            'phone'      => '+52 555 123 4567',
            'position'   => 'Desarrollador Frontend',
            'department' => 'Tecnología',
            'bio'        => 'Desarrollador frontend especializado en React y Vue.js con 3 años de experiencia.',
            'avatar'     => '/placeholder-40x40.png',
            'location'   => 'Ciudad de México, México',
            'linkedin'   => 'https://linkedin.com/in/juanperez',
            'github'     => 'https://github.com/juanperez',
        ]);

        $accountSettings = Session::get('accountSettings', [
            'language'    => 'es',
            'timezone'    => 'America/Mexico_City',
            'date_format' => 'DD/MM/YYYY',
            'time_format' => '24h',
        ]);

        $privacySettings = Session::get('privacySettings', [
            'profile_visibility'    => 'team',
            'show_email'            => false,
            'show_phone'            => false,
            'allow_messages'        => true,
            'show_activity_status'  => true,
        ]);

        return view('private.colaborador.configuracion', compact(
            'profileData',
            'accountSettings',
            'privacySettings'
        ));
    }

    /**
     * Simular actualización del perfil guardando en sesión.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email',
            'phone'      => 'nullable|string|max:20',
            'position'   => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'bio'        => 'nullable|string|max:500',
            'location'   => 'nullable|string|max:100',
            'linkedin'   => 'nullable|url',
            'github'     => 'nullable|url',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Obtener datos existentes de sesión
        $profileData = Session::get('profileData', []);

        // Manejar avatar simulado: conservar placeholder o reproducción local
        if ($request->hasFile('avatar')) {
            // Para simulación, no almacenamos realmente, solo obtenemos nombre
            $avatarFile = $request->file('avatar');
            $avatarName = 'avatars/' . time() . '_' . $avatarFile->getClientOriginalName();
            // Simular almacenamiento copiando a almacenamiento local temporal
            $avatarFile->storeAs('public/'.$avatarName);
            $profileData['avatar'] = '/storage/' . $avatarName;
        }

        // Actualizar datos en el array simulado
        foreach (['name','email','phone','position','department','bio','location','linkedin','github'] as $field) {
            if ($request->filled($field)) {
                $profileData[$field] = $request->input($field);
            }
        }

        // Guardar en sesión
        Session::put('profileData', $profileData);

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente (simulado)',
            'profileData' => $profileData,
        ]);
    }

    /**
     * Simular actualización de configuración de cuenta en sesión.
     */
    public function updateAccount(Request $request)
    {
        $request->validate([
            'language'    => 'required|in:es,en',
            'timezone'    => 'required|string',
            'date_format' => 'required|in:DD/MM/YYYY,MM/DD/YYYY,YYYY-MM-DD',
            'time_format' => 'required|in:12h,24h',
        ]);

        $accountSettings = $request->only(['language','timezone','date_format','time_format']);
        Session::put('accountSettings', $accountSettings);

        return response()->json([
            'success' => true,
            'message' => 'Configuración de cuenta actualizada correctamente (simulado)',
            'accountSettings' => $accountSettings,
        ]);
    }

    /**
     * Simular actualización de privacidad guardando en sesión.
     */
    public function updatePrivacy(Request $request)
    {
        $request->validate([
            'profile_visibility'   => 'required|in:public,team,private',
            'show_email'           => 'boolean',
            'show_phone'           => 'boolean',
            'allow_messages'       => 'boolean',
            'show_activity_status' => 'boolean',
        ]);

        $privacySettings = [
            'profile_visibility'   => $request->profile_visibility,
            'show_email'           => $request->boolean('show_email'),
            'show_phone'           => $request->boolean('show_phone'),
            'allow_messages'       => $request->boolean('allow_messages'),
            'show_activity_status' => $request->boolean('show_activity_status'),
        ];
        Session::put('privacySettings', $privacySettings);

        return response()->json([
            'success' => true,
            'message' => 'Configuración de privacidad actualizada correctamente (simulado)',
            'privacySettings' => $privacySettings,
        ]);
    }

    /**
     * Simular cambio de contraseña sin verificar BD.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => ['required','confirmed','min:8'],
        ]);

        // En simulación, siempre aceptar la contraseña actual
        Session::put('simulated_password', bcrypt($request->new_password));

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente (simulado)',
        ]);
    }
}
