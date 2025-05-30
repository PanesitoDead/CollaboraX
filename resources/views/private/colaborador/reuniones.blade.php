@extends('layouts.private.colaborador')

@section('title', 'Mis Reuniones')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mis Reuniones</h1>
            <p class="text-gray-600">Gestiona y participa en tus reuniones programadas</p>
        </div>
    </div>

    {{-- Tabs Content --}}
    <div class="bg-white rounded-lg border border-gray-300 overflow-hidden">
        {{-- Tabs Meetings --}}
        <nav class="border-b border-gray-200">
            <div class="flex space-x-8 px-6">
                <button type="button" data-tab="proximas" class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-blue-500 py-4 px-1 font-medium text-sm text-blue-600 transition" onclick="activateTab('proximas')">
                    <i data-lucide="calendar" class="w-4 h-4 mr-1"></i>Próximas
                </button>
                <button type="button" data-tab="hoy" class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-transparent py-4 px-1 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition" onclick="activateTab('hoy')">
                    <i data-lucide="sun" class="w-4 h-4 mr-1"></i>Hoy
                </button>
                <button type="button" data-tab="pasadas" class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-transparent py-4 px-1 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition" onclick="activateTab('pasadas')">
                    <i data-lucide="clock" class="w-4 h-4 mr-1"></i>Pasadas
                </button>
            </div>
        </nav>
        {{-- Contents Meetings --}}
        <div id="tab-proximas" class="tab-content p-6 space-y-4">
            @include('partials.shared.table.meetings-table', [
                'meetings'    => $upcomingMeetings,
                'showJoin'    => true,
                'showEdit'    => false,
                'showDetails' => true
            ])
        </div>
        <div id="tab-hoy" class="tab-content hidden p-6 space-y-4">
            @include('partials.shared.table.meetings-table', [
                'meetings'    => $todayMeetings,
                'showJoin'    => true,
                'showEdit'    => false,
                'showDetails' => true
            ])
        </div>
        <div id="tab-pasadas" class="tab-content hidden p-6 space-y-4">
            @include('partials.shared.table.meetings-table', [
                'meetings'    => $pastMeetings,
                'showJoin'    => false,
                'showEdit'    => false,
                'showDetails' => true
            ])
        </div>
    </div>

    {{-- Toasts --}}
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>
</div>
@endsection
@push('scripts')
<script>
    // Función para activar pestaña
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
    function activateTab(tab) {
        showTab(tab); // use same logic
    }
    document.addEventListener('DOMContentLoaded', () => showTab('proximas'));
</script>
@endpush
