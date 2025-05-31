@extends('layouts.coordinador-general.app')

@section('content')
<div class="flex-1 overflow-auto">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 slide-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Actividades</h1>
                <p class="text-gray-600 mt-1">Gestiona y supervisa las actividades de todos los equipos</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium tab-transition hover-scale">
                    <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                    Nueva Actividad
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 slide-in">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" id="searchInput" placeholder="Buscar actividades..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition"
                       onkeyup="filterActivities()">
            </div>
            <div>
                <select id="teamFilter" onchange="filterActivities()" 
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                    <option value="">Todos los equipos</option>
                    @foreach($equipos as $equipo)
                        <option value="{{ $equipo['nombre'] }}">{{ $equipo['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex-1 p-6">
        <div class="text-sm text-gray-600 mb-4">
            Mostrando <span id="activityCount">{{ $tareas->count() }}</span> actividades de todos los equipos
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Pendientes/Incompleta Column -->
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl form-transition">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Pendientes</h3>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full" id="pendientes-count">0</span>
                    </div>
                </div>
                <div class="p-4 space-y-3 overflow-y-auto h-96 kanban-column" id="pendientes-column" data-estado="Incompleta">
                    <!-- Actividades se cargan con JavaScript -->
                </div>
            </div>

            <!-- En Proceso Column -->
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl form-transition">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">En Proceso</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full" id="en-proceso-count">0</span>
                    </div>
                </div>
                <div class="p-4 space-y-3 overflow-y-auto h-96 kanban-column" id="en-proceso-column" data-estado="En proceso">
                    <!-- Actividades se cargan con JavaScript -->
                </div>
            </div>

            <!-- Completadas Column -->
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl form-transition">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Completadas</h3>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full" id="completadas-count">0</span>
                    </div>
                </div>
                <div class="p-4 space-y-3 overflow-y-auto h-96 kanban-column" id="completadas-column" data-estado="Completo">
                    <!-- Actividades se cargan con JavaScript -->
                </div>
            </div>

            <!-- Suspendidas Column -->
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl form-transition">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Suspendidas</h3>
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full" id="suspendidas-count">0</span>
                    </div>
                </div>
                <div class="p-4 space-y-3 overflow-y-auto h-96 kanban-column" id="suspendidas-column" data-estado="Suspendida">
                    <!-- Actividades se cargan con JavaScript -->
                </div>
            </div>
        </div>

        <!-- Resumen de Actividades por Equipo -->
        <div class="bg-white rounded-lg shadow-lg p-6 form-transition hover-scale">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Resumen de Actividades por Equipo</h3>
                    <p class="text-gray-600 mt-1">Distribución de actividades según su estado para cada equipo</p>
                </div>
                <div class="flex space-x-3">
                    <button class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 tab-transition">
                        <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                        Ver detalles
                    </button>
                    <button class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 tab-transition">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                        Exportar
                    </button>
                </div>
            </div>

            <div id="teamSummary" class="space-y-6">
                <!-- El resumen se generará dinámicamente -->
            </div>
        </div>
    </div>
</div>

<!-- Create Activity Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 opacity-0 transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full transform scale-95 transition-all duration-300" id="createModalContent">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Nueva Actividad</h3>
                    <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 tab-transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="createActivityForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                        <input type="text" name="nombre" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea name="descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Equipo</label>
                            <select name="equipo_id" id="equipoSelect" required onchange="loadMetasPorEquipo()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                <option value="">Seleccionar equipo...</option>
                                @foreach($equipos as $equipo)
                                    <option value="{{ $equipo['id'] }}">{{ $equipo['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                            <select name="estado_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                <option value="">Seleccionar estado...</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado['id'] }}">{{ $estado['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Asignado a (Meta)</label>
                        <select name="meta_id" id="metaSelect" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                            <option value="">Primero selecciona un equipo...</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha límite</label>
                        <input type="date" name="fecha_entrega" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                    </div>
                </form>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeCreateModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 tab-transition">
                        Cancelar
                    </button>
                    <button onclick="createActivity()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg tab-transition">
                        Crear Actividad
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 opacity-0 transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full transform scale-95 transition-all duration-300" id="detailsModalContent">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900" id="activityTitle">Detalles de Actividad</h3>
                    <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600 tab-transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="space-y-4" id="activityDetails">
                    <!-- Content loaded by JavaScript -->
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeDetailsModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 tab-transition">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 form-transition">
        <div class="p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Confirmar eliminación</h3>
            <p id="confirmDeleteText" class="text-gray-600 mb-6">¿Estás seguro de que deseas eliminar esta actividad?</p>
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

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-300 z-50">
    <div class="flex items-center">
        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
        <span id="toast-message">Actividad creada correctamente</span>
    </div>
</div>

<!-- Datos del servidor -->
<script type="application/json" id="tareas-data">@json($tareas)</script>
<script type="application/json" id="equipos-data">@json($equipos)</script>
<script type="application/json" id="estados-data">@json($estados)</script>

<!-- Sortable.js para drag and drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
// Variables globales - Datos desde el servidor
let actividades = [];
let equiposDisponibles = [];
let estadosDisponibles = [];
let filteredActividades = [];
let sortableInstances = {};

// Cargar datos del servidor
document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos desde elementos ocultos en el DOM
    const tareasData = document.getElementById('tareas-data');
    const equiposData = document.getElementById('equipos-data');
    const estadosData = document.getElementById('estados-data');
    
    if (tareasData) actividades = JSON.parse(tareasData.textContent);
    if (equiposData) equiposDisponibles = JSON.parse(equiposData.textContent);
    if (estadosData) estadosDisponibles = JSON.parse(estadosData.textContent);
    
    filteredActividades = [...actividades];
    
    console.log('Datos cargados:', {
        actividades: actividades.length,
        equipos: equiposDisponibles.length,
        estados: estadosDisponibles.length
    });
    
    lucide.createIcons();
    loadActivities();
    generateTeamSummary();
    initSortable();
});

// Mapeo de estados de la base de datos a columnas del kanban
const estadoToColumn = {
    'Incompleta': 'pendientes',
    'En proceso': 'en-proceso', 
    'Completo': 'completadas',
    'Suspendida': 'suspendidas'
};

const columnToEstado = {
    'pendientes': 'Incompleta',
    'en-proceso': 'En proceso',
    'completadas': 'Completo', 
    'suspendidas': 'Suspendida'
};

function loadActivities() {
    const columns = {
        'pendientes': document.getElementById('pendientes-column'),
        'en-proceso': document.getElementById('en-proceso-column'),
        'completadas': document.getElementById('completadas-column'),
        'suspendidas': document.getElementById('suspendidas-column')
    };

    // Clear columns
    Object.values(columns).forEach(column => column.innerHTML = '');

    // Count activities by status
    const counts = {
        'pendientes': 0,
        'en-proceso': 0,
        'completadas': 0,
        'suspendidas': 0
    };

    // Load activities into columns
    filteredActividades.forEach(actividad => {
        const columnKey = estadoToColumn[actividad.estado] || 'pendientes';
        const column = columns[columnKey];
        if (column) {
            const activityCard = createActivityCard(actividad);
            column.appendChild(activityCard);
            counts[columnKey]++;
        }
    });

    // Update counts
    document.getElementById('pendientes-count').textContent = counts['pendientes'];
    document.getElementById('en-proceso-count').textContent = counts['en-proceso'];
    document.getElementById('completadas-count').textContent = counts['completadas'];
    document.getElementById('suspendidas-count').textContent = counts['suspendidas'];
    document.getElementById('activityCount').textContent = filteredActividades.length;

    // Re-initialize icons
    lucide.createIcons();
}

function createActivityCard(actividad) {
    const card = document.createElement('div');
    card.className = 'bg-white border border-gray-200 rounded-lg p-3 shadow-md hover:shadow-lg cursor-move form-transition hover-scale activity-card';
    card.setAttribute('data-id', actividad.id);
    card.setAttribute('data-estado-id', actividad.estado_id);
    
    // Evitar que el click para arrastrar abra el modal de detalles
    card.addEventListener('click', function(e) {
        // Solo mostrar detalles si no estamos arrastrando y no se hizo click en un botón
        if (!card.classList.contains('sortable-drag') && !e.target.closest('button')) {
            showActivityDetails(actividad);
        }
    });

    const isOverdue = actividad.esta_vencida;

    card.innerHTML = `
        <div class="flex items-start justify-between mb-2">
            <h4 class="font-medium text-gray-900 text-sm flex-1 mr-2">${actividad.titulo}</h4>
            <div class="flex space-x-1">
                <button onclick="event.stopPropagation(); deleteActivity(${actividad.id}, '${actividad.titulo}')" class="text-red-600 hover:text-red-800 p-1" title="Eliminar">
                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                </button>
            </div>
        </div>
        <p class="text-gray-600 text-xs mb-3">${actividad.descripcion || 'Sin descripción'}</p>
        <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
            <span class="bg-gray-100 px-2 py-1 rounded">${actividad.equipo}</span>
            ${actividad.fecha_entrega ? `<span class="${isOverdue ? 'text-red-600 font-medium' : ''}">${actividad.fecha_entrega}</span>` : '<span>Sin fecha</span>'}
        </div>
        <div class="text-xs text-gray-600">
            <i data-lucide="target" class="w-3 h-3 inline mr-1"></i>
            ${actividad.meta}
        </div>
        ${isOverdue ? '<div class="mt-2 text-xs text-red-600 font-medium">¡Vencida!</div>' : ''}
    `;

    if (isOverdue) {
        card.classList.add('border-red-300', 'bg-red-50');
    }

    return card;
}

// Inicializar Sortable.js para drag and drop
function initSortable() {
    // Destruir instancias previas si existen
    Object.values(sortableInstances).forEach(instance => {
        if (instance && typeof instance.destroy === 'function') {
            instance.destroy();
        }
    });
    
    sortableInstances = {};
    
    // Obtener todas las columnas
    const columns = document.querySelectorAll('.kanban-column');
    
    // Inicializar Sortable en cada columna
    columns.forEach(column => {
        const estadoNombre = column.getAttribute('data-estado');
        
        sortableInstances[estadoNombre] = new Sortable(column, {
            group: 'actividades', // Permite arrastrar entre columnas
            animation: 150, // Duración de la animación en ms
            ghostClass: 'bg-gray-100', // Clase para el elemento fantasma durante el arrastre
            chosenClass: 'bg-gray-200', // Clase para el elemento seleccionado
            dragClass: 'sortable-drag', // Clase para el elemento durante el arrastre
            
            // Cuando se completa el arrastre
            onEnd: function(evt) {
                const actividadId = parseInt(evt.item.getAttribute('data-id'));
                const nuevoEstadoNombre = evt.to.getAttribute('data-estado');
                
                // Actualizar el estado de la actividad
                updateActivityStatus(actividadId, nuevoEstadoNombre);
            }
        });
    });
}

// Actualizar el estado de una actividad
async function updateActivityStatus(actividadId, nuevoEstadoNombre) {
    // Encontrar el estado por nombre
    const nuevoEstado = estadosDisponibles.find(e => e.nombre === nuevoEstadoNombre);
    if (!nuevoEstado) {
        showToast('Error: Estado no encontrado', 'error');
        return;
    }

    // Encontrar la actividad en el array
    const actividad = actividades.find(a => a.id === actividadId);
    
    if (actividad) {
        const estadoAnterior = actividad.estado;
        
        try {
            // Enviar actualización al servidor
            const response = await fetch('/coordinador-general/actividades/actualizar-estado', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    id: actividadId,
                    estado_id: nuevoEstado.id
                })
            });

            const data = await response.json();

            if (data.success) {
                // Actualizar el estado localmente
                actividad.estado = nuevoEstadoNombre;
                actividad.estado_id = nuevoEstado.id;
                
                // Actualizar también en el array filtrado si existe
                const actividadFiltrada = filteredActividades.find(a => a.id === actividadId);
                if (actividadFiltrada) {
                    actividadFiltrada.estado = nuevoEstadoNombre;
                    actividadFiltrada.estado_id = nuevoEstado.id;
                }
                
                // Actualizar contadores
                updateStatusCounts();
                
                // Actualizar resumen por equipo
                generateTeamSummary();
                
                // Mostrar notificación
                showToast(`Actividad "${actividad.titulo}" movida a ${nuevoEstadoNombre}`);
                
            } else {
                showToast(data.error || 'Error al actualizar el estado', 'error');
                // Recargar actividades para revertir el cambio visual
                loadActivities();
                initSortable();
            }

        } catch (error) {
            console.error('Error al actualizar estado:', error);
            showToast('Error de conexión al actualizar el estado', 'error');
            // Recargar actividades para revertir el cambio visual
            loadActivities();
            initSortable();
        }
    }
}

// Actualizar contadores de actividades por estado
function updateStatusCounts() {
    const counts = {
        'pendientes': 0,
        'en-proceso': 0,
        'completadas': 0,
        'suspendidas': 0
    };
    
    filteredActividades.forEach(actividad => {
        const columnKey = estadoToColumn[actividad.estado] || 'pendientes';
        if (counts[columnKey] !== undefined) {
            counts[columnKey]++;
        }
    });
    
    document.getElementById('pendientes-count').textContent = counts['pendientes'];
    document.getElementById('en-proceso-count').textContent = counts['en-proceso'];
    document.getElementById('completadas-count').textContent = counts['completadas'];
    document.getElementById('suspendidas-count').textContent = counts['suspendidas'];
}

function filterActivities() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const teamFilter = document.getElementById('teamFilter').value;

    filteredActividades = actividades.filter(actividad => {
        const matchesSearch = actividad.titulo.toLowerCase().includes(searchTerm) || 
                            (actividad.descripcion && actividad.descripcion.toLowerCase().includes(searchTerm));
        const matchesTeam = !teamFilter || actividad.equipo === teamFilter;

        return matchesSearch && matchesTeam;
    });

    loadActivities();
    initSortable(); // Reinicializar sortable después de filtrar
}

