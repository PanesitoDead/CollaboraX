{{-- resources/views/admin/colaboradores/index.blade.php --}}
@extends('layouts.private.admin')

@section('title', 'Colaboradores')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Colaboradores</h1>
            <p class="text-gray-600">Gestión de colaboradores de la empresa</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openInviteModal()" 
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Invitar Colaborador
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-gray-300 p-4">
        <div class="flex flex-col gap-4 md:flex-row md:items-center">
            <div class="flex-1">
                <input type="text" placeholder="Buscar colaboradores..." 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex gap-2">
                <select class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas las áreas</option>
                    @foreach($areas as $area)
                    {{-- <option value="{{ $area->id }}">{{ $area->nombre }}</option> --}}
                    @endforeach
                </select>
                <select class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                    <option value="pendiente">Pendiente</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Colaboradores</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            <p class="text-xs text-gray-500">+{{ $stats['nuevos'] }} este mes</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Activos</h3>
                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['activos'] }}</div>
            <p class="text-xs text-gray-500">{{ number_format(($stats['activos']/$stats['total'])*100, 1) }}% del total</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Pendientes</h3>
                <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['pendientes'] }}</div>
            <p class="text-xs text-gray-500">Invitaciones enviadas</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Productividad</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $stats['productividad'] }}%</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['productividad'] }}%"></div>
            </div>
        </div>
    </div>

    {{-- Colaboradores Table --}}
    <div class="bg-white rounded-lg border border-gray-300">
        {{-- Table Header Actions --}}
        {{-- Table Header --}}
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium">Lista de Colaboradores</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Colaborador
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Área
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Equipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Último acceso
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($colaboradores as $colaborador)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-sm font-medium">{{ $colaborador['initials'] }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $colaborador['name'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $colaborador['email'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $colaborador['area'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $colaborador['equipo'] ?? 'Sin asignar' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($colaborador['estado'] === 'activo')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Activo
                                </span>
                            @elseif($colaborador['estado'] === 'pendiente')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pendiente
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $colaborador['ultimo_acceso'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-900">Ver</button>
                                <button class="text-blue-600 hover:text-blue-900">Editar</button>
                                @if($colaborador['estado'] === 'activo')
                                    <button class="text-red-600 hover:text-red-900">Desactivar</button>
                                @else
                                    <button class="text-green-600 hover:text-green-900">Activar</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200">
            {{-- {{ $colaboradores->links() }} --}}
        </div>
    </div>
</div>

{{-- Modal Invitar Colaborador --}}
<div id="inviteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            {{-- action="{{ route('admin.colaboradores.invite') }}"  --}}
            <form method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium">Invitar Colaborador</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Envía una invitación por correo electrónico para unirse a la empresa.
                    </p>
                </div>
                
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                        <input type="email" name="email" id="email" required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                        <input type="text" name="nombre" id="nombre" required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="area_id" class="block text-sm font-medium text-gray-700">Área</label>
                        <select name="area_id" id="area_id" required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccionar área</option>
                            @foreach($areas as $area)
                            {{-- <option value="{{ $area->id }}">{{ $area->name }}</option> --}}
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="mensaje" class="block text-sm font-medium text-gray-700">Mensaje personalizado (opcional)</label>
                        <textarea name="mensaje" id="mensaje" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Mensaje de bienvenida personalizado..."></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeInviteModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-gray-300 border-transparent rounded-md hover:bg-blue-700">
                        Enviar Invitación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openInviteModal() {
    document.getElementById('inviteModal').classList.remove('hidden');
}

function closeInviteModal() {
    document.getElementById('inviteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('inviteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeInviteModal();
    }
});
</script>
@endsection