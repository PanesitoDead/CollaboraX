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
                </select>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex-1 p-6">
        <div class="text-sm text-gray-600 mb-4">
            Mostrando <span id="activityCount">0</span> actividades de todos los equipos
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Pendientes Column -->
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl form-transition">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Pendientes</h3>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full" id="pendientes-count">0</span>
                    </div>
                </div>
                <div class="p-4 space-y-3 overflow-y-auto h-96" id="pendientes-column">
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
                <div class="p-4 space-y-3 overflow-y-auto h-96" id="en-proceso-column">
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
                <div class="p-4 space-y-3 overflow-y-auto h-96" id="completadas-column">
                    <!-- Actividades se cargan con JavaScript -->
                </div>
            </div>

            <!-- Retrasadas Column -->
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl form-transition">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Retrasadas</h3>
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full" id="retrasadas-count">0</span>
                    </div>
                </div>
                <div class="p-4 space-y-3 overflow-y-auto h-96" id="retrasadas-column">
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                        <input type="text" name="titulo" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea name="descripcion" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Equipo</label>
                            <select name="equipo" id="equipoSelect" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                <option value="">Seleccionar equipo...</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prioridad</label>
                            <select name="prioridad" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                <option value="Alta">Alta</option>
                                <option value="Media">Media</option>
                                <option value="Baja">Baja</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha límite</label>
                        <input type="date" name="fechaLimite" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Asignado a</label>
                        <input type="text" name="asignadoA" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
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
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg tab-transition">
                        Editar
                    </button>
                </div>
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

<script>
// Variables globales
let actividades = [];
let equiposDisponibles = [];
let metasDisponibles = [];
let filteredActividades = [];
let nextId = 25;

