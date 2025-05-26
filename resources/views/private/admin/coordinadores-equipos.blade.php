@extends('layouts.private.admin')

@section('title', 'Coordinadores de Equipo')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Coordinadores de Equipo</h1>
            <p class="text-gray-600">Gestiona los coordinadores de equipo de la empresa</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Buscar por nombre o email..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            <div class="min-w-48">
                <select name="area" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todas las áreas</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ request('area') == $area->id ? 'selected' : '' }}>
                            {{ $area->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-40">
                <select name="estado" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los estados</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Filtrar
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-300 ">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coordinador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($coordinadores as $coordinador)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <img class="h-10 w-10 rounded-full object-cover" 
                                             src="{{ $coordinador->avatar ?? '/placeholder-40x40.png' }}" 
                                             alt="{{ $coordinador->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $coordinador->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $coordinador->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $coordinador->area->nombre ?? 'Sin área' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($coordinador->equipo)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $coordinador->equipo->nombre }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">Sin equipo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="toggleStatus({{ $coordinador->id }}, '{{ $coordinador->estado }}')" 
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors {{ $coordinador->estado === 'activo' ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $coordinador->estado === 'activo' ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                    {{ ucfirst($coordinador->estado) }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $coordinador->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <button onclick="openModal({{ $coordinador->id }})" 
                                            class="text-blue-600 hover:text-blue-900 transition-colors">
                                        <!-- Icono Ver -->
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button onclick="editCoordinador({{ $coordinador->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                        <!-- Icono Editar -->
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <!-- Icono Sin datos -->
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay coordinadores</h3>
                                    <p class="mt-1 text-sm text-gray-500">Comienza creando un nuevo coordinador de equipo.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($coordinadores->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $coordinadores->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal --}}
<div id="coordinadorModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Detalles del Coordinador</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div id="modalContent" class="space-y-6">
                        {{-- Contenido de ejemplo para demo --}}
                        <div class="flex items-center space-x-4">
                            <img class="h-16 w-16 rounded-full object-cover" src="/placeholder-40x40.png" alt="Ejemplo">
                            <div>
                                <h4 class="text-xl font-semibold text-gray-900">Nombre Coordinador</h4>
                                <p class="text-gray-600">coordinador@empresa.com</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Activo
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Área</label>
                                <p class="text-sm text-gray-900">Marketing</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                                <p class="text-sm text-gray-900">01/01/2025</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Equipo Asignado</label>
                            <div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Equipo A
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 pt-4 border-t">
                            <div class="text-center">
                                <p class="text-2xl font-semibold text-blue-600">1</p>
                                <p class="text-sm text-gray-600">Equipo</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-semibold text-green-600">5</p>
                                <p class="text-sm text-gray-600">Actividades</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-semibold text-purple-600">2</p>
                                <p class="text-sm text-gray-600">Reuniones</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button onclick="closeModal()" type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Scripts --}}
@push('scripts')
<script>
let exampleData = {
    id: 1,
    name: 'Nombre Coordinador',
    email: 'coordinador@empresa.com',
    estado: 'activo',
    area: { nombre: 'Marketing' },
    created_at: '2025-01-01T00:00:00Z',
    equipo: { nombre: 'Equipo A' },
    actividades_count: 5,
    reuniones_count: 2
};

function openModal(coordinadorId) {
    // Para uso real: reemplazar exampleData con fetch a ruta
    let data = exampleData;
    document.getElementById('modalContent').innerHTML = generateModalContent(data);
    document.getElementById('coordinadorModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function generateModalContent(coordinador) {
    return `
        <div class="flex items-center space-x-4">
            <img class="h-16 w-16 rounded-full object-cover" src="/placeholder-40x40.png" alt="${coordinador.name}">
            <div>
                <h4 class="text-xl font-semibold text-gray-900">${coordinador.name}</h4>
                <p class="text-gray-600">${coordinador.email}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${coordinador.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${coordinador.estado.charAt(0).toUpperCase() + coordinador.estado.slice(1)}
                </span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Área</label>
                <p class="text-sm text-gray-900">${coordinador.area.nombre}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                <p class="text-sm text-gray-900">${new Date(coordinador.created_at).toLocaleDateString('es-ES')}</p>
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Equipo Asignado</label>
            <div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    ${coordinador.equipo ? coordinador.equipo.nombre : 'Sin equipo'}
                </span>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4 pt-4 border-t mt-4">
            <div class="text-center">
                <p class="text-2xl font-semibold text-blue-600">1</p>
                <p class="text-sm text-gray-600">Equipo</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-semibold text-green-600">${coordinador.actividades_count}</p>
                <p class="text-sm text-gray-600">Actividades</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-semibold text-purple-600">${coordinador.reuniones_count}</p>
                <p class="text-sm text-gray-600">Reuniones</p>
            </div>
        </div>
    `;
}

function closeModal() {
    document.getElementById('coordinadorModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function toggleStatus(coordinadorId, currentStatus) {
    const newStatus = currentStatus === 'activo' ? 'inactivo' : 'activo';
    if (!confirm(`¿Estás seguro de cambiar el estado a ${newStatus}?`)) return;
    // Aquí iría la llamada real al endpoint
    console.log(`POST /admin/coordinadores-equipo/${coordinadorId}/toggle-status`, newStatus);
    location.reload();
}

// Debounce búsqueda
document.querySelector('input[name="search"]').addEventListener('input', function() {
    clearTimeout(window.searchTimeout);
    window.searchTimeout = setTimeout(() => this.form.submit(), 500);
});
</script>
@endpush
