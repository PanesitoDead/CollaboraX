@extends('layouts.private.colaborador')

@section('title', 'Mis Reuniones')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mis Reuniones</h1>
            <p class="text-gray-600">Gestiona y participa en tus reuniones programadas</p>
        </div>
    </div>

    {{-- Tabs Content --}}
    <div class="bg-white rounded-lg border border-gray-300">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button 
                    onclick="activateTab('proximas')" 
                    class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    data-tab="proximas"
                >
                    Próximas
                </button>
                <button 
                    onclick="activateTab('hoy')" 
                    class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    data-tab="hoy"
                >
                    Hoy
                </button>
                <button 
                    onclick="activateTab('pasadas')" 
                    class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    data-tab="pasadas"
                >
                    Pasadas
                </button>
            </nav>
        </div>

        {{-- Contenidos --}}
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


    {{-- Modal Detalles --}}
    <div id="meeting-details-modal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50" onclick="closeMeetingDetailsModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">Detalles de la reunión</h3>
                    <button onclick="closeMeetingDetailsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-4 space-y-4" id="modal-content">
                    {{-- Se carga dinámicamente --}}
                </div>
                <div class="px-6 py-3 bg-gray-50 text-right">
                    <button 
                        onclick="closeMeetingDetailsModal()" 
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toasts --}}
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>
</div>
@endsection
@push('scripts')
<script>
    // Función para activar pestaña
    function activateTab(tabName) {
        // Reset botones
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('border-blue-600', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        // Oculta todos los contenidos
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));

        // Activa seleccionados
        const btn = document.querySelector(`.tab-button[data-tab="${tabName}"]`);
        btn.classList.add('border-blue-600', 'text-blue-600');
        btn.classList.remove('border-transparent', 'text-gray-500');
        document.getElementById(`tab-${tabName}`).classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Inicializa en “Próximas”
        activateTab('proximas');

        // Asigna evento a cada botón
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.addEventListener('click', () => activateTab(btn.dataset.tab));
        });
    });

    // Funciones reuseadas
    function joinMeeting(meetingId) {
        window.location.href = `{{ route('colaborador.reuniones', '') }}/${meetingId}`;
    }

    function viewMeetingDetails(meetingId) {
        fetch(`{{ route('colaborador.reuniones', '') }}/${meetingId}`, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json' 
            }
        })
        .then(r => r.json())
        .then(data => data.meeting && showDetails(data.meeting))
        .catch(() => showToast('Error al cargar detalles', 'error'));
    }

    function showDetails(meeting) {
        document.getElementById('modal-title').textContent = meeting.title;
        const cont = document.getElementById('modal-content');
        cont.innerHTML = `
            <div class="space-y-3">
                <p class="text-sm text-gray-700">${meeting.description}</p>
                <!-- Agrega más campos según necesites -->
            </div>
        `;
        document.getElementById('meeting-details-modal').classList.remove('hidden');
    }

    function closeMeetingDetailsModal() {
        document.getElementById('meeting-details-modal').classList.add('hidden');
    }

    function showToast(msg, type = 'success') {
        const toasts = document.getElementById('toast-container');
        const bg = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const t = document.createElement('div');
        t.className = `${bg} text-white px-4 py-2 rounded-lg shadow-lg`;
        t.textContent = msg;
        toasts.appendChild(t);
        setTimeout(() => toasts.removeChild(t), 3000);
    }
</script>
@endpush
