@extends('layouts.private.coord-equipo')

@section('title', 'Mi Equipo')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Mi Equipo</h1>
            <p class="text-gray-600">Gestiona los miembros y actividades de tu equipo.</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Miembros</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['miembros'] }}</div>
            <p class="text-xs text-gray-500">{{ $stats['miembros_nuevos'] }} nuevos este mes</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Metas Activas</h3>
                <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['metas_activas'] }}</div>
            <p class="text-xs text-gray-500">{{ $stats['metas_completadas'] }} completada este mes</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Actividades</h3>
                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['actividades_total'] }}</div>
            <p class="text-xs text-gray-500">{{ $stats['actividades_progreso'] }} en progreso, {{ $stats['actividades_completadas'] }} completadas</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Rendimiento</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['rendimiento'] }}%</div>
            <p class="text-xs text-gray-500">+{{ $stats['rendimiento_cambio'] }}% respecto al mes anterior</p>
        </div>
    </div>

    {{-- Tabs Content --}}
    <div class="bg-white rounded-lg border border-gray-300">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button id="tab-equipo-btn" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="equipo">
                    Miembros del Equipo
                </button>
                <button id="tab-invitaciones-btn" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="invitaciones">
                    Invitaciones
                </button>
            </nav>
        </div>

        {{-- Tab Content: Miembros del Equipo --}}
        <div id="tab-equipo" class="tab-content p-6">
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-medium">Equipo Desarrollo Frontend</h3>
                        <p class="text-gray-600">Información general y rendimiento del equipo</p>
                    </div>
                    <div class="flex gap-2">
                        <button id="invitar-colaborador-btn" 
                                class="flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg border border-gray-300 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Invitar Colaborador
                        </button>
                        <button id="programar-reunion-btn" 
                                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Programar Reunión
                        </button>
                    </div>
                </div>

                {{-- Progreso general --}}
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span>Progreso general</span>
                        <span class="font-medium">{{ $stats['rendimiento'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['rendimiento'] }}%"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-center text-sm">
                        <div>
                            <div class="font-medium">{{ $stats['metas_completadas'] }}/{{ $stats['metas_activas'] + $stats['metas_completadas'] }}</div>
                            <div class="text-xs text-gray-500">Metas completadas</div>
                        </div>
                        <div>
                            <div class="font-medium">{{ $stats['actividades_completadas'] }}/{{ $stats['actividades_total'] }}</div>
                            <div class="text-xs text-gray-500">Actividades completadas</div>
                        </div>
                        <div>
                            <div class="font-medium">3</div>
                            <div class="text-xs text-gray-500">Reuniones pendientes</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de miembros --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actividades</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rendimiento</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($miembros as $miembro)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium">{{ substr($miembro['nombre'], 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $miembro['nombre'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $miembro['email'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $miembro['rol'] === 'Coordinador' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $miembro['rol'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="mr-2 text-sm">{{ $miembro['actividades_completadas'] }}/{{ $miembro['actividades_totales'] }}</span>
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($miembro['actividades_completadas'] / $miembro['actividades_totales']) * 100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $miembro['rendimiento'] }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900">Ver Detalles</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab Content: Invitaciones --}}
        <div id="tab-invitaciones" class="tab-content p-6 hidden">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium">Invitaciones a Colaboradores</h3>
                    <p class="text-gray-600">Gestiona las invitaciones enviadas a colaboradores para unirse al equipo</p>
                </div>
                <button id="nueva-invitacion-btn" 
                        class="flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg border border-gray-300 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nueva Invitación
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Colaborador</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Invitación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invitaciones as $invitacion)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium">{{ substr($invitacion['colaborador']['nombre'], 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $invitacion['colaborador']['nombre'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $invitacion['colaborador']['email'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $invitacion['colaborador']['rol'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($invitacion['fecha'])->format('d/m/Y H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($invitacion['estado'] === 'pendiente')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pendiente
                                    </span>
                                @elseif($invitacion['estado'] === 'aceptada')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aceptada
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Rechazada
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($invitacion['estado'] === 'pendiente')
                                        <form action="{{ route('coord-equipo.equipo.cancelar-invitacion', $invitacion['id']) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <button class="text-blue-600 hover:text-blue-900">Ver Detalles</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal para Invitar Colaboradores --}}
