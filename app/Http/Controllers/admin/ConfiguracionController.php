<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\EmpresaRepositorio;
use App\Services\SuscripcionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ConfiguracionController extends Controller
{
    protected EmpresaRepositorio $empresaRepositorio;
    protected SuscripcionService $suscripcionService;
    
    public function __construct(EmpresaRepositorio $empresaRepositorio, SuscripcionService $suscripcionService)
    {
        $this->empresaRepositorio = $empresaRepositorio;
        $this->suscripcionService = $suscripcionService;
    }

    public function index()
    {
        $empresa = $this->getEmpresa();
        
        // Si getEmpresa() retorna un redirect, retornarlo directamente
        if ($empresa instanceof \Illuminate\Http\RedirectResponse) {
            return $empresa;
        }
        
        // Obtener datos de suscripción con manejo de errores
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();
        $usuarioId = $usuario->id;
        
        try {
            // Usar el nuevo endpoint de resumen para obtener datos completos
            $resumenCompleto = $this->suscripcionService->obtenerResumenUsuario($usuarioId);
            
            if ($resumenCompleto) {
                // Usar datos del resumen
                $planesDisponibles = $this->suscripcionService->obtenerPlanes();
                $suscripcionActual = $resumenCompleto['suscripcion_activa'] ?? null;
                $historialPagos = $resumenCompleto['historial_pagos'] ?? [];
                $historialSuscripciones = $resumenCompleto['historial_suscripciones'] ?? [];
                $tieneSuscripcionActiva = $resumenCompleto['tiene_suscripcion_activa'] ?? false;
                $diasRestantes = $resumenCompleto['dias_restantes'] ?? 0;
                
                // Formatear paginación para el historial
                $paginacion = [
                    'pagina_actual' => 1,
                    'total_paginas' => 1,
                    'total_elementos' => count($historialPagos),
                    'elementos_por_pagina' => count($historialPagos)
                ];
            } else {
                // Fallback al método anterior si el resumen no está disponible
                $planesDisponibles = $this->suscripcionService->obtenerPlanes();
                $suscripcionActual = $this->suscripcionService->obtenerSuscripcionActual($usuarioId);
                $historialResult = $this->suscripcionService->obtenerHistorialPagos($usuarioId, 1, 10);
                $historialPagos = $historialResult['pagos'] ?? [];
                $paginacion = $historialResult['paginacion'] ?? [];
                $historialSuscripciones = [];
                $tieneSuscripcionActiva = !empty($suscripcionActual);
                $diasRestantes = 0;
            }
        } catch (\Exception $e) {
            // Si hay error con el servicio de suscripciones, usar valores por defecto
            Log::error('Error obteniendo datos de suscripción: ' . $e->getMessage());
            $planesDisponibles = [];
            $suscripcionActual = null;
            $historialPagos = [];
            $paginacion = [];
            $historialSuscripciones = [];
            $tieneSuscripcionActiva = false;
            $diasRestantes = 0;
        }
        
        return view('private.admin.configuracion', [
            'empresa' => $empresa,
            'planesDisponibles' => $planesDisponibles,
            'suscripcionActual' => $suscripcionActual,
            'historialPagos' => $historialPagos,
            'paginacion' => $paginacion,
            'historialSuscripciones' => $historialSuscripciones,
            'tieneSuscripcionActiva' => $tieneSuscripcionActiva,
            'diasRestantes' => $diasRestantes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getEmpresa()
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();
        
        if (!$usuario) {
            Log::error('Usuario no autenticado intentando acceder a configuración');
            return redirect()->route('login')->with('error', 'Sesión expirada.');
        }
        
        $usuarioId = $usuario->id;
        
        $empresa = $this->empresaRepositorio->findOneBy('usuario_id', $usuarioId);
        
        if (!$empresa) {
            Log::warning('No se encontró empresa para usuario', ['user_id' => $usuarioId]);
            return redirect()->route('admin.dashboard.index')->with('error', 'No se pudo cargar la información de la empresa.');
        }
        
        // Asegurar que la relación usuario esté cargada
        if (!$empresa->usuario) {
            $empresa->load('usuario');
        }
        
        return $empresa;
    }

    /**
     * Actualizar datos de la empresa
     */
    public function updateEmpresa(Request $request)
    {
        try {
            $empresa = $this->getEmpresa();
            
            // Si getEmpresa() retorna un redirect, retornarlo directamente
            if ($empresa instanceof \Illuminate\Http\RedirectResponse) {
                return $empresa;
            }

            if (!$empresa) {
                Log::error('Empresa no encontrada al intentar actualizar');
                return redirect()->back()->with('error', 'No se pudo acceder a la información de la empresa.');
            }

            // Cargar usuario si no está cargado
            if (!$empresa->usuario) {
                $empresa->load('usuario');
            }

            Log::info('Iniciando actualización de empresa', [
                'empresa_id' => $empresa->id,
                'usuario_id' => $empresa->usuario_id
            ]);

            // Validar datos según la estructura real de la BD
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'ruc' => 'required|string|max:255|unique:empresas,ruc,' . $empresa->id,
                'descripcion' => 'nullable|string|max:255',
                'telefono' => 'required|string|max:255',
                'correo' => 'required|email|max:255|unique:usuarios,correo,' . ($empresa->usuario ? $empresa->usuario->id : ''),
                'correo_personal' => 'nullable|email|max:255|unique:usuarios,correo_personal,' . ($empresa->usuario ? $empresa->usuario->id : ''),
            ]);

            // Actualizar empresa (solo campos que existen en la tabla)
            $empresaData = [
                'nombre' => $validated['nombre'],
                'ruc' => $validated['ruc'],
                'descripcion' => $validated['descripcion'],
                'telefono' => $validated['telefono'],
            ];

            // Usar directamente el modelo en lugar del repositorio
            $empresa->update($empresaData);

            // Actualizar emails del usuario si cambiaron
            $usuario = $empresa->usuario;
            if ($usuario) {
                $usuarioData = [];
                
                if ($usuario->correo !== $validated['correo']) {
                    $usuarioData['correo'] = $validated['correo'];
                }
                
                if ($request->has('correo_personal') && $usuario->correo_personal !== $validated['correo_personal']) {
                    $usuarioData['correo_personal'] = $validated['correo_personal'];
                }
                
                if (!empty($usuarioData)) {
                    $usuario->update($usuarioData);
                }
            }

            return redirect()->back()->with('success', 'Datos de la empresa actualizados correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en actualización de empresa', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error inesperado al actualizar empresa: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Error al actualizar los datos. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Actualizar contraseña del usuario
     */
    public function updatePassword(Request $request)
    {
        Log::info('=== INICIO updatePassword ===', [
            'request_data' => $request->except(['current_password', 'new_password', 'new_password_confirmation']),
            'has_current_password' => $request->has('current_password'),
            'has_new_password' => $request->has('new_password'),
            'user_authenticated' => Auth::check()
        ]);

        try {
            /** @var \App\Models\Usuario $usuario */
            $usuario = Auth::user();
            
            if (!$usuario) {
                Log::error('Usuario no autenticado intentando cambiar contraseña');
                return redirect()->route('login')->with('error', 'Usuario no autenticado.');
            }

            $usuarioId = $usuario->id;
            
            Log::info('Usuario encontrado para cambio de contraseña', [
                'user_id' => $usuarioId,
                'email' => $usuario->correo
            ]);

            // Validar datos con reglas de seguridad estrictas
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => [
                    'required',
                    'string',
                    'confirmed',
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                ],
            ], [
                'current_password.required' => 'La contraseña actual es obligatoria.',
                'new_password.required' => 'La nueva contraseña es obligatoria.',
                'new_password.confirmed' => 'La confirmación de contraseña no coincide.',
                'new_password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'new_password.letters' => 'La contraseña debe contener al menos una letra.',
                'new_password.mixed_case' => 'La contraseña debe contener al menos una mayúscula y una minúscula.',
                'new_password.numbers' => 'La contraseña debe contener al menos un número.',
                'new_password.symbols' => 'La contraseña debe contener al menos un símbolo especial.',
            ]);

            // Verificar contraseña actual
            if (!Hash::check($validated['current_password'], $usuario->clave)) {
                Log::warning('Contraseña actual incorrecta', ['user_id' => $usuarioId]);
                return redirect()->back()->withErrors([
                    'current_password' => 'La contraseña actual es incorrecta.'
                ])->withInput($request->except('current_password', 'new_password', 'new_password_confirmation'));
            }

            // Verificar que la nueva contraseña sea diferente a la actual
            if (Hash::check($validated['new_password'], $usuario->clave)) {
                Log::warning('Nueva contraseña igual a la actual', ['user_id' => $usuarioId]);
                return redirect()->back()->withErrors([
                    'new_password' => 'La nueva contraseña debe ser diferente a la actual.'
                ])->withInput($request->except('current_password', 'new_password', 'new_password_confirmation'));
            }

            // Actualizar contraseña
            $usuario->clave = Hash::make($validated['new_password']);
            $guardado = $usuario->save();

            if ($guardado) {
                Log::info('Contraseña actualizada exitosamente', ['user_id' => $usuarioId, 'email' => $usuario->correo]);
                
                // Cerrar sesión después de cambiar contraseña por seguridad
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('success', 'Contraseña actualizada correctamente. Por favor, inicie sesión nuevamente.');
            } else {
                Log::error('Error al guardar la nueva contraseña', ['user_id' => $usuarioId]);
                return redirect()->back()->with('error', 'Error al guardar la nueva contraseña.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en cambio de contraseña', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput($request->except('current_password', 'new_password', 'new_password_confirmation'));
        } catch (\Exception $e) {
            Log::error('Error inesperado al actualizar contraseña: ' . $e->getMessage(), ['user_id' => $usuario->id ?? 'unknown']);
            return redirect()->back()->with('error', 'Error al actualizar la contraseña. Por favor, inténtelo de nuevo.');
        }
    }
}
