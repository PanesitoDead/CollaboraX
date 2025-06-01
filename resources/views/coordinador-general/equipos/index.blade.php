@extends('layouts.coordinador-general.app')

@section('content')
<!-- Header -->
<div class="bg-white border-b border-gray-200 px-6 py-4 slide-in">
    <div class="flex items-center justify-between">
        <!-- Modificar la línea que muestra el nombre de la empresa -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Equipos</h1>
            <p class="text-gray-600 mt-1">Administra y supervisa todos los equipos de trabajo{{ isset($empresa) ? ' de ' . $empresa->nombre : '' }}</p>
        </div>
        <button onclick="openCreateTeamModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover-scale">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Crear Equipo</span>
        </button>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white border-b border-gray-200 px-6 py-4 slide-in">
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4"></i>
                <input 
                    type="text" 
                    placeholder="Buscar equipos..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    id="searchInput"
                    onkeyup="filterTeams()"
                >
            </div>
        </div>
        <div class="flex gap-2">
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="statusFilter" onchange="filterTeams()">
                <option value="">Todos los estados</option>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
                <option value="pausado">Pausado</option>
            </select>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="areaFilter" onchange="filterTeams()">
                <option value="">Todas las áreas</option>
                @foreach($areas as $area)
                <option value="{{ strtolower($area->nombre) }}">{{ $area->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<!-- Teams Grid -->
<div class="flex-1 overflow-auto p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="teamsGrid">
        @foreach($equipos as $equipo)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 team-card slide-in" 
             data-name="{{ strtolower($equipo['nombre']) }}" 
             data-status="{{ $equipo['estado'] }}" 
             data-area="{{ strtolower($equipo['area']) }}">
            
            <!-- Team Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $equipo['nombre'] }}</h3>
                    <p class="text-sm text-gray-600">{{ $equipo['area'] }}</p>
                </div>
                <div class="relative">
                    <button onclick="toggleDropdown('dropdown-{{ $equipo['id'] }}')" class="p-2 hover:bg-gray-100 rounded-lg">
                        <i data-lucide="more-vertical" class="w-4 h-4 text-gray-500"></i>
                    </button>
                    <div id="dropdown-{{ $equipo['id'] }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                        <a href="{{ route('coordinador-general.equipos.show', $equipo['id']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Ver detalles</a>
                        <hr class="my-1">
                        <a href="#" onclick="confirmarEliminar({{ $equipo['id'] }}, '{{ $equipo['nombre'] }}')" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Eliminar equipo</a>
                    </div>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="mb-4">
                @if($equipo['estado'] === 'activo')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></div>
                        Activo
                    </span>
                @elseif($equipo['estado'] === 'inactivo')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <div class="w-1.5 h-1.5 bg-red-400 rounded-full mr-1.5"></div>
                        Inactivo
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <div class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1.5"></div>
                        Pausado
                    </span>
                @endif
            </div>

            <!-- Team Stats -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $equipo['miembros_count'] }}</div>
                    <div class="text-xs text-gray-500">Miembros</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $equipo['metas_activas'] }}</div>
                    <div class="text-xs text-gray-500">Metas activas</div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Progreso general</span>
                    <span>{{ $equipo['progreso'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $equipo['progreso'] }}%"></div>
                </div>
            </div>

            <!-- Team Members -->
            <div class="mb-4">
                <div class="text-sm text-gray-600 mb-2">Coordinador</div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-blue-600">{{ substr($equipo['coordinador'], 0, 1) }}</span>
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ $equipo['coordinador'] }}</span>
                </div>
            </div>

            <!-- Team Members Avatars -->
            <div class="mb-4">
                <div class="text-sm text-gray-600 mb-2">Miembros del equipo</div>
                <div class="flex -space-x-2">
                    @foreach(array_slice($equipo['miembros'], 0, 4) as $index => $miembro)
                    <div class="w-8 h-8 bg-gray-100 rounded-full border-2 border-white flex items-center justify-center">
                        <span class="text-xs font-medium text-gray-600">{{ substr($miembro, 0, 1) }}</span>
                    </div>
                    @endforeach
                    @if(count($equipo['miembros']) > 4)
                    <div class="w-8 h-8 bg-gray-200 rounded-full border-2 border-white flex items-center justify-center">
                        <span class="text-xs font-medium text-gray-500">+{{ count($equipo['miembros']) - 4 }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                <a href="{{ route('coordinador-general.equipos.show', $equipo['id']) }}" class="flex-1 bg-blue-50 text-blue-600 py-2 px-3 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors text-center">
                    Ver equipo
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden text-center py-12">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="users" class="w-12 h-12 text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron equipos</h3>
        <p class="text-gray-500 mb-6">Intenta ajustar los filtros de búsqueda o crear un nuevo equipo.</p>
        <button onclick="openCreateTeamModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            Crear primer equipo
        </button>
    </div>
</div>

<!-- Create Team Modal -->
<div id="createTeamModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 form-transition">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Crear Nuevo Equipo</h2>
            <button onclick="closeCreateTeamModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="createTeamForm" action="{{ route('coordinador-general.equipos.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="teamName" class="block text-sm font-medium text-gray-700 mb-1">Nombre del equipo</label>
                    <input type="text" id="teamName" name="nombre" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Ej: Equipo de Desarrollo Frontend">
                </div>
                
                <div>
                    <label for="teamArea" class="block text-sm font-medium text-gray-700 mb-1">Área</label>
                    <select id="teamArea" name="area_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar área</option>
                        @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="teamCoordinator" class="block text-sm font-medium text-gray-700 mb-1">Coordinador</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="coordinatorSearch" 
                            placeholder="Buscar colaborador..." 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            autocomplete="off"
                            onkeyup="buscarColaboradores(this.value)"
                            onfocus="mostrarColaboradores()"
                        >
                        <input type="hidden" id="teamCoordinator" name="coordinador_id" required>
                        
                        <!-- Dropdown de resultados -->
                        <div id="coordinatorDropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <div id="coordinatorResults" class="py-1">
                                <!-- Los resultados se cargarán aquí -->
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Busca y selecciona un colaborador para convertirlo en coordinador del equipo</p>
                </div>
                
                <div>
                    <label for="teamDescription" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="teamDescription" name="descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Describe el propósito y objetivos del equipo..."></textarea>
                </div>
            </div>
            
            <div class="flex space-x-3 mt-6">
                <button type="button" onclick="closeCreateTeamModal()" class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Crear Equipo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmDeleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 form-transition">
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Confirmar eliminación</h2>
            <p class="text-gray-600" id="confirmDeleteText">¿Estás seguro de que deseas eliminar este equipo?</p>
        </div>
        
        <form id="deleteTeamForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex space-x-3">
                <button type="button" onclick="closeConfirmDeleteModal()" class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Eliminar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-300 z-50">
    <div class="flex items-center">
        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
        <span id="toast-message">Operación completada exitosamente</span>
    </div>
</div>

<script>
// Variables globales
let colaboradoresData = @json($colaboradores);
let searchTimeout;

// Modal functions
function openCreateTeamModal() {
    document.getElementById('createTeamModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    // Cargar colaboradores iniciales
    mostrarColaboradores();
}

function closeCreateTeamModal() {
    document.getElementById('createTeamModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('createTeamForm').reset();
    document.getElementById('coordinatorSearch').value = '';
    document.getElementById('teamCoordinator').value = '';
    document.getElementById('coordinatorDropdown').classList.add('hidden');
}

// Función para buscar colaboradores
function buscarColaboradores(termino) {
    clearTimeout(searchTimeout);
    
    if (termino.length < 1) {
        mostrarColaboradores();
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch(`/coordinador-general/equipos/buscar-colaboradores?q=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(data => {
                mostrarResultadosColaboradores(data);
            })
            .catch(error => {
                console.error('Error al buscar colaboradores:', error);
                showToast('Error al buscar colaboradores', 'error');
            });
    }, 300);
}

// Función para mostrar todos los colaboradores
function mostrarColaboradores() {
    mostrarResultadosColaboradores(colaboradoresData);
}

// Función para mostrar resultados de colaboradores
function mostrarResultadosColaboradores(colaboradores) {
    const resultsContainer = document.getElementById('coordinatorResults');
    const dropdown = document.getElementById('coordinatorDropdown');
    
    if (colaboradores.length === 0) {
        resultsContainer.innerHTML = '<div class="px-4 py-2 text-gray-500 text-sm">No se encontraron colaboradores</div>';
    } else {
        resultsContainer.innerHTML = colaboradores.map(colaborador => `
            <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                 onclick="seleccionarColaborador(${colaborador.id}, '${colaborador.nombre}', '${colaborador.email}')">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-xs font-medium text-blue-600">${colaborador.nombre.charAt(0)}</span>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">${colaborador.nombre}</div>
                        <div class="text-xs text-gray-500">${colaborador.email}</div>
                        <div class="text-xs text-blue-600">${colaborador.rol}</div>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    dropdown.classList.remove('hidden');
}

// Función para seleccionar un colaborador
function seleccionarColaborador(id, nombre, email) {
    document.getElementById('coordinatorSearch').value = nombre;
    document.getElementById('teamCoordinator').value = id;
    document.getElementById('coordinatorDropdown').classList.add('hidden');
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('coordinatorDropdown');
    const searchInput = document.getElementById('coordinatorSearch');
    
    if (!dropdown.contains(event.target) && event.target !== searchInput) {
        dropdown.classList.add('hidden');
    }
});

// Dropdown functions
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.add('hidden');
        }
    });
    
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleDropdown"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

// Filter teams function
function filterTeams() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const areaFilter = document.getElementById('areaFilter').value.toLowerCase();
    const teamCards = document.querySelectorAll('.team-card');
    let visibleCount = 0;

    teamCards.forEach(card => {
        const name = card.dataset.name;
        const status = card.dataset.status;
        const area = card.dataset.area;

        const matchesSearch = name.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        const matchesArea = !areaFilter || area === areaFilter;

        if (matchesSearch && matchesStatus && matchesArea) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Show/hide empty state
    const emptyState = document.getElementById('emptyState');
    if (visibleCount === 0) {
        emptyState.classList.remove('hidden');
    } else {
        emptyState.classList.add('hidden');
    }
}

// Confirmation modal functions
function confirmarEliminar(id, nombre) {
    // Configurar el formulario de eliminación
    const form = document.getElementById('deleteTeamForm');
    form.action = `/coordinador-general/equipos/${id}`;
    
    // Configurar el texto del modal
    document.getElementById('confirmDeleteText').textContent = `¿Estás seguro de que deseas eliminar el equipo "${nombre}"? Esta acción no se puede deshacer.`;
    
    // Mostrar el modal
    document.getElementById('confirmDeleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeConfirmDeleteModal() {
    document.getElementById('confirmDeleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    
    toastMessage.textContent = message;
    
    // Set color based on type
    if (type === 'error') {
        toast.classList.remove('bg-green-500');
        toast.classList.add('bg-red-500');
    } else {
        toast.classList.remove('bg-red-500');
        toast.classList.add('bg-green-500');
    }
    
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
        closeCreateTeamModal();
        closeConfirmDeleteModal();
    }
});

// Show success message if exists in session
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    @if(session('success'))
        showToast("{{ session('success') }}");
    @endif
    
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
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

.team-card {
    transition: all 0.3s ease;
}

.team-card:hover {
    transform: translateY(-2px);
}

/* Estilos para el dropdown de búsqueda */
#coordinatorDropdown {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

#coordinatorDropdown .cursor-pointer:hover {
    background-color: #f8fafc;
}
</style>
@endsection