// Datos hardcodeados completos para carga instantánea
const actividadesHardcoded = [
    // Pendientes (6 actividades)
    { id: 1, titulo: "Implementar autenticación", descripcion: "Desarrollar sistema de login y registro de usuarios", equipo: "Equipo Desarrollo", prioridad: "Alta", fechaLimite: "2024-02-15", asignadoA: "Carlos Ruiz", estado: "pendiente" },
    { id: 2, titulo: "Diseñar landing page", descripcion: "Crear diseño para página principal del sitio web", equipo: "Equipo Marketing", prioridad: "Media", fechaLimite: "2024-02-20", asignadoA: "Ana García", estado: "pendiente" },
    { id: 3, titulo: "Configurar base de datos", descripcion: "Establecer estructura de base de datos principal", equipo: "Equipo Desarrollo", prioridad: "Alta", fechaLimite: "2024-02-10", asignadoA: "Miguel Torres", estado: "pendiente" },
    { id: 4, titulo: "Análisis de mercado", descripcion: "Investigar competencia y tendencias del mercado", equipo: "Equipo Ventas", prioridad: "Media", fechaLimite: "2024-02-25", asignadoA: "Laura Mendez", estado: "pendiente" },
    { id: 5, titulo: "Preparar presentación", descripcion: "Crear slides para reunión con cliente importante", equipo: "Equipo Ventas", prioridad: "Alta", fechaLimite: "2024-02-12", asignadoA: "Roberto Silva", estado: "pendiente" },
    { id: 6, titulo: "Documentar API", descripcion: "Crear documentación técnica completa de la API", equipo: "Equipo IT", prioridad: "Baja", fechaLimite: "2024-03-01", asignadoA: "Elena Vargas", estado: "pendiente" },
    
    // En Proceso (6 actividades)
    { id: 7, titulo: "Desarrollar dashboard", descripcion: "Panel de control administrativo para gestión", equipo: "Equipo Desarrollo", prioridad: "Alta", fechaLimite: "2024-02-18", asignadoA: "Pedro López", estado: "en-proceso" },
    { id: 8, titulo: "Campaña redes sociales", descripcion: "Estrategia de marketing digital en redes sociales", equipo: "Equipo Marketing", prioridad: "Media", fechaLimite: "2024-02-22", asignadoA: "Sofia Herrera", estado: "en-proceso" },
    { id: 9, titulo: "Optimizar rendimiento", descripcion: "Mejorar velocidad de carga de la aplicación", equipo: "Equipo Desarrollo", prioridad: "Media", fechaLimite: "2024-02-28", asignadoA: "Diego Morales", estado: "en-proceso" },
    { id: 10, titulo: "Seguimiento clientes", descripcion: "Contactar y dar seguimiento a leads potenciales", equipo: "Equipo Ventas", prioridad: "Alta", fechaLimite: "2024-02-16", asignadoA: "Carmen Jiménez", estado: "en-proceso" },
    { id: 11, titulo: "Testing aplicación", descripcion: "Pruebas de funcionalidad y usabilidad", equipo: "Equipo Desarrollo", prioridad: "Alta", fechaLimite: "2024-02-20", asignadoA: "Andrés Castro", estado: "en-proceso" },
    { id: 12, titulo: "Manual usuario", descripcion: "Guía de uso completa para clientes finales", equipo: "Equipo IT", prioridad: "Media", fechaLimite: "2024-02-26", asignadoA: "Valeria Ramos", estado: "en-proceso" },
    
    // Completadas (6 actividades)
    { id: 13, titulo: "Configurar servidor", descripcion: "Setup inicial del hosting y configuración", equipo: "Equipo Desarrollo", prioridad: "Alta", fechaLimite: "2024-02-05", asignadoA: "Fernando Díaz", estado: "completada" },
    { id: 14, titulo: "Crear logo empresa", descripcion: "Diseño de identidad visual corporativa", equipo: "Equipo Marketing", prioridad: "Media", fechaLimite: "2024-02-08", asignadoA: "Gabriela Soto", estado: "completada" },
    { id: 15, titulo: "Definir arquitectura", descripcion: "Estructura técnica del sistema completo", equipo: "Equipo Desarrollo", prioridad: "Alta", fechaLimite: "2024-02-03", asignadoA: "Ricardo Peña", estado: "completada" },
    { id: 16, titulo: "Estrategia contenido", descripcion: "Plan de publicaciones y contenido digital", equipo: "Equipo Marketing", prioridad: "Media", fechaLimite: "2024-02-07", asignadoA: "Mónica Reyes", estado: "completada" },
    { id: 17, titulo: "Contactar proveedores", descripcion: "Negociar precios y términos comerciales", equipo: "Equipo Ventas", prioridad: "Baja", fechaLimite: "2024-02-06", asignadoA: "Javier Ortiz", estado: "completada" },
    { id: 18, titulo: "Análisis competencia", descripcion: "Estudio detallado de mercado y competidores", equipo: "Equipo Marketing", prioridad: "Media", fechaLimite: "2024-02-09", asignadoA: "Patricia Luna", estado: "completada" },
    
    // Retrasadas (6 actividades)
    { id: 19, titulo: "Integrar pasarela pago", descripcion: "Conectar sistema de pagos en línea", equipo: "Equipo Desarrollo", prioridad: "Alta", fechaLimite: "2024-01-30", asignadoA: "Alejandro Vega", estado: "retrasada" },
    { id: 20, titulo: "Auditoría seguridad", descripcion: "Revisar vulnerabilidades del sistema", equipo: "Equipo IT", prioridad: "Alta", fechaLimite: "2024-01-28", asignadoA: "Cristina Flores", estado: "retrasada" },
    { id: 21, titulo: "Capacitar equipo ventas", descripcion: "Training sobre nuevo producto y procesos", equipo: "Equipo Ventas", prioridad: "Media", fechaLimite: "2024-01-25", asignadoA: "Raúl Guerrero", estado: "retrasada" },
    { id: 22, titulo: "Backup automático", descripcion: "Sistema de respaldos automatizado", equipo: "Equipo IT", prioridad: "Alta", fechaLimite: "2024-01-20", asignadoA: "Beatriz Campos", estado: "retrasada" },
    { id: 23, titulo: "Optimizar SEO", descripcion: "Mejorar posicionamiento en buscadores", equipo: "Equipo Marketing", prioridad: "Media", fechaLimite: "2024-01-31", asignadoA: "Sergio Medina", estado: "retrasada" },
    { id: 24, titulo: "Monitoreo sistema", descripcion: "Implementar alertas y métricas de rendimiento", equipo: "Equipo IT", prioridad: "Alta", fechaLimite: "2024-01-22", asignadoA: "Natalia Cruz", estado: "retrasada" }
];

const equiposHardcoded = ['Equipo Desarrollo', 'Equipo Marketing', 'Equipo Ventas', 'Equipo Operaciones', 'Equipo IT', 'Equipo RRHH'];

// Inicializar con datos hardcodeados inmediatamente
function initializeData() {
    actividades = [...actividadesHardcoded];
    equiposDisponibles = [...equiposHardcoded];
    metasDisponibles = [
        { id: 1, titulo: "Lanzamiento MVP", descripcion: "Versión mínima viable del producto" },
        { id: 2, titulo: "Incrementar ventas 20%", descripcion: "Meta trimestral de crecimiento" },
        { id: 3, titulo: "Mejorar satisfacción cliente", descripcion: "Alcanzar 95% de satisfacción" },
        { id: 4, titulo: "Optimización de procesos", descripcion: "Reducir tiempos de entrega en 30%" }
    ];
    
    filteredActividades = [...actividades];
    nextId = Math.max(...actividades.map(a => a.id)) + 1;
    
    // Renderizar inmediatamente
    loadActivities();
    renderEquiposInSelects();
    generateTeamSummary();
}

