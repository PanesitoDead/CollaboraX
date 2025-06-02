@extends('layouts.private.coord-equipo')

@section('title', 'Reuniones del Equipo')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Reuniones del Equipo</h1>
            <p class="text-gray-600">Gestión y seguimiento de reuniones del grupo</p>
        </div>
        <div class="flex gap-2">
            <button id="btn-nueva-reunion" 
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nueva Reunión
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Reuniones Programadas</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['reuniones_programadas'] }}</div>
            <p class="text-xs text-gray-500">Reuniones activas</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Reuniones Completadas</h3>
                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['reuniones_completadas'] }}</div>
            <p class="text-xs text-gray-500">En total</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Participación Promedio</h3>
                <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['participacion_promedio'] }}%</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $stats['participacion_promedio'] }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Duración Promedio</h3>
                <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['duracion_promedio'] }} min</div>
            <p class="text-xs text-gray-500">Tiempo efectivo de reunión</p>
        </div>
    </div>

    {{-- Tabs Content --}}
    <div class="bg-white rounded-lg border border-gray-300">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button id="tab-btn-programadas" 
                        class="tab-button border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="programadas">
                    Reuniones Programadas
                </button>
                <button id="tab-btn-completadas" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="completadas">
                    Reuniones Completadas
                </button>
                {{-- <button id="tab-btn-calendario" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="calendario">
                    Vista Calendario
                </button> --}}
            </nav>
        </div>

        {{-- Tab Content: Reuniones Programadas --}}
        <div id="tab-programadas" class="tab-content p-6">
            <div class="mb-4">
                <h3 class="text-lg font-medium">Próximas Reuniones</h3>
                <p class="text-gray-600">Reuniones programadas para los próximos días</p>
            </div>
            <div class="space-y-4">
                @foreach($reunionesProgramadas as $reunion)
                <div class="flex items-center justify-between rounded-lg border border-gray-300 p-4">
                    <div class="flex items-start gap-4">
                        <div class="flex flex-col items-center">
                            <div class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($reunion->fecha)->format('d') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($reunion->fecha)->format('M') }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h4 class="font-medium">{{ $reunion->asunto }}</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($reunion->modalidad->nombre === 'Virtual') bg-blue-100 text-blue-800
                                    @elseif($reunion->modalidad->nombre === 'Presencial') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($reunion->modalidad->nombre) }}
                                </span>
                            </div>
                            @php
                                $partes = [];

                                if (!empty($reunion->descripcion)) {
                                    $partes[] = $reunion->descripcion;
                                }

                                if (!empty($reunion->sala)) {
                                    $partes[] = $reunion->sala;
                                }
                            @endphp

                            @if (!empty($partes))
                                <p class="text-sm text-gray-600 mt-1">{{ implode(' - ', $partes) }}</p>
                            @endif

                            <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                <span>{{ \Carbon\Carbon::parse($reunion->hora)->format('H:i') }} - {{ $reunion->duracion }} min</span>
                                <div class="flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($reunion->link_reunion)
                        <form action="{{ route('coord-equipo.reuniones.join', $reunion->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="px-3 py-1 text-sm bg-green-600 text-white rounded-md hover:bg-green-700">
                                Unirse
                            </button>
                        </form>
                        @endif
                        <button onclick="openReprogramarModal({{ $reunion->id }})" 
                                class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                            Reprogramar
                        </button>
                        <form action="{{ route('coord-equipo.reuniones.cancel', $reunion->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="px-3 py-1 text-sm border border-red-300 text-red-600 rounded-md hover:bg-red-50"
                                    onclick="return confirm('¿Estás seguro de cancelar esta reunión?')">
                                Cancelar
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tab Content: Reuniones Completadas --}}
        <div id="tab-completadas" class="tab-content p-6 hidden">
            <div class="mb-4">
                <h3 class="text-lg font-medium">Reuniones Completadas</h3>
                <p class="text-gray-600">Historial de reuniones realizadas</p>
            </div>
            <div class="space-y-4">
                @foreach($reunionesCompletadas as $reunion)
                <div class="flex items-center justify-between rounded-lg border border-gray-300 p-4">
                    <div class="flex items-start gap-4">
                        <div class="flex flex-col items-center">
                            <div class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($reunion->fecha)->format('d') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($reunion->fecha)->format('M') }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h4 class="font-medium">{{ $reunion->asunto }}</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Completada
                                </span>
                            </div>
                             @php
                                $partes = [];

                                if (!empty($reunion->descripcion)) {
                                    $partes[] = $reunion->descripcion;
                                }

                                if (!empty($reunion->sala)) {
                                    $partes[] = $reunion->sala;
                                }
                            @endphp

                            @if (!empty($partes))
                                <p class="text-sm text-gray-600 mt-1">{{ implode(' - ', $partes) }}</p>
                            @endif
                            <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                <span>{{ \Carbon\Carbon::parse($reunion->hora)->format('H:i') }} - {{ $reunion->duracion }} min</span>
                                <div class="flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- @if($reunion['grabacion'])
                        <a href="{{ $reunion['grabacion'] }}" target="_blank" 
                           class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Ver Grabación
                        </a>
                        @endif --}}
                        <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                            Ver Detalles
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tab Content: Vista Calendario --}}
        <div id="tab-calendario" class="tab-content p-6 hidden">
            <div class="mb-4">
                <h3 class="text-lg font-medium">Calendario de Reuniones</h3>
                <p class="text-gray-600">Vista mensual de todas las reuniones</p>
            </div>
            <div class="h-96 flex items-center justify-center bg-gray-50 rounded-lg">
                <p class="text-gray-500">Vista de calendario - Implementar con librería de calendario</p>
            </div>
        </div>
    </div>
