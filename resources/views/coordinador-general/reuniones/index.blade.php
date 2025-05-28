@extends('layouts.coordinador-general.app')

@section('content')
<div class="flex-1 overflow-auto">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Gestión de Reuniones</h1>
                <p class="text-gray-600">Programa y gestiona reuniones con los diferentes equipos</p>
            </div>
            <button onclick="openCreateMeetingModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nueva Reunión
            </button>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        <!-- Tabs -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('proximas')" id="tab-proximas" class="tab-button active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600">
                        Próximas
                    </button>
                    <button onclick="showTab('hoy')" id="tab-hoy" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Hoy
                    </button>
                    <button onclick="showTab('pasadas')" id="tab-pasadas" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Pasadas
                    </button>
                    <button onclick="showTab('calendario')" id="tab-calendario" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Calendario
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content -->
        <div id="content-proximas" class="tab-content">
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6">
                    @include('coordinador-general.reuniones.meetings-table', ['meetings' => $upcomingMeetings, 'showActions' => true])
                </div>
            </div>
        </div>

        <div id="content-hoy" class="tab-content hidden">
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6">
                    @include('coordinador-general.reuniones.meetings-table', ['meetings' => $todayMeetings, 'showActions' => true])
                </div>
            </div>
        </div>

        <div id="content-pasadas" class="tab-content hidden">
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6">
                    @include('coordinador-general.reuniones.meetings-table', ['meetings' => $pastMeetings, 'showActions' => false])
                </div>
            </div>
        </div>

        <div id="content-calendario" class="tab-content hidden">
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6">
                    @include('coordinador-general.reuniones.calendar')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Nueva Reunión -->
<div id="createMeetingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
            <form action="{{ route('coordinador-general.reuniones.store') }}" method="POST">
                @csrf
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Programar Nueva Reunión</h3>
                        <button type="button" onclick="closeCreateMeetingModal()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Completa los detalles para programar una nueva reunión</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título de la Reunión</label>
                            <input type="text" id="title" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ej: Revisión de Objetivos Trimestrales" required>
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Describe el propósito y agenda de la reunión" required></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                                <input type="date" id="date" name="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                                <input type="time" id="time" name="time" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duración</label>
                                <select id="duration" name="duration" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Seleccionar duración</option>
                                    <option value="30">30 minutos</option>
                                    <option value="60">1 hora</option>
                                    <option value="90">1 hora 30 minutos</option>
                                    <option value="120">2 horas</option>
                                </select>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Reunión</label>
                                <select id="type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="Virtual">Virtual</option>
                                    <option value="Presencial">Presencial</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label for="group" class="block text-sm font-medium text-gray-700 mb-1">Grupo</label>
                            <select id="group" name="group" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Seleccionar grupo</option>
                                <option value="Marketing Digital">Equipo de Marketing Digital</option>
                                <option value="Ventas Corporativas">Ventas Corporativas</option>
                                <option value="Logística">Logística y Distribución</option>
                                <option value="Finanzas">Análisis Financiero</option>
                                <option value="Todos los grupos">Todos los grupos</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                            <select id="location" name="location" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Seleccionar ubicación</option>
                                <option value="Sala Virtual Principal">Sala Virtual Principal</option>
                                <option value="Sala Virtual 2">Sala Virtual 2</option>
                                <option value="Sala Virtual 3">Sala Virtual 3</option>
                                <option value="Sala Virtual 4">Sala Virtual 4</option>
                                <option value="Sala de Conferencias A">Sala de Conferencias A</option>
                                <option value="Sala de Juntas">Sala de Juntas</option>
                                <option value="Sala de Capacitación">Sala de Capacitación</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeCreateMeetingModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Programar Reunión
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(`content-${tabName}`).classList.remove('hidden');
    
    // Add active class to selected tab button
    const activeButton = document.getElementById(`tab-${tabName}`);
    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
}

function openCreateMeetingModal() {
    document.getElementById('createMeetingModal').classList.remove('hidden');
}

function closeCreateMeetingModal() {
    document.getElementById('createMeetingModal').classList.add('hidden');
}

function joinMeeting(meetingId) {
    fetch(`{{ route('coordinador-general.reuniones.join', '') }}/${meetingId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Uniéndose a la reunión...');
            // Aquí podrías redirigir a la sala de videollamada
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al unirse a la reunión');
    });
}

function editMeeting(meetingId) {
    alert(`Editar reunión: ${meetingId}`);
    // Implementar lógica de edición
}

function viewMeetingDetails(meetingId) {
    alert(`Ver detalles de la reunión: ${meetingId}`);
    // Implementar vista de detalles
}

// Initialize Lucide icons when modal opens
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endsection