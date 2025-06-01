@extends('layouts.private.coord-equipo')

@section('title', 'Gestión de Metas')

@section('content')
<div class="flex flex-col gap-6">
    {{-- 1) Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Visualización de Metas</h1>
            <p class="text-gray-600">Lista de metas designadas a su equipo</p>
        </div>
    </div>

    {{-- 2) Filtros y botones de cambio de vista --}}
    <div class="bg-white rounded-lg border border-gray-300">
        <div class="border-b border-gray-200 p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                {{-- 2.1) Filtro de Estados (dinámico) --}}
                <div class="flex gap-4">
                    <select id="filtroEstado" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option value="">Todos los estados</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->nombre }}">
                                {{ ucwords(str_replace('_', ' ', $estado->nombre)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 2.2) Botones para alternar entre Card / Kanban --}}
                <div class="flex gap-2">
                    <button id="vistaCards"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-md bg-blue-50 text-blue-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2
                                     2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2
                                     0 012-2h2a2 2 0 012 2v2a2 2 0 01-2
                                     2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2
                                     2 0 012 2v2a2 2 0 01-2 2H6a2 2 0
                                     01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012
                                     2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </button>
                    <button id="vistaKanban"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 17V7m0 10a2 2 0 01-2
                                     2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2
                                     0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9
                                     7a2 2 0 012-2h2a2 2 0 012 2m0 0v10a2 2
                                     0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2
                                     2 0 00-2-2"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- =============== 3) VISTA DE CARDS (Grid de Metas) ============ --}}
        <div id="contenidoCards" class="p-6">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($metas as $meta)
                    <div class="meta-card bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                         onclick='abrirMetaModal(@json($meta))'
                         data-estado="{{ $meta->estado->nombre }}"
                         data-prioridad="{{ $meta->prioridad }}"
                         data-categoria="{{ $meta->categoria ?? '' }}">
                        {{-- 3.1) Header de la Meta --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-900 mb-2">{{ $meta->nombre }}</h3>
                                <p class="text-gray-600 text-sm mb-3">{{ $meta->descripcion }}</p>
                            </div>
                        </div>

                        {{-- 3.2) Estado y Prioridad --}}
                        <div class="flex items-center gap-2 mb-4">
                            @php
                                $e = strtolower($meta->estado->nombre);
                            @endphp

                            @if($e === 'completada' || $e === 'completo' || $e === 'terminada')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ ucwords(str_replace('_',' ',$meta->estado->nombre)) }}
                                </span>
                            @elseif($e === 'en_progreso' || $e === 'en progreso')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    {{ ucwords(str_replace('_',' ',$meta->estado->nombre)) }}
                                </span>
                            @elseif($e === 'pendiente')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Pendiente
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ ucwords(str_replace('_',' ',$meta->estado->nombre)) }}
                                </span>
                            @endif
                        </div>

                        {{-- 3.3) Progreso --}}
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Progreso</span>
                                <span class="text-sm text-gray-500">{{ $meta->porcentaje }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                     style="width: {{ $meta->porcentaje }}%"></div>
                            </div>
                            <div class="flex items-center justify-between mt-1 text-xs text-gray-500">
                                <span>
                                    {{ $meta->tareas_completadas }}/{{ $meta->tareas_totales }} actividades
                                </span>
                            </div>
                        </div>

                        {{-- 3.4) Fecha de entrega --}}
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>Vence: {{ \Carbon\Carbon::parse($meta->fecha_entrega)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ================ 4) VISTA KANBAN (Columnas dinámicas) ============== --}}
        <div id="contenidoKanban" class="p-6 hidden">
            <div class="grid grid-cols-1 md:grid-cols-{{ count($estados) }} gap-6">
                @foreach($estados as $estado)
                    @php
                        $metasPorEstado = $metas->where('estado_id', $estado->id);
                    @endphp

                    <div class="rounded-lg p-4
                                @if($estado->nombre === 'Incompleta') bg-gray-50
                                @elseif($estado->nombre === 'En proceso') bg-orange-50
                                @elseif($estado->nombre === 'Completo') bg-green-50
                                @else bg-red-50 @endif">
                        {{-- 4.1) Título de la columna --}}
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            {{-- Punto de color según estado --}}
                            <div class="w-3 h-3 rounded-full
                                        @if($estado->nombre === 'Incompleta') bg-gray-400
                                        @elseif($estado->nombre === 'En proceso') bg-orange-400
                                        @elseif($estado->nombre === 'Completo') bg-green-400
                                        @else bg-red-400 @endif">
                            </div>

                            {{-- Nombre legible del estado --}}
                            {{ ucwords(str_replace('_', ' ', $estado->nombre)) }}

                            {{-- Badge con el conteo de metas en este estado --}}
                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full ml-auto">
                                {{ $metasPorEstado->count() }}
                            </span>
                        </h3>

                        {{-- 4.2) Tarjetas (metas) de este estado --}}
                        <div class="space-y-3">
                            @foreach($metasPorEstado as $meta)
                                <div onclick='abrirMetaModal(@json($meta))' class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm
                                            @if($estado->nombre !== 'Incompleta' 
                                                  && $estado->nombre !== 'En proceso') opacity-75 @endif">
                                    {{-- 4.2.1) Título y descripción corta --}}
                                    <h4 class="font-medium text-sm mb-2">{{ $meta->nombre }}</h4>
                                    <p class="text-xs text-gray-600 mb-3">{{ Str::limit($meta->descripcion, 80) }}</p>

                                    {{-- 4.2.2) En “En Progreso”, mostrar barra de progreso --}}
                                    {{-- @if(strtolower($estado->nombre) === 'en proceso') --}}
                                        <div class="mb-3">
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-orange-500 h-1.5 rounded-full"
                                                     style="width: {{ $meta->porcentaje }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500 mt-1">{{ $meta->porcentaje }}%</span>
                                        </div>
                                    {{-- @endif --}}

                                    {{-- 4.2.3) Avatares de asignados (si existieran) y prioridad --}}
                                    <div class="flex items-center justify-between">
                                        <div class="flex -space-x-1">
                                            {{-- Si tienes relación usuariosAsignados, podrías hacer: --}}
                                            @isset($meta->usuariosAsignados)
                                                @foreach($meta->usuariosAsignados->take(2) as $usuario)
                                                    <div class="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center text-xs font-medium text-blue-800 border-2 border-white">
                                                        {{ $usuario->iniciales }}
                                                    </div>
                                                @endforeach
                                            @endisset
                                        </div>

                                        @php $p = strtolower($meta->prioridad); @endphp
                                        @if($p === 'alta')
                                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                        @elseif($p === 'media')
                                            <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                        @else
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        @endif
                                    </div>

                                    {{-- 4.2.4) Iconos finales: check o cruz si aplica --}}
                                    @if(strtolower($estado->nombre) === 'completa' || strtolower($estado->nombre) === 'completo')
                                        <svg class="h-4 w-4 text-green-500 mt-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @elseif(strtolower($estado->nombre) === 'suspendida')
                                        <svg class="h-4 w-4 text-red-500 mt-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>


