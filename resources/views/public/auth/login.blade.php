@extends('layouts.public.auth')

@section('title', 'Iniciar Sesión - CollaboraX')

@section('content')
<div class="max-w-4xl mx-auto mt-8 px-4">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden min-h-[600px] flex flex-col md:flex-row">
        <!-- Panel Izquierdo: Formulario de Login -->
        <div class="w-full md:w-1/2 p-6">
            <!-- Logo y título -->
            <div class="text-center mb-6">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 shadow-sm">
                    <i data-lucide="lock" class="w-8 h-8 text-blue-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Bienvenido de vuelta</h1>
                <p class="text-gray-600">Inicia sesión en tu cuenta</p>
            </div>
            <!-- Mensajes de error -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-2"></i>
                        <span class="text-red-600 text-sm font-medium">
                            {{ $errors->first() }}
                        </span>
                    </div>
                </div>
            @endif
            <!-- Mensaje de éxito -->
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-2"></i>
                        <span class="text-green-600 text-sm font-medium">
                            {{ session('success') }}
                        </span>
                    </div>
                </div>
            @endif
            <!-- Formulario de login -->
            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required 
                            autocomplete="email"
                            placeholder="tu@email.com"
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition-colors"
                        >
                            <i data-lucide="eye" id="eye-open" class="w-5 h-5"></i>
                            <i data-lucide="eye-off" id="eye-closed" class="w-5 h-5 hidden"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Botón de login -->
                <button 
                    type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                >
                    <span>Iniciar Sesión</span>
                    <i data-lucide="chevron-right" class="w-5 h-5 ml-2"></i>
                </button>
            </form>

            <!-- Link a registro -->
            <div class="mt-6 text-center">
                <span class="text-gray-600">¿No tienes cuenta?</span>
                <a 
                    href="{{ route('register') }}" 
                    class="text-blue-600 hover:underline font-medium ml-1"
                >
                    Crear cuenta nueva
                </a>
            </div>
        </div>

        <!-- Panel Derecho: Espacio para texto o imagen -->
        <div class="w-full md:w-1/2 bg-gray-100 px-6 flex flex-col justify-between">
            <!-- Texto descriptivo arriba -->
            <div class="flex flex-col items-center text-center py-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Nos alegra verte aquí</h2>
                <p class="text-gray-600 mb-4">CollaboraX es la plataforma ideal para gestionar tus tareas, reuniones, equipos, metas y más.</p>
                <p class="text-gray-500 text-sm">Inicia sesión para continuar colaborando con tu equipo.</p>
            </div>
            <!-- Imagen abajo (esconde en pantallas pequeñas) -->
            <div class="flex justify-center">
                <img src="{{ asset('/images/login-side.png') }}" alt="Colabora" class="hidden md:block w-96 h-96 object-cover">
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        passwordInput.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}
</script>
@endsection