// Cargar datos desde el controlador en background
async function loadDataFromServer() {
    try {
        const actividadesResponse = await fetch('/coordinador-general/api/actividades');
        const equiposResponse = await fetch('/coordinador-general/api/actividades/equipos');
        const metasResponse = await fetch('/coordinador-general/api/actividades/metas');
        
        if (actividadesResponse.ok && equiposResponse.ok && metasResponse.ok) {
            const serverActividades = await actividadesResponse.json();
            const serverEquipos = await equiposResponse.json();
            const serverMetas = await metasResponse.json();
            
            // Solo actualizar si los datos del servidor son diferentes
            if (JSON.stringify(serverActividades) !== JSON.stringify(actividades)) {
                actividades = serverActividades;
                filteredActividades = [...actividades];
                loadActivities();
                generateTeamSummary();
            }
            
            if (JSON.stringify(serverEquipos) !== JSON.stringify(equiposDisponibles)) {
                equiposDisponibles = serverEquipos;
                renderEquiposInSelects();
            }
            
            if (JSON.stringify(serverMetas) !== JSON.stringify(metasDisponibles)) {
                metasDisponibles = serverMetas;
            }
        }
    } catch (error) {
        console.log('Manteniendo datos hardcodeados debido a:', error.message);
    }
}

// Renderizar equipos en selects
function renderEquiposInSelects() {
    const teamFilter = document.getElementById('teamFilter');
    const equipoSelect = document.getElementById('equipoSelect');

    const equiposHTML = equiposDisponibles.map(equipo => 
        `<option value="${equipo}">${equipo}</option>`
    ).join('');

    teamFilter.innerHTML = '<option value="">Todos los equipos</option>' + equiposHTML;
    equipoSelect.innerHTML = '<option value="">Seleccionar equipo...</option>' + equiposHTML;
}

// Initialize the board
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    // Cargar datos inmediatamente
    initializeData();
    // Cargar datos del servidor en background
    loadDataFromServer();
});

function loadActivities() {
    const columns = {
        'pendiente': document.getElementById('pendientes-column'),
        'en-proceso': document.getElementById('en-proceso-column'),
        'completada': document.getElementById('completadas-column'),
        'retrasada': document.getElementById('retrasadas-column')
    };

    // Clear columns
    Object.values(columns).forEach(column => column.innerHTML = '');

    // Count activities by status
    const counts = {
        'pendiente': 0,
        'en-proceso': 0,
        'completada': 0,
        'retrasada': 0
    };

    // Load activities into columns
    filteredActividades.forEach(actividad => {
        const column = columns[actividad.estado];
        if (column) {
            const activityCard = createActivityCard(actividad);
            column.appendChild(activityCard);
            counts[actividad.estado]++;
        }
    });

    // Update counts
    document.getElementById('pendientes-count').textContent = counts['pendiente'];
    document.getElementById('en-proceso-count').textContent = counts['en-proceso'];
    document.getElementById('completadas-count').textContent = counts['completada'];
    document.getElementById('retrasadas-count').textContent = counts['retrasada'];
    document.getElementById('activityCount').textContent = filteredActividades.length;

    // Re-initialize icons
    lucide.createIcons();
}

function createActivityCard(actividad) {
    const card = document.createElement('div');
    card.className = 'bg-white border border-gray-200 rounded-lg p-3 shadow-md hover:shadow-lg cursor-pointer form-transition hover-scale';
    card.onclick = () => showActivityDetails(actividad);

    const priorityColors = {
        'Alta': 'bg-red-100 text-red-800',
        'Media': 'bg-yellow-100 text-yellow-800',
        'Baja': 'bg-green-100 text-green-800'
    };

    card.innerHTML = `
        <div class="flex items-start justify-between mb-2">
            <h4 class="font-medium text-gray-900 text-sm">${actividad.titulo}</h4>
            <span class="text-xs px-2 py-1 rounded-full ${priorityColors[actividad.prioridad]}">${actividad.prioridad}</span>
        </div>
        <p class="text-gray-600 text-xs mb-3">${actividad.descripcion}</p>
        <div class="flex items-center justify-between text-xs text-gray-500">
            <span class="bg-gray-100 px-2 py-1 rounded">${actividad.equipo}</span>
            <span>${actividad.fechaLimite}</span>
        </div>
        <div class="mt-2 text-xs text-gray-600">
            <i data-lucide="user" class="w-3 h-3 inline mr-1"></i>
            ${actividad.asignadoA}
        </div>
    `;

    return card;
}

