{{-- resources/views/admin/coordinadores-generales/index.blade.php --}}
@extends('layouts.private.admin')

@section('title', 'Coordinadores Generales')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Coordinadores Generales</h1>
            <p class="text-gray-600">Gestión de coordinadores generales de áreas</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openAsignModal()" 
                    class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Asignar Coordinador General
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Coordinadores Activos</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['activos'] ?? 0 }}</div>
            <p class="text-xs text-gray-500">De {{ $stats['total_areas'] ?? 0 }} áreas</p>
        </div>

        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Equipos Gestionados</h3>
                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['equipos_total'] ?? 0 }}</div>
            <p class="text-xs text-gray-500">{{ $stats['colaboradores_total'] ?? 0 }} colaboradores</p>
        </div>

        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Rendimiento Global</h3>
                <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['rendimiento_global'] ?? 0 }}%</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $stats['rendimiento_global'] ?? 0 }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg border p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Metas del Mes</h3>
                <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['metas_completadas'] ?? 0 }}/{{ $stats['metas_total'] ?? 0 }}</div>
            {{-- {{ number_format(($stats['metas_completadas']/$stats['metas_total'])*100, 1) }} --}}
            <p class="text-xs text-gray-500">0% completado</p>
        </div>
    </div>

    {{-- Coordinadores por Área --}}
    <div class="space-y-6">
        @foreach($areas=[] as $area)
        <div class="bg-white rounded-lg border">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium">{{ $area['nombre'] ?? 0 }}</h3>
                        <p class="text-sm text-gray-500">{{ $area['descripcion'] ?? 0 }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Rendimiento del Área</div>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $area['rendimiento'] ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $area['rendimiento'] ?? 0 }}%</span>
                            </div>
                        </div>
                        @if(!$area['coordinador'] ?? 0)
                        <button onclick="openAsignModal('{{ $area['id'] ?? 0 }}')" 
                                class="px-3 py-1 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700">
                            Asignar Coordinador
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            @if($area['coordinador'] ?? 0)
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="h-16 w-16 rounded-full bg-purple-100 flex items-center justify-center">
                            {{-- {{ $area['coordinador']['initials'] }} --}}
                            <span class="text-xl font-medium text-purple-600"></span>
                        </div>
                        <div>
                            <h4 class="text-xl font-medium">{{ $area['coordinador']['name'] }}</h4>
                            <p class="text-gray-500">{{ $area['coordinador']['email'] }}</p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Coordinador General
                                </span>
                                <span class="text-sm text-gray-500">
                                    Desde {{ $area['coordinador']['fecha_asignacion'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                            Ver Perfil
                        </button>
                        <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                            Gestionar Área
                        </button>
                        <button class="px-3 py-1 text-sm border border-red-300 text-red-600 rounded-md hover:bg-red-50">
                            Remover
                        </button>
                    </div>
                </div>

                {{-- Estadísticas del Coordinador --}}
                <div class="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $area['coordinador']['equipos'] }}</div>
                        <div class="text-sm text-gray-500">Equipos</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $area['coordinador']['colaboradores'] }}</div>
                        <div class="text-sm text-gray-500">Colaboradores</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-500">{{ $area['coordinador']['metas_activas'] }}</div>
                        <div class="text-sm text-gray-500">Metas Activas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $area['coordinador']['ultimo_acceso'] }}</div>
                        <div class="text-sm text-gray-500">Último Acceso</div>
                    </div>
                </div>
            </div>
            @else
            <div class="p-6 text-center">
                <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h4 class="text-lg font-medium text-gray-900 mb-2">Sin Coordinador Asignado</h4>
                <p class="text-gray-500 mb-4">Esta área necesita un coordinador general para gestionar los equipos y colaboradores.</p>
                <button onclick="openAsignModal('{{ $area['id'] }}')" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                    Asignar Coordinador
                </button>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- Modal Asignar Coordinador General --}}
<div id="asignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            {{-- action="{{ route('admin.coordinadores-generales.store') }}" --}}
            <form  method="POST">
                @csrf
                <input type="hidden" name="area_id" id="modal_area_id">
                
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium">Asignar Coordinador General</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Selecciona un colaborador para asignarle la coordinación general del área.
                    </p>
                </div>
                
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="usuario_id" class="block text-sm font-medium text-gray-700">Colaborador</label>
                        <select name="usuario_id" id="usuario_id" required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Seleccionar colaborador</option>
                            @foreach($colaboradores_disponibles=[] as $colaborador)
                            <option value="{{ $colaborador->id }}">{{ $colaborador->name }} ({{ $colaborador->email }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Solo se muestran colaboradores activos sin rol de coordinación.
                        </p>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="enviar_notificacion" id="enviar_notificacion" checked 
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="enviar_notificacion" class="ml-2 block text-sm text-gray-700">
                            Enviar notificación al colaborador
                        </label>
                    </div>
                    <p class="text-xs text-gray-500">
                        Se enviará un correo informando sobre su nueva responsabilidad como Coordinador General.
                    </p>
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
function openAsignModal(areaId = null) {
    if (areaId) {
        document.getElementById('modal_area_id').value = areaId;
    }
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