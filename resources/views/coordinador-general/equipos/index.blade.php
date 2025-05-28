@extends('layouts.coordinador-general.app')

@section('content')
<!-- Header -->
<div class="bg-white border-b border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Equipos</h1>
            <p class="text-gray-600 mt-1">Administra y supervisa todos los equipos de trabajo</p>
        </div>
        <button onclick="openCreateTeamModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover-scale">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Crear Equipo</span>
        </button>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white border-b border-gray-200 px-6 py-4">
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
                <option value="desarrollo">Desarrollo</option>
                <option value="marketing">Marketing</option>
                <option value="ventas">Ventas</option>
                <option value="soporte">Soporte</option>
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
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Ver detalles</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Editar equipo</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Gestionar miembros</a>
                        <hr class="my-1">
                        <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Eliminar equipo</a>
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
                    <div class="text-2xl font-bold text-green-600">{{ $equipo['proyectos_activos'] }}</div>
                    <div class="text-xs text-gray-500">Proyectos</div>
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
                <button class="flex-1 bg-blue-50 text-blue-600 py-2 px-3 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                    Ver equipo
                </button>
                <button class="flex-1 bg-gray-50 text-gray-600 py-2 px-3 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors">
                    Gestionar
                </button>
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
        
        <form id="createTeamForm" onsubmit="handleCreateTeam(event)">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="teamName" class="block text-sm font-medium text-gray-700 mb-1">Nombre del equipo</label>
                    <input type="text" id="teamName" name="nombre" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Ej: Equipo de Desarrollo Frontend">
                </div>
                
                <div>
                    <label for="teamArea" class="block text-sm font-medium text-gray-700 mb-1">Área</label>
                    <select id="teamArea" name="area" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar área</option>
                        <option value="desarrollo">Desarrollo</option>
                        <option value="marketing">Marketing</option>
                        <option value="ventas">Ventas</option>
                        <option value="soporte">Soporte</option>
                        <option value="recursos-humanos">Recursos Humanos</option>
                        <option value="finanzas">Finanzas</option>
                    </select>
                </div>
                
                <div>
                    <label for="teamCoordinator" class="block text-sm font-medium text-gray-700 mb-1">Coordinador</label>
                    <select id="teamCoordinator" name="coordinador_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar coordinador</option>
                        @foreach($coordinadores as $coordinador)
                        <option value="{{ $coordinador['id'] }}">{{ $coordinador['nombre'] }}</option>
                        @endforeach
                    </select>
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

<script>
// Modal functions
function openCreateTeamModal() {
    document.getElementById('createTeamModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCreateTeamModal() {
    document.getElementById('createTeamModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('createTeamForm').reset();
}

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

// Handle form submission
function handleCreateTeam(event) {
    event.preventDefault();
    
    // Here you would normally send the data to your Laravel backend
    // For now, we'll just show a success message and close the modal
    
    alert('Equipo creado exitosamente!');
    closeCreateTeamModal();
    
    // In a real application, you would reload the page or update the teams list
    // window.location.reload();
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCreateTeamModal();
    }
});

// Initialize Lucide icons when page loads
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endsection