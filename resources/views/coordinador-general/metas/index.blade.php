@extends('layouts.coordinador-general.app')

@section('content')
<div class="flex-1 overflow-auto">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 slide-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestión de Metas</h1>
                <p class="text-gray-600 mt-1">Supervisa y gestiona las metas de todos los equipos</p>
            </div>
            <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium tab-transition hover-scale">
                <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                Nueva Meta
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 slide-in">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-64">
                <input type="text" id="searchInput" placeholder="Buscar metas..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition"
                       onkeyup="filterMetas()">
            </div>
            <select id="estadoFilter" onchange="filterMetas()" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                <option value="">Todos los estados</option>
                @foreach($estados as $estado)
                <option value="{{ $estado->nombre }}">{{ $estado->nombre }}</option>
                @endforeach
            </select>
            <select id="equipoFilter" onchange="filterMetas()" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                <option value="">Todos los equipos</option>
                @foreach($equipos as $equipo)
                <option value="{{ $equipo['nombre'] }}">{{ $equipo['nombre'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Metas Grid -->
    <div class="flex-1 p-6">
        <div id="metasGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($metas as $meta)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 hover:shadow-lg form-transition hover-scale cursor-pointer meta-card" 
                     data-meta-id="{{ $meta['id'] }}" 
                     data-estado="{{ $meta['estado'] }}"
                     data-equipo="{{ $meta['equipo'] }}"
                     onclick="openDetailsModalById({{ $meta['id'] }})">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $meta['titulo'] }}</h3>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($meta['estado'] == 'Incompleta') bg-yellow-100 text-yellow-800
                            @elseif($meta['estado'] == 'En proceso') bg-blue-100 text-blue-800
                            @elseif($meta['estado'] == 'Completo') bg-green-100 text-green-800
                            @elseif($meta['estado'] == 'Suspendida') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">{{ $meta['estado'] }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">{{ $meta['descripcion'] ?: 'Sin descripción' }}</p>
                    
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Progreso</span>
                            <span class="text-sm font-medium text-gray-900">{{ $meta['progreso'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $progressColor = 'bg-red-500';
                                if($meta['progreso'] >= 75) $progressColor = 'bg-green-500';
                                elseif($meta['progreso'] >= 50) $progressColor = 'bg-blue-500';
                                elseif($meta['progreso'] >= 25) $progressColor = 'bg-yellow-500';
                            @endphp
                            <div class="h-2 rounded-full transition-all duration-300 {{ $progressColor }}" style="width: {{ $meta['progreso'] }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Equipo:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $meta['equipo'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Tareas:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $meta['tareas_completadas'] }}/{{ $meta['tareas_count'] }}</span>
                        </div>
                        @if($meta['fecha_entrega'])
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Fecha límite:</span>
                            <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($meta['fecha_entrega'])->format('d/m/Y') }}</span>
                        </div>
                        @endif
                        @if($meta['fecha_creacion'])
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Creada:</span>
                            <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($meta['fecha_creacion'])->format('d/m/Y') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="border-t pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">ID: {{ $meta['id'] }}</span>
                            <div class="flex space-x-2">
                                <button onclick="event.stopPropagation(); editMeta({{ $meta['id'] }})" class="text-blue-600 hover:text-blue-800 text-xs">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button onclick="event.stopPropagation(); deleteMeta({{ $meta['id'] }})" class="text-red-600 hover:text-red-800 text-xs">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="target" class="w-12 h-12 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron metas</h3>
            <p class="text-gray-500 mb-6">Intenta ajustar los filtros de búsqueda o crear una nueva meta.</p>
            <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Crear primera meta
            </button>
        </div>
    </div>
</div>

<!-- Modal Crear Meta -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Crear Nueva Meta</h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 tab-transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <form id="createMetaForm" action="{{ route('coordinador-general.metas.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Meta</label>
                    <input type="text" name="nombre" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Equipo Asignado</label>
                    <select name="equipo_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                        <option value="">Seleccionar equipo...</option>
                        @foreach($equipos as $equipo)
                            <option value="{{ $equipo['id'] }}">{{ $equipo['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select name="estado_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                        <option value="">Seleccionar estado...</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Límite</label>
                    <input type="date" name="fecha_entrega" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-150 tab-transition">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 tab-transition">
                    Crear Meta
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Meta -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Editar Meta</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 tab-transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <form id="editMetaForm" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_meta_id" name="meta_id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Meta</label>
                    <input type="text" id="edit_nombre" name="nombre" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Equipo Asignado</label>
                    <select id="edit_equipo_id" name="equipo_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                        <option value="">Seleccionar equipo...</option>
                        @foreach($equipos as $equipo)
                            <option value="{{ $equipo['id'] }}">{{ $equipo['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea id="edit_descripcion" name="descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="edit_estado_id" name="estado_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                        <option value="">Seleccionar estado...</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Límite</label>
                    <input type="date" id="edit_fecha_entrega" name="fecha_entrega" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 tab-transition">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 tab-transition">
                    Actualizar Meta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalles Meta -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900"></h3>
                <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600 tab-transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <div id="modalContent" class="p-6">
            <!-- El contenido se cargará dinámicamente -->
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-150 z-50">
    <div class="flex items-center">
        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
        <span id="toast-message">Operación realizada correctamente</span>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 form-transition">
        <div class="p-6 text-center">
            <!-- Icono de advertencia -->
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            
            <!-- Título -->
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Confirmar eliminación</h3>
            
            <!-- Texto descriptivo -->
            <p id="confirmDeleteText" class="text-gray-600 mb-6">¿Estás seguro de que deseas eliminar esta meta?</p>
            
            <!-- Botones -->
            <div class="flex space-x-3">
                <button type="button" onclick="closeConfirmDeleteModal()" class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancelar
                </button>
                <button type="button" id="confirmDeleteButton" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let currentMeta = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    @if(session('success'))
        showToast("{{ session('success') }}");
    @endif
    
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
});

// Función para abrir modal por ID
async function openDetailsModalById(metaId) {
    try {
        const response = await fetch(`/coordinador-general/metas/${metaId}`);
        const meta = await response.json();
        
        if (meta.error) {
            showToast(meta.error, 'error');
            return;
        }
        
        openDetailsModal(meta);
    } catch (error) {
        console.error('Error al cargar meta:', error);
        showToast('Error al cargar los detalles de la meta', 'error');
    }
}

// Filtrar metas
function filterMetas() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const estadoFilter = document.getElementById('estadoFilter').value;
    const equipoFilter = document.getElementById('equipoFilter').value;
    const metaCards = document.querySelectorAll('.meta-card');
    let visibleCount = 0;

    metaCards.forEach(card => {
        const titulo = card.querySelector('h3').textContent.toLowerCase();
        const descripcion = card.querySelector('p').textContent.toLowerCase();
        const estado = card.dataset.estado;
        const equipo = card.dataset.equipo;

        const matchesSearch = titulo.includes(searchTerm) || descripcion.includes(searchTerm);
        const matchesEstado = !estadoFilter || estado === estadoFilter;
        const matchesEquipo = !equipoFilter || equipo === equipoFilter;

        if (matchesSearch && matchesEstado && matchesEquipo) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Show/hide empty state
    const emptyState = document.getElementById('emptyState');
    if (visibleCount === 0) {
        emptyState.classList.remove('hidden');
    } else {
        emptyState.classList.add('hidden');
    }
}

// Modal functions
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('createMetaForm').reset();
}

function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('editMetaForm').reset();
}

function openDetailsModal(meta) {
    currentMeta = meta;
    const modal = document.getElementById('detailsModal');
    const title = document.getElementById('modalTitle');
    const content = document.getElementById('modalContent');

    title.textContent = meta.titulo;

    const estadoColors = {
        'Incompleta': 'bg-yellow-100 text-yellow-800',
        'En proceso': 'bg-blue-100 text-blue-800',
        'Completo': 'bg-green-100 text-green-800',
        'Suspendida': 'bg-red-100 text-red-800'
    };

    content.innerHTML = `
        <div class="space-y-6">
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-2">Descripción</h4>
                <p class="text-gray-600">${meta.descripcion || 'Sin descripción'}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Información General</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Estado:</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${estadoColors[meta.estado] || 'bg-gray-100 text-gray-800'}">${meta.estado}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Equipo:</span>
                            <span class="text-sm font-medium text-gray-900">${meta.equipo}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Fechas</h4>
                    <div class="space-y-3">
                        ${meta.fecha_creacion ? `
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Fecha creación:</span>
                            <span class="text-sm font-medium text-gray-900">${meta.fecha_creacion}</span>
                        </div>
                        ` : ''}
                        ${meta.fecha_entrega ? `
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Fecha límite:</span>
                            <span class="text-sm font-medium text-gray-900">${meta.fecha_entrega}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>

            ${meta.tareas && meta.tareas.length > 0 ? `
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Tareas Asociadas</h4>
                <div class="space-y-2">
                    ${meta.tareas.map(tarea => `
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-900">${tarea.nombre}</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${estadoColors[tarea.estado] || 'bg-gray-100 text-gray-800'}">${tarea.estado}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}
        </div>
    `;

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentMeta = null;
}

// CRUD Operations
async function editMeta(metaId) {
    try {
        const response = await fetch(`/coordinador-general/metas/${metaId}`);
        const meta = await response.json();
        
        if (meta.error) {
            showToast(meta.error, 'error');
            return;
        }

        // Llenar el formulario de edición
        document.getElementById('edit_meta_id').value = meta.id;
        document.getElementById('edit_nombre').value = meta.titulo;
        document.getElementById('edit_descripcion').value = meta.descripcion || '';
        document.getElementById('edit_equipo_id').value = meta.equipo_id;
        document.getElementById('edit_estado_id').value = meta.estado_id;
        
        // Convertir fecha para input date
        if (meta.fecha_entrega) {
            const fechaParts = meta.fecha_entrega.split('/');
            if (fechaParts.length === 3) {
                const fechaFormatted = `${fechaParts[2]}-${fechaParts[1].padStart(2, '0')}-${fechaParts[0].padStart(2, '0')}`;
                document.getElementById('edit_fecha_entrega').value = fechaFormatted;
            }
        }

        openEditModal();
    } catch (error) {
        console.error('Error al cargar meta para editar:', error);
        showToast('Error al cargar los datos de la meta', 'error');
    }
}

function deleteMeta(metaId) {
    // Obtener el nombre de la meta para mostrar en el modal
    const metaCard = document.querySelector(`.meta-card[data-meta-id="${metaId}"]`);
    const metaNombre = metaCard ? metaCard.querySelector('h3').textContent : 'esta meta';
    
    // Configurar el texto del modal
    document.getElementById('confirmDeleteText').textContent = `¿Estás seguro de que deseas eliminar la meta "${metaNombre}"?`;
    
    // Configurar el botón de confirmación
    const confirmButton = document.getElementById('confirmDeleteButton');
    confirmButton.onclick = async function() {
        try {
            const response = await fetch(`/coordinador-general/metas/${metaId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.error) {
                showToast(data.error, 'error');
            } else {
                showToast('Meta eliminada exitosamente');
                closeConfirmDeleteModal();
                // Recargar la página para actualizar la lista
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error al eliminar meta:', error);
            showToast('Error al eliminar la meta', 'error');
        }
    };
    
    // Mostrar el modal
    document.getElementById('confirmDeleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeConfirmDeleteModal() {
    document.getElementById('confirmDeleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Event Listeners
document.getElementById('editMetaForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const metaId = document.getElementById('edit_meta_id').value;
    const formData = new FormData(this);
    
    try {
        const response = await fetch(`/coordinador-general/metas/${metaId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });

        const data = await response.json();

        if (data.error) {
            showToast(data.error, 'error');
        } else {
            showToast('Meta actualizada exitosamente');
            closeEditModal();
            // Recargar la página para actualizar la lista
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    } catch (error) {
        console.error('Error al actualizar meta:', error);
        showToast('Error al actualizar la meta', 'error');
    }
});

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    
    // Cambiar color según el tipo
    if (type === 'error') {
        toast.className = toast.className.replace('bg-green-500', 'bg-red-500');
    } else {
        toast.className = toast.className.replace('bg-red-500', 'bg-green-500');
    }
    
    toastMessage.textContent = message;
    toast.classList.remove('translate-x-full', 'opacity-0');
    toast.classList.add('translate-x-0', 'opacity-100');
    
    setTimeout(() => {
        toast.classList.remove('translate-x-0', 'opacity-100');
        toast.classList.add('translate-x-full', 'opacity-0');
    }, 3000);
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCreateModal();
        closeEditModal();
        closeDetailsModal();
        closeConfirmDeleteModal();
    }
});
</script>
@endsection
