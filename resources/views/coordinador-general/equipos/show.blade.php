@extends('layouts.coordinador-general.app')

@section('content')
<!-- Header -->
<div class="bg-white border-b border-gray-200 px-6 py-4 slide-in">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('coordinador-general.equipos') }}" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $equipo->nombre }}</h1>
                
                @if($equipo->deleted_at)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <div class="w-1.5 h-1.5 bg-red-400 rounded-full mr-1.5"></div>
                        Inactivo
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></div>
                        Activo
                    </span>
                @endif
            </div>
            <p class="text-gray-600 mt-1">{{ $equipo->area->nombre }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('coordinador-general.equipos.edit', $equipo->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover-scale">
                <i data-lucide="edit" class="w-4 h-4"></i>
                <span>Editar Equipo</span>
            </a>
        </div>
    </div>
</div>

<div class="flex-1 overflow-auto p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información del equipo -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 slide-in">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Información del Equipo</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Descripción</h3>
                        <p class="mt-1 text-gray-900">{{ $equipo->descripcion ?: 'Sin descripción' }}</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Fecha de creación</h3>
                            <p class="mt-1 text-gray-900">{{ $equipo->fecha_creacion instanceof \Carbon\Carbon ? $equipo->fecha_creacion->format('d/m/Y') : \Carbon\Carbon::parse($equipo->fecha_creacion)->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Última actualización</h3>
                            <p class="mt-1 text-gray-900">{{ $equipo->updated_at instanceof \Carbon\Carbon ? $equipo->updated_at->format('d/m/Y') : \Carbon\Carbon::parse($equipo->updated_at)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Coordinador</h3>
                        <div class="mt-1 flex items-center space-x-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-blue-600">{{ $equipo->coordinador->iniciales }}</span>
                            </div>
                            <span class="text-gray-900">{{ $equipo->coordinador->nombre_completo }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Metas y Proyectos -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 slide-in">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Metas y Proyectos</h2>
                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                        Agregar meta
                    </button>
                </div>
                
                @if($equipo->metas->count() > 0)
                    <div class="space-y-4">
                        @foreach($equipo->metas as $meta)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $meta->nombre }}</h3>
                                        <p class="text-sm text-gray-600">{{ $meta->descripcion }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($meta->estado && $meta->estado->nombre === 'completado')
                                            bg-green-100 text-green-800
                                        @elseif($meta->estado && $meta->estado->nombre === 'en_proceso')
                                            bg-blue-100 text-blue-800
                                        @elseif($meta->estado && $meta->estado->nombre === 'pausado')
                                            bg-yellow-100 text-yellow-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $meta->estado ? ucfirst(str_replace('_', ' ', $meta->estado->nombre)) : 'Sin estado' }}
                                    </span>
                                </div>
                                
                                <div class="mt-2">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>Progreso</span>
                                        <span>{{ $meta->porcentaje_completado }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" style="width: {{ $meta->porcentaje_completado }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="mt-3 flex justify-between items-center">
                                    <div class="text-xs text-gray-500">
                                        @if($meta->fecha_entrega)
                                            <span>Fecha límite: {{ \Carbon\Carbon::parse($meta->fecha_entrega)->format('d/m/Y') }}</span>
                                        @else
                                            <span>Sin fecha límite</span>
                                        @endif
                                    </div>
                                    <button class="text-sm text-blue-600 hover:text-blue-800">Ver detalles</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="target" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h3 class="text-base font-medium text-gray-900 mb-1">No hay metas definidas</h3>
                        <p class="text-sm text-gray-500 mb-4">Agrega metas para hacer seguimiento al progreso del equipo</p>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                            Crear primera meta
                        </button>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Estadísticas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 slide-in">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $equipo->miembros->where('activo', true)->count() }}</div>
                        <div class="text-xs text-gray-500">Miembros</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">{{ $equipo->metas->filter(function($meta) { return $meta->estado && in_array($meta->estado->nombre, ['pendiente', 'en_proceso']); })->count() }}</div>
                        <div class="text-xs text-gray-500">Metas activas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ $equipo->reuniones->count() }}</div>
                        <div class="text-xs text-gray-500">Reuniones</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-600">{{ $equipo->metas->filter(function($meta) { return $meta->estado && $meta->estado->nombre === 'completado'; })->count() }}</div>
                        <div class="text-xs text-gray-500">Metas completadas</div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Progreso general</span>
                        <span>{{ $equipo->progreso_promedio }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $equipo->progreso_promedio }}%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Miembros -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 slide-in">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Miembros del Equipo</h2>
                    <button onclick="openMiembrosModal({{ $equipo->id }})" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-1"></i>
                        Gestionar
                    </button>
                </div>
                
                <div class="space-y-3">
                    @foreach($equipo->miembros->where('activo', true) as $miembro)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium text-gray-600">{{ $miembro->trabajador->iniciales }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $miembro->trabajador->nombre_completo }}</p>
                                    <p class="text-xs text-gray-500">{{ $miembro->es_coordinador ? 'Coordinador' : 'Miembro' }}</p>
                                </div>
                            </div>
                            @if(!$miembro->es_coordinador)
                                <button class="text-gray-400 hover:text-gray-600">
                                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Acciones -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 slide-in">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones</h2>
                
                <div class="space-y-3">
                    <button class="w-full bg-blue-50 text-blue-600 py-2 px-3 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors flex items-center justify-center">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                        Programar reunión
                    </button>
                    <button class="w-full bg-green-50 text-green-600 py-2 px-3 rounded-lg text-sm font-medium hover:bg-green-100 transition-colors flex items-center justify-center">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                        Generar reporte
                    </button>
                    <button onclick="confirmarEliminar({{ $equipo->id }}, '{{ $equipo->nombre }}')" class="w-full bg-red-50 text-red-600 py-2 px-3 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors flex items-center justify-center">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                        Eliminar equipo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Funciones para gestión de miembros y confirmación de eliminación
function openMiembrosModal(equipoId) {
    // Implementar modal de gestión de miembros
    console.log('Abrir modal de miembros para equipo:', equipoId);
}

function confirmarEliminar(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar el equipo "${nombre}"?`)) {
        // Crear formulario para eliminar
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/coordinador-general/equipos/${id}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Inicializar iconos de Lucide
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
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
</style>
@endsection
