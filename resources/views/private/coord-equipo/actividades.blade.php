@extends('layouts.private.coord-equipo')

@section('content')
<div class="flex-1 overflow-auto">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 slide-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Actividades</h1>
                <p class="text-gray-600 mt-1">Gestiona y supervisa las actividades del equipo</p>
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
    <div class="flex-1 p-6 min-h-screen flex flex-col">
        <div class="text-sm text-gray-600 mb-4">
            Mostrando <span id="activityCount">0</span> actividades de todos los equipos
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 flex-1">
            @foreach ($estados as $estado)
                @php
                    // Mapeo de colores según el estado (puedes mover esto a un helper o método del modelo Estado)
                    $colors = [
                        'incompleta' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                        'en-proceso' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                        'completo' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                        'suspendida' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                    ];

                    $slug = \Illuminate\Support\Str::slug($estado->nombre); // genera ids como 'en-proceso'
                    $color = $colors[$slug] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
                @endphp

                <div class="bg-white rounded-lg shadow-lg hover:shadow-xl form-transition flex flex-col">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">{{ $estado->nombre }}</h3>
                            <span class="{{ $color['bg'] }} {{ $color['text'] }} text-xs font-medium px-2.5 py-0.5 rounded-full" id="{{ $slug }}-count">0</span>
                        </div>
                    </div>
                    <div class="p-4 space-y-3 overflow-y-auto flex-1 kanban-column" id="{{ $slug }}-column" data-estado="{{ $slug }}">
                        <!-- Actividades se cargan con JavaScript -->
                    </div>
                </div>
            @endforeach
        </div>
    </div>


    <!-- Kanban Board -->
    {{-- <div class="flex-1 p-6 min-h-screen flex flex-col">
        <div class="text-sm text-gray-600 mb-4">
            Mostrando <span id="activityCount">0</span> actividades de todos los equipos
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 flex-1">
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl form-transition flex flex-col">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Pendientes</h3>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full" id="pendientes-count">0</span>
                    </div>
                </div>
                <div class="p-4 space-y-3 overflow-y-auto flex-1 kanban-column" id="pendientes-column" data-estado="pendiente">
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl form-transition flex flex-col">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">En Proceso</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full" id="en-proceso-count">0</span>
                    </div>
                </div>
                <div class="p-4 space-y-3 overflow-y-auto flex-1 kanban-column" id="en-proceso-column" data-estado="en-proceso">
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl form-transition flex flex-col">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Completadas</h3>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full" id="completadas-count">0</span>
                    </div>
                </div>
                <div class="p-4 space-y-3 overflow-y-auto flex-1 kanban-column" id="completadas-column" data-estado="completada">
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl form-transition flex flex-col">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Retrasadas</h3>
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full" id="retrasadas-count">0</span>
                    </div>
                </div>
                <div class="p-4 space-y-3 overflow-y-auto flex-1 kanban-column" id="retrasadas-column" data-estado="retrasada">
                </div>
            </div>
        </div>
    </div> --}}


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
<div id="custom-toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-300 z-50">
    <div class="flex items-center">
        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
        <span id="custom-toast-message">Actividad creada correctamente</span>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    // Arrays globales que ahora vendrán del servidor
    let actividades = [];
    let estadosDisponibles = [];
    let metasDisponibles = [];
    let filteredActividades = [];

    let nextId = 0;            // en caso de crear nuevas actividades desde cliente
    let sortableInstances = {};

    // ====================================================
    // CARGA INICIAL Y FETCH A LA API
    // ====================================================
    document.addEventListener('DOMContentLoaded', function() {
        // Primero, jalar estados y metas (para llenar selects y/o preparar columnas)
        Promise.all([
            fetch('/api/estados'),
            fetch('/api/metas'),
            fetch('/api/actividades')
        ])
        .then(async ([estadosRes, metasRes, actividadesRes]) => {
            if (!estadosRes.ok || !metasRes.ok || !actividadesRes.ok) {
                throw new Error('Error al obtener datos del servidor');
            }

            estadosDisponibles = await estadosRes.json();   // ej: [{ id:1, nombre:'Pendiente', slug:'pendiente' }, …]
            metasDisponibles   = await metasRes.json();      // ej: [{ id:1, titulo:'Lanzamiento MVP' }, …]
            actividades       = await actividadesRes.json(); // ej: [{ id:5, titulo:'…', descripcion:'…', prioridad:'Alta', fecha_limite:'2025-06-01', asignado_a:'Juan', estado_slug:'pendiente', meta: {id:2, titulo:'…'} }, …]

            // Inicializamos el filtrado completo
            filteredActividades = [...actividades];
            nextId = actividades.length > 0
                ? Math.max(...actividades.map(a => a.id)) + 1
                : 1;

            // Renderizamos el select de metas para filtrar
            renderMetasInSelects();

            // Renderizamos todas las actividades en columnas
            loadActivities();

            // Inicializar drag & drop
            initSortable();
        })
        .catch(err => {
            console.error('No se pudo cargar todo desde servidor, revisa la consola:', err);
        });
    });

    // ====================================================
    // RENDERIZAR SELECT DE METAS
    // ====================================================
    function renderMetasInSelects() {
        const metaFilter = document.getElementById('metaFilter'); // asume que en tu HTML tienes <select id="metaFilter">
        
        if (!metaFilter) return;

        // Opcional: puedes agregar 'Todas las metas' o vacío
        let html = '<option value="">Todas las metas</option>';

        metasDisponibles.forEach(meta => {
            html += `<option value="${meta.id}">${meta.titulo}</option>`;
        });

        metaFilter.innerHTML = html;
    }

    // ====================================================
    // CARGAR ACTIVIDADES EN CADA COLUMNA
    // ====================================================
    function loadActivities() {
        // Obtenemos dinámicamente todas las columnas según los estados disponibles
        // Cada columna ya debería existir en el HTML generada por Blade con data-estado="{{ $slug }}"
        const columns = {};
        estadosDisponibles.forEach(est => {
            const slug = est.slug; // ej: 'pendiente','en-proceso','completada','retrasada',…
            const colEl = document.querySelector(`.kanban-column[data-estado="${slug}"]`);
            if (colEl) {
                columns[slug] = colEl;
                colEl.innerHTML = ''; // limpiamos antes de re-llenar
            }
        });

        // Contadores de forma dinámica
        const counts = {};
        estadosDisponibles.forEach(est => {
            counts[est.slug] = 0;
        });

        // Iteramos sobre las actividades filtradas y las insertamos en su columna
        filteredActividades.forEach(act => {
            const estadoSlug = act.estado_slug; // suponemos que tu API retorna `estado_slug`
            const column = columns[estadoSlug];
            if (column) {
                const card = createActivityCard(act);
                column.appendChild(card);
                counts[estadoSlug]++;
            }
        });

        // Actualizar los badges de cada estado (asumiendo que en Blade el span tiene id="{slug}-count")
        estadosDisponibles.forEach(est => {
            const slug = est.slug;
            const badge = document.getElementById(`${slug}-count`);
            if (badge) badge.textContent = counts[slug] || 0;
        });

        // Total de actividades mostradas
        const totalEl = document.getElementById('activityCount');
        if (totalEl) totalEl.textContent = filteredActividades.length;
    }

    // ====================================================
    // CREAR LA CARTA DE CADA ACTIVIDAD
    // ====================================================
    function createActivityCard(actividad) {
        const card = document.createElement('div');
        card.className = 'bg-white border border-gray-200 rounded-lg p-3 shadow-md hover:shadow-lg cursor-move form-transition hover-scale activity-card';
        card.setAttribute('data-id', actividad.id);

        // Si tienen datos adicionales (por ejemplo: meta), puedes mostrarlos también si gustas
        // Mapeo de colores según prioridad
        const priorityColors = {
            'Alta': 'bg-red-100 text-red-800',
            'Media': 'bg-yellow-100 text-yellow-800',
            'Baja': 'bg-green-100 text-green-800'
        };

        // Construimos el innerHTML con los campos que sí trae tu API:
        // - actividad.titulo
        // - actividad.descripcion
        // - actividad.prioridad
        // - actividad.fecha_limite (o fechaLimite si lo transformas en el controlador)
        // - actividad.asignado_a
        // - (opcional) actividad.meta.titulo
        card.innerHTML = `
            <div class="flex items-start justify-between mb-2">
                <h4 class="font-medium text-gray-900 text-sm">${actividad.titulo}</h4>
                <span class="text-xs px-2 py-1 rounded-full ${priorityColors[actividad.prioridad] || 'bg-gray-100 text-gray-800'}">${actividad.prioridad}</span>
            </div>
            <p class="text-gray-600 text-xs mb-2">${actividad.descripcion}</p>
            <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                <span>${actividad.fecha_limite}</span>
                ${actividad.meta ? `<span class="italic text-xs text-gray-400">Meta: ${actividad.meta.titulo}</span>` : ''}
            </div>
            <div class="mt-2 text-xs text-gray-600 flex items-center">
                <i data-lucide="user" class="w-3 h-3 inline mr-1"></i>
                ${actividad.asignado_a}
            </div>
        `;

        // Cuando clickean la tarjeta, abrimos el modal de detalles
        card.addEventListener('click', function(e) {
            if (!card.classList.contains('sortable-drag')) {
                showActivityDetails(actividad);
            }
        });

        return card;
    }

    // ====================================================
    // INICIALIZAR SORTABLE.JS PARA ARRASTRE
    // ====================================================
    function initSortable() {
        // Destruir instancias anteriores
        Object.values(sortableInstances).forEach(instance => {
            if (instance && typeof instance.destroy === 'function') {
                instance.destroy();
            }
        });
        sortableInstances = {};

        // Recolectamos todas las columnas que Blade generó (tienen la clase .kanban-column)
        const columns = document.querySelectorAll('.kanban-column');

        columns.forEach(column => {
            const estado = column.getAttribute('data-estado');
            sortableInstances[estado] = new Sortable(column, {
                group: 'actividades',
                animation: 150,
                ghostClass: 'bg-gray-100',
                chosenClass: 'bg-gray-200',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    const actividadId = parseInt(evt.item.getAttribute('data-id'));
                    const nuevoEstado = evt.to.getAttribute('data-estado');
                    updateActivityStatus(actividadId, nuevoEstado);
                }
            });
        });
    }

    // ====================================================
    // ACTUALIZAR ESTADO DE LA ACTIVIDAD (CLIENTE → SERVIDOR)
    // ====================================================
    function updateActivityStatus(actividadId, nuevoEstado) {
        const actividad = actividades.find(a => a.id === actividadId);
        if (!actividad) return;

        actividad.estado_slug = nuevoEstado;

        // Actualizar en el array de filtradas
        const idx = filteredActividades.findIndex(a => a.id === actividadId);
        if (idx > -1) filteredActividades[idx].estado_slug = nuevoEstado;

        // Actualizar contadores en pantalla (sin recargar todo)
        updateStatusCounts();

        // Mostrar notificación
        const estadosLabels = {};
        estadosDisponibles.forEach(e => {
            estadosLabels[e.slug] = e.nombre;
        });
        showToast(`Actividad "${actividad.titulo}" movida a ${estadosLabels[nuevoEstado]}`);

        // Llamada al endpoint para persistir (opcional, depende de tu API)
        fetch(`/api/actividades/${actividadId}/cambiar-estado`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ estado_slug: nuevoEstado })
        })
        .then(res => {
            if (!res.ok) console.error('No se pudo actualizar el estado en el servidor');
        })
        .catch(err => console.error('Error en fetch al actualizar estado:', err));
    }

    function updateStatusCounts() {
        const counts = {};
        estadosDisponibles.forEach(e => counts[e.slug] = 0);
        filteredActividades.forEach(a => {
            if (counts[a.estado_slug] !== undefined) {
                counts[a.estado_slug]++;
            }
        });
        estadosDisponibles.forEach(e => {
            const badge = document.getElementById(`${e.slug}-count`);
            if (badge) badge.textContent = counts[e.slug];
        });
        const totalEl = document.getElementById('activityCount');
        if (totalEl) totalEl.textContent = filteredActividades.length;
    }

    // ====================================================
    // FILTRADO POR BÚSQUEDA Y META
    // ====================================================
    function filterActivities() {
        const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
        const metaFilterValue = document.getElementById('metaFilter')?.value;

        filteredActividades = actividades.filter(act => {
            const matchesSearch = act.titulo.toLowerCase().includes(searchTerm) ||
                                  act.descripcion.toLowerCase().includes(searchTerm);

            const matchesMeta = !metaFilterValue || (act.meta && act.meta.id.toString() === metaFilterValue);

            return matchesSearch && matchesMeta;
        });

        loadActivities();
        initSortable();
    }

    // Si usas input de búsqueda y select de meta, agrégalos con event listeners:
    document.addEventListener('input', function(e) {
        if (e.target && e.target.id === 'searchInput') {
            filterActivities();
        }
    });
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'metaFilter') {
            filterActivities();
        }
    });

    // ====================================================
    // MOSTRAR DETALLES EN UN MODAL
    // ====================================================
    function showActivityDetails(actividad) {
        const modal = document.getElementById('detailsModal');
        const title = document.getElementById('activityTitle');
        const details = document.getElementById('activityDetails');

        // Mapeo dinámico de colores según estado y prioridad
        const priorityColors = {
            'Alta': 'bg-red-100 text-red-800',
            'Media': 'bg-yellow-100 text-yellow-800',
            'Baja': 'bg-green-100 text-green-800'
        };
        const statusColors = {};
        const statusLabels = {};
        estadosDisponibles.forEach(e => {
            // Ej: asumiendo que en tu modelo Estado definiste clases CSS o las mapeas aquí
            const mapeo = {
                'pendiente': ['bg-yellow-100','text-yellow-800'],
                'en-proceso': ['bg-blue-100','text-blue-800'],
                'completada': ['bg-green-100','text-green-800'],
                'retrasada': ['bg-red-100','text-red-800']
            };
            if (mapeo[e.slug]) {
                statusColors[e.slug] = mapeo[e.slug].join(' ');
            } else {
                statusColors[e.slug] = 'bg-gray-100 text-gray-800';
            }
            statusLabels[e.slug] = e.nombre;
        });

        title.textContent = actividad.titulo;
        details.innerHTML = `
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                    <p class="text-gray-900">${actividad.descripcion}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Meta</label>
                        <p class="text-gray-900">${actividad.meta ? actividad.meta.titulo : '—'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prioridad</label>
                        <span class="inline-block px-2 py-1 text-xs rounded-full ${priorityColors[actividad.prioridad] || 'bg-gray-100 text-gray-800'}">${actividad.prioridad}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <span class="inline-block px-2 py-1 text-xs rounded-full ${statusColors[actividad.estado_slug]}">${statusLabels[actividad.estado_slug]}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha límite</label>
                        <p class="text-gray-900">${actividad.fecha_limite}</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Asignado a</label>
                    <p class="text-gray-900">${actividad.asignado_a}</p>
                </div>
            </div>
        `;

        // Mostrar modal con animación
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

    // ====================================================
    // CREAR NUEVA ACTIVIDAD DESDE EL CLIENTE (OPCIONAL)
    // ====================================================
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

        // Asumimos que tu formulario en el HTML ahora incluye:
        // nombre="titulo", "descripcion", "prioridad", "fecha_limite", "asignado_a", "meta_id"
        const nuevaActividad = {
            id: nextId++,
            titulo: formData.get('titulo'),
            descripcion: formData.get('descripcion'),
            prioridad: formData.get('prioridad'),
            fecha_limite: formData.get('fecha_limite'),
            asignado_a: formData.get('asignado_a'),
            estado_slug: 'pendiente',
            meta: metasDisponibles.find(m => m.id.toString() === formData.get('meta_id')) || null
        };

        // La agregamos localmente y recargamos
        actividades.push(nuevaActividad);
        filteredActividades = [...actividades];
        loadActivities();
        initSortable();
        closeCreateModal();
        showToast('Actividad creada correctamente');

        // Opcional: también puedes mandar un POST a tu API para guardarla permanentemente
        // fetch('/api/actividades', { method:'POST', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}' },
        // body: JSON.stringify(nuevaActividad) })
        //   .then(res => res.json()).then(data => { /* actualizar id real si viene del servidor */ });
        
        form.reset();
    }

    // ====================================================
    // TOAST (NOTIFICACIÓN)
    // ====================================================
    function showToast(message) {
        const toast = document.getElementById('custom-toast');
        const toastMessage = document.getElementById('custom-toast-message');
        
        toastMessage.textContent = message;
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
        
        setTimeout(() => {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
        }, 3000);
    }
</script>


<style>
.sortable-ghost {
    opacity: 0.5;
    background-color: #f3f4f6 !important;
    border: 2px dashed #d1d5db !important;
}

.sortable-drag {
    opacity: 0.9;
    transform: rotate(2deg);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
}

.kanban-column {
    min-height: 50px;
}

.kanban-column.sortable-drag > * {
    margin-bottom: 0 !important;
}

.sortable-chosen {
    background-color: #f9fafb;
}

.activity-card {
    cursor: grab;
}

.activity-card:active {
    cursor: grabbing;
}
</style>
@endpush