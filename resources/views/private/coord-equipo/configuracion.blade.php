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
                <button id="tab-btn-password" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="password">
                    Contraseña
                </button>
            </nav>
        </div>


        {{-- Tab Content: Perfil --}}
        <div id="tab-perfil" class="tab-content p-6">
            <div class="mb-6">
                <h3 class="text-lg font-medium">Información Personal</h3>
                <p class="text-gray-600">Puedes visualizar tu información personal y de contacto registrada</p>
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
                                <input type="text" name="nombre" id="nombre" value="{{ $trabajador->nombre_completo }}"
                                    disabled
                                    class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm cursor-not-allowed">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                                <input type="email" name="email" id="email" value="{{ $usuario->correo }}"
                                    disabled
                                    class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm cursor-not-allowed">
                            </div>

                            <div>
                                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" value="{{ $trabajador->telefono }}"
                                    disabled
                                    class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm cursor-not-allowed">
                            </div>

                            <div>
                                <label for="cargo" class="block text-sm font-medium text-gray-700">Cargo</label>
                                <input type="text" name="cargo" id="cargo" value="{{ $usuario->rol->nombre }}"
                                    disabled
                                    class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm cursor-not-allowed">
                            </div>

                            <div>
                                <label for="departamento" class="block text-sm font-medium text-gray-700">Equipo</label>
                                <input type="text" name="departamento" id="departamento"
                                    value="{{ $trabajador->equiposCoordinados->first()->nombre ?? 'No asignado' }}"
                                    disabled
                                    class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm cursor-not-allowed">
                            </div>

                            <div>
                                <label for="ubicacion" class="block text-sm font-medium text-gray-700">Empresa</label>
                                <input type="text" name="ubicacion" id="ubicacion" value="{{ $trabajador->empresa->nombre }}"
                                    disabled
                                    class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    {{-- <div class="w-full md:w-1/3">
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Foto de perfil</label>
                            <div class="flex flex-col items-center space-y-4">
                                <div class="h-32 w-32 rounded-full overflow-hidden bg-gray-100">
                                    <img id="avatar-preview"
                                        src="{{ $usuario->fotoPerfil ? asset($usuario->fotoPerfil->ruta) : asset('images/default-avatar.png') }}"
                                        alt="Avatar"
                                        class="h-full w-full object-cover">
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
                    </div> --}}
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

    </div>
</div>
@endsection

@push('scripts')
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
@endpush