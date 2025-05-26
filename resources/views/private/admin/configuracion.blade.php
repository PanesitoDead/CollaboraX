{{-- resources/views/admin/configuracion.blade.php --}}
@extends('layouts.private.admin')

@section('title', 'Configuración')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Configuración</h1>
            <p class="text-gray-600">Configuración general del sistema y empresa</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-lg border border-gray-300">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showConfigTab('empresa')" 
                        class="config-tab-button border-purple-500 text-purple-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="empresa">
                    Información de la Empresa
                </button>
                <button onclick="showConfigTab('usuarios')" 
                        class="config-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="usuarios">
                    Gestión de Usuarios
                </button>
                <button onclick="showConfigTab('notificaciones')" 
                        class="config-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="notificaciones">
                    Notificaciones
                </button>
                <button onclick="showConfigTab('seguridad')" 
                        class="config-tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="seguridad">
                    Seguridad
                </button>
            </nav>
        </div>

        {{-- Tab Content: Empresa --}}
        <div id="config-tab-empresa" class="config-tab-content p-6">
             {{-- action="{{ route('admin.configuracion.empresa.update') }}" --}}
            <form method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium mb-4">Información Básica</h3>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="nombre_empresa" class="block text-sm font-medium text-gray-700">Nombre de la Empresa</label>
                                <input type="text" name="nombre_empresa" id="nombre_empresa" 
                                       value="{{ old('nombre_empresa', $empresa->nombre ?? 'TechSolutions S.A.') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label for="ruc" class="block text-sm font-medium text-gray-700">RUC</label>
                                <input type="text" name="ruc" id="ruc" 
                                       value="{{ old('ruc', $empresa->ruc ?? '20123456789') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" 
                                       value="{{ old('telefono', $empresa->telefono ?? '+51 1 234-5678') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email Corporativo</label>
                                <input type="email" name="email" id="email" 
                                       value="{{ old('email', $empresa->email ?? 'contacto@techsolutions.com') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                        <textarea name="direccion" id="direccion" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">{{ old('direccion', $empresa->direccion ?? 'Av. Javier Prado Este 123, San Isidro, Lima, Perú') }}</textarea>
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción de la Empresa</label>
                        <textarea name="descripcion" id="descripcion" rows="4" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">{{ old('descripcion', $empresa->descripcion ?? 'Empresa líder en soluciones tecnológicas innovadoras para el sector empresarial.') }}</textarea>
                    </div>

                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700">Logo de la Empresa</label>
                        <div class="mt-1 flex items-center space-x-4">
                            <div class="h-16 w-16 rounded-lg bg-gray-100 flex items-center justify-center">
                                @if(isset($empresa->logo))
                                    <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo" class="h-16 w-16 rounded-lg object-cover">
                                @else
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>
                            <input type="file" name="logo" id="logo" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG hasta 2MB. Recomendado: 200x200px</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tab Content: Usuarios --}}
        <div id="config-tab-usuarios" class="config-tab-content p-6 hidden">
            {{-- action="{{ route('admin.configuracion.usuarios.update') }}" --}}
            <form  method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium mb-4">Configuración de Usuarios</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium">Registro automático</h4>
                                    <p class="text-sm text-gray-500">Permitir que los usuarios se registren automáticamente</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="registro_automatico" class="sr-only peer" 
                                           {{ old('registro_automatico', $config->registro_automatico ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium">Verificación de email</h4>
                                    <p class="text-sm text-gray-500">Requerir verificación de email para nuevos usuarios</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="verificacion_email" class="sr-only peer" 
                                           {{ old('verificacion_email', $config->verificacion_email ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium">Autenticación de dos factores</h4>
                                    <p class="text-sm text-gray-500">Habilitar 2FA para todos los usuarios</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="two_factor_auth" class="sr-only peer" 
                                           {{ old('two_factor_auth', $config->two_factor_auth ?? false) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Configuración de Sesiones</h4>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="session_timeout" class="block text-sm font-medium text-gray-700">Tiempo de sesión (minutos)</label>
                                <input type="number" name="session_timeout" id="session_timeout" 
                                       value="{{ old('session_timeout', $config->session_timeout ?? 120) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label for="max_login_attempts" class="block text-sm font-medium text-gray-700">Intentos máximos de login</label>
                                <input type="number" name="max_login_attempts" id="max_login_attempts" 
                                       value="{{ old('max_login_attempts', $config->max_login_attempts ?? 5) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                            Guardar Configuración
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tab Content: Notificaciones --}}
        <div id="config-tab-notificaciones" class="config-tab-content p-6 hidden">
             {{-- action="{{ route('admin.configuracion.notificaciones.update') }}" --}}
            <form method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium mb-4">Configuración de Notificaciones</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium">Notificaciones por email</h4>
                                    <p class="text-sm text-gray-500">Enviar notificaciones importantes por correo electrónico</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="email_notifications" class="sr-only peer" 
                                           {{ old('email_notifications', $config->email_notifications ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium">Notificaciones push</h4>
                                    <p class="text-sm text-gray-500">Mostrar notificaciones en tiempo real en el navegador</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="push_notifications" class="sr-only peer" 
                                           {{ old('push_notifications', $config->push_notifications ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium">Resumen diario</h4>
                                    <p class="text-sm text-gray-500">Enviar resumen diario de actividades a coordinadores</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="daily_summary" class="sr-only peer" 
                                           {{ old('daily_summary', $config->daily_summary ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Configuración de Email</h4>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="smtp_host" class="block text-sm font-medium text-gray-700">Servidor SMTP</label>
                                <input type="text" name="smtp_host" id="smtp_host" 
                                       value="{{ old('smtp_host', $config->smtp_host ?? 'smtp.gmail.com') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label for="smtp_port" class="block text-sm font-medium text-gray-700">Puerto SMTP</label>
                                <input type="number" name="smtp_port" id="smtp_port" 
                                       value="{{ old('smtp_port', $config->smtp_port ?? 587) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label for="smtp_username" class="block text-sm font-medium text-gray-700">Usuario SMTP</label>
                                <input type="text" name="smtp_username" id="smtp_username" 
                                       value="{{ old('smtp_username', $config->smtp_username ?? '') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label for="smtp_password" class="block text-sm font-medium text-gray-700">Contraseña SMTP</label>
                                <input type="password" name="smtp_password" id="smtp_password" 
                                       placeholder="••••••••"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                            Guardar Configuración
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tab Content: Seguridad --}}
        <div id="config-tab-seguridad" class="config-tab-content p-6 hidden">
            {{-- action="{{ route('admin.configuracion.seguridad.update') }}" --}}
            <form  method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium mb-4">Configuración de Seguridad</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium">Forzar HTTPS</h4>
                                    <p class="text-sm text-gray-500">Redirigir todas las conexiones HTTP a HTTPS</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="force_https" class="sr-only peer" 
                                           {{ old('force_https', $config->force_https ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium">Auditoría de accesos</h4>
                                    <p class="text-sm text-gray-500">Registrar todos los accesos al sistema</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="audit_logs" class="sr-only peer" 
                                           {{ old('audit_logs', $config->audit_logs ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium">Bloqueo por IP</h4>
                                    <p class="text-sm text-gray-500">Bloquear IPs sospechosas automáticamente</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="ip_blocking" class="sr-only peer" 
                                           {{ old('ip_blocking', $config->ip_blocking ?? false) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Políticas de Contraseña</h4>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="min_password_length" class="block text-sm font-medium text-gray-700">Longitud mínima</label>
                                <input type="number" name="min_password_length" id="min_password_length" 
                                       value="{{ old('min_password_length', $config->min_password_length ?? 8) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label for="password_expiry_days" class="block text-sm font-medium text-gray-700">Expiración (días)</label>
                                <input type="number" name="password_expiry_days" id="password_expiry_days" 
                                       value="{{ old('password_expiry_days', $config->password_expiry_days ?? 90) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                        
                        <div class="mt-4 space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="require_uppercase" 
                                       {{ old('require_uppercase', $config->require_uppercase ?? true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Requerir mayúsculas</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="require_numbers" 
                                       {{ old('require_numbers', $config->require_numbers ?? true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Requerir números</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="require_symbols" 
                                       {{ old('require_symbols', $config->require_symbols ?? true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Requerir símbolos</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                            Guardar Configuración
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tab functionality for configuration
function showConfigTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.config-tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active state from all buttons
    document.querySelectorAll('.config-tab-button').forEach(button => {
        button.classList.remove('border-purple-500', 'text-purple-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('config-tab-' + tabName).classList.remove('hidden');
    
    // Add active state to selected button
    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    activeButton.classList.remove('border-transparent', 'text-gray-500');
    activeButton.classList.add('border-purple-500', 'text-purple-600');
}

// Initialize first tab as active
document.addEventListener('DOMContentLoaded', function() {
    showConfigTab('empresa');
});
</script>
@endsection