{{-- resources/views/admin/configuracion.blade.php --}}
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

    {{-- Tabs Container --}}
    <div class="bg-white rounded-lg border border-gray-300">
        {{-- Tabs Navigation --}}
        <nav class="border-b border-gray-200">
            <div class="flex space-x-8 px-6">
                @foreach($planes as $plan)
                    @php
                        // Generamos una clave única a partir del nombre, en minúsculas y sin espacios
                        $key = strtolower($plan->nombre);
                    @endphp

                    <button
                        type="button"
                        data-tab="{{ $key }}"
                        class="tab-button inline-flex items-center whitespace-nowrap border-b-2 
                               {{ $loop->first ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                               py-4 px-1 font-medium text-sm transition"
                        onclick="showTab('{{ $key }}')"
                    >
                        @if($key === 'standard')
                            <i data-lucide="credit-card" class="w-4 h-4 mr-1"></i>
                        @elseif($key === 'business')
                            <i data-lucide="briefcase" class="w-4 h-4 mr-1"></i>
                        @elseif($key === 'enterprise')
                            <i data-lucide="building-2" class="w-4 h-4 mr-1"></i>
                        @endif

                        {{ $plan->nombre }}
                    </button>
                @endforeach
            </div>
        </nav>

        {{-- Tab Contents --}}
        @foreach($planes as $plan)
            @php
                $key = strtolower($plan->nombre);
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
                                    value="{{ old("planes.{$key}.precio", $plan->costo_soles) }}"
                                    required
                                    disabled
                                    class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg"
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
                                    value="{{ old("planes.{$key}.usuarios", $plan->cant_usuarios) }}"
                                    required
                                    disabled
                                    class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg"
                                />
                            </div>
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
                                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg resize-none"
                            >{{ old("planes.{$key}.beneficios", $plan->beneficios) }}</textarea>
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
                            type="submit"
                            id="save-{{ $key }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hidden"
                        >
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
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
        document.getElementById(`tab-${tab}`).classList.remove('hidden');

        // 3. Marcar el botón activo
        const activeBtn = document.querySelector(`.tab-button[data-tab="${tab}"]`);
        activeBtn.classList.add('border-blue-500', 'text-blue-600');
        activeBtn.classList.remove('border-transparent', 'text-gray-500');

        // 4. Actualizar el hash de la URL sin recargar la página
        if (history.replaceState) {
            history.replaceState(null, null, `#${tab}`);
        } else {
            // fallback simple para navegadores muy viejos
            window.location.hash = tab;
        }
    }

    // Habilitar inputs para edición de cada plan
    function enableEdit(plan) {
        document.querySelectorAll(`#${plan}-fields input, #${plan}-fields textarea`).forEach(input => {
            input.disabled = false;
            input.classList.add('focus:ring-2', 'focus:ring-blue-500');
        });
        // Ocultar botón "Editar" y mostrar "Guardar"
        document.getElementById(`edit-${plan}`).classList.add('hidden');
        document.getElementById(`save-${plan}`).classList.remove('hidden');
    }

    // Mostrar por defecto el primer tab o el que venga en el hash
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Leer el hash de la URL (sin el '#')
        let hash = window.location.hash.substring(1);

        // 2. Comprobar que exista un tab con ese nombre; si no, usar el primero
        const allTabs = Array.from(document.querySelectorAll('.tab-button')).map(btn => btn.getAttribute('data-tab'));
        if (!allTabs.includes(hash)) {
            // Si no coincide con ningún tab, tomar el primero
            hash = document.querySelector('.tab-button').getAttribute('data-tab');
        }

        // 3. Mostrar la pestaña correspondiente
        showTab(hash);
    });

</script>
@endpush
