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
                <select id="metaFilter" onchange="filterActivities()" 
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                    <option value="">Todas las metas</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex-1 p-6 min-h-screen flex flex-col">
        <div class="text-sm text-gray-600 mb-4">
            Mostrando <span id="activityCount">0</span> actividades
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 flex-1">
            @foreach ($estados as $estado)
                @php
                    $colors = [
                        'incompleta' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                        'en-proceso' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                        'completo' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                        'suspendida' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                    ];

                    $slug = \Illuminate\Support\Str::slug($estado->nombre);
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

</div>

<!-- Create Activity Modal -->
<div id="createModal" class="fixed inset-0 hidden z-50" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/50"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[70vh] overflow-y-auto" id="createModalContent">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Crear Nueva Actividad</h3>
                    <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 tab-transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form id="createActivityForm" class="space-y-4">
                    <div class="border-b border-gray-200">
                        <p class="text-sm text-gray-500 mt-1">Ingrese los datos de la nueva actividad.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="actividad_titulo" class="block text-sm font-medium text-gray-700">Título</label>
                            <input type="text" name="nombre" id="actividad_titulo" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="actividad_descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea name="descripcion" id="actividad_descripcion" rows="3" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div>
                            <label for="meta_id" class="block text-sm font-medium text-gray-700">Meta asociada</label>
                            <select name="meta_id" id="meta_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione una meta</option>
                                @foreach ($metas as $meta)
                                    <option value="{{ $meta->id }}">{{ $meta->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="fecha_entrega" class="block text-sm font-medium text-gray-700">Fecha y hora de entrega</label>
                            <input type="datetime-local" name="fecha_entrega" id="fecha_entrega" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
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
<div id="detailsModal" class="fixed inset-0 hidden z-50" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/50"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[70vh] overflow-y-auto" id="detailsModalContent">
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
            fetch('/coord-equipo/api/estados'),
            fetch('/coord-equipo/api/metas/equipo'),
            fetch('/coord-equipo/api/actividades/equipo')
        ])
        .then(async ([estadosRes, metasRes, actividadesRes]) => {
            if (!estadosRes.ok || !metasRes.ok || !actividadesRes.ok) {
                throw new Error('Error al obtener datos del servidor');
            }

            estadosDisponibles = await estadosRes.json();   
            metasDisponibles   = await metasRes.json();     
            actividades       = await actividadesRes.json();

            console.log('Estados:', estadosDisponibles);
            console.log('Metas:', metasDisponibles);
            console.log('Actividades:', actividades);


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
        console.log("ANTES DE ENTRAR A SELECT");
        if (!metaFilter) return;

        console.log("DENTRO DE SELECT");
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
        // - (opcional) actividad.meta.titulo
        card.innerHTML = `
            <div class="bg-white shadow rounded-lg p-3 border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-start justify-between mb-2">
                    <h4 class="font-semibold text-gray-800 text-sm leading-snug">${actividad.titulo}</h4>
                    <span class="text-[10px] px-2 py-[2px] rounded-full font-medium bg-blue-100 text-blue-700 capitalize">
                        Tarea
                    </span>
                </div>

                <p class="text-gray-600 text-xs mb-3 leading-tight">${actividad.descripcion}</p>

                <div class="flex items-center justify-between text-xs text-gray-500">
                    <div class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>${actividad.fecha_limite}</span>
                    </div>

                    ${
                        actividad.meta
                        ? `<span class="italic text-[11px] text-gray-400">🎯 ${actividad.meta.titulo}</span>`
                        : ''
                    }
                </div>
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
    // function updateActivityStatus(actividadId, nuevoEstado) {
    //     const actividad = actividades.find(a => a.id === actividadId);
    //     if (!actividad) return;

    //     actividad.estado_slug = nuevoEstado;

    //     // Actualizar en el array de filtradas
    //     const idx = filteredActividades.findIndex(a => a.id === actividadId);
    //     if (idx > -1) filteredActividades[idx].estado_slug = nuevoEstado;

    //     // Actualizar contadores en pantalla (sin recargar todo)
    //     updateStatusCounts();

    //     // Mostrar notificación
    //     const estadosLabels = {};
    //     estadosDisponibles.forEach(e => {
    //         estadosLabels[e.slug] = e.nombre;
    //     });
    //     showToast(`Actividad "${actividad.titulo}" movida a ${estadosLabels[nuevoEstado]}`);


    //     console.log("ANTES DEL LLAMAR AL FETCH");

    //     // Llamada al endpoint para persistir (opcional, depende de tu API)
    //     fetch(`/coord-equipo/api/actividades/${actividadId}/cambiar-estado`, {
    //         method: 'POST',
    //         headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    //         body: JSON.stringify({ estado_slug: nuevoEstado })
    //     })
    //     .then(res => {
    //         if (!res.ok) console.error('No se pudo actualizar el estado en el servidor');
    //     })
    //     .catch(err => console.error('Error en fetch al actualizar estado:', err));

    //     console.log("DESPUES DE LLAMAR AL FETCH");
    // }

    function updateActivityStatus(actividadId, nuevoEstadoSlug) {
        const actividad = actividades.find(a => a.id === actividadId);

        if (!actividad) return;

        // Buscar el estado correspondiente por slug
        const estado = estadosDisponibles.find(e => e.slug === nuevoEstadoSlug);
        if (!estado) return;

        const estadoId = estado.id;

        // Actualizar en memoria
        actividad.estado_slug = nuevoEstadoSlug;

        const idx = filteredActividades.findIndex(a => a.id === actividadId);
        if (idx > -1) filteredActividades[idx].estado_slug = nuevoEstadoSlug;

        // Actualizar contadores
        updateStatusCounts();

        // Mostrar notificación
        showToast(`Actividad "${actividad.titulo}" movida a "${estado.nombre}"`);

        console.log("ACTIVIDAD ID " + actividadId);
        console.log("ESTADO ID " + estadoId);

        // Llamada al backend para persistir el cambio
        fetch(`/coord-equipo/api/actividades/${actividadId}/cambiar-estado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ estado_id: estadoId })
        })
        .then(res => {
            if (!res.ok) {
                console.error('No se pudo actualizar el estado en el servidor');
            }
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
                        <label class="block text-sm font-medium text-gray-700">Tipo</label>
                        <span class="inline-block px-2 py-1 text-xs rounded-full font-medium bg-blue-100 text-blue-700 capitalize">
                            Tarea
                        </span>
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

    async function createActivity() {
        const form = document.getElementById('createActivityForm');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);

        const nuevaActividad = {
            nombre: formData.get('nombre'),
            descripcion: formData.get('descripcion'),
            meta_id: formData.get('meta_id'),
            fecha_entrega: formData.get('fecha_entrega'),
        };

        try {
            const response = await fetch('/coord-equipo/api/actividades/crear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(nuevaActividad)
            });

            if (!response.ok) throw new Error('Error al crear la actividad');

            const data = await response.json();

            if (!data.success) throw new Error('Error en la respuesta');

            actividades.push(data.tarea);
            filteredActividades = [...actividades];
            loadActivities();
            initSortable();
            closeCreateModal();
            showToast('Actividad creada correctamente');

            form.reset();

        } catch (error) {
            console.error(error);
            showToast('Error al crear la actividad', 'error');
        }
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