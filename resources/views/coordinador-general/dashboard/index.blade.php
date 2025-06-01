@extends('layouts.coordinador-general.app')

@section('content')
<div class="flex-1 overflow-auto">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-6 slide-in">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bienvenido, Coordinador General</h1>
            <p class="text-gray-600 mt-1">Supervise el progreso de los grupos y gestione las metas globales{{ isset($empresa) ? ' de ' . $empresa->nombre : '' }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="flex-1 p-6">
        <!-- Error Message -->
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 slide-in">
            <!-- Grupos Activos -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Grupos Activos</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $metricas['equipos_activos'] }}</p>
                        <p class="text-sm text-blue-600 mt-1">Equipos de trabajo activos</p>
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
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $metricas['metas_activas'] }}</p>
                        <p class="text-sm text-blue-600 mt-1">Metas asignadas a equipos</p>
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
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $metricas['total_actividades'] }}</p>
                        <p class="text-sm text-blue-600 mt-1">{{ $metricas['actividades_completadas'] }} completadas, {{ $metricas['actividades_en_progreso'] }} en progreso</p>
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
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $metricas['reuniones_proximas'] }}</p>
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
                @if($metasRecientes->count() > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        @foreach($metasRecientes as $meta)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $meta['nombre'] }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ $meta['equipo'] }}</p>
                            
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Progreso</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $meta['progreso'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $progressColor = 'bg-red-500';
                                        if($meta['progreso'] >= 75) $progressColor = 'bg-green-500';
                                        elseif($meta['progreso'] >= 50) $progressColor = 'bg-blue-500';
                                        elseif($meta['progreso'] >= 25) $progressColor = 'bg-yellow-500';
                                    @endphp
                                    <div class="{{ $progressColor }} h-2 rounded-full transition-all duration-300" style="width: {{ $meta['progreso'] }}%"></div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                    <span>
                                        @if($meta['dias_vencimiento'])
                                            @if(is_numeric($meta['dias_vencimiento']))
                                                Vence en {{ $meta['dias_vencimiento'] }} días
                                            @else
                                                {{ $meta['dias_vencimiento'] }}
                                            @endif
                                        @else
                                            Sin fecha límite
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <i data-lucide="check-square" class="w-4 h-4 mr-1"></i>
                                    <span>{{ $meta['tareas_completadas'] }}/{{ $meta['total_tareas'] }} tareas</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="target" class="w-12 h-12 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay metas disponibles</h3>
                        <p class="text-gray-500 mb-6">Crea metas para hacer seguimiento al progreso de los equipos.</p>
                    </div>
                @endif

                <!-- Ver todas las metas button -->
                <div class="flex justify-end mt-6">
                    <a href="{{ route('coordinador-general.metas') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-lg hover:shadow-xl tab-transition hover-scale">
                        Ver todas las metas
                    </a>
                </div>
            </div>

            <!-- Grupos Tab -->
            <div id="dashboard-content-grupos" class="dashboard-tab-content hidden">
                @if($equiposRecientes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($equiposRecientes as $equipo)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 form-transition hover-scale">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $equipo['nombre'] }}</h3>
                            <p class="text-sm text-gray-600 mb-2">{{ $equipo['area'] }}</p>
                            <p class="text-sm text-gray-600 mb-4">{{ $equipo['miembros_count'] }} miembros activos</p>
                            
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Progreso</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $equipo['progreso'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $equipo['progreso'] }}%"></div>
                                </div>
                            </div>

                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Metas: {{ $equipo['metas_activas'] }}</span>
                                <span>Coordinador: {{ Str::limit($equipo['coordinador'], 15) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="users" class="w-12 h-12 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay equipos disponibles</h3>
                        <p class="text-gray-500 mb-6">Crea equipos para organizar el trabajo.</p>
                    </div>
                @endif

                <!-- Ver todos los grupos button -->
                <div class="flex justify-end mt-6">
                    <a href="{{ route('coordinador-general.equipos') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-lg hover:shadow-xl tab-transition hover-scale">
                        Ver todos los grupos
                    </a>
                </div>
            </div>

            <!-- Actividades Tab -->
            <div id="dashboard-content-actividades" class="dashboard-tab-content hidden">
                @if($actividadesRecientes->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividades Recientes</h3>
                        <div class="space-y-4">
                            @foreach($actividadesRecientes as $actividad)
                            <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $actividad['titulo'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $actividad['equipo'] }} • {{ $actividad['meta'] }}</p>
                                    @if($actividad['fecha_creacion'])
                                        <p class="text-xs text-gray-500">Creada: {{ $actividad['fecha_creacion'] }}</p>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    @if($actividad['estado'] === 'Completo')
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Completada</span>
                                    @elseif($actividad['estado'] === 'En proceso')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">En progreso</span>
                                    @elseif($actividad['estado'] === 'Suspendida')
                                        <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">Suspendida</span>
                                    @else
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Pendiente</span>
                                    @endif
                                    @if($actividad['esta_vencida'])
                                        <div class="text-xs text-red-600 font-medium mt-1">¡Vencida!</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="check-square" class="w-12 h-12 text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay actividades disponibles</h3>
                            <p class="text-gray-500 mb-6">Crea actividades para hacer seguimiento a las tareas.</p>
                        </div>
                    </div>
                @endif

                <!-- Ver todas las actividades button -->
                <div class="flex justify-end mt-6">
                    <a href="{{ route('coordinador-general.actividades') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-lg hover:shadow-xl tab-transition hover-scale">
                        Ver todas las actividades
                    </a>
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
        
        // Show success message if exists in session
        @if(session('success'))
            showToast("{{ session('success') }}");
        @endif
    });
</script>

<style>
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
