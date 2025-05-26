@extends('layouts.private.admin')

@section('title', 'Coordinadores Generales')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Coordinadores Generales</h1>
            <p class="text-gray-600">Gestiona los coordinadores generales de tu organización</p>
        </div>
        <div class="flex items-center gap-2">
            <button id="openAsignarModal" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Nuevo Coordinador General
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64 relative">
                <i data-lucide="search" class="absolute left-3 top-3 text-gray-400 w-4 h-4"></i>
                <input type="text" name="search" id="searchInput"
                       value="{{ request('search') }}"
                       placeholder="Buscar por nombre o email..."
                       class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="min-w-48">
                <select name="area" id="areaFilter" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todas las áreas</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ request('area') == $area->id ? 'selected' : '' }}>
                            {{ $area->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-40">
                <select name="estado" id="estadoFilter" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los estados</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <button type="submit"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Filtrar
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-300">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coordinadores</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($coordinadores as $c)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $c->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $c->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $c->area->nombre ?? 'Sin área' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $c->equipos_count }} equipos</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $c->coordinadores_count }} coord.</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="toggleStatus({{ $c->id }}, '{{ $c->estado }}')"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors {{ $c->estado === 'activo' ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $c->estado === 'activo' ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                    {{ ucfirst($c->estado) }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="verDetalles({{ $c->id }})"
                                        class="inline-flex items-center px-3 py-2 text-blue-600 hover:text-blue-900 transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                    Ver
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                No se encontraron coordinadores generales.
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

{{-- Modal Asignar Coordinador General --}}
<div id="asignarModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeAsignarModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-4 py-5 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Asignar Coordinador General</h3>
                <button onclick="closeAsignarModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="asignarForm" method="POST" class="px-6 py-4">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="usuario_id" class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                        <select name="usuario_id" id="usuario_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar usuario</option>
                            @foreach($usuariosDisponibles as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Solo usuarios activos con rol Colaborador.</p>
                    </div>
                    <div>
                        <label for="area_id" class="block text-sm font-medium text-gray-700 mb-1">Área</label>
                        <select name="area_id" id="area_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar área</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="enviar_notificacion" id="enviar_notificacion" checked
                               class="h-4 w-4 rounded border-gray-300 focus:ring-blue-500">
                        <label for="enviar_notificacion" class="text-sm text-gray-700">Enviar notificación al usuario</label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" onclick="closeAsignarModal()"
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Asignar Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Detalles --}}
<div id="detallesModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeDetallesModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
            <div class="px-4 py-5 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Detalles del Coordinador General</h3>
                <button onclick="closeDetallesModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="detallesContent" class="p-6">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Datos de ejemplo
    const MOCK_COORDINADORES = @json($mock ?? []);

    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();

        // Búsqueda y filtros
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => this.form.submit(), 500);
        });
        document.getElementById('areaFilter').addEventListener('change', () => this.form.submit());
        document.getElementById('estadoFilter').addEventListener('change', () => this.form.submit());

        // Modales
        document.getElementById('openAsignarModal').addEventListener('click', openAsignarModal);
        document.getElementById('asignarForm').addEventListener('submit', handleAsignarSubmit);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeAsignarModal();
                closeDetallesModal();
            }
        });
    });

    function openAsignarModal() {
        document.getElementById('asignarModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeAsignarModal() {
        document.getElementById('asignarModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('asignarForm').reset();
    }
    function handleAsignarSubmit(e) {
        e.preventDefault();
        // Aquí iría la petición real...
        showToast('Coordinador asignado con éxito', 'success');
        closeAsignarModal();
        setTimeout(() => location.reload(), 800);
    }

    function verDetalles(id) {
        document.getElementById('detallesModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('detallesContent').innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>`;
        // Simula carga
        setTimeout(() => {
            const c = MOCK_COORDINADORES.find(x => x.id === id);
            if (!c) return showToast('No encontrado', 'error');
            document.getElementById('detallesContent').innerHTML = generateDetalleHTML(c);
        }, 600);
    }
    function closeDetallesModal() {
        document.getElementById('detallesModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    function toggleStatus(id, current) {
        const next = current === 'activo' ? 'inactivo' : 'activo';
        if (!confirm(`¿Cambiar estado a ${next}?`)) return;
        // petición real...
        showToast(`Estado cambiado a ${next}`, 'success');
        setTimeout(() => location.reload(), 500);
    }
    function showToast(msg, type) {
        const bg = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white ${bg} shadow-lg`;
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(() => { toast.remove(); }, 3000);
    }
    function generateDetalleHTML(c) {
        return `
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <img src="/placeholder-80x80.png" alt="${c.name}" class="h-16 w-16 rounded-full object-cover">
                    <div>
                        <h4 class="text-xl font-semibold text-gray-900">${c.name}</h4>
                        <p class="text-gray-600">${c.email}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${c.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${c.estado.charAt(0).toUpperCase() + c.estado.slice(1)}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Área</label>
                        <p class="text-sm text-gray-900">${c.area?.nombre ?? 'Sin área'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Equipos</label>
                        <p class="text-sm text-gray-900">${c.equipos_count} equipos</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Coordinadores</label>
                    <p class="text-sm text-gray-900">${c.coordinadores_count} coordinadores</p>
                </div>
            </div>
        `;
    }
</script>
@endpush
