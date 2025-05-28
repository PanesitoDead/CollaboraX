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
                <option value="Pendiente">Pendiente</option>
                <option value="En Progreso">En Progreso</option>
                <option value="Completada">Completada</option>
            </select>
            <select id="prioridadFilter" onchange="filterMetas()" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                <option value="">Todas las prioridades</option>
                <option value="Alta">Alta</option>
                <option value="Media">Media</option>
                <option value="Baja">Baja</option>
            </select>
        </div>
    </div>

    <!-- Metas Grid -->
    <div class="flex-1 p-6">
        <div id="metasGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($metas as $index => $meta)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 hover:shadow-lg form-transition hover-scale cursor-pointer" 
                     data-meta-id="{{ $meta['id'] }}" onclick="openDetailsModalById({{ $meta['id'] }})">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $meta['titulo'] }}</h3>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($meta['estado'] == 'Pendiente') bg-yellow-100 text-yellow-800
                            @elseif($meta['estado'] == 'En Progreso') bg-blue-100 text-blue-800
                            @elseif($meta['estado'] == 'Completada') bg-green-100 text-green-800
                            @endif">{{ $meta['estado'] }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">{{ $meta['descripcion'] }}</p>
                    
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
                            <span class="text-sm text-gray-500">Tipo:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $meta['tipo'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Prioridad:</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($meta['prioridad'] == 'Alta') bg-red-100 text-red-800
                                @elseif($meta['prioridad'] == 'Media') bg-yellow-100 text-yellow-800
                                @elseif($meta['prioridad'] == 'Baja') bg-green-100 text-green-800
                                @endif">{{ $meta['prioridad'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Grupo Asignado:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $meta['responsable'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Fecha límite:</span>
                            <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($meta['fecha_limite'])->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="border-t pt-3">
                        <div class="flex flex-wrap gap-1">
                            @foreach($meta['equipos'] as $equipo)
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">{{ $equipo }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
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
        <form id="createMetaForm" class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                    <input type="text" name="titulo" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                    <select name="tipo" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                        <option value="Mensual">Mensual</option>
                        <option value="Trimestral">Trimestral</option>
                        <option value="Semestral">Semestral</option>
                        <option value="Anual">Anual</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea name="descripcion" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prioridad</label>
                    <select name="prioridad" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                        <option value="Alta">Alta</option>
                        <option value="Media">Media</option>
                        <option value="Baja">Baja</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grupo Asignado</label>
                    <select name="responsable" id="grupo_asignado" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                        <option value="">Seleccionar grupo...</option>
                        @foreach($equiposDisponibles as $equipo)
                            <option value="{{ $equipo }}">{{ $equipo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Límite</label>
                    <input type="date" name="fecha_limite" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Equipos Asignados</label>
                <div id="equiposContainer" class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($equiposDisponibles as $equipo)
                        <label class="flex items-center">
                            <input type="checkbox" name="equipos" value="{{ $equipo }}" class="mr-2"> {{ $equipo }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 tab-transition">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 tab-transition">
                    Crear Meta
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detalles Meta -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900"></h3>
                <div class="flex items-center space-x-2">
                    <button onclick="editMeta(currentMeta)" class="text-blue-600 hover:text-blue-800 tab-transition">
                        <i data-lucide="edit" class="w-5 h-5"></i>
                    </button>
                    <button onclick="deleteMeta(currentMeta.id)" class="text-red-600 hover:text-red-800 tab-transition">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                    </button>
                    <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600 tab-transition">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>
        <div id="modalContent" class="p-6">
            <!-- El contenido se cargará dinámicamente -->
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-300 z-50">
    <div class="flex items-center">
        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
        <span id="toast-message">Operación realizada correctamente</span>
    </div>
</div>

<!-- Script con datos del servidor -->
<script>
// Datos del servidor
window.metasData = @json($metas);
window.equiposData = @json($equiposDisponibles);
</script>

<script>
// Variables globales
let metas = window.metasData || [];
let equiposDisponibles = window.equiposData || [];
let filteredMetas = [...metas];
let currentMeta = null;
let nextId = metas.length > 0 ? Math.max(...metas.map(m => m.id)) + 1 : 1;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    // Los datos ya están cargados desde el controlador
    renderMetas();
});

// Función para abrir modal por ID
function openDetailsModalById(metaId) {
    const meta = metas.find(m => m.id === metaId);
    if (meta) {
        openDetailsModal(meta);
    }
}

// Renderizar metas (para filtros)
function renderMetas() {
    const grid = document.getElementById('metasGrid');
    grid.innerHTML = '';

    filteredMetas.forEach(meta => {
        const metaCard = createMetaCard(meta);
        grid.appendChild(metaCard);
    });

    // Reinicializar iconos de Lucide
    lucide.createIcons();
}

// Crear tarjeta de meta
function createMetaCard(meta) {
    const div = document.createElement('div');
    div.className = 'bg-white rounded-lg shadow-md border border-gray-200 p-6 hover:shadow-lg form-transition hover-scale cursor-pointer';
    div.onclick = () => openDetailsModal(meta);

    const estadoColor = {
        'Pendiente': 'bg-yellow-100 text-yellow-800',
        'En Progreso': 'bg-blue-100 text-blue-800',
        'Completada': 'bg-green-100 text-green-800'
    };

    const prioridadColor = {
        'Alta': 'bg-red-100 text-red-800',
        'Media': 'bg-yellow-100 text-yellow-800',
        'Baja': 'bg-green-100 text-green-800'
    };

    const progressColor = meta.progreso >= 75 ? 'bg-green-500' : meta.progreso >= 50 ? 'bg-blue-500' : meta.progreso >= 25 ? 'bg-yellow-500' : 'bg-red-500';

    div.innerHTML = `
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">${meta.titulo}</h3>
            <span class="px-2 py-1 text-xs font-medium rounded-full ${estadoColor[meta.estado]}">${meta.estado}</span>
        </div>
        <p class="text-gray-600 text-sm mb-4">${meta.descripcion}</p>
        
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progreso</span>
                <span class="text-sm font-medium text-gray-900">${meta.progreso}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="${progressColor} h-2 rounded-full transition-all duration-300" style="width: ${meta.progreso}%"></div>
            </div>
        </div>

        <div class="space-y-2 mb-4">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Tipo:</span>
                <span class="text-sm font-medium text-gray-900">${meta.tipo}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Prioridad:</span>
                <span class="px-2 py-1 text-xs font-medium rounded-full ${prioridadColor[meta.prioridad]}">${meta.prioridad}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Grupo Asignado:</span>
                <span class="text-sm font-medium text-gray-900">${meta.responsable}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Fecha límite:</span>
                <span class="text-sm font-medium text-gray-900">${new Date(meta.fecha_limite).toLocaleDateString()}</span>
            </div>
        </div>

        <div class="border-t pt-3">
            <div class="flex flex-wrap gap-1">
                ${meta.equipos.map(equipo => 
                    `<span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">${equipo}</span>`
                ).join('')}
            </div>
        </div>
    `;

    return div;
}

// Filtrar metas
function filterMetas() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const estadoFilter = document.getElementById('estadoFilter').value;
    const prioridadFilter = document.getElementById('prioridadFilter').value;

    filteredMetas = metas.filter(meta => {
        const matchesSearch = meta.titulo.toLowerCase().includes(searchTerm) || 
                            meta.descripcion.toLowerCase().includes(searchTerm);
        const matchesEstado = !estadoFilter || meta.estado === estadoFilter;
        const matchesPrioridad = !prioridadFilter || meta.prioridad === prioridadFilter;

        return matchesSearch && matchesEstado && matchesPrioridad;
    });

    renderMetas();
}

// Modal functions
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.getElementById('createMetaForm').reset();
}

function openDetailsModal(meta) {
    currentMeta = meta;
    const modal = document.getElementById('detailsModal');
    const title = document.getElementById('modalTitle');
    const content = document.getElementById('modalContent');

    title.textContent = meta.titulo;

    const estadoColor = {
        'Pendiente': 'bg-yellow-100 text-yellow-800',
        'En Progreso': 'bg-blue-100 text-blue-800',
        'Completada': 'bg-green-100 text-green-800'
    };

    const prioridadColor = {
        'Alta': 'bg-red-100 text-red-800',
        'Media': 'bg-yellow-100 text-yellow-800',
        'Baja': 'bg-green-100 text-green-800'
    };

    const progressColor = meta.progreso >= 75 ? 'bg-green-500' : meta.progreso >= 50 ? 'bg-blue-500' : meta.progreso >= 25 ? 'bg-yellow-500' : 'bg-red-500';

    content.innerHTML = `
        <div class="space-y-6">
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-2">Descripción</h4>
                <p class="text-gray-600">${meta.descripcion}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Información General</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Estado:</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${estadoColor[meta.estado]}">${meta.estado}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Prioridad:</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${prioridadColor[meta.prioridad]}">${meta.prioridad}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Tipo:</span>
                            <span class="text-sm font-medium text-gray-900">${meta.tipo}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Grupo Asignado:</span>
                            <span class="text-sm font-medium text-gray-900">${meta.responsable}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Fechas</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Fecha inicio:</span>
                            <span class="text-sm font-medium text-gray-900">${new Date(meta.fecha_inicio).toLocaleDateString()}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Fecha límite:</span>
                            <span class="text-sm font-medium text-gray-900">${new Date(meta.fecha_limite).toLocaleDateString()}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Progreso</h4>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-500">Completado</span>
                    <span class="text-sm font-medium text-gray-900">${meta.progreso}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="${progressColor} h-3 rounded-full transition-all duration-300" style="width: ${meta.progreso}%"></div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Equipos Asignados</h4>
                <div class="flex flex-wrap gap-2">
                    ${meta.equipos.map(equipo => 
                        `<span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full">${equipo}</span>`
                    ).join('')}
                </div>
            </div>
        </div>
    `;

    modal.classList.remove('hidden');
}

function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
    currentMeta = null;
}

// CRUD Operations
function createMeta() {
    const form = document.getElementById('createMetaForm');
    const formData = new FormData(form);
    
    // Obtener equipos seleccionados
    const equiposSeleccionados = [];
    const equiposCheckboxes = form.querySelectorAll('input[name="equipos"]:checked');
    equiposCheckboxes.forEach(checkbox => {
        equiposSeleccionados.push(checkbox.value);
    });

    const nuevaMeta = {
        id: nextId++,
        titulo: formData.get('titulo'),
        descripcion: formData.get('descripcion'),
        tipo: formData.get('tipo'),
        prioridad: formData.get('prioridad'),
        responsable: formData.get('responsable'),
        fecha_inicio: formData.get('fecha_inicio'),
        fecha_limite: formData.get('fecha_limite'),
        estado: 'Pendiente',
        progreso: 0,
        equipos: equiposSeleccionados
    };

    metas.push(nuevaMeta);
    filteredMetas = [...metas];
    renderMetas();
    closeCreateModal();
    showToast('Meta creada correctamente');
}

function editMeta(meta) {
    // Implementar edición
    showToast('Función de edición en desarrollo');
}

function deleteMeta(metaId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta meta?')) {
        metas = metas.filter(m => m.id !== metaId);
        filteredMetas = [...metas];
        renderMetas();
        closeDetailsModal();
        showToast('Meta eliminada correctamente');
    }
}

// Event Listeners
document.getElementById('createMetaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    createMeta();
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
</script>
@endsection
