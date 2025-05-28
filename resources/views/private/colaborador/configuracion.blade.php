@extends('layouts.private.colaborador')

@section('title', 'Configuración')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuración</h1>
            <p class="text-gray-600">Ajustes generales del sistema</p>
        </div>
    </div>

    {{-- Container --}}
    <div class="bg-white rounded-lg border border-gray-300 overflow-hidden">
        {{-- Tabs --}}
        <nav class="border-b border-gray-200">
            <div class="flex space-x-8 px-6">
                <button
                    type="button"
                    data-tab="perfil"
                    class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-blue-500 py-4 px-1 font-medium text-sm text-blue-600 transition"
                    onclick="showTab('perfil')"
                >
                    <i data-lucide="user" class="w-4 h-4 mr-1"></i>
                    Perfil
                </button>
                <button
                    type="button"
                    data-tab="seguridad"
                    class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-transparent py-4 px-1 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition"
                    onclick="showTab('seguridad')"
                >
                    <i data-lucide="lock" class="w-4 h-4 mr-1"></i>
                    Seguridad
                </button>
            </div>
        </nav>

        {{-- Tab Contents --}}
        <div id="tab-perfil" class="tab-content p-6">
            @include('partials.colaborador.perfil-form')
        </div>
        <div id="tab-seguridad" class="tab-content p-6 hidden">
            @include('partials.colaborador.seguridad-form')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showTab(tab) {
        // Oculta todos los contenidos
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
        });
        // Resetea todos los botones
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        // Muestra el contenido activo
        document.getElementById(`tab-${tab}`).classList.remove('hidden');
        // Activa el botón correspondiente
        const activeBtn = document.querySelector(`.tab-button[data-tab="${tab}"]`);
        activeBtn.classList.add('border-blue-500', 'text-blue-600');
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
    }

    // Inicializa la pestaña por defecto (perfil)
    document.addEventListener('DOMContentLoaded', function() {
        showTab('perfil');
    });
</script>
@endpush
