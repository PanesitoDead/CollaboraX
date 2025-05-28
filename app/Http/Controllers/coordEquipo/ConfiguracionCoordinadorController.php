<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Password;
use PhpParser\Node\Expr\Cast\Object_;
use Storage;
use User;
use Validator;

class ConfiguracionCoordinadorController extends Controller
{
    public function index()
    {
        // Datos del usuario actual
        //$usuario = Auth::user();
        $usuario = null;
        
        // Datos de configuración
        $configuracion = [
            'notificaciones' => [
                'email' => true,
                'push' => true,
                'reuniones' => true,
                'mensajes' => true,
                'actividades' => true,
                'metas' => false,
            ],
            'privacidad' => [
                'perfil_publico' => true,
                'mostrar_email' => false,
                'mostrar_telefono' => false,
                'actividad_visible' => true,
            ],
            'apariencia' => [
                'tema' => 'claro',
                'sidebar_compacta' => false,
                'mostrar_avatares' => true,
            ]
        ];
        
        // Datos para el formulario de perfil
        $perfil = [
            'nombre' => $usuario->name ?? 'Carlos Mendoza',
            'email' => $usuario->email ?? 'carlos.mendoza@empresa.com',
            'telefono' => $usuario->telefono ?? '+52 55 1234 5678',
            'cargo' => $usuario->cargo ?? 'Coordinador de Grupo',
            'departamento' => $usuario->departamento ?? 'Ventas',
            'ubicacion' => $usuario->ubicacion ?? 'Ciudad de México',
            'bio' => $usuario->bio ?? 'Coordinador de grupo con 5 años de experiencia en gestión de equipos de ventas.',
            'avatar' => $usuario->avatar ?? '/placeholder-40x40.png',
        ];
        
        return view('private.coord-equipo.configuracion', [
            'usuario' => $usuario,
            'configuracion' => $configuracion,
            'perfil' => $perfil,
        ]);
    }
    
    public function actualizarPerfil(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'ubicacion' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Lógica para actualizar el perfil
        // ...
        
        return redirect()->route('coordinador-grupo.configuracion')
            ->with('success', 'Perfil actualizado correctamente');
    }
    
    public function actualizarNotificaciones(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'boolean',
            'push' => 'boolean',
            'reuniones' => 'boolean',
            'mensajes' => 'boolean',
            'actividades' => 'boolean',
            'metas' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Lógica para actualizar notificaciones
        // ...
        
        return redirect()->route('coordinador-grupo.configuracion')
            ->with('success', 'Preferencias de notificaciones actualizadas');
    }
    
    public function actualizarPrivacidad(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'perfil_publico' => 'boolean',
            'mostrar_email' => 'boolean',
            'mostrar_telefono' => 'boolean',
            'actividad_visible' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Lógica para actualizar privacidad
        // ...
        
        return redirect()->route('coordinador-grupo.configuracion')
            ->with('success', 'Configuración de privacidad actualizada');
    }
    
    public function cambiarPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password_actual' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Verificar contraseña actual
        // if (!Hash::check($request->password_actual, Auth::user()->password)) {
        //     return redirect()->back()
        //         ->withErrors(['password_actual' => 'La contraseña actual es incorrecta'])
        //         ->withInput();
        // }
        
        // Actualizar contraseña
        // ...
        
        return redirect()->route('coordinador-grupo.configuracion')
            ->with('success', 'Contraseña actualizada correctamente');
    }
    
    public function actualizarApariencia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tema' => 'required|in:claro,oscuro',
            'sidebar_compacta' => 'boolean',
            'mostrar_avatares' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Lógica para actualizar apariencia
        // ...
        
        return redirect()->route('coordinador-grupo.configuracion')
            ->with('success', 'Preferencias de apariencia actualizadas');
    }
}