<div id="invitarColaboradorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[70vh] overflow-y-auto">
            <form action="{{ route('coord-equipo.equipo.invitar') }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium">Invitar Colaboradores</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Selecciona colaboradores para invitarlos a unirse a tu equipo.
                    </p>
                </div>
                
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar colaborador</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="search-colaborador" placeholder="Buscar colaborador..." 
                                   class="pl-10 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div id="selected-colaboradores" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Colaboradores seleccionados</label>
                        <div id="selected-list" class="flex flex-wrap gap-2 p-2 border rounded-md min-h-[40px]">
                            <!-- Los colaboradores seleccionados aparecerán aquí -->
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Colaboradores disponibles</label>
                        <div class="border rounded-md max-h-48 overflow-y-auto">
                            @foreach($colaboradores_disponibles as $colaborador)
                            <div class="colaborador-item flex items-center justify-between p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                                 data-id="{{ $colaborador['id'] }}"
                                 data-nombre="{{ $colaborador['nombre'] }}"
                                 data-email="{{ $colaborador['email'] }}"
                                 data-rol="{{ $colaborador['rol'] }}"
                                 data-departamento="{{ $colaborador['departamento'] }}">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium">{{ substr($colaborador['nombre'], 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $colaborador['nombre'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $colaborador['email'] }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $colaborador['rol'] }}
                                    </span>
                                    <div class="colaborador-check hidden">
                                        <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" id="close-invitar-modal" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" id="submit-invitacion" disabled
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Invitar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal para Programar Reunión --}}
<div id="programarReunionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <form action="{{ route('coord-equipo.equipo.reunion') }}" method="POST">
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
                        <label for="participantes" class="block text-sm font-medium text-gray-700">Participantes</label>
                        <select name="participantes[]" id="participantes" multiple required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @foreach($miembros as $miembro)
                            <option value="{{ $miembro['id'] }}">{{ $miembro['nombre'] }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Mantén presionado Ctrl para seleccionar múltiples participantes</p>
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción (opcional)</label>
                        <textarea name="descripcion" id="descripcion" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Agenda de la reunión..."></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" id="close-reunion-modal" 
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
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script cargado');
    
    // Variables para el modal de invitación
    let selectedColaboradores = [];
    
    // Tab functionality
    function showTab(tabName) {
        console.log('Cambiando a tab:', tabName);
        
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
        const tabContent = document.getElementById('tab-' + tabName);
        if (tabContent) {
            tabContent.classList.remove('hidden');
        }
        
        // Add active state to selected button
        const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
        if (activeButton) {
            activeButton.classList.remove('border-transparent', 'text-gray-500');
            activeButton.classList.add('border-blue-500', 'text-blue-600');
        }
    }

    // Tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
        });
    });

    // Modal functionality
    const invitarModal = document.getElementById('invitarColaboradorModal');
    const reunionModal = document.getElementById('programarReunionModal');
    
    // Botones para abrir modales
    const invitarBtn = document.getElementById('invitar-colaborador-btn');
    const nuevaInvitacionBtn = document.getElementById('nueva-invitacion-btn');
    const reunionBtn = document.getElementById('programar-reunion-btn');
    
    // Botones para cerrar modales
    const closeInvitarBtn = document.getElementById('close-invitar-modal');
    const closeReunionBtn = document.getElementById('close-reunion-modal');

    // Abrir modal de invitación
    if (invitarBtn) {
        invitarBtn.addEventListener('click', function() {
            console.log('Abriendo modal de invitación');
            invitarModal.classList.remove('hidden');
        });
    }

    if (nuevaInvitacionBtn) {
        nuevaInvitacionBtn.addEventListener('click', function() {
            console.log('Abriendo modal de nueva invitación');
            invitarModal.classList.remove('hidden');
        });
    }

    // Abrir modal de reunión
    if (reunionBtn) {
        reunionBtn.addEventListener('click', function() {
            console.log('Abriendo modal de reunión');
            reunionModal.classList.remove('hidden');
        });
    }

    // Cerrar modales
    if (closeInvitarBtn) {
        closeInvitarBtn.addEventListener('click', function() {
            invitarModal.classList.add('hidden');
            resetInvitacionModal();
        });
    }

    if (closeReunionBtn) {
        closeReunionBtn.addEventListener('click', function() {
            reunionModal.classList.add('hidden');
        });
    }

    // Cerrar modales al hacer clic fuera
    invitarModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            resetInvitacionModal();
        }
    });

    reunionModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // Funcionalidad del modal de invitación
    const searchInput = document.getElementById('search-colaborador');
    const colaboradorItems = document.querySelectorAll('.colaborador-item');
    const selectedContainer = document.getElementById('selected-colaboradores');
    const selectedList = document.getElementById('selected-list');
    const submitBtn = document.getElementById('submit-invitacion');

    // Búsqueda de colaboradores
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            colaboradorItems.forEach(item => {
                const nombre = item.getAttribute('data-nombre').toLowerCase();
                const email = item.getAttribute('data-email').toLowerCase();
                const rol = item.getAttribute('data-rol').toLowerCase();
                const departamento = item.getAttribute('data-departamento').toLowerCase();
                
                if (nombre.includes(searchTerm) || email.includes(searchTerm) || 
                    rol.includes(searchTerm) || departamento.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Selección de colaboradores
    colaboradorItems.forEach(item => {
        item.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const email = this.getAttribute('data-email');
            const rol = this.getAttribute('data-rol');
            
            const colaborador = { id, nombre, email, rol };
            
            if (selectedColaboradores.find(c => c.id === id)) {
                // Deseleccionar
                selectedColaboradores = selectedColaboradores.filter(c => c.id !== id);
                this.classList.remove('bg-blue-50');
                this.querySelector('.colaborador-check').classList.add('hidden');
            } else {
                // Seleccionar
                selectedColaboradores.push(colaborador);
                this.classList.add('bg-blue-50');
                this.querySelector('.colaborador-check').classList.remove('hidden');
            }
            
            updateSelectedList();
        });
    });

    function updateSelectedList() {
        if (selectedColaboradores.length > 0) {
            selectedContainer.classList.remove('hidden');
            selectedList.innerHTML = '';
            
            selectedColaboradores.forEach(colaborador => {
                const badge = document.createElement('div');
                badge.className = 'flex items-center gap-1 bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs';
                badge.innerHTML = `
                    <span>${colaborador.nombre}</span>
                    <button type="button" class="remove-colaborador ml-1" data-id="${colaborador.id}">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <input type="hidden" name="colaboradores[]" value="${colaborador.id}">
                `;
                selectedList.appendChild(badge);
            });
            
            // Event listeners para remover colaboradores
            document.querySelectorAll('.remove-colaborador').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    selectedColaboradores = selectedColaboradores.filter(c => c.id !== id);
                    
                    // Actualizar UI del item
                    const item = document.querySelector(`[data-id="${id}"]`);
                    if (item) {
                        item.classList.remove('bg-blue-50');
                        item.querySelector('.colaborador-check').classList.add('hidden');
                    }
                    
                    updateSelectedList();
                });
            });
            
            submitBtn.disabled = false;
        } else {
            selectedContainer.classList.add('hidden');
            submitBtn.disabled = true;
        }
    }

    function resetInvitacionModal() {
        selectedColaboradores = [];
        searchInput.value = '';
        selectedContainer.classList.add('hidden');
        submitBtn.disabled = true;
        
        colaboradorItems.forEach(item => {
            item.classList.remove('bg-blue-50');
            item.querySelector('.colaborador-check').classList.add('hidden');
            item.style.display = 'flex';
        });
    }

    // Initialize first tab as active
    showTab('equipo');
    
    console.log('Elementos encontrados:', {
        tabButtons: tabButtons.length,
        invitarBtn: !!invitarBtn,
        reunionBtn: !!reunionBtn,
        modales: !!invitarModal && !!reunionModal
    });
});
</script>
@endsection