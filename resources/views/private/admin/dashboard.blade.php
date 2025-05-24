{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.private.admin')

@section('title', 'Panel de Administrador')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Panel de Administrador</h1>
            <p class="text-gray-600">Gestión de la empresa TechSolutions S.A.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openCoordinadorModal()" 
                    class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo Coordinador General
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Áreas</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            {{-- {{ $stats['areas'] }} --}}
            <div class="text-2xl font-bold">Areas</div>
            <p class="text-xs text-gray-500">Marketing, Ventas, Operaciones, Finanzas, TI</p>
        </div>

        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Usuarios Totales</h3>
                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            {{-- {{ $stats['usuarios_totales'] }} --}}
            <div class="text-2xl font-bold">Usuarios Totales</div>
            {{-- {{ $stats['usuarios_nuevos'] }} --}}
            <p class="text-xs text-gray-500">+Usuarios nuevos desde la semana pasada</p>
        </div>

        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Metas Activas</h3>
                <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            {{-- {{ $stats['metas_activas'] }} --}}
            <div class="text-2xl font-bold">Metas Activas</div>
            {{-- {{ $stats['metas_progreso'] }} --}}
            {{-- {{ $stats['metas_pendientes'] }} --}}
            <p class="text-xs text-gray-500">+Metas pendientes desde la semana pasada</p>
        </div>

        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Cumplimiento Global</h3>
                <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            {{-- {{ $stats['cumplimiento'] }} --}}
            <div class="text-2xl font-bold">20%</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ 20 }}%"></div>
            </div>
        </div>
    </div>

    {{-- Tabs Content --}}
    <div class="bg-white rounded-lg border">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('coordinadores')" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="coordinadores">
                    Coordinadores Generales
                </button>
                <button onclick="showTab('areas')" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="areas">
                    Áreas
                </button>
                <button onclick="showTab('rendimiento')" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="rendimiento">
                    Rendimiento
                </button>
            </nav>
        </div>

        {{-- Tab Content: Coordinadores --}}
        <div id="tab-coordinadores" class="tab-content p-6">
            <div class="mb-4">
                <h3 class="text-lg font-medium">Coordinadores Generales</h3>
                <p class="text-gray-600">Responsables de las áreas de la empresa</p>
            </div>
            <div class="space-y-4">
                @foreach($coordinadores=[] as $coordinador)
                <div class="flex items-center justify-between rounded-lg border p-4">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-sm font-medium">{{ $coordinador['initials'] }}</span>
                        </div>
                        <div>
                            <p class="font-medium">{{ $coordinador['name'] }}</p>
                            <p class="text-sm text-gray-500">{{ $coordinador['email'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $coordinador['area'] }}
                        </span>
                        <div class="text-sm text-gray-500">{{ $coordinador['last_active'] }}</div>
                        <div class="flex gap-2">
                            <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                Ver Perfil
                            </button>
                            <button class="px-3 py-1 text-sm border border-red-300 text-red-600 rounded-md hover:bg-red-50">
                                Desactivar
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tab Content: Áreas --}}
        <div id="tab-areas" class="tab-content p-6 hidden">
            <div class="mb-4">
                <h3 class="text-lg font-medium">Áreas de la Empresa</h3>
                <p class="text-gray-600">Estructura organizativa de TechSolutions S.A.</p>
            </div>
            <div class="space-y-4">
                @foreach($areas=[] as $area)
                <div class="flex items-center justify-between rounded-lg border p-4">
                    <div>
                        <p class="font-medium">{{ $area['name'] }}</p>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span>Coordinador: {{ $area['coordinator'] }}</span>
                            <span>•</span>
                            <span>{{ $area['groups'] }} grupos</span>
                            <span>•</span>
                            <span>{{ $area['users'] }} usuarios</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col items-end">
                            <span class="text-sm">Cumplimiento</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $area['progress'] }}%"></div>
                                </div>
                                <span class="text-xs">{{ $area['progress'] }}%</span>
                            </div>
                        </div>
                        <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                            Gestionar
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tab Content: Rendimiento --}}
        <div id="tab-rendimiento" class="tab-content p-6 hidden">
            <div class="mb-4">
                <h3 class="text-lg font-medium">Rendimiento por Área</h3>
                <p class="text-gray-600">Métricas de cumplimiento y productividad</p>
            </div>
            <div class="h-96 flex items-center justify-center bg-gray-50 rounded-lg">
                <p class="text-gray-500">Gráfico de rendimiento por área</p>
            </div>
        </div>
    </div>
</div>

{{-- Modal para Nuevo Coordinador General --}}
<div id="coordinadorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            {{-- action="{{ route('private.admin.coordinadores-generales.store') }}" --}}
            <form  method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium">Asignar Coordinador General</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Selecciona un usuario para asignarle el rol de Coordinador General y el área que gestionará.
                    </p>
                </div>
                
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="usuario_id" class="block text-sm font-medium text-gray-700">Usuario</label>
                        <select name="usuario_id" id="usuario_id" required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Seleccionar usuario</option>
                            @foreach($usuarios_disponibles=[] as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }} ({{ $usuario->email }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Solo se muestran usuarios con rol de Colaborador y estado Activo.
                        </p>
                    </div>

                    <div>
                        <label for="area_id" class="block text-sm font-medium text-gray-700">Área</label>
                        <select name="area_id" id="area_id" required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Seleccionar área</option>
                            @foreach($areas_disponibles=[] as $area)
                            <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="enviar_notificacion" id="enviar_notificacion" checked 
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="enviar_notificacion" class="ml-2 block text-sm text-gray-700">
                            Enviar notificación al usuario
                        </label>
                    </div>
                    <p class="text-xs text-gray-500">
                        Se enviará un correo electrónico al usuario informándole de su nuevo rol y responsabilidades.
                    </p>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeCoordinadorModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700">
                        Asignar Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active state from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-purple-500', 'text-purple-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('tab-' + tabName).classList.remove('hidden');
    
    // Add active state to selected button
    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    activeButton.classList.remove('border-transparent', 'text-gray-500');
    activeButton.classList.add('border-purple-500', 'text-purple-600');
}

// Modal functionality
function openCoordinadorModal() {
    document.getElementById('coordinadorModal').classList.remove('hidden');
}

function closeCoordinadorModal() {
    document.getElementById('coordinadorModal').classList.add('hidden');
}

// Initialize first tab as active
document.addEventListener('DOMContentLoaded', function() {
    showTab('coordinadores');
});

// Close modal when clicking outside
document.getElementById('coordinadorModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCoordinadorModal();
    }
});
</script>
@endsection