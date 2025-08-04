@extends('layouts.public.auth')

@section('title', 'Crear Cuenta - CollaboraX')

@section('content')
<div class="max-w-5xl mx-auto mt-8 px-4">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden flex flex-col md:flex-row min-h-[600px]">
        <!-- Panel Izquierdo: Imagen o Información -->
        <div class="w-full md:w-1/2 bg-gray-100 px-6 py-8 flex flex-col justify-center items-center text-center">
            <i data-lucide="users" class="w-12 h-12 text-blue-600 mb-4"></i>
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">¡Bienvenido a CollaboraX!</h2>
            <p class="text-gray-600 mb-4">
                Completa el siguiente formulario para crear tu cuenta y comenzar a colaborar con tu equipo.
            </p>
            <img 
                src="{{ asset('images/register-side.png') }}" 
                alt="Registro CollaboraX" 
                class="w-96 h-96 object-cover hidden lg:block"
            >
        </div>
        <!-- Panel Derecho: Multi-Step Form -->
        <div class="w-full md:w-1/2 p-6">
            {{-- Indicador de pasos --}}
            <div class="bg-gray-100 px-4 py-3 rounded-full">
                <div class="flex items-center">
                    <div id="step-indicator-1" class="flex items-center text-blue-600">
                        <div class="w-7 h-7 rounded-full border-2 border-blue-600 flex items-center justify-center font-semibold">1</div>
                        <span class="ml-2 font-medium text-sm">Empresa</span>
                    </div>
                    <div class="flex-1 border-t-2 border-gray-300 mx-4"></div>
                    <div id="step-indicator-2" class="flex items-center text-gray-400">
                        <div class="w-7 h-7 rounded-full border-2 border-gray-300 flex items-center justify-center font-semibold">2</div>
                        <span class="ml-2 font-medium text-sm">Contacto</span>
                    </div>
                    <div class="flex-1 border-t-2 border-gray-300 mx-4"></div>
                    <div id="step-indicator-3" class="flex items-center text-gray-400">
                        <div class="w-7 h-7 rounded-full border-2 border-gray-300 flex items-center justify-center font-semibold">3</div>
                        <span class="ml-2 font-medium text-sm">Cuenta</span>
                    </div>
                </div>
            </div>

            {{-- Contenedor de formulario --}}
            <form method="POST" action="{{ route('register.post') }}" class="pt-6 space-y-6" id="multiStepForm" novalidate>
                @csrf

                {{-- Paso 1: Empresa --}}
                <div id="step-1">
                    <div class="text-center mb-4">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 shadow-sm">
                            <i data-lucide="briefcase" class="w-8 h-8 text-blue-600"></i>
                        </div>
                        <h1 class="text-xl font-bold text-gray-900">Paso 1: Empresa</h1>
                        <p class="text-gray-600 mt-1 text-sm">Información básica</p>
                    </div>

                    {{-- Errores paso 1 --}}
                    @if ($errors->hasAny(['nombre', 'descripcion', 'ruc']))
                        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex items-center mb-1">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-2"></i>
                                <span class="text-red-600 text-sm font-medium">Por favor corrige los errores</span>
                            </div>
                            <ul class="list-disc list-inside text-red-600 text-sm">
                                @foreach (['nombre', 'descripcion', 'ruc'] as $campo)
                                    @foreach ($errors->get($campo) as $mensaje)
                                        <li>{{ $mensaje }}</li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Nombre --}}
                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la empresa</label>
                        <div class="relative">
                            <i data-lucide="building" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                placeholder="Nombre comercial"
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-4">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <div class="relative">
                            <i data-lucide="file-text" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                            <textarea
                                id="descripcion"
                                name="descripcion"
                                placeholder="Breve descripción de la empresa"
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >{{ old('descripcion') }}</textarea>
                        </div>
                        @error('descripcion')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- RUC --}}
                    <div class="mb-6">
                        <label for="ruc" class="block text-sm font-medium text-gray-700 mb-1">RUC</label>
                        <div class="relative">
                            <i data-lucide="hash" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                            <input
                                type="text"
                                id="ruc"
                                name="ruc"
                                value="{{ old('ruc') }}"
                                placeholder="Número de RUC"
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        @error('ruc')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="button"
                            onclick="showStep(2)"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center"
                        >
                            <span>Siguiente</span>
                            <i data-lucide="chevron-right" class="w-5 h-5 ml-2"></i>
                        </button>
                    </div>
                </div>

                {{-- Paso 2: Contacto y Plan --}}
                <div id="step-2" class="hidden">
                    <div class="text-center mb-4">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 shadow-sm">
                            <i data-lucide="settings" class="w-8 h-8 text-blue-600"></i>
                        </div>
                        <h1 class="text-xl font-bold text-gray-900">Paso 2: Contacto y Plan</h1>
                        <p class="text-gray-600 mt-1 text-sm">Datos de registro personal</p>
                    </div>

                    {{-- Errores paso 2 --}}
                    @if ($errors->hasAny(['email_personal', 'telefono']))
                        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex items-center mb-1">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-2"></i>
                                <span class="text-red-600 text-sm font-medium">Por favor corrige los errores</span>
                            </div>
                            <ul class="list-disc list-inside text-red-600 text-sm">
                                @foreach (['email_personal', 'telefono'] as $campo)
                                    @foreach ($errors->get($campo) as $mensaje)
                                        <li>{{ $mensaje }}</li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Email Personal --}}
                    <div class="mb-4">
                        <label for="email_personal" class="block text-sm font-medium text-gray-700 mb-1">Correo personal</label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                            <input
                                type="email"
                                id="email_personal"
                                name="email_personal"
                                value="{{ old('email_personal') }}"
                                required
                                placeholder="tucorreo@ejemplo.com"
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        @error('email_personal')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div class="mb-4">
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <div class="relative">
                            <i data-lucide="phone" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                            <input
                                type="text"
                                id="telefono"
                                name="telefono"
                                value="{{ old('telefono') }}"
                                required
                                placeholder="Número de contacto"
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Información sobre planes --}}
                    <div class="mb-6">
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-start">
                                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0"></i>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-800 mb-1">Configuración de Plan</h4>
                                    <p class="text-sm text-blue-700">
                                        Podrás seleccionar y configurar tu plan de suscripción después del registro en la sección 
                                        <span class="font-semibold">Configuración → Suscripciones y Pagos</span> de tu cuenta.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button
                            type="button"
                            onclick="showStep(1)"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center"
                        >
                            <i data-lucide="chevron-left" class="w-5 h-5 mr-2"></i>
                            <span>Anterior</span>
                        </button>
                        <button
                            type="button"
                            onclick="showStep(3)"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center"
                        >
                            <span>Siguiente</span>
                            <i data-lucide="chevron-right" class="w-5 h-5 ml-2"></i>
                        </button>
                    </div>
                </div>

                {{-- Paso 3: Cuenta --}}
                <div id="step-3" class="hidden">
                    <div class="text-center mb-4">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 shadow-sm">
                            <i data-lucide="user-plus" class="w-8 h-8 text-blue-600"></i>
                        </div>
                        <h1 class="text-xl font-bold text-gray-900">Paso 3: Cuenta</h1>
                        <p class="text-gray-600 mt-1 text-sm">Configura tus credenciales</p>
                    </div>

                    {{-- Errores paso 3 --}}
                    @if ($errors->hasAny(['email', 'password', 'password_confirmation']))
                        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex items-center mb-1">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-2"></i>
                                <span class="text-red-600 text-sm font-medium">Por favor corrige los errores</span>
                            </div>
                            <ul class="list-disc list-inside text-red-600 text-sm">
                                @foreach (['email', 'password', 'password_confirmation'] as $campo)
                                    @foreach ($errors->get($campo) as $mensaje)
                                        <li>{{ $mensaje }}</li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Correo empresarial --}}
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo empresarial</label>
                        <div class="flex rounded-lg border border-gray-300 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent overflow-hidden">
                            <input
                                type="text"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                autocomplete="username"
                                placeholder="correo-empresa"
                                class="flex-grow pl-3 pr-2 py-2 focus:outline-none"
                            >
                            <span class="inline-flex items-center px-3 bg-gray-100 text-gray-500 select-none">
                                @collaborax.com
                            </span>
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Contraseña --}}
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                autocomplete="new-password"
                                placeholder="••••••••"
                                class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition-colors">
                                <i data-lucide="eye" id="eye-open-password" class="w-5 h-5"></i>
                                <i data-lucide="eye-off" id="eye-closed-password" class="w-5 h-5 hidden"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirmar contraseña --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                autocomplete="new-password"
                                placeholder="••••••••"
                                class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition-colors">
                                <i data-lucide="eye" id="eye-open-password_confirmation" class="w-5 h-5"></i>
                                <i data-lucide="eye-off" id="eye-closed-password_confirmation" class="w-5 h-5 hidden"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Términos y Condiciones --}}
                    <div class="mb-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="terms" name="terms" type="checkbox" value="true" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="font-light text-gray-700">He leído y <strong>acepto</strong> los <a href="#" class="font-medium text-blue-600 hover:underline">Términos y Condiciones</a></label>
                            </div>
                        </div>
                        @error('terms')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-between">
                        <button
                            type="button"
                            onclick="showStep(2)"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center"
                        >
                            <i data-lucide="chevron-left" class="w-5 h-5 mr-2"></i>
                            <span>Anterior</span>
                        </button>
                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center"
                        >
                            <span>Crear cuenta</span>
                            <i data-lucide="check" class="w-5 h-5 ml-2"></i>
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection

