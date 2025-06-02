@extends('layouts.private.colaborador')

@section('title', 'Invitaciones')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invitaciones</h1>
            <p class="text-gray-600">Gestiona tus invitaciones a equipos y proyectos</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-lg border border-gray-300 overflow-hidden">
        {{-- Tabs Invitaciones --}}
        <nav class="border-b border-gray-200">
            <div class="flex space-x-8 px-6">
                <button type="button" data-tab="pendientes" class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-blue-500 py-4 px-1 font-medium text-sm text-blue-600 transition" onclick="showTab('pendientes')">
                    <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                    <span>Pendientes</span>
                    @if($nro_invitacionesPendientes > 0)
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $nro_invitacionesPendientes }}
                        </span>
                    @endif
                </button>
                <button type="button" data-tab="historial" class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-transparent py-4 px-1 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition" onclick="showTab('historial')">
                    <i data-lucide="history" class="w-4 h-4 mr-1"></i>
                    Historial
                </button>
            </div>
        </nav>
        <div id="tab-pendientes" class="tab-content p-6">
            @include('partials.colaborador.invitaciones-pendientes')
        </div>
        <div id="tab-historial" class="tab-content hidden p-6">
            {{-- @include('partials.colaborador.historial-invitaciones') --}}
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function showTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        document.getElementById(`tab-${tab}`).classList.remove('hidden');
        const activeBtn = document.querySelector(`.tab-button[data-tab="${tab}"]`);
        activeBtn.classList.add('border-blue-500', 'text-blue-600');
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
    }

    // Inicializa default
    document.addEventListener('DOMContentLoaded', () => showTab('pendientes'));
</script>
@endpush
