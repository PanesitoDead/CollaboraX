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
            {{-- <button id="exportBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                Exportar Datos
            </button> --}}
        </div>
    </div>
    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300">
        <form class="flex flex-wrap gap-4" method="GET">
            <div class="relative flex-1 min-w-64">
                <i data-lucide="search" class="absolute left-3 top-3 h-4 w-4 text-gray-400"></i>
                <input type="text" name="searchTerm" value="{{ request('searchTerm') }}" placeholder="Buscar por nombre o email..." onkeyup="setTimeout(() => {this.form.submit()}, 500)" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
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
    @include('partials.super-admin.tablas.pag.empresas-tabla-pag')
</div>
@endsection

@push('scripts')
<script>
</script>
@endpush
