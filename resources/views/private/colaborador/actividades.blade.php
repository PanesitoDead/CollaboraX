{{-- resources/views/admin/areas/index.blade.php --}}
@extends('layouts.private.colaborador')

@section('title', 'Mis Actividades')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mis Actividades</h1>
            <p class="text-gray-600">Puedes ver el estado de tus actividades. Para cambiar el estado, usa el botón "Ver Detalles" en cada tarjeta.</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="relative w-64">
                <i data-lucide="search" class="absolute left-3 top-3 h-4 w-4 text-gray-400"></i>
                <form method="GET">
                    <input
                        type="text"
                        name="search"
                        value="{{ $searchQuery }}"
                        placeholder="Buscar actividades..."
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        onchange="this.form.submit()"
                    />
                </form>
            </div>
            <button type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i data-lucide="filter" class="h-4 w-4 mr-2"></i>
                Filtrar
            </button>
        </div>
    </div>

    {{-- Board --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($kanbanColumns as $column)
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium uppercase tracking-wider text-gray-500">{{ $column['title'] }}</h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        {{ $column['items']->count() }}
                    </span>
                </div>
                <div class="space-y-4">
                    @foreach($column['items'] as $actividad)
                        @include('partials.colaborador.activity-card', ['actividad' => $actividad])
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Modal detailles --}}
<div id="activity-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeActivityModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div id="modal-content" class="p-6 space-y-6">
                    {{-- Contenido dinámico --}}
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" onclick="closeActivityModal()" class="w-full inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:ml-3 sm:w-auto">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Simulación de datos hasta la API real
const enableMock = true;
const mockActivities = {
  1: {
    id: 1,
    title: "Revisar documentación del proyecto",
    description: "Revisar y actualizar la documentación técnica antes de la entrega final.",
    due_date: "2025-06-10T00:00:00Z",
    team: "Equipo Docs",
    priority: "alta",
    status: "en-proceso",
    goal: "Mejorar claridad y cobertura de la documentación",
    assigned_by: "Johan"
  },
  2: {
    id: 2,
    title: "Desarrollar módulo de login",
    description: "Implementar autenticación con JWT y protección de rutas.",
    due_date: "2025-06-15T00:00:00Z",
    team: "Equipo Backend",
    priority: "media",
    status: "incompleta",
    goal: null,
    assigned_by: null
  }
};

lucide.createIcons();

function openActivityModal(activityId) {
    const data = enableMock ? mockActivities[activityId] : null;
    if (!data) return showToast('Actividad no encontrada', 'error');
    document.getElementById('modal-content').innerHTML = generateModalContent(data);
    document.getElementById('activity-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeActivityModal() {
    document.getElementById('activity-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function generateModalContent(activity) {
    const priorityColors = {
        'alta': 'bg-red-100 text-red-800',
        'media': 'bg-yellow-100 text-yellow-800',
        'baja': 'bg-green-100 text-green-800'
    };
    const statusColors = {
        'completa': 'bg-green-100 text-green-800',
        'incompleta': 'bg-yellow-100 text-yellow-800',
        'en-proceso': 'bg-blue-100 text-blue-800',
        'suspendida': 'bg-red-100 text-red-800'
    };
    const statusLabels = {
        'completa': 'Completa',
        'incompleta': 'Incompleta',
        'en-proceso': 'En Proceso',
        'suspendida': 'Suspendida'
    };

    return `
        <div class="flex items-start justify-between">
            <h3 class="text-lg font-medium text-gray-900">${activity.title}</h3>
            <button onclick="closeActivityModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <p class="mt-1 text-sm text-gray-600">${activity.description}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha límite</label>
                    <p class="mt-1 text-sm text-gray-600">${new Date(activity.due_date).toLocaleDateString('es-ES')}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Equipo</label>
                    <p class="mt-1 text-sm text-gray-600">${activity.team}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prioridad</label>
                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${priorityColors[activity.priority]}">${activity.priority.charAt(0).toUpperCase() + activity.priority.slice(1)}</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[activity.status]}">${statusLabels[activity.status]}</span>
                </div>
            </div>
            ${activity.goal ? `
            <div>
                <label class="block text-sm font-medium text-gray-700">Meta asociada</label>
                <p class="mt-1 text-sm text-gray-600">${activity.goal}</p>
            </div>
            ` : ''}
            ${activity.assigned_by ? `
            <div>
                <label class="block text-sm font-medium text-gray-700">Asignado por</label>
                <p class="mt-1 text-sm text-gray-600">${activity.assigned_by}</p>
            </div>
            ` : ''}
        </div>
    `;
}

// Toast function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden ${type === 'error' ? 'border-l-4 border-red-400' : 'border-l-4 border-green-400'}`;
    toast.innerHTML = `
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i data-lucide="${type === 'error' ? 'x-circle' : 'check-circle'}" class="h-6 w-6 ${type === 'error' ? 'text-red-400' : 'text-green-400'}"></i>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none" onclick="this.closest('div').remove()">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    document.getElementById('toast-container').appendChild(toast);
    lucide.createIcons();
    setTimeout(() => toast.remove(), 5000);
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') closeActivityModal();
});
</script>
@endpush