function filterActivities() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const teamFilter = document.getElementById('teamFilter').value;

    filteredActividades = actividades.filter(actividad => {
        const matchesSearch = actividad.titulo.toLowerCase().includes(searchTerm) || 
                            actividad.descripcion.toLowerCase().includes(searchTerm);
        const matchesTeam = !teamFilter || actividad.equipo === teamFilter;

        return matchesSearch && matchesTeam;
    });

    loadActivities();
}

function generateTeamSummary() {
    const teamSummary = document.getElementById('teamSummary');
    const teamStats = {};

    // Calculate stats for each team
    equiposDisponibles.forEach(equipo => {
        teamStats[equipo] = {
            pendiente: 0,
            'en-proceso': 0,
            completada: 0,
            retrasada: 0,
            total: 0
        };
    });

    actividades.forEach(actividad => {
        if (teamStats[actividad.equipo]) {
            teamStats[actividad.equipo][actividad.estado]++;
            teamStats[actividad.equipo].total++;
        }
    });

    // Generate HTML for each team
    const summaryHTML = Object.entries(teamStats).map(([equipo, stats]) => {
        if (stats.total === 0) return '';

        const pendientePercent = (stats.pendiente / stats.total) * 100;
        const procesoPercent = (stats['en-proceso'] / stats.total) * 100;
        const completadaPercent = (stats.completada / stats.total) * 100;
        const retrasadaPercent = (stats.retrasada / stats.total) * 100;

        return `
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">${equipo}</h4>
                    <span class="text-sm text-gray-500">${stats.total} actividades</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    <div class="h-2 rounded-full flex">
                        <div class="bg-yellow-500 h-2 rounded-l-full" style="width: ${pendientePercent}%"></div>
                        <div class="bg-blue-500 h-2" style="width: ${procesoPercent}%"></div>
                        <div class="bg-green-500 h-2" style="width: ${completadaPercent}%"></div>
                        <div class="bg-red-500 h-2 rounded-r-full" style="width: ${retrasadaPercent}%"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-600">
                    <span><span class="inline-block w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>Pendientes (${stats.pendiente})</span>
                    <span><span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-1"></span>En progreso (${stats['en-proceso']})</span>
                    <span><span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-1"></span>Completadas (${stats.completada})</span>
                    <span><span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-1"></span>Retrasadas (${stats.retrasada})</span>
                </div>
            </div>
        `;
    }).join('');

    teamSummary.innerHTML = summaryHTML;
}

function showActivityDetails(actividad) {
    const modal = document.getElementById('detailsModal');
    const title = document.getElementById('activityTitle');
    const details = document.getElementById('activityDetails');

    title.textContent = actividad.titulo;

    const priorityColors = {
        'Alta': 'bg-red-100 text-red-800',
        'Media': 'bg-yellow-100 text-yellow-800',
        'Baja': 'bg-green-100 text-green-800'
    };

    const statusColors = {
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'en-proceso': 'bg-blue-100 text-blue-800',
        'completada': 'bg-green-100 text-green-800',
        'retrasada': 'bg-red-100 text-red-800'
    };

    const statusLabels = {
        'pendiente': 'Pendiente',
        'en-proceso': 'En Proceso',
        'completada': 'Completada',
        'retrasada': 'Retrasada'
    };

    details.innerHTML = `
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <p class="text-gray-900">${actividad.descripcion}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Equipo</label>
                    <p class="text-gray-900">${actividad.equipo}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prioridad</label>
                    <span class="inline-block px-2 py-1 text-xs rounded-full ${priorityColors[actividad.prioridad]}">${actividad.prioridad}</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <span class="inline-block px-2 py-1 text-xs rounded-full ${statusColors[actividad.estado]}">${statusLabels[actividad.estado]}</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha límite</label>
                    <p class="text-gray-900">${actividad.fechaLimite}</p>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Asignado a</label>
                <p class="text-gray-900">${actividad.asignadoA}</p>
            </div>
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
    }, 300);
}

function createActivity() {
    const form = document.getElementById('createActivityForm');
    const formData = new FormData(form);

    const nuevaActividad = {
        id: nextId++,
        titulo: formData.get('titulo'),
        descripcion: formData.get('descripcion'),
        equipo: formData.get('equipo'),
        prioridad: formData.get('prioridad'),
        fechaLimite: formData.get('fechaLimite'),
        asignadoA: formData.get('asignadoA'),
        estado: 'pendiente'
    };

    actividades.push(nuevaActividad);
    filteredActividades = [...actividades];
    loadActivities();
    generateTeamSummary();
    closeCreateModal();
    showToast('Actividad creada correctamente');
    form.reset();
}

function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    
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