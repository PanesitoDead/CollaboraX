{{-- resources/views/admin/areas/index.blade.php --}}
@extends('layouts.private.admin')

@section('title', 'Áreas')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Áreas</h1>
            <p class="text-gray-600">Gestión de áreas organizacionales de la empresa</p>
        </div>
        <div>
            <button onclick="openAreaModal()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nueva Área
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-4">
        {{-- Total Áreas --}}
        <div class="bg-white p-4 rounded-lg border border-gray-300">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Áreas</h3>
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            <p class="text-xs text-gray-500">{{ $stats['activas'] }} activas</p>
        </div>

        {{-- Equipos Totales --}}
        <div class="bg-white p-4 rounded-lg border border-gray-300">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-medium text-gray-600">Equipos Totales</h3>
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['equipos_total'] }}</div>
            <p class="text-xs text-gray-500">Distribuidos en todas las áreas</p>
        </div>

        {{-- Colaboradores --}}
        <div class="bg-white p-4 rounded-lg border border-gray-300">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-medium text-gray-600">Colaboradores</h3>
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['colaboradores_total'] }}</div>
            <p class="text-xs text-gray-500">{{ $stats['colaboradores_activos'] }} activos</p>
        </div>

        {{-- Rendimiento Promedio --}}
        <div class="bg-white p-4 rounded-lg border border-gray-300">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-medium text-gray-600">Rendimiento Promedio</h3>
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['rendimiento_promedio'] }}%</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['rendimiento_promedio'] }}%"></div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg border border-gray-300">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar áreas..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="min-w-48">
                <select name="estado" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los estados</option>
                    <option value="activa" {{ request('estado')=='activa'?'selected':'' }}>Activa</option>
                    <option value="inactiva" {{ request('estado')=='inactiva'?'selected':'' }}>Inactiva</option>
                </select>
            </div>
            <div class="min-w-48">
                <select name="sort" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Ordenar por</option>
                    <option value="nombre" {{ request('sort')=='nombre'?'selected':'' }}>Nombre</option>
                    <option value="colaboradores" {{ request('sort')=='colaboradores'?'selected':'' }}>Colaboradores</option>
                    <option value="rendimiento" {{ request('sort')=='rendimiento'?'selected':'' }}>Rendimiento</option>
                    <option value="fecha" {{ request('sort')=='fecha'?'selected':'' }}>Fecha creación</option>
                </select>
            </div>
        </form>
    </div>

    {{-- Áreas Grid --}}
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($areas as $area)
            <div class="bg-white rounded-lg border border-gray-300 hover:shadow-lg transition-shadow">
                {{-- Área Header --}}
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-lg bg-{{ $area['color'] }}-100 flex items-center justify-center">
                                {!! $area['icon'] !!}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $area['nombre'] }}</h3>
                                <p class="text-sm text-gray-500">{{ $area['codigo'] }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editArea({{ $area['id'] }})"
                                    class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            <button onclick="deleteArea({{ $area['id'] }})"
                                    class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">{{ $area['descripcion'] }}</p>
                    <div class="flex justify-between items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $area['estado']=='activa'?'bg-green-100 text-green-800':'bg-red-100 text-red-800' }}">
                            {{ ucfirst($area['estado']) }}
                        </span>
                        <span class="text-xs text-gray-500">Creada {{ $area['fecha_creacion'] }}</span>
                    </div>
                </div>

                {{-- Coordinador --}}
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Coordinador General</h4>
                    @if($area['coordinador'])
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-{{ $area['color'] }}-100 flex items-center justify-center">
                                <span class="text-sm font-medium text-{{ $area['color'] }}-600">{{ $area['coordinador']['initials'] }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $area['coordinador']['nombre'] }}</p>
                                <p class="text-xs text-gray-500">{{ $area['coordinador']['email'] }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center space-x-3 text-gray-400">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm">Sin coordinador asignado</p>
                                <button onclick="assignCoordinator({{ $area['id'] }})"
                                        class="text-xs text-blue-600 hover:text-blue-800">
                                    Asignar coordinador
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Estadísticas --}}
                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $area['equipos'] }}</p>
                            <p class="text-xs text-gray-500">Equipos</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ $area['colaboradores'] }}</p>
                            <p class="text-xs text-gray-500">Colaboradores</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-orange-500">{{ $area['metas_activas'] }}</p>
                            <p class="text-xs text-gray-500">Metas Activas</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $area['proyectos'] }}</p>
                            <p class="text-xs text-gray-500">Proyectos</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Rendimiento</span>
                            <span class="text-sm font-bold text-gray-900">{{ $area['rendimiento'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-{{ $area['color'] }}-600 h-2 rounded-full"
                                 style="width: {{ $area['rendimiento'] }}%"></div>
                        </div>
                    </div>

                    <div class="flex space-x-2">
                        <button onclick="viewAreaDetails({{ $area['id'] }})"
                                class="flex-1 px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Ver Detalles
                        </button>
                        <button onclick="manageArea({{ $area['id'] }})"
                                class="flex-1 px-4 py-2 text-sm bg-{{ $area['color'] }}-600 text-white rounded-lg hover:bg-{{ $area['color'] }}-700 transition-colors">
                            Gestionar
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($areas->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $areas->links() }}
        </div>
    @endif
</div>

{{-- Modal Crear/Editar Área --}}
<div id="areaModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeAreaModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden">
            <form id="areaForm" method="POST">
                @csrf
                <div id="methodField"></div>

                <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
                    <h3 id="modalTitle" class="text-lg font-medium text-gray-900">Nueva Área</h3>
                    <button type="button" onclick="closeAreaModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Área</label>
                            <input type="text" name="nombre" id="nombre" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="codigo" class="block text-sm font-medium text-gray-700">Código</label>
                            <input type="text" name="codigo" id="codigo" required placeholder="Ej: MKT, VNT"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Describe responsabilidades y objetivos..."></textarea>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
                            <select name="color" id="color" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Seleccionar color</option>
                                <option value="blue">Azul</option>
                                <option value="green">Verde</option>
                                <option value="purple">Morado</option>
                                <option value="orange">Naranja</option>
                                <option value="red">Rojo</option>
                                <option value="indigo">Índigo</option>
                                <option value="pink">Rosa</option>
                                <option value="teal">Verde azulado</option>
                            </select>
                        </div>
                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select name="estado" id="estado" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="activa">Activa</option>
                                <option value="inactiva">Inactiva</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="objetivos" class="block text-sm font-medium text-gray-700">Objetivos Principales</label>
                        <textarea name="objetivos" id="objetivos" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Lista los objetivos del área..."></textarea>
                    </div>

                    <div>
                        <label for="coordinador_id" class="block text-sm font-medium text-gray-700">Coordinador General (Opcional)</label>
                        <select name="coordinador_id" id="coordinador_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Sin coordinador asignado</option>
                            @foreach($coordinadores_disponibles as $coord)
                                <option value="{{ $coord->id }}">{{ $coord->name }} ({{ $coord->email }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Puedes asignar ahora o más tarde.</p>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2">
                    <button type="button" onclick="closeAreaModal()"
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <span id="submitText">Crear Área</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Confirmar Eliminación --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeDeleteModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-start space-x-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Eliminar Área</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        ¿Estás seguro de que deseas eliminar esta área? Esta acción no se puede deshacer.
                    </p>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2">
                <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="confirmDelete()"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Eliminar Área
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentAreaId = null;

    function openAreaModal() {
        document.getElementById('modalTitle').textContent = 'Nueva Área';
        document.getElementById('submitText').textContent = 'Crear Área';
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('areaForm').reset();
        document.getElementById('areaModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAreaModal() {
        document.getElementById('areaModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function editArea(id) {
        fetch(`/admin/areas/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = 'Editar Área';
                document.getElementById('submitText').textContent = 'Actualizar Área';
                document.getElementById('areaForm').action = `/admin/areas/${id}`;
                document.getElementById('methodField').innerHTML = '@method("PUT")';
                // llenar formulario...
                for (let key of ['nombre','codigo','descripcion','color','estado','objetivos','coordinador_id']) {
                    const el = document.getElementById(key);
                    if (el) el.value = data[key] ?? '';
                }
                openAreaModal();
            });
    }

    function deleteArea(id) {
        currentAreaId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentAreaId = null;
    }

    function confirmDelete() {
        if (!currentAreaId) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/areas/${currentAreaId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    }

    function viewAreaDetails(id) {
        window.location.href = `/admin/areas/${id}`;
    }

    function manageArea(id) {
        window.location.href = `/admin/areas/${id}/manage`;
    }

    function assignCoordinator(id) {
        window.location.href = `/admin/areas/${id}/assign-coordinator`;
    }

    // Cerrar modales al hacer click fuera
    document.querySelectorAll('#areaModal, #deleteModal').forEach(modal => {
        modal.addEventListener('click', e => {
            if (e.target === modal) closeAreaModal(), closeDeleteModal();
        });
    });

    // Auto-genera código
    document.getElementById('nombre').addEventListener('input', e => {
        const codigo = e.target.value
            .split(' ')
            .map(w => w.charAt(0).toUpperCase())
            .join('')
            .substring(0, 3);
        document.getElementById('codigo').value = codigo;
    });
</script>
@endpush