</div>

{{-- Modal para Nueva Reunión --}}
<div id="reunionModal" class="fixed inset-0 hidden z-50" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/50" onclick="closeReunionModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[70vh] overflow-y-auto">
            <form action="{{ route('coord-equipo.reuniones.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium">Programar Reunión</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Programa una reunión con tu equipo.
                    </p>
                </div>
                
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
                        <input type="text" name="titulo" id="titulo" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Reunión de equipo">
                    </div>

                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha y hora</label>
                        <input type="datetime-local" name="fecha" id="fecha" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="duracion" class="block text-sm font-medium text-gray-700">Duración (minutos)</label>
                        <select name="duracion" id="duracion" required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="30">30 minutos</option>
                            <option value="60">1 hora</option>
                            <option value="90">1.5 horas</option>
                            <option value="120">2 horas</option>
                        </select>
                    </div>

                    <div>
                        <label for="modalidad_id" class="block text-sm font-medium text-gray-700">Modalidad</label>
                        <select name="modalidad_id" id="modalidad_id" required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="" disabled selected>Seleccione una modalidad</option>
                            @foreach($modalidades as $modalidad)
                                <option value="{{ $modalidad->id }}">{{ $modalidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción (opcional)</label>
                        <textarea name="descripcion" id="descripcion" rows="3" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Agenda de la reunión..."></textarea>
                    </div>

                    <div>
                        <label for="sala" class="block text-sm font-medium text-gray-700">Sala (opcional)</label>
                        <input type="text" name="sala" id="sala"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Ejemplo: Sala de Zoom o Aula virtual N°1">
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" id="btn-cancelar-reunion" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        Programar Reunión
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal para Reprogramar Reunión --}}
<div id="reprogramarModal" class="fixed inset-0 hidden z-50" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/50"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[70vh] overflow-y-auto">
            <form id="reprogramar-form" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium">Reprogramar Reunión</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Selecciona una nueva fecha y hora para la reunión
                    </p>
                </div>
                
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="nueva_fecha" class="block text-sm font-medium text-gray-700">Nueva fecha</label>
                        <input type="date" name="nueva_fecha" id="nueva_fecha" required 
                               min="{{ date('Y-m-d') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="nueva_hora" class="block text-sm font-medium text-gray-700">Nueva hora</label>
                        <input type="time" name="nueva_hora" id="nueva_hora" required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo (opcional)</label>
                        <textarea name="motivo" id="motivo" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Explica el motivo de la reprogramación"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" id="btn-cancelar-reprogramar" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        Reprogramar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active state from all buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab content
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        
        // Add active state to selected button
        const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
        activeButton.classList.remove('border-transparent', 'text-gray-500');
        activeButton.classList.add('border-blue-500', 'text-blue-600');
    }

    // Tab event listeners
    document.getElementById('tab-btn-programadas').addEventListener('click', () => showTab('programadas'));
    document.getElementById('tab-btn-completadas').addEventListener('click', () => showTab('completadas'));
    //document.getElementById('tab-btn-calendario').addEventListener('click', () => showTab('calendario'));

    // Modal functionality
    const reunionModal = document.getElementById('reunionModal');
    const reprogramarModal = document.getElementById('reprogramarModal');
    
    // Nueva reunión modal
    document.getElementById('btn-nueva-reunion').addEventListener('click', function() {
        reunionModal.classList.remove('hidden');
    });

    document.getElementById('btn-cancelar-reunion').addEventListener('click', function() {
        reunionModal.classList.add('hidden');
    });

    // Reprogramar modal
    window.openReprogramarModal = function(reunionId) {
        const form = document.getElementById('reprogramar-form');
        form.action = `/coord-equipo/reuniones/${reunionId}/reschedule`;
        reprogramarModal.classList.remove('hidden');
    };

    document.getElementById('btn-cancelar-reprogramar').addEventListener('click', function() {
        reprogramarModal.classList.add('hidden');
    });

    // Close modals when clicking outside
    reunionModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    reprogramarModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // Reunión recurrente toggle
    const esRecurrenteCheckbox = document.getElementById('es_recurrente');
    const frecuenciaContainer = document.getElementById('frecuencia-container');
    
    esRecurrenteCheckbox.addEventListener('change', function() {
        if (this.checked) {
            frecuenciaContainer.classList.remove('hidden');
        } else {
            frecuenciaContainer.classList.add('hidden');
        }
    });

    // Initialize first tab as active
    showTab('programadas');
});
</script>
@endpush