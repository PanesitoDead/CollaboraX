{{-- resources/views/coordinador-grupo/dashboard.blade.php --}}
@extends('layouts.private.coord-equipo')

@section('title', 'Panel de Coordinador de Grupo')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Panel de Coordinador de Grupo</h1>
            <p class="text-gray-600">Gestión del Grupo de Ventas - Área Marketing</p>
        </div>
        <div class="flex gap-2">
            <button id="btn-nueva-meta" 
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nueva Meta
            </button>
            <button id="btn-nueva-actividad" 
                    class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nueva Actividad
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Colaboradores</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">8</div>
            <p class="text-xs text-gray-500">7 activos</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Metas Activas</h3>
                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">5</div>
            <p class="text-xs text-gray-500">3 completadas, 2 pendientes</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Actividades</h3>
                <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">12</div>
            <p class="text-xs text-gray-500">8 en progreso, 15 completadas</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Cumplimiento</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">85%</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: 85%"></div>
            </div>
        </div>
    </div>

    {{-- Tabs Content --}}
    <div class="bg-white rounded-lg border border-gray-300">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button id="tab-btn-metas" 
                        class="tab-button border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="metas">
                    Metas
                </button>
                <button id="tab-btn-actividades" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="actividades">
                    Actividades
                </button>
                <button id="tab-btn-colaboradores" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="colaboradores">
                    Colaboradores
                </button>
            </nav>
        </div>

        {{-- Tab Content: Metas --}}
        <div id="tab-metas" class="tab-content p-6">
            <div class="mb-4">
                <h3 class="text-lg font-medium">Metas del Grupo</h3>
                <p class="text-gray-600">Objetivos y metas asignadas al grupo de trabajo</p>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between rounded-lg border border-gray-300 p-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <p class="font-medium">Incrementar ventas Q4</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Alta
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                En progreso
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Aumentar las ventas en un 20% durante el último trimestre</p>
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span>Asignado a: Juan Pérez</span>
                            <span>•</span>
                            <span>Fecha límite: 31/12/2024</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col items-end">
                            <span class="text-sm">Progreso</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: 65%"></div>
                                </div>
                                <span class="text-xs">65%</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                Ver Detalles
                            </button>
                            <button class="px-3 py-1 text-sm border border-blue-300 text-blue-600 rounded-md hover:bg-blue-50">
                                Editar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab Content: Actividades --}}
        <div id="tab-actividades" class="tab-content p-6 hidden">
            <div class="mb-4">
                <h3 class="text-lg font-medium">Actividades del Grupo</h3>
                <p class="text-gray-600">Tareas y actividades asignadas a los colaboradores</p>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between rounded-lg border border-gray-300 p-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <p class="font-medium">Análisis de mercado</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Alta
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                En progreso
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Realizar estudio de mercado para nuevos productos</p>
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span>Asignado a: Ana Martínez</span>
                            <span>•</span>
                            <span>Fecha límite: 15/11/2024</span>
                            <span>•</span>
                            <span>Meta: Incrementar ventas Q4</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                            Ver Detalles
                        </button>
                        <button class="px-3 py-1 text-sm border border-blue-300 text-blue-600 rounded-md hover:bg-blue-50">
                            Editar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab Content: Colaboradores --}}
        <div id="tab-colaboradores" class="tab-content p-6 hidden">
            <div class="mb-4">
                <h3 class="text-lg font-medium">Colaboradores del Grupo</h3>
                <p class="text-gray-600">Miembros del equipo y su estado actual</p>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between rounded-lg border border-gray-300 p-4">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-sm font-medium">JP</span>
                        </div>
                        <div>
                            <p class="font-medium">Juan Pérez</p>
                            <p class="text-sm text-gray-500">juan.perez@empresa.com</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-center">
                            <p class="text-sm font-medium">2</p>
                            <p class="text-xs text-gray-500">Metas</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium">3</p>
                            <p class="text-xs text-gray-500">Pendientes</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Colaborador Senior
                        </span>
                        <div class="text-sm text-gray-500">Hace 2 horas</div>
                        <div class="flex gap-2">
                            <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                Ver Perfil
                            </button>
                            <button class="px-3 py-1 text-sm border border-blue-300 text-blue-600 rounded-md hover:bg-blue-50">
                                Asignar Tarea
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal para Nueva Meta --}}
<div id="metaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium">Crear Nueva Meta</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Define una nueva meta para el grupo de trabajo.
                </p>
            </div>
            
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
                    <input type="text" name="titulo" id="titulo" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="3" required 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" id="btn-cerrar-meta" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                    Crear Meta
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal para Nueva Actividad --}}
<div id="actividadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium">Crear Nueva Actividad</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Asigna una nueva actividad a un colaborador.
                </p>
            </div>
            
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label for="actividad_titulo" class="block text-sm font-medium text-gray-700">Título</label>
                    <input type="text" name="titulo" id="actividad_titulo" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="actividad_descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea name="descripcion" id="actividad_descripcion" rows="3" required 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" id="btn-cerrar-actividad" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700">
                    Crear Actividad
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
console.log('Script cargado'); // Debug

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado'); // Debug
    
    // Verificar que los elementos existen
    const btnNuevaMeta = document.getElementById('btn-nueva-meta');
    const btnNuevaActividad = document.getElementById('btn-nueva-actividad');
    const metaModal = document.getElementById('metaModal');
    const actividadModal = document.getElementById('actividadModal');
    
    console.log('Elementos encontrados:', {
        btnNuevaMeta: !!btnNuevaMeta,
        btnNuevaActividad: !!btnNuevaActividad,
        metaModal: !!metaModal,
        actividadModal: !!actividadModal
    });

    // Tab functionality
    function showTab(tabName) {
        console.log('Cambiando a tab:', tabName); // Debug
        
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active state from all buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab content
        const tabContent = document.getElementById('tab-' + tabName);
        if (tabContent) {
            tabContent.classList.remove('hidden');
            console.log('Tab mostrado:', tabName);
        } else {
            console.log('Tab no encontrado:', 'tab-' + tabName);
        }
        
        // Add active state to selected button
        const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
        if (activeButton) {
            activeButton.classList.remove('border-transparent', 'text-gray-500');
            activeButton.classList.add('border-blue-500', 'text-blue-600');
            console.log('Botón activado:', tabName);
        } else {
            console.log('Botón no encontrado:', tabName);
        }
    }

    // Add event listeners to tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            console.log('Click en tab:', tabName); // Debug
            showTab(tabName);
        });
    });

    // Modal functionality
    if (btnNuevaMeta && metaModal) {
        btnNuevaMeta.addEventListener('click', function() {
            console.log('Abriendo modal meta'); // Debug
            metaModal.classList.remove('hidden');
        });
    }

    if (btnNuevaActividad && actividadModal) {
        btnNuevaActividad.addEventListener('click', function() {
            console.log('Abriendo modal actividad'); // Debug
            actividadModal.classList.remove('hidden');
        });
    }

    // Close modals
    const btnCerrarMeta = document.getElementById('btn-cerrar-meta');
    const btnCerrarActividad = document.getElementById('btn-cerrar-actividad');

    if (btnCerrarMeta && metaModal) {
        btnCerrarMeta.addEventListener('click', function() {
            console.log('Cerrando modal meta'); // Debug
            metaModal.classList.add('hidden');
        });
    }

    if (btnCerrarActividad && actividadModal) {
        btnCerrarActividad.addEventListener('click', function() {
            console.log('Cerrando modal actividad'); // Debug
            actividadModal.classList.add('hidden');
        });
    }

    // Close modals when clicking outside
    if (metaModal) {
        metaModal.addEventListener('click', function(e) {
            if (e.target === metaModal) {
                console.log('Cerrando modal meta por click fuera'); // Debug
                metaModal.classList.add('hidden');
            }
        });
    }

    if (actividadModal) {
        actividadModal.addEventListener('click', function(e) {
            if (e.target === actividadModal) {
                console.log('Cerrando modal actividad por click fuera'); // Debug
                actividadModal.classList.add('hidden');
            }
        });
    }

    // Initialize first tab as active
    showTab('metas');
});
</script>
@endsection