{{-- resources/views/admin/areas/index.blade.php --}}
@extends('layouts.private.admin')

@section('title', 'Áreas')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Áreas</h1>
            <p class="text-gray-600">Gestión de áreas organizacionales de la empresa</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openCreateAreaModal()" 
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nueva Área
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Áreas</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            <p class="text-xs text-gray-500">{{ $stats['activas'] }} activas</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Equipos Totales</h3>
                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['equipos_total'] }}</div>
            <p class="text-xs text-gray-500">Distribuidos en todas las áreas</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Colaboradores</h3>
                <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['colaboradores_total'] }}</div>
            <p class="text-xs text-gray-500">{{ $stats['colaboradores_activos'] }} activos</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Rendimiento Promedio</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['rendimiento_promedio'] }}%</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['rendimiento_promedio'] }}%"></div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-gray-300 p-4">
        <div class="flex flex-col gap-4 md:flex-row md:items-center">
            <div class="flex-1">
                <input type="text" placeholder="Buscar áreas..." 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex gap-2">
                <select class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos los estados</option>
                    <option value="activa">Activa</option>
                    <option value="inactiva">Inactiva</option>
                </select>
                <select class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Ordenar por</option>
                    <option value="nombre">Nombre</option>
                    <option value="colaboradores">Colaboradores</option>
                    <option value="rendimiento">Rendimiento</option>
                    <option value="fecha">Fecha creación</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Areas Grid --}}
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($areas as $area)
        <div class="bg-white rounded-lg border border-gray-300 hover:shadow-lg transition-shadow duration-200">
            {{-- Header del área --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-{{ $area['color'] }}-400 to-{{ $area['color'] }}-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $area['icon'] !!}
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $area['nombre'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $area['codigo'] }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-1">
                        <button onclick="editArea({{ $area['id'] }})" 
                                class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </button>
                        <button onclick="deleteArea({{ $area['id'] }})" 
                                class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600 mb-4">{{ $area['descripcion'] }}</p>
                
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $area['estado'] === 'activa' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($area['estado']) }}
                    </span>
                    <span class="text-xs text-gray-500">Creada {{ $area['fecha_creacion'] }}</span>
                </div>
            </div>

            {{-- Coordinador --}}
            <div class="p-6 border-b border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Coordinador General</h4>
                @if($area['coordinador'])
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-sm font-medium text-blue-600">{{ $area['coordinador']['initials'] }}</span>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
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
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $area['equipos'] }}</div>
                        <div class="text-xs text-gray-500">Equipos</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $area['colaboradores'] }}</div>
                        <div class="text-xs text-gray-500">Colaboradores</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-500">{{ $area['metas_activas'] }}</div>
                        <div class="text-xs text-gray-500">Metas Activas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $area['proyectos'] }}</div>
                        <div class="text-xs text-gray-500">Proyectos</div>
                    </div>
                </div>

                {{-- Rendimiento --}}
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Rendimiento</span>
                        <span class="text-sm font-bold text-gray-900">{{ $area['rendimiento'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-{{ $area['color'] }}-400 to-{{ $area['color'] }}-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $area['rendimiento'] }}%"></div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex space-x-2">
                    <button onclick="viewAreaDetails({{ $area['id'] }})" 
                            class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        Ver Detalles
                    </button>
                    <button onclick="manageArea({{ $area['id'] }})" 
                            class="flex-1 px-3 py-2 text-sm bg-{{ $area['color'] }}-600 text-white rounded-md hover:bg-{{ $area['color'] }}-700 transition-colors">
                        Gestionar
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $areas->links() }}
    </div>
</div>