function generateTeamSummary() {
    const teamSummary = document.getElementById('teamSummary');
    const teamStats = {};

    // Calculate stats for each team
    equiposDisponibles.forEach(equipo => {
        teamStats[equipo.nombre] = {
            'Incompleta': 0,
            'En proceso': 0,
            'Completo': 0,
            'Suspendida': 0,
            total: 0
        };
    });

    actividades.forEach(actividad => {
        if (teamStats[actividad.equipo]) {
            teamStats[actividad.equipo][actividad.estado] = (teamStats[actividad.equipo][actividad.estado] || 0) + 1;
            teamStats[actividad.equipo].total++;
        }
    });

    // Generate HTML for each team
    const summaryHTML = Object.entries(teamStats).map(([equipo, stats]) => {
        if (stats.total === 0) return '';

        const incompletaPercent = (stats['Incompleta'] / stats.total) * 100;
        const procesoPercent = (stats['En proceso'] / stats.total) * 100;
        const completoPercent = (stats['Completo'] / stats.total) * 100;
        const suspendidaPercent = (stats['Suspendida'] / stats.total) * 100;

        return `
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">${equipo}</h4>
                    <span class="text-sm text-gray-500">${stats.total} actividades</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    <div class="h-2 rounded-full flex">
                        <div class="bg-yellow-500 h-2 rounded-l-full" style="width: ${incompletaPercent}%"></div>
                        <div class="bg-blue-500 h-2" style="width: ${procesoPercent}%"></div>
                        <div class="bg-green-500 h-2" style="width: ${completoPercent}%"></div>
                        <div class="bg-red-500 h-2 rounded-r-full" style="width: ${suspendidaPercent}%"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-600">
                    <span><span class="inline-block w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>Pendientes (${stats['Incompleta'] || 0})</span>
                    <span><span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-1"></span>En progreso (${stats['En proceso'] || 0})</span>
                    <span><span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-1"></span>Completadas (${stats['Completo'] || 0})</span>
                    <span><span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-1"></span>Suspendidas (${stats['Suspendida'] || 0})</span>
                </div>
            </div>
        `;
    }).join('');

    teamSummary.innerHTML = summaryHTML;
}

