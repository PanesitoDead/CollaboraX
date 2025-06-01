@if($areas->isEmpty()) 
    <div class="flex flex-col items-center justify-center py-20 space-y-6">
        {{-- Ícono ilustrativo (puedes sustituir por una imagen SVG o un icono de lucide) --}}
        <div class="w-36 h-36 flex items-center justify-center rounded-full bg-gray-100">
            <i data-lucide="layers" class="w-24 h-24 text-gray-400"></i>
        </div>
        <h2 class="text-2xl font-semibold text-gray-700">Aún no hay áreas registradas</h2>
        <p class="text-sm text-gray-500 text-center px-4">
            Crea tu primera área para comenzar a organizar equipos, colaboradores y metas.
        </p>
    </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($areas as $area)
                <article class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                    <!-- Área Header -->
                    <header class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center space-x-4">
                                <span class="w-2 h-2 rounded-full bg-{{ $area->color }}-500"></span>
                                <h2 class="text-lg font-semibold text-gray-900">{{ $area->nombre }}</h2>
                                <p class="text-sm text-gray-500">Cód. {{ $area->codigo }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="abrirAreaModal({{ $area->id }})" aria-label="Editar {{ $area->nombre }}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <i data-lucide="edit-2" class="w-4 h-4 text-gray-500 hover:text-gray-700"></i>
                                </button>
                                <button onclick="abrirModalEliminarArea({{ $area->id }}, '{{ addslashes($area->nombre) }}')" aria-label="Eliminar {{ $area->nombre }}" class="p-2 rounded-lg hover:bg-red-50 transition-colors">
                                <i data-lucide="trash" class="w-4 h-4 text-gray-500 hover:text-red-600"></i>
                                </button>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ $area->descripcion }}</p>
                        <div class="mt-4 flex justify-between items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $area->activo==1? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $area->activo== 1 ? 'Activo' : 'Inactivo' }}
                        </span>
                        <time datetime="{{ $area->fecha_creacion }}" class="text-xs text-gray-500">Creada {{ $area->fecha_creacion }}</time>
                        </div>
                    </header>

                    <!-- Coordinador -->
                    <section class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Coordinador General</h3>
                        @if($area->coordinador_nombres)
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-{{ $area->color }}-100 flex items-center justify-center">
                                <span class="text-sm font-medium text-{{ $area->color }}-600">
                                    {{ strtoupper(substr($area->coordinador_apellido_paterno, 0, 1)) }}{{ strtoupper(substr($area->coordinador_apellido_materno, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $area->coordinador_nombres }} 
                                {{ $area->coordinador_apellido_paterno }} 
                                {{ $area->coordinador_apellido_materno }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $area->coordinador_correo }}</p>
                            </div>
                        </div>
                        @else
                        <div class="flex items-center space-x-3 text-gray-400">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                            <i data-lucide="user" class="w-5 h-5"></i>
                            </div>
                            <div>
                            <p class="text-sm">Sin coordinador asignado</p>
                            <button onclick="abrirAreaModal({{ $area->id }}, {{true}})" class="mt-1 text-xs font-medium text-blue-600 hover:text-blue-800">
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
                            <p class="text-2xl font-bold text-blue-600">{{ $area->nro_equipos }}</p>
                            <p class="text-xs text-gray-500">Equipos</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-green-600">{{ $area->nro_colaboradores }}</p>
                            <p class="text-xs text-gray-500">Colaboradores</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-orange-500">{{ $area->nro_metas_activas }}</p>
                            <p class="text-xs text-gray-500">Metas Activas</p>
                        </div>
                        </div>

                        <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Progreso</span>
                            <span class="text-sm font-bold text-gray-900">{{ $area->progreso }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-{{ $area->color }}-600 h-2 rounded-full" style="width: {{ $area->progreso }}%"></div>
                        </div>
                        </div>
                    </footer>
                </article>
            @endforeach 
        </div>
    @endif
{{-- Pagination --}}
@if($areas->hasPages())
    <div class="px-6 py-3 border-t border-gray-200">
        {{ $areas->links() }}
    </div>
@endif

{{-- Modales --}}
@include('partials.admin.modales.edicion.areas-modal-edicion')
@include('partials.admin.modales.confirmacion.areas-modal-confirmacion')

{{-- Scripts --}}