{{-- Scripts para lucide.js y control de pasos --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Control de pasos
        window.currentStep = 1;
        window.showStep = function(step) {
            // Ocultar todos los pasos
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('step-3').classList.add('hidden');

            // Desactivar indicadores
            for (let i = 1; i <= 3; i++) {
                const indicator = document.getElementById(`step-indicator-${i}`);
                indicator.classList.remove('text-blue-600');
                indicator.classList.add('text-gray-400');
                indicator.querySelector('div').classList.remove('border-blue-600');
                indicator.querySelector('div').classList.add('border-gray-300');
            }

            // Mostrar paso seleccionado
            document.getElementById(`step-${step}`).classList.remove('hidden');

            // Activar indicador
            const currentIndicator = document.getElementById(`step-indicator-${step}`);
            currentIndicator.classList.remove('text-gray-400');
            currentIndicator.classList.add('text-blue-600');
            currentIndicator.querySelector('div').classList.remove('border-gray-300');
            currentIndicator.querySelector('div').classList.add('border-blue-600');

            window.currentStep = step;
        };

        // Si hay errores de validación, mostrar el paso correspondiente
        @if ($errors->hasAny(['email_personal', 'telefono']))
            showStep(2);
        @elseif ($errors->hasAny(['email', 'password', 'password_confirmation']))
            showStep(3);
        @endif
    });

    // Función para mostrar/ocultar contraseñas
    function togglePassword(id) {
        const input = document.getElementById(id);
        const eyeOpen = document.getElementById(`eye-open-${id}`) || document.getElementById('eye-open');
        const eyeClosed = document.getElementById(`eye-closed-${id}`) || document.getElementById('eye-closed');

        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
</script>
@endpush
