{{-- resources/views/admin/coordinadores-equipo/index.blade.php --}}
@extends('layouts.private.admin')

@section('title', 'Coordinadores de Equipo')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Coordinadores de Equipo</h1>
            <p class="text-gray-600">Gestión de coordinadores de equipos de trabajo</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openAsignModal()" 
                    class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Asignar Coordinador
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Coordinadores</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            {{-- {{ $stats['total'] }} --}}
            <div class="text-2xl font-bold">Total</div>
            {{-- {{ $stats['equipos'] }} --}}
            <p class="text-xs text-gray-500">En 10 equipos</p>
        </div>

        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Equipos Activos</h3>
                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            {{-- {{ $stats['equipos_activos'] }} --}}
            <div class="text-2xl font-bold">Equipo activo</div>
            {{-- {{ $stats['miembros_total'] }} --}}
            <p class="text-xs text-gray-500">10 miembros total</p>
        </div>

        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Rendimiento Promedio</h3>
                <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            {{-- {{ $stats['rendimiento'] }} --}}
            <div class="text-2xl font-bold">10%</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-orange-500 h-2 rounded-full" style="width: 10%"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Metas Completadas</h3>
                <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            {{-- {{ $stats['metas_completadas'] }} --}}
            {{-- {{ $stats['metas_total'] }} --}}
            <div class="text-2xl font-bold">metas</div>
            <p class="text-xs text-gray-500">De meta totales metas asignadas</p>
        </div>
    </div>

    {{-- Coordinadores Grid --}}
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($coordinadores=[] as $coordinador)
        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                        <span class="text-lg font-medium text-purple-600">{{ $coordinador['initials'] }}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium">{{ $coordinador['name'] }}</h3>
                        <p class="text-sm text-gray-500">{{ $coordinador['email'] }}</p>
                    </div>
                </div>
                <div class="flex space-x-1">
                    <button class="p-1 text-gray-400 hover:text-gray-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>
                    <button class="p-1 text-gray-400 hover:text-red-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Área:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $coordinador['area'] }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Equipo:</span>
                    <span class="text-sm font-medium">{{ $coordinador['equipo'] }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Miembros:</span>
                    <span class="text-sm font-medium">{{ $coordinador['miembros'] }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Rendimiento:</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-16 bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $coordinador['rendimiento'] }}%"></div>
                        </div>
                        <span class="text-xs font-medium">{{ $coordinador['rendimiento'] }}%</span>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Estado:</span>
                    @if($coordinador['estado'] === 'activo')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Activo
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Inactivo
                        </span>
                    @endif
                </div>

                <div class="pt-3 border-t border-gray-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Último acceso:</span>
                        {{-- {{ $coordinador['ultimo_acceso'] }} --}}
                        <span class="font-medium">ultimo_acceso</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex space-x-2">
                <button class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                    Ver Equipo
                </button>
                <button class="flex-1 px-3 py-2 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700">
                    Gestionar
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Modal Asignar Coordinador --}}
<div id="asignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            {{-- action="{{ route('admin.coordinadores-equipo.store') }}" --}}
            <form  method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium">Asignar Coordinador de Equipo</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Selecciona un colaborador para asignarle la coordinación de un equipo.
                    </p>
                </div>
                
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="usuario_id" class="block text-sm font-medium text-gray-700">Colaborador</label>
                        <select name="usuario_id" id="usuario_id" required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Seleccionar colaborador</option>
                            @foreach($colaboradores_disponibles=[] as $colaborador)
                            <option value="{{ $colaborador->id }}">{{ $colaborador->name }} - {{ $colaborador->area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="equipo_id" class="block text-sm font-medium text-gray-700">Equipo</label>
                        <select name="equipo_id" id="equipo_id" required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Seleccionar equipo</option>
                            @foreach($equipos_disponibles=[] as $equipo)
                            <option value="{{ $equipo->id }}">{{ $equipo->nombre }} - {{ $equipo->area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="enviar_notificacion" id="enviar_notificacion" checked 
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="enviar_notificacion" class="ml-2 block text-sm text-gray-700">
                            Notificar al colaborador
                        </label>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeAsignModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700">
                        Asignar Coordinador
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAsignModal() {
    document.getElementById('asignModal').classList.remove('hidden');
}

function closeAsignModal() {
    document.getElementById('asignModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('asignModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAsignModal();
    }
});
</script>
@endsection