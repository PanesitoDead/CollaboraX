@extends('layouts.private.coord-equipo')

@section('title', 'Configuración')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Configuración</h1>
            <p class="text-gray-600">Administra tus preferencias y datos personales</p>
        </div>
    </div>

    {{-- Tabs Content --}}
    <div class="bg-white rounded-lg border border-gray-300">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button id="tab-btn-perfil" 
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600"
                        data-tab="perfil">
                    Perfil
                </button>
                <button id="tab-btn-notificaciones" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="notificaciones">
                    Notificaciones
                </button>
                <button id="tab-btn-privacidad" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="privacidad">
                    Privacidad
                </button>
                <button id="tab-btn-password" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="password">
                    Contraseña
                </button>
                <button id="tab-btn-apariencia" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="apariencia">
                    Apariencia
                </button>
            </nav>
        </div>

        {{-- Tab Content: Perfil --}}
        <div id="tab-perfil" class="tab-content p-6">
            <div class="mb-6">
                <h3 class="text-lg font-medium">Información Personal</h3>
                <p class="text-gray-600">Actualiza tu información personal y de contacto</p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('coord-equipo.configuracion.perfil') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-2/3 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                                <input type="text" name="nombre" id="nombre" value="{{ $perfil['nombre'] }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('nombre')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                                <input type="email" name="email" id="email" value="{{ $perfil['email'] }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" value="{{ $perfil['telefono'] }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('telefono')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="cargo" class="block text-sm font-medium text-gray-700">Cargo</label>
                                <input type="text" name="cargo" id="cargo" value="{{ $perfil['cargo'] }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('cargo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="departamento" class="block text-sm font-medium text-gray-700">Departamento</label>
                                <input type="text" name="departamento" id="departamento" value="{{ $perfil['departamento'] }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('departamento')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="ubicacion" class="block text-sm font-medium text-gray-700">Ubicación</label>
                                <input type="text" name="ubicacion" id="ubicacion" value="{{ $perfil['ubicacion'] }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('ubicacion')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700">Biografía</label>
                            <textarea name="bio" id="bio" rows="4" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ $perfil['bio'] }}</textarea>
                            @error('bio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="w-full md:w-1/3">
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Foto de perfil</label>
                            <div class="flex flex-col items-center space-y-4">
                                <div class="h-32 w-32 rounded-full overflow-hidden bg-gray-100">
                                    <img id="avatar-preview" src="{{ asset($perfil['avatar']) }}" alt="Avatar" class="h-full w-full object-cover">
                                </div>
                                <div class="flex flex-col items-center">
                                    <label for="avatar" class="cursor-pointer px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        Cambiar foto
                                    </label>
                                    <input id="avatar" name="avatar" type="file" accept="image/*" class="sr-only">
                                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF hasta 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab Content: Notificaciones --}}
        <div id="tab-notificaciones" class="tab-content p-6 hidden">
            <div class="mb-6">
                <h3 class="text-lg font-medium">Preferencias de Notificaciones</h3>
                <p class="text-gray-600">Configura cómo y cuándo quieres recibir notificaciones</p>
            </div>
            
            <form action="{{ route('coord-equipo.configuracion.notificaciones') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-4">
                    <h4 class="text-base font-medium">Canales de notificación</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Correo electrónico</p>
                                <p class="text-sm text-gray-500">Recibir notificaciones por correo electrónico</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email" value="1" class="sr-only peer" {{ $configuracion['notificaciones']['email'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Notificaciones push</p>
                                <p class="text-sm text-gray-500">Recibir notificaciones en el navegador</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="push" value="1" class="sr-only peer" {{ $configuracion['notificaciones']['push'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h4 class="text-base font-medium">Tipos de notificaciones</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Reuniones</p>
                                <p class="text-sm text-gray-500">Notificaciones sobre reuniones programadas</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="reuniones" value="1" class="sr-only peer" {{ $configuracion['notificaciones']['reuniones'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Mensajes</p>
                                <p class="text-sm text-gray-500">Notificaciones sobre mensajes nuevos</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="mensajes" value="1" class="sr-only peer" {{ $configuracion['notificaciones']['mensajes'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Actividades</p>
                                <p class="text-sm text-gray-500">Notificaciones sobre actividades asignadas</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="actividades" value="1" class="sr-only peer" {{ $configuracion['notificaciones']['actividades'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Metas</p>
                                <p class="text-sm text-gray-500">Notificaciones sobre metas y objetivos</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="metas" value="1" class="sr-only peer" {{ $configuracion['notificaciones']['metas'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Guardar preferencias
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab Content: Privacidad --}}
        <div id="tab-privacidad" class="tab-content p-6 hidden">
            <div class="mb-6">
                <h3 class="text-lg font-medium">Configuración de Privacidad</h3>
                <p class="text-gray-600">Controla qué información es visible para otros usuarios</p>
            </div>
            
            <form action="{{ route('coord-equipo.configuracion.privacidad') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">Perfil público</p>
                            <p class="text-sm text-gray-500">Tu perfil será visible para todos los usuarios de la plataforma</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="perfil_publico" value="1" class="sr-only peer" {{ $configuracion['privacidad']['perfil_publico'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">Mostrar correo electrónico</p>
                            <p class="text-sm text-gray-500">Tu correo electrónico será visible en tu perfil</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="mostrar_email" value="1" class="sr-only peer" {{ $configuracion['privacidad']['mostrar_email'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">Mostrar teléfono</p>
                            <p class="text-sm text-gray-500">Tu número de teléfono será visible en tu perfil</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="mostrar_telefono" value="1" class="sr-only peer" {{ $configuracion['privacidad']['mostrar_telefono'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">Actividad visible</p>
                            <p class="text-sm text-gray-500">Tu actividad reciente será visible para otros usuarios</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="actividad_visible" value="1" class="sr-only peer" {{ $configuracion['privacidad']['actividad_visible'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Guardar configuración
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab Content: Contraseña --}}
        <div id="tab-password" class="tab-content p-6 hidden">
            <div class="mb-6">
                <h3 class="text-lg font-medium">Cambiar Contraseña</h3>
                <p class="text-gray-600">Actualiza tu contraseña para mantener tu cuenta segura</p>
            </div>
            
            <form action="{{ route('coord-equipo.configuracion.password') }}" method="POST" class="space-y-6 max-w-md">
                @csrf
                
                <div>
                    <label for="password_actual" class="block text-sm font-medium text-gray-700">Contraseña actual</label>
                    <input type="password" name="password_actual" id="password_actual" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('password_actual')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Nueva contraseña</label>
                    <input type="password" name="password" id="password" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cambiar contraseña
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab Content: Apariencia --}}
        <div id="tab-apariencia" class="tab-content p-6 hidden">
            <div class="mb-6">
                <h3 class="text-lg font-medium">Preferencias de Apariencia</h3>
                <p class="text-gray-600">Personaliza la apariencia de la plataforma</p>
            </div>
            
            <form action="{{ route('coord-equipo.configuracion.apariencia') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-4">
                    <h4 class="text-base font-medium">Tema</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative border rounded-lg p-4 cursor-pointer {{ $configuracion['apariencia']['tema'] == 'claro' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                            <input type="radio" name="tema" value="claro" class="sr-only" {{ $configuracion['apariencia']['tema'] == 'claro' ? 'checked' : '' }}>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">Tema claro</p>
                                    <p class="text-sm text-gray-500">Fondo blanco con texto oscuro</p>
                                </div>
                                <div class="h-10 w-10 bg-white border border-gray-300 rounded-md flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                        </label>
                        
                        <label class="relative border rounded-lg p-4 cursor-pointer {{ $configuracion['apariencia']['tema'] == 'oscuro' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                            <input type="radio" name="tema" value="oscuro" class="sr-only" {{ $configuracion['apariencia']['tema'] == 'oscuro' ? 'checked' : '' }}>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">Tema oscuro</p>
                                    <p class="text-sm text-gray-500">Fondo oscuro con texto claro</p>
                                </div>
                                <div class="h-10 w-10 bg-gray-800 border border-gray-700 rounded-md flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h4 class="text-base font-medium">Opciones de interfaz</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Barra lateral compacta</p>
                                <p class="text-sm text-gray-500">Mostrar la barra lateral en modo compacto</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="sidebar_compacta" value="1" class="sr-only peer" {{ $configuracion['apariencia']['sidebar_compacta'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Mostrar avatares</p>
                                <p class="text-sm text-gray-500">Mostrar avatares de usuarios en las listas</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="mostrar_avatares" value="1" class="sr-only peer" {{ $configuracion['apariencia']['mostrar_avatares'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Guardar preferencias
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad de tabs
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    function showTab(tabId) {
        // Ocultar todos los contenidos de tabs
        tabContents.forEach(content => {
            content.classList.add('hidden');
        });
        
        // Quitar estado activo de todos los botones
        tabButtons.forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Mostrar el contenido del tab seleccionado
        document.getElementById('tab-' + tabId).classList.remove('hidden');
        
        // Añadir estado activo al botón seleccionado
        document.querySelector(`[data-tab="${tabId}"]`).classList.remove('border-transparent', 'text-gray-500');
        document.querySelector(`[data-tab="${tabId}"]`).classList.add('border-blue-500', 'text-blue-600');
    }
    
    // Asignar event listeners a los botones de tabs
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            showTab(this.getAttribute('data-tab'));
        });
    });
    
    // Preview de imagen de perfil
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');
    
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Inicializar el primer tab como activo
    // showTab('perfil');
});
</script>
@endsection