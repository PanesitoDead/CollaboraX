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
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button 
                    class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    data-tab="pendientes"
                >
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pendientes
                        @if($estadisticas['total_pendientes'] > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $estadisticas['total_pendientes'] }}
                            </span>
                        @endif
                    </div>
                </button>
                <button 
                    class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    data-tab="historial"
                >
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Historial
                    </div>
                </button>
            </nav>
        </div>

        {{-- Tab Contents --}}
        <div class="p-6 space-y-6">
            <div id="pendientes-content" class="tab-content">
                @include('partials.colaborador.invitaciones-pendientes', ['invitaciones' => $invitacionesPendientes])
            </div>
            <div id="historial-content" class="tab-content hidden">
                @include('partials.colaborador.historial-invitaciones', ['historial' => $historialInvitaciones])
            </div>
        </div>
    </div>

    {{-- Detalles Modal --}}
    <div id="detalles-modal" class="fixed inset-0 bg-black/50 hidden z-50">
        <div class="flex items-center justify-center h-full p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Detalles de la Invitación</h3>
                    <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="modal-content" class="px-6 py-4 space-y-4">
                    {{-- cargado dinámicamente --}}
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                    <button 
                        onclick="rechazarInvitacion()"
                        class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200"
                    >
                        Rechazar
                    </button>
                    <button 
                        onclick="aceptarInvitacion()"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                    >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Container --}}
    <div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50"></div>
</div>
@endsection

@push('scripts')
<script>
    let currentInvitacionId = null;

    // Función para cambiar pestaña
    function switchTab(tab) {
        // Reset de clases en botones
        document.querySelectorAll('.tab-button').forEach(b => {
            b.classList.remove('border-blue-600','text-blue-600');
            b.classList.add('border-transparent','text-gray-500');
        });
        // Ocultar todos los contenidos
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));

        // Activar el botón y contenido seleccionado
        const btn = document.querySelector(`.tab-button[data-tab="${tab}"]`);
        btn.classList.remove('border-transparent','text-gray-500');
        btn.classList.add('border-blue-600','text-blue-600');
        document.getElementById(`${tab}-content`).classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Inicializo la pestaña por defecto
        switchTab('pendientes');

        // Asigno el evento click a todos los botones de las pestañas
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.addEventListener('click', () => {
                switchTab(btn.getAttribute('data-tab'));
            });
        });
    });

    // Modal de detalles
    function cerrarModal() {
        document.getElementById('detalles-modal').classList.add('hidden');
    }
    function verDetalles(id) {
        currentInvitacionId = id;
        fetch(`{{ route('colaborador.invitaciones', '') }}/${id}`)
            .then(r => r.json())
            .then(data => {
                const m = document.getElementById('modal-content');
                m.innerHTML = `
                    <div class="flex items-center space-x-4">
                        <img src="${data.coordinador_avatar}" class="w-12 h-12 rounded-full" alt="">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">${data.equipo}</h4>
                            <p class="text-sm text-gray-600">Invitado por ${data.coordinador}</p>
                            <p class="text-xs text-gray-500">Fecha: ${new Date(data.fecha_invitacion).toLocaleDateString()}</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h5 class="font-medium text-gray-900 mb-2">Mensaje:</h5>
                        <p class="text-gray-700">${data.mensaje}</p>
                    </div>
                `;
                document.getElementById('detalles-modal').classList.remove('hidden');
            });
    }

    // Aceptar / Rechazar
    function aceptarInvitacion() {
        fetch(`{{ route('colaborador.invitaciones', '') }}/${currentInvitacionId}`, {
            method:'POST',
            headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(r => r.json())
        .then(res => {
            mostrarToast(res.message,'success');
            cerrarModal();
            setTimeout(() => location.reload(), 1500);
        });
    }
    function rechazarInvitacion() {
        fetch(`{{ route('colaborador.invitaciones', '') }}/${currentInvitacionId}`, {
            method:'DELETE',
            headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(r => r.json())
        .then(res => {
            mostrarToast(res.message,'error');
            cerrarModal();
            setTimeout(() => location.reload(), 1500);
        });
    }

    // Toast
    function mostrarToast(msg,type='info'){
        const colors = { success:'bg-green-500', error:'bg-red-500', info:'bg-blue-500' };
        const toast = document.createElement('div');
        toast.className = `${colors[type]} text-white px-4 py-2 rounded-lg shadow-lg`;
        toast.textContent = msg;
        document.getElementById('toast-container').appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>
@endpush