// Cargar metas por equipo
async function loadMetasPorEquipo() {
    const equipoId = document.getElementById('equipoSelect').value;
    const metaSelect = document.getElementById('metaSelect');
    
    if (!equipoId) {
        metaSelect.innerHTML = '<option value="">Primero selecciona un equipo...</option>';
        return;
    }
    
    // Mostrar estado de carga
    metaSelect.innerHTML = '<option value="">Cargando metas...</option>';
    metaSelect.disabled = true;
    
    try {
        const url = `/coordinador-general/actividades/metas-por-equipo/${equipoId}`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const responseText = await response.text();
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${responseText.substring(0, 100)}`);
        }
        
        let metas;
        try {
            metas = JSON.parse(responseText);
        } catch (parseError) {
            throw new Error('Respuesta no es JSON válido: ' + responseText.substring(0, 100));
        }
        
        // Habilitar el select
        metaSelect.disabled = false;
        
        if (metas.error) {
            metaSelect.innerHTML = '<option value="">Error: ' + metas.error + '</option>';
            showToast(metas.error, 'error');
            return;
        }
        
        if (!Array.isArray(metas)) {
            metaSelect.innerHTML = '<option value="">Error: Respuesta inválida del servidor</option>';
            showToast('Error: Respuesta inválida del servidor', 'error');
            return;
        }
        
        if (metas.length === 0) {
            metaSelect.innerHTML = '<option value="">No hay metas disponibles para este equipo</option>';
            showToast('Este equipo no tiene metas asignadas. Crea una meta primero.', 'warning');
            return;
        }
        
        // Cargar las metas en el select
        const optionsHtml = '<option value="">Seleccionar meta...</option>' + 
            metas.map(meta => `<option value="${meta.id}" title="${meta.descripcion || ''}">${meta.nombre}</option>`).join('');
        
        metaSelect.innerHTML = optionsHtml;
            
    } catch (error) {
        metaSelect.disabled = false;
        metaSelect.innerHTML = '<option value="">Error de conexión</option>';
        showToast('Error: ' + error.message, 'error');
    }
}

function showActivityDetails(actividad) {
    const modal = document.getElementById('detailsModal');
    const title = document.getElementById('activityTitle');
    const details = document.getElementById('activityDetails');

    title.textContent = actividad.titulo;

    details.innerHTML = `
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <p class="text-gray-900">${actividad.descripcion || 'Sin descripción'}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Equipo</label>
                    <p class="text-gray-900">${actividad.equipo}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <p class="text-gray-900">${actividad.estado}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Meta</label>
                    <p class="text-gray-900">${actividad.meta}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha límite</label>
                    <p class="text-gray-900 ${actividad.esta_vencida ? 'text-red-600 font-medium' : ''}">${actividad.fecha_entrega || 'Sin fecha límite'}</p>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha de creación</label>
                <p class="text-gray-900">${actividad.fecha_creacion || 'Sin fecha'}</p>
            </div>
            ${actividad.esta_vencida ? '<div class="text-red-600 font-medium text-sm">⚠️ Esta actividad está vencida</div>' : ''}
        </div>
    `;

    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        document.getElementById('detailsModalContent').classList.remove('scale-95');
    }, 10);
}

function closeDetailsModal() {
    const modal = document.getElementById('detailsModal');
    const content = document.getElementById('detailsModalContent');
    
    modal.classList.add('opacity-0');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function openCreateModal() {
    const modal = document.getElementById('createModal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        document.getElementById('createModalContent').classList.remove('scale-95');
    }, 10);
}

function closeCreateModal() {
    const modal = document.getElementById('createModal');
    const content = document.getElementById('createModalContent');
    
    modal.classList.add('opacity-0');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.getElementById('createActivityForm').reset();
        document.getElementById('metaSelect').innerHTML = '<option value="">Primero selecciona un equipo...</option>';
    }, 300);
}

async function createActivity() {
    const form = document.getElementById('createActivityForm');
    const formData = new FormData(form);

    try {
        const response = await fetch('/coordinador-general/actividades', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Agregar la nueva actividad al array
            actividades.push(data.tarea);
            filteredActividades = [...actividades];
            
            loadActivities();
            generateTeamSummary();
            initSortable();
            closeCreateModal();
            showToast('Actividad creada correctamente');
        } else {
            showToast(data.error || 'Error al crear la actividad', 'error');
        }
    } catch (error) {
        console.error('Error al crear actividad:', error);
        showToast('Error de conexión al crear la actividad', 'error');
    }
}

function deleteActivity(activityId, activityName) {
    // Configurar el texto del modal
    document.getElementById('confirmDeleteText').textContent = `¿Estás seguro de que deseas eliminar la actividad "${activityName}"?`;
    
    // Configurar el botón de confirmación
    const confirmButton = document.getElementById('confirmDeleteButton');
    confirmButton.onclick = async function() {
        try {
            const response = await fetch(`/coordinador-general/actividades/${activityId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                showToast('Actividad eliminada exitosamente');
                closeConfirmDeleteModal();
                
                // Remover la actividad del array local
                actividades = actividades.filter(a => a.id !== activityId);
                filteredActividades = filteredActividades.filter(a => a.id !== activityId);
                
                // Recargar la vista
                loadActivities();
                generateTeamSummary();
                initSortable();
            } else {
                showToast(data.error || 'Error al eliminar la actividad', 'error');
            }
        } catch (error) {
            console.error('Error al eliminar actividad:', error);
            showToast('Error de conexión al eliminar la actividad', 'error');
        }
    };
    
    // Mostrar el modal
    document.getElementById('confirmDeleteModal').classList.remove('hidden');
}

