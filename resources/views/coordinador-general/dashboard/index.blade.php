{{-- resources/views/coordinador-general/dashboard/index.blade.php --}}
@extends('layouts.coordinador-general.app')

@section('content')
<div class="flex-1 overflow-auto">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-6 slide-in">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bienvenido, Coordinador General</h1>
            <p class="text-gray-600 mt-1">Supervise el progreso de los grupos y gestione las metas globales</p>
        </div>
    </div>

    <!-- Content -->
    <div class="flex-1 p-6">
        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 slide-in">
            <!-- Grupos Activos -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Grupos Activos</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">12</p>
                        <p class="text-sm text-blue-600 mt-1">+2 grupos nuevos este mes</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-full">
                        <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Metas Activas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Metas Activas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">24</p>
                        <p class="text-sm text-blue-600 mt-1">8 metas completadas este mes</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-full">
                        <i data-lucide="target" class="w-6 h-6 text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Actividades -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Actividades</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">142</p>
                        <p class="text-sm text-blue-600 mt-1">67 completadas, 75 en progreso</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-full">
                        <i data-lucide="check-square" class="w-6 h-6 text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Próximas Reuniones -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Próximas Reuniones</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">8</p>
                        <p class="text-sm text-blue-600 mt-1">3 reuniones esta semana</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-full">
                        <i data-lucide="calendar" class="w-6 h-6 text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white border-b border-gray-200 mb-6 slide-in">
            <nav class="flex space-x-8 px-6">
                <button onclick="showDashboardTab('metas')" id="dashboard-tab-metas" class="dashboard-tab-button py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600 tab-transition">
                    Metas
                </button>
                <button onclick="showDashboardTab('grupos')" id="dashboard-tab-grupos" class="dashboard-tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 tab-transition">
                    Grupos
                </button>
                <button onclick="showDashboardTab('actividades')" id="dashboard-tab-actividades" class="dashboard-tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 tab-transition">
                    Actividades
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="relative">
            <!-- Metas Tab -->
            <div id="dashboard-content-metas" class="dashboard-tab-content slide-in">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Meta 1: Incrementar Productividad -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Incrementar Productividad</h3>
                        <p class="text-sm text-gray-600 mb-4">Meta Trimestral</p>
                        
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progreso</span>
                                <span class="text-sm font-bold text-gray-900">68%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 68%"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <div class="flex items-center">
                                <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                <span>Vence en 22 días</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                                <span>8 grupos asignados</span>
                            </div>
                        </div>
                    </div>

                    <!-- Meta 2: Optimizar Procesos -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Optimizar Procesos</h3>
                        <p class="text-sm text-gray-600 mb-4">Meta Semestral</p>
                        
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progreso</span>
                                <span class="text-sm font-bold text-gray-900">42%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 42%"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <div class="flex items-center">
                                <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                <span>Vence en 68 días</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                                <span>5 grupos asignados</span>
                            </div>
                        </div>
                    </div>

                    <!-- Meta 3: Reducir Costos Operativos -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Reducir Costos Operativos</h3>
                        <p class="text-sm text-gray-600 mb-4">Meta Anual</p>
                        
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progreso</span>
                                <span class="text-sm font-bold text-gray-900">25%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 25%"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <div class="flex items-center">
                                <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                <span>Vence en 245 días</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                                <span>12 grupos asignados</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ver todas las metas button -->
                <div class="flex justify-end mt-6">
                    <button onclick="showToast('Redirigiendo a todas las metas')" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-lg hover:shadow-xl tab-transition hover-scale">
                        Ver todas las metas
                    </button>
                </div>
            </div>

            <!-- Grupos Tab -->
            <div id="dashboard-content-grupos" class="dashboard-tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Equipo Desarrollo</h3>
                        <p class="text-sm text-gray-600 mb-4">8 miembros activos</p>
                        <div class="flex justify-between text-sm">
                            <span>Actividades: 45</span>
                            <span class="text-green-600">85% completadas</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Equipo Marketing</h3>
                        <p class="text-sm text-gray-600 mb-4">6 miembros activos</p>
                        <div class="flex justify-between text-sm">
                            <span>Actividades: 32</span>
                            <span class="text-green-600">78% completadas</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Equipo Ventas</h3>
                        <p class="text-sm text-gray-600 mb-4">5 miembros activos</p>
                        <div class="flex justify-between text-sm">
                            <span>Actividades: 28</span>
                            <span class="text-green-600">92% completadas</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividades Tab -->
            <div id="dashboard-content-actividades" class="dashboard-tab-content hidden">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividades Recientes</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div>
                                <p class="font-medium text-gray-900">Implementar nuevo sistema CRM</p>
                                <p class="text-sm text-gray-600">Equipo Desarrollo</p>
                            </div>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">En progreso</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div>
                                <p class="font-medium text-gray-900">Campaña de marketing digital</p>
                                <p class="text-sm text-gray-600">Equipo Marketing</p>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Completada</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <p class="font-medium text-gray-900">Análisis de ventas Q4</p>
                                <p class="text-sm text-gray-600">Equipo Ventas</p>
                            </div>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Pendiente</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-300 z-50">
    <div class="flex items-center">
        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
        <span id="toast-message">Acción completada</span>
    </div>
</div>

<script>
    // Dashboard tab functionality
    function showDashboardTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.dashboard-tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active state from all tabs
        document.querySelectorAll('.dashboard-tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab content with animation
        const selectedContent = document.getElementById(`dashboard-content-${tabName}`);
        selectedContent.classList.remove('hidden');
        selectedContent.classList.add('slide-in');
        
        // Add active state to selected tab
        const selectedTab = document.getElementById(`dashboard-tab-${tabName}`);
        selectedTab.classList.remove('border-transparent', 'text-gray-500');
        selectedTab.classList.add('border-blue-500', 'text-blue-600');
    }

    // Toast notification
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

    // Initialize icons when page loads
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endsection