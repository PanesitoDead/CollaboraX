{{-- resources/views/super-admin/empresas.blade.php --}}
@extends('layouts.super-admin.super-admin')

@section('title', 'Gestión de Empresas')
@section('page-title', 'Gestión de Empresas')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Empresas Registradas</h1>
            <p class="text-gray-600">Gestiona todas las empresas del sistema</p>
        </div>
        <div class="flex items-center gap-2">
            <button id="exportBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                Exportar Datos
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300">
        <form class="flex flex-wrap gap-4" method="GET">
            <div class="relative flex-1 min-w-64">
                <i data-lucide="search" class="absolute left-3 top-3 h-4 w-4 text-gray-400"></i>
                <input type="text" name="searchTerm" value="{{ request('searchTerm') }}" placeholder="Buscar por nombre o email..."
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div class="min-w-48 relative">
                <select name="filters[plan_servicio_id]" onchange="this.form.submit()"
                    class="w-full pr-4 pl-3 py-2 border border-gray-300 rounded-lg
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        appearance-none">
                    <option value="">Todos los planes</option>
                    <option value="1" {{ request('filters.plan_servicio_id')=='1'?'selected':'' }}>Standard</option>
                    <option value="2" {{ request('filters.plan_servicio_id')=='2'?'selected':'' }}>Business</option>
                    <option value="3" {{ request('filters.plan_servicio_id')=='3'?'selected':'' }}>Enterprise</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                    <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500"></i>
                </div>
            </div>
            <div class="min-w-48 relative">
                <select name="filters[estado]" onchange="this.form.submit()"
                    class="w-full pr-4 pl-3 py-2 border border-gray-300 rounded-lg
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        appearance-none">
                    <option value="">Todos los estados</option>
                    <option value="1" {{ request('filters.estado')=='1'?'selected':'' }}>Activo</option>
                    <option value="0" {{ request('filters.estado')=='0'?'selected':'' }}>Inactivo</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                    <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500"></i>
                </div>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                Filtrar
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-300">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuarios</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($empresas as $e)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">{{ substr($e->nombre,0,2) }}</span>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $e->nombre }}</p>
                                    <p class="text-sm text-gray-500">{{ $e->correo }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{
                                    $e->plan_servicio=='Enterprise'? 'bg-purple-100 text-purple-800': (
                                    $e->plan_servicio=='Business'? 'bg-blue-100 text-blue-800': 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($e->plan_servicio) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $e->nro_usuarios }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $e->activo ? 'bg-green-100 text-green-800':'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($e->activo ? 'Activo' : 'Inactivo') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $e->fecha_registro}}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="abrirModalDetallesEmpresa({{ $e->id }})" class="inline-flex items-center px-3 py-2 text-blue-600 hover:text-blue-900 transition-colors">
                                  <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                  Ver
                                </button>
                                <button onclick="abrirModalEmpresa({{$e->id}})" class="inline-flex items-center px-3 py-2 text-blue-600 hover:text-blue-900 transition-colors">
                                    <i data-lucide="edit" class="w-4 h-4 mr-1"></i>
                                  Editar
                                </button>
                                <button
                                    onclick="abrirModalCambioEstado({{ $e->id }}, {{ $e->activo ? 'true' : 'false' }})"
                                    class="inline-flex items-center px-3 py-2 text-red-600 hover:text-red-900 transition-colors">
                                    <i data-lucide="{{ $e->activo ? 'x' : 'check' }}" class="w-4 h-4 mr-1"></i>
                                    {{ $e->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay empresas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($empresas->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $empresas->links() }}
            </div>
        @endif
    </div>
</div>


<!-- Modal para ver detalles de la empresa -->
@include('partials.super-admin.modales.modal-detalles')
<!-- Modal para crear/editar empresa -->
@include('partials.super-admin.modales.modal-editar')
<!-- Modal para cambiar estado de la empresa -->
@include('partials.super-admin.modales.modal-switch')

@endsection

@push('scripts')
<script>
    
</script>
@endpush