<div id="metaDetalleModal" class="fixed inset-0 hidden z-50" role="dialog" aria-modal="true">
    <!-- Fondo oscuro -->
    <div class="fixed inset-0 bg-black/50" onclick="cerrarMetaModal()"></div>

    <!-- Contenedor del modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] overflow-y-auto border border-gray-300">
            <!-- Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-t-2xl">
                <h2 id="metaTitulo" class="text-2xl font-bold">Nombre de la Meta</h2>
                <p id="metaDescripcion" class="text-sm text-blue-100 mt-1">Descripción breve de la meta</p>
            </div>

            <!-- Detalles -->
            <div class="px-6 py-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500">Estado</span>
                        <span id="metaEstado" class="font-medium text-gray-900"></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500">Fecha de creación</span>
                        <span id="metaFechaCreacion" class="font-medium text-gray-900"></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500">Fecha de entrega</span>
                        <span id="metaFechaEntrega" class="font-medium text-gray-900"></span>
                    </div>
                </div>

                <!-- Actividades -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Actividades asociadas</h4>
                    <ul id="metaActividades" class="space-y-2">
                        <!-- Rellenado por JS -->
                    </ul>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end bg-gray-50 rounded-b-2xl">
                <button type="button" onclick="cerrarMetaModal()"
                        class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos
    const vistaCards = document.getElementById('vistaCards');
    const vistaKanban = document.getElementById('vistaKanban');
    const contenidoCards = document.getElementById('contenidoCards');
    const contenidoKanban = document.getElementById('contenidoKanban');
    const filtroEstado = document.getElementById('filtroEstado');

    // Para edición/eliminación, usamos 'metasData' como array JS
    const metasData = @json($metas);

    // Cambiar de vista
    if (vistaCards && vistaKanban) {
        vistaCards.addEventListener('click', function() {
            contenidoCards.classList.remove('hidden');
            contenidoKanban.classList.add('hidden');
            vistaCards.classList.add('bg-blue-50', 'text-blue-600');
            vistaCards.classList.remove('hover:bg-gray-50');
            vistaKanban.classList.remove('bg-blue-50', 'text-blue-600');
            vistaKanban.classList.add('hover:bg-gray-50');
        });
        vistaKanban.addEventListener('click', function() {
            contenidoKanban.classList.remove('hidden');
            contenidoCards.classList.add('hidden');
            vistaKanban.classList.add('bg-blue-50', 'text-blue-600');
            vistaKanban.classList.remove('hover:bg-gray-50');
            vistaCards.classList.remove('bg-blue-50', 'text-blue-600');
            vistaCards.classList.add('hover:bg-gray-50');
        });
    }

    // Filtrar cards por estado
    function aplicarFiltros() {
        const estadoSeleccionado = filtroEstado.value;
        const metaCards = document.querySelectorAll('.meta-card');

        metaCards.forEach(card => {
            const estado = card.dataset.estado;
            const mostrar = (!estadoSeleccionado || estado === estadoSeleccionado);
            card.style.display = mostrar ? 'block' : 'none';
        });
    }
    if (filtroEstado) {
        filtroEstado.addEventListener('change', aplicarFiltros);
    }

    // Funciones globales para editar/eliminar
    window.editarMeta = function(metaId) {
        const meta = metasData.find(m => m.id === metaId);
        if (meta) {
            document.getElementById('edit_titulo').value = meta.nombre;
            document.getElementById('edit_descripcion').value = meta.descripcion;
            document.getElementById('edit_categoria').value = meta.categoria ?? '';
            document.getElementById('edit_prioridad').value = meta.prioridad;
            document.getElementById('edit_estado').value = meta.estado.nombre;
            document.getElementById('edit_fecha_limite').value = meta.fecha_entrega;
            document.getElementById('edit_progreso').value = meta.porcentaje;

            editarMetaForm.action = `/coord-equipo/metas/${metaId}`;
            editarMetaModal.classList.remove('hidden');
        }
    };

    window.eliminarMeta = function(metaId) {
        if (confirm('¿Estás seguro de que deseas eliminar esta meta? Esta acción no se puede deshacer.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/coord-equipo/metas/${metaId}`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    };

    window.abrirMetaModal = function(meta) {
        function formatearFecha(fechaStr) {
            if (!fechaStr) return '-';
            const fecha = new Date(fechaStr);
            if (isNaN(fecha)) return '-';
            const dia = fecha.getDate();
            const mes = fecha.getMonth() + 1;
            const año = fecha.getFullYear();
            return `${dia}/${mes.toString().padStart(2, '0')}/${año}`;
        }

        document.getElementById('metaTitulo').textContent = meta.nombre || '-';
        document.getElementById('metaDescripcion').textContent = meta.descripcion || '-';
        document.getElementById('metaFechaCreacion').textContent = formatearFecha(meta.fecha_creacion);
        document.getElementById('metaFechaEntrega').textContent = formatearFecha(meta.fecha_entrega);
        document.getElementById('metaEstado').textContent = meta.estado?.nombre || '-';

        const actividadesList = document.getElementById('metaActividades');
        actividadesList.innerHTML = '';

        if (meta.tareas && meta.tareas.length > 0) {
            meta.tareas.forEach(act => {
                const li = document.createElement('li');
                li.className = 'pl-4 border-l-4 border-blue-500 text-sm text-gray-800';
                li.textContent = `${act.nombre} (${act.estado?.nombre || 'Sin estado'})`;
                actividadesList.appendChild(li);
            });
        } else {
            const li = document.createElement('li');
            li.className = 'text-sm text-gray-500 italic';
            li.textContent = 'No hay actividades registradas.';
            actividadesList.appendChild(li);
        }

        document.getElementById('metaDetalleModal').classList.remove('hidden');
    };

    window.cerrarMetaModal = function() {
        document.getElementById('metaDetalleModal').classList.add('hidden');
    };

});
</script>
@endpush