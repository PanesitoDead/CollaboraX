{{-- resources/views/admin/areas/index.blade.php --}}
@extends('layouts.private.admin')

@section('title', 'Áreas')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Áreas</h1>
            <p class="text-gray-600">Gestión de áreas organizacionales de la empresa</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openAreaModal()" 
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors cursor-pointer">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Nuevo Área
            </button>
        </div>
    </div>
    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="relative flex-1 min-w-64">
                <i data-lucide="search" class="absolute left-3 top-3 h-4 w-4 text-gray-400"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Buscar por nombre ..." 
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            <div class="min-w-48 relative">
                <select
                    name="estado"
                    onchange="this.form.submit()"
                    class="w-full pl-3 pr-4 py-2 border border-gray-300 rounded-lg
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        appearance-none"
                >
                    <option value="">Todos los estados</option>
                    <option value="activo"   {{ request('estado') == 'activo'   ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                    <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500"></i>
                </div>
                </div>
            <button type="submit" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors cursor-pointe">
                <i data-lucide="filter" class="h-4 w-4 mr-2"></i>
                Filtrar
            </button>
        </form>
    </div>

    {{-- Áreas Grid --}}
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($areas as $area)
            <article class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                <!-- Área Header -->
                <header class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-lg bg-{{ $area['color'] }}-100 flex items-center justify-center">
                        {!! $area['icon'] !!}
                        </div>
                        <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $area['nombre'] }}</h2>
                        <p class="text-sm text-gray-500">Código: {{ $area['codigo'] }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editArea({{ $area['id'] }})" aria-label="Editar {{ $area['nombre'] }}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <i data-lucide="edit-2" class="w-4 h-4 text-gray-500 hover:text-gray-700"></i>
                        </button>
                        <button onclick="deleteArea({{ $area['id'] }})" aria-label="Eliminar {{ $area['nombre'] }}" class="p-2 rounded-lg hover:bg-red-50 transition-colors">
                        <i data-lucide="trash" class="w-4 h-4 text-gray-500 hover:text-red-600"></i>
                        </button>
                    </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-600">{{ $area['descripcion'] }}</p>
                    <div class="mt-4 flex justify-between items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $area['estado']=='activa'? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($area['estado']) }}
                    </span>
                    <time datetime="{{ $area['fecha_creacion'] }}" class="text-xs text-gray-500">Creada {{ $area['fecha_creacion'] }}</time>
                    </div>
                </header>

                <!-- Coordinador -->
                <section class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Coordinador General</h3>
                    @if($area['coordinador'])
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-{{ $area['color'] }}-100 flex items-center justify-center">
                        <span class="text-sm font-medium text-{{ $area['color'] }}-600">{{ $area['coordinador']['initials'] }}</span>
                        </div>
                        <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $area['coordinador']['nombre'] }}</p>
                        <p class="text-xs text-gray-500">{{ $area['coordinador']['email'] }}</p>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center space-x-3 text-gray-400">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <div>
                        <p class="text-sm">Sin coordinador asignado</p>
                        <button onclick="assignCoordinator({{ $area['id'] }})" class="mt-1 text-xs font-medium text-blue-600 hover:text-blue-800">
                            Asignar coordinador
                        </button>
                        </div>
                    </div>
                    @endif
                </section>

                <!-- Estadísticas y acciones -->
                <footer class="px-6 py-4">
                    <div class="grid grid-cols-3 gap-4 mb-6 text-center">
                    <div>
                        <p class="text-2xl font-bold text-blue-600">{{ $area['equipos'] }}</p>
                        <p class="text-xs text-gray-500">Equipos</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-600">{{ $area['colaboradores'] }}</p>
                        <p class="text-xs text-gray-500">Colaboradores</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-orange-500">{{ $area['metas_activas'] }}</p>
                        <p class="text-xs text-gray-500">Metas Activas</p>
                    </div>
                    </div>

                    <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Rendimiento</span>
                        <span class="text-sm font-bold text-gray-900">{{ $area['rendimiento'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-{{ $area['color'] }}-600 h-2 rounded-full" style="width: {{ $area['rendimiento'] }}%"></div>
                    </div>
                    </div>

                    <div class="flex space-x-3">
                    <button onclick="viewAreaDetails({{ $area['id'] }})" class="flex-1 px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Ver Detalles
                    </button>
                    <button onclick="manageArea({{ $area['id'] }})" class="flex-1 px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Gestionar
                    </button>
                    </div>
                </footer>
                </article>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($areas->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $areas->links() }}
        </div>
    @endif
</div>

{{-- Modal Crear/Editar Área --}}
<div id="areaModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="fixed inset-0 bg-black/50" onclick="closeAreaModal()"></div>
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg flex flex-col overflow-hidden min-h-0">
      <form id="areaForm" method="POST" class="flex flex-col h-full">
        @csrf
        <div id="methodField"></div>

        <!-- Header fijo -->
        <header class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0">
          <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Nueva Área</h3>
          <button type="button" onclick="closeAreaModal()" aria-label="Cerrar modal" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
          </button>
        </header>

        <!-- Contenido scrollable -->
        <div class="px-6 py-4 space-y-4 overflow-y-auto flex-1 max-h-[70vh] min-h-0">
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label for="nombre" class="block mb-1 text-sm font-medium text-gray-700">Nombre del Área</label>
              <input type="text" name="nombre" id="nombre" required
                     class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label for="codigo" class="block mb-1 text-sm font-medium text-gray-700">Código</label>
              <input type="text" name="codigo" id="codigo" required placeholder="Ej: MKT, VNT"
                     class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
          </div>         
          <div>
            <label for="descripcion" class="block mb-1 text-sm font-medium text-gray-700">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3" placeholder="Describe responsabilidades y objetivos..."
                      class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label for="color" class="block mb-1 text-sm font-medium text-gray-700">Color</label>
              <select name="color" id="color" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Seleccionar color</option>
                <option value="blue" selected>Azul</option>
                <option value="green">Verde</option>
                <option value="red">Rojo</option>
                <option value="yellow">Amarillo</option>
                <option value="purple">Morado</option>
                <option value="orange">Naranja</option>
              </select>
            </div>
            <div>
              <label for="estado" class="block mb-1 text-sm font-medium text-gray-700">Estado</label>
              <select name="estado" id="estado" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="activa">Activa</option>
                <option value="inactiva">Inactiva</option>
              </select>
            </div>
          </div>
          <div>
            <label for="coordinador_id" class="block mb-1 text-sm font-medium text-gray-700">Coordinador General (Opcional)</label>
            <select name="coordinador_id" id="coordinador_id" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Sin coordinador asignado</option>
              <!-- ... -->
            </select>
            <p class="mt-1 text-xs text-gray-500">Puedes asignar ahora o más tarde.</p>
          </div>
        </div>

        <!-- Footer fijo -->
        <footer class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-2 flex-shrink-0">
          <button type="button" onclick="closeAreaModal()" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <span id="submitText">Crear Área</span>
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>


{{-- Modal Confirmar Eliminación --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeDeleteModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-start space-x-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Eliminar Área</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        ¿Estás seguro de que deseas eliminar esta área? Esta acción no se puede deshacer.
                    </p>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2">
                <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="confirmDelete()"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Eliminar Área
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentAreaId = null;

    function openAreaModal() {
        document.getElementById('modalTitle').textContent = 'Nueva Área';
        document.getElementById('submitText').textContent = 'Crear Área';
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('areaForm').reset();
        document.getElementById('areaModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAreaModal() {
        document.getElementById('areaModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function editArea(id) {
        fetch(`/admin/areas/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = 'Editar Área';
                document.getElementById('submitText').textContent = 'Actualizar Área';
                document.getElementById('areaForm').action = `/admin/areas/${id}`;
                document.getElementById('methodField').innerHTML = '@method("PUT")';
                // llenar formulario...
                for (let key of ['nombre','codigo','descripcion','color','estado','objetivos','coordinador_id']) {
                    const el = document.getElementById(key);
                    if (el) el.value = data[key] ?? '';
                }
                openAreaModal();
            });
    }

    function deleteArea(id) {
        currentAreaId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentAreaId = null;
    }

    function confirmDelete() {
        if (!currentAreaId) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/areas/${currentAreaId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    }

    function viewAreaDetails(id) {
        window.location.href = `/admin/areas/${id}`;
    }

    function manageArea(id) {
        window.location.href = `/admin/areas/${id}/manage`;
    }

    function assignCoordinator(id) {
        window.location.href = `/admin/areas/${id}/assign-coordinator`;
    }

    // Cerrar modales al hacer click fuera
    document.querySelectorAll('#areaModal, #deleteModal').forEach(modal => {
        modal.addEventListener('click', e => {
            if (e.target === modal) closeAreaModal(), closeDeleteModal();
        });
    });

    // Auto-genera código
    document.getElementById('nombre').addEventListener('input', e => {
        const codigo = e.target.value
            .split(' ')
            .map(w => w.charAt(0).toUpperCase())
            .join('')
            .substring(0, 3);
        document.getElementById('codigo').value = codigo;
    });
</script>
@endpush
