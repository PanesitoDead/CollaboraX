{{-- resources/views/super-admin/configuracion.blade.php --}}
@extends('layouts.super-admin.super-admin')

@section('title', 'Configuración')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp

<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuración</h1>
            <p class="text-gray-600">Ajustes generales del sistema</p>
        </div>
    </div>

    {{-- Mostrar mensajes de éxito --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="check-circle" class="h-5 w-5 text-green-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Éxito</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Mostrar mensajes de error de sesión --}}
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Mostrar error si existe --}}
    @if(isset($error))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error al cargar planes</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ $error }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Mostrar mensaje si no hay planes --}}
    @if(empty($planes))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="info" class="h-5 w-5 text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">No hay planes disponibles</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>No se encontraron planes en el sistema. Contacte al administrador.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Tabs Container --}}
        <div class="bg-white rounded-lg border border-gray-300">
            {{-- Tabs Navigation --}}
            <nav class="border-b border-gray-200">
                <div class="flex space-x-8 px-6">
                    @foreach($planes as $plan)
                        @php
                            // Convertir array de API a objeto si es necesario
                            if (is_array($plan)) {
                                $plan = (object) $plan;
                            }
                            
                            // Verificar que el plan tenga las propiedades necesarias
                            if (!isset($plan->nombre) || !isset($plan->id)) {
                                continue;
                            }
                            
                            // Generamos una clave única a partir del nombre
                            $key = preg_replace('/[^a-z0-9]+/', '_', strtolower($plan->nombre));
                        @endphp

                        <button
                            type="button"
                            data-tab="{{ $key }}"
                            class="tab-button inline-flex items-center whitespace-nowrap border-b-2 
                                   {{ $loop->first ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                                   py-4 px-1 font-medium text-sm transition"
                            onclick="showTab('{{ $key }}')"
                        >
                            @if(str_contains(strtolower($plan->nombre), 'standard'))
                                <i data-lucide="credit-card" class="w-4 h-4 mr-1"></i>
                            @elseif(str_contains(strtolower($plan->nombre), 'business'))
                                <i data-lucide="briefcase" class="w-4 h-4 mr-1"></i>
                            @elseif(str_contains(strtolower($plan->nombre), 'enterprise'))
                                <i data-lucide="building-2" class="w-4 h-4 mr-1"></i>
                            @else
                                <i data-lucide="package" class="w-4 h-4 mr-1"></i>
                            @endif

                            {{ $plan->nombre }}
                        </button>
                    @endforeach
                </div>
            </nav>

            {{-- Tab Contents --}}
            @foreach($planes as $plan)
                @php
                    // Convertir array de API a objeto si es necesario
                    if (is_array($plan)) {
                        $plan = (object) $plan;
                    }
                    
                    // Verificar que el plan tenga las propiedades necesarias
                    if (!isset($plan->nombre) || !isset($plan->id)) {
                        continue;
                    }
                    
                    $key = preg_replace('/[^a-z0-9]+/', '_', strtolower($plan->nombre));
                @endphp

                <div 
                    id="tab-{{ $key }}" 
                    class="tab-content p-6 {{ $loop->first ? '' : 'hidden' }}"
                >
                    <form 
                        method="POST" 
                        action="{{ route('super-admin.configuracion.planes.update', $plan->id) }}"
                    >
                        @csrf
                        @method('PUT')

                        <h3 class="text-lg font-medium text-gray-900 mb-4">Plan {{ $plan->nombre }}</h3>

                        {{-- Contenedor de campos del plan --}}
                        <div id="{{ $key }}-fields">
                            {{-- Nombre del Plan --}}
                            <div class="mb-4">
                                <label 
                                    for="nombre" 
                                    class="block mb-1 text-sm font-medium text-gray-700"
                                >
                                    Nombre del Plan
                                </label>
                                <input
                                    type="text"
                                    name="nombre"
                                    id="{{ $key }}_nombre"
                                    value="{{ old("planes.{$key}.nombre", $plan->nombre ?? '') }}"
                                    required
                                    disabled
                                    class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 disabled:bg-gray-50 disabled:text-gray-500 transition-all duration-200"
                                />
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                {{-- Precio --}}
                                <div>
                                    <label 
                                        for="costo_soles" 
                                        class="block mb-1 text-sm font-medium text-gray-700"
                                    >
                                        Precio (PEN)
                                    </label>
                                    <input
                                        type="number"
                                        step="0.1"
                                        name="costo_soles"
                                        id="{{ $key }}_precio"
                                        value="{{ old("planes.{$key}.precio", $plan->costo_soles ?? 0) }}"
                                        required
                                        disabled
                                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 disabled:bg-gray-50 disabled:text-gray-500 transition-all duration-200"
                                    />
                                </div>

                                {{-- Cantidad de Usuarios --}}
                                <div>
                                    <label 
                                        for="cant_usuarios" 
                                        class="block mb-1 text-sm font-medium text-gray-700"
                                    >
                                        Cantidad de Usuarios
                                    </label>
                                    <input
                                        type="number"
                                        name="cant_usuarios"
                                        id="{{ $key }}_usuarios"
                                        value="{{ old("planes.{$key}.usuarios", $plan->cant_usuarios ?? 1) }}"
                                        required
                                        disabled
                                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 disabled:bg-gray-50 disabled:text-gray-500 transition-all duration-200"
                                    />
                                </div>
                            </div>

                            <div class="mt-4">
                                {{-- Descripción --}}
                                <label 
                                    for="descripcion" 
                                    class="block mb-1 text-sm font-medium text-gray-700"
                                >
                                    Descripción
                                </label>
                                <input
                                    type="text"
                                    name="descripcion"
                                    id="{{ $key }}_descripcion"
                                    value="{{ old("planes.{$key}.descripcion", $plan->descripcion ?? '') }}"
                                    disabled
                                    class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 disabled:bg-gray-50 disabled:text-gray-500 transition-all duration-200"
                                />
                            </div>

                            <div class="mt-4">
                                {{-- Beneficios --}}
                                <label 
                                    for="beneficios" 
                                    class="block mb-1 text-sm font-medium text-gray-700"
                                >
                                    Beneficios
                                </label>
                                <textarea
                                    name="beneficios"
                                    id="beneficios"
                                    rows="3"
                                    required
                                    disabled
                                    class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg resize-none bg-gray-50 disabled:bg-gray-50 disabled:text-gray-500 transition-all duration-200"
                                >{{ old("planes.{$key}.beneficios", $plan->beneficios ?? '') }}</textarea>
                            </div>
                        </div>

                        {{-- Botones de Editar/Guardar --}}
                        <div class="flex justify-end mt-6 space-x-2">
                            <button
                                type="button"
                                onclick="enableEdit('{{ $key }}')"
                                id="edit-{{ $key }}"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
                            >
                                Editar Plan
                            </button>
                            <button
                                type="button"
                                onclick="cancelEdit('{{ $key }}')"
                                id="cancel-{{ $key }}"
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 hidden"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                id="save-{{ $key }}"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 hidden"
                                onclick="console.log('Guardando plan {{ $key }}...'); return true;"
                            >
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Mostrar/ocultar contenido de tabs
    function showTab(tab) {
        // 1. Ocultar todas las pestañas y resetear clases en los botones
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        // 2. Mostrar la pestaña seleccionada
        const tabElement = document.getElementById(`tab-${tab}`);
        
        if (tabElement) {
            tabElement.classList.remove('hidden');
        } else {
            console.error(`No se encontró el tab con ID: tab-${tab}`);
            return;
        }

        // 3. Marcar el botón activo
        const activeBtn = document.querySelector(`.tab-button[data-tab="${tab}"]`);
        
        if (activeBtn) {
            activeBtn.classList.add('border-blue-500', 'text-blue-600');
            activeBtn.classList.remove('border-transparent', 'text-gray-500');
        }

        // 4. Actualizar el hash de la URL sin recargar la página
        if (history.replaceState) {
            history.replaceState(null, null, `#${tab}`);
        } else {
            window.location.hash = tab;
        }
    }

    // Habilitar inputs para edición de cada plan
    function enableEdit(plan) {
        // Buscar el tab activo
        const activeTab = document.querySelector(`.tab-content:not(.hidden)`);
        
        if (activeTab) {
            // Buscar todos los inputs y textareas en el tab activo
            const inputs = activeTab.querySelectorAll('input, textarea');
            
            inputs.forEach((input) => {
                input.disabled = false;
                input.classList.add('focus:ring-2', 'focus:ring-blue-500', 'border-blue-300');
                input.classList.remove('bg-gray-50');
            });
        }
        
        // Cambiar visibilidad de botones
        const editBtn = document.getElementById(`edit-${plan}`);
        const cancelBtn = document.getElementById(`cancel-${plan}`);
        const saveBtn = document.getElementById(`save-${plan}`);
        
        if (editBtn) editBtn.classList.add('hidden');
        if (cancelBtn) cancelBtn.classList.remove('hidden');
        if (saveBtn) saveBtn.classList.remove('hidden');
    }

    // Cancelar edición y volver al modo de solo lectura
    function cancelEdit(plan) {
        // Buscar el tab activo
        const activeTab = document.querySelector(`.tab-content:not(.hidden)`);
        
        if (activeTab) {
            // Deshabilitar todos los inputs y textareas
            const inputs = activeTab.querySelectorAll('input, textarea');
            
            inputs.forEach((input) => {
                input.disabled = true;
                input.classList.remove('focus:ring-2', 'focus:ring-blue-500', 'border-blue-300');
                input.classList.add('bg-gray-50');
            });
        }
        
        // Cambiar visibilidad de botones
        const editBtn = document.getElementById(`edit-${plan}`);
        const cancelBtn = document.getElementById(`cancel-${plan}`);
        const saveBtn = document.getElementById(`save-${plan}`);
        
        if (editBtn) editBtn.classList.remove('hidden');
        if (cancelBtn) cancelBtn.classList.add('hidden');
        if (saveBtn) saveBtn.classList.add('hidden');
    }

    // Mostrar por defecto el primer tab o el que venga en el hash
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Leer el hash de la URL (sin el '#')
        let hash = window.location.hash.substring(1);

        // 2. Comprobar que exista un tab con ese nombre; si no, usar el primero
        const allTabs = Array.from(document.querySelectorAll('.tab-button')).map(btn => btn.getAttribute('data-tab'));
        if (!allTabs.includes(hash) || !hash) {
            // Si no coincide con ningún tab, tomar el primero
            const firstButton = document.querySelector('.tab-button');
            hash = firstButton ? firstButton.getAttribute('data-tab') : null;
        }
        
        // 3. Mostrar la pestaña correspondiente
        if (hash) {
            showTab(hash);
        }
    });
</script>
@endpush
