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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o email..."
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div class="min-w-48 relative">
                <select name="plan" onchange="this.form.submit()"
                    class="w-full pr-4 pl-3 py-2 border border-gray-300 rounded-lg
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        appearance-none">
                    <option value="">Todos los planes</option>
                    <option value="basic" {{ request('plan')=='basic'?'selected':'' }}>Basic</option>
                    <option value="professional" {{ request('plan')=='professional'?'selected':'' }}>Professional</option>
                    <option value="enterprise" {{ request('plan')=='enterprise'?'selected':'' }}>Enterprise</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                    <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500"></i>
                </div>
            </div>
            <div class="min-w-48 relative">
                <select name="estado" onchange="this.form.submit()"
                    class="w-full pr-4 pl-3 py-2 border border-gray-300 rounded-lg
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        appearance-none">
                    <option value="">Todos los estados</option>
                    <option value="active" {{ request('estado')=='active'?'selected':'' }}>Activo</option>
                    <option value="inactive" {{ request('estado')=='inactive'?'selected':'' }}>Inactivo</option>
                    <option value="suspended" {{ request('estado')=='suspended'?'selected':'' }}>Suspendido</option>
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
                                    <p class="text-sm text-gray-500">{{ $e->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{
                                    $e->plan=='Enterprise'? 'bg-purple-100 text-purple-800': (
                                    $e->plan=='Professional'? 'bg-blue-100 text-blue-800': 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($e->plan) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $e->usuarios_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{
                                    $e->estado=='active'? 'bg-green-100 text-green-800': (
                                    $e->estado=='inactive'? 'bg-red-100 text-red-800': 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($e->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $e->created_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="verEmpresa({{ $e->id }})" class="text-blue-600 hover:text-blue-900 mr-2">Ver</button>
                                <button onclick="editarEmpresa({{ $e->id }})" class="text-green-600 hover:text-green-900 mr-2">Editar</button>
                                <button onclick="suspenderEmpresa({{ $e->id }})" class="text-red-600 hover:text-red-900">Suspender</button>
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());

    function verEmpresa(id) {
        // lógica abrir modal detalle
    }
    function editarEmpresa(id) {
        // lógica editar
    }
    function suspenderEmpresa(id) {
        if(!confirm('¿Suspender empresa?')) return;
        // lógica suspender
    }
</script>
@endpush