function closeConfirmDeleteModal() {
    document.getElementById('confirmDeleteModal').classList.add('hidden');
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    
    // Cambiar color según el tipo
    if (type === 'error') {
        toast.className = toast.className.replace('bg-green-500', 'bg-red-500');
    } else if (type === 'warning') {
        toast.className = toast.className.replace('bg-green-500', 'bg-yellow-500').replace('bg-red-500', 'bg-yellow-500');
    } else {
        toast.className = toast.className.replace('bg-red-500', 'bg-green-500').replace('bg-yellow-500', 'bg-green-500');
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
        closeDetailsModal();
        closeConfirmDeleteModal();
    }
});
</script>

<style>
/* Estilos para el drag and drop */
.sortable-ghost {
    opacity: 0.5;
    background-color: #f3f4f6 !important;
    border: 2px dashed #d1d5db !important;
}

.sortable-drag {
    opacity: 0.9;
    transform: rotate(2deg);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
}

.kanban-column {
    min-height: 50px; /* Altura mínima para que se pueda arrastrar a columnas vacías */
}

/* Eliminar el espacio entre tarjetas cuando se arrastran */
.kanban-column.sortable-drag > * {
    margin-bottom: 0 !important;
}

/* Indicador visual de que se puede soltar */
.sortable-chosen {
    background-color: #f9fafb;
}

/* Cursor de mover para las tarjetas */
.activity-card {
    cursor: grab;
}

.activity-card:active {
    cursor: grabbing;
}

/* Animaciones y transiciones */
.slide-in {
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hover-scale {
    transition: transform 0.2s ease-in-out;
}

.hover-scale:hover {
    transform: scale(1.02);
}

.form-transition {
    transition: all 0.3s ease;
}

.tab-transition {
    transition: all 0.2s ease-in-out;
}
</style>
@endsection