{{-- Modal Crear/Editar Área --}}
<div id="areaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            {{--  action="{{ route('admin.areas.store') }}"  --}}
            <form id="areaForm" method="POST">
                @csrf
                <div id="methodField"></div>
                
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 id="modalTitle" class="text-lg font-medium">Nueva Área</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Configura la información básica del área organizacional.
                    </p>
                </div>
                
                <div class="px-6 py-4 space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Área</label>
                            <input type="text" name="nombre" id="nombre" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="codigo" class="block text-sm font-medium text-gray-700">Código</label>
                            <input type="text" name="codigo" id="codigo" required 
                                   placeholder="Ej: MKT, VNT, OPS"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Describe las responsabilidades y objetivos del área..."></textarea>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700">Color del Área</label>
                            <select name="color" id="color" required 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccionar color</option>
                                <option value="blue">Azul</option>
                                <option value="green">Verde</option>
                                <option value="blue">Morado</option>
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
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="activa">Activa</option>
                                <option value="inactiva">Inactiva</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="objetivos" class="block text-sm font-medium text-gray-700">Objetivos Principales</label>
                        <textarea name="objetivos" id="objetivos" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Lista los objetivos principales del área..."></textarea>
                    </div>

                    <div>
                        <label for="coordinador_id" class="block text-sm font-medium text-gray-700">Coordinador General (Opcional)</label>
                        <select name="coordinador_id" id="coordinador_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sin coordinador asignado</option>
                            @foreach($coordinadores_disponibles as $coordinador)
                            <option value="{{ $coordinador->id }}">{{ $coordinador->name }} ({{ $coordinador->email }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Puedes asignar un coordinador ahora o hacerlo más tarde.
                        </p>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeAreaModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        <span id="submitText">Crear Área</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Confirmar Eliminación --}}
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-gray-900">Eliminar Área</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                ¿Estás seguro de que deseas eliminar esta área? Esta acción no se puede deshacer y afectará a todos los equipos y colaboradores asignados.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" onclick="confirmDelete()" 
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-gray-300 border-transparent rounded-md hover:bg-red-700">
                    Eliminar Área
                </button>
            </div>
        </div>
    </div>
</div>


{{-- Modal functions
function openCreateAreaModal() {
    document.getElementById('modalTitle').textContent = 'Nueva Área';
    document.getElementById('submitText').textContent = 'Crear Área';
     document.getElementById('areaForm').action = '{{ route("admin.areas.store") }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('areaForm').reset();
    document.getElementById('areaModal').classList.remove('hidden');
} --}}

<script>
let currentAreaId = null;


function editArea(areaId) {
    // Aquí cargarías los datos del área desde el servidor
    fetch(`/admin/areas/${areaId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalTitle').textContent = 'Editar Área';
            document.getElementById('submitText').textContent = 'Actualizar Área';
            document.getElementById('areaForm').action = `/admin/areas/${areaId}`;
            document.getElementById('methodField').innerHTML = '@method("PUT")';
            
            // Llenar el formulario con los datos
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('codigo').value = data.codigo;
            document.getElementById('descripcion').value = data.descripcion;
            document.getElementById('color').value = data.color;
            document.getElementById('estado').value = data.estado;
            document.getElementById('objetivos').value = data.objetivos;
            document.getElementById('coordinador_id').value = data.coordinador_id || '';
            
            document.getElementById('areaModal').classList.remove('hidden');
        });
}

function closeAreaModal() {
    document.getElementById('areaModal').classList.add('hidden');
}

function deleteArea(areaId) {
    currentAreaId = areaId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    currentAreaId = null;
}

function confirmDelete() {
    if (currentAreaId) {
        // Crear formulario para enviar DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/areas/${currentAreaId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
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

function viewAreaDetails(areaId) {
    window.location.href = `/admin/areas/${areaId}`;
}

function manageArea(areaId) {
    window.location.href = `/admin/areas/${areaId}/manage`;
}

function assignCoordinator(areaId) {
    // Redirigir a la página de asignación de coordinador
    window.location.href = `/admin/areas/${areaId}/assign-coordinator`;
}

// Close modals when clicking outside
document.getElementById('areaModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAreaModal();
    }
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Auto-generate codigo from nombre
document.getElementById('nombre').addEventListener('input', function(e) {
    const nombre = e.target.value;
    const codigo = nombre.split(' ').map(word => word.charAt(0).toUpperCase()).join('').substring(0, 3);
    document.getElementById('codigo').value = codigo;
});
</script>
@endsection