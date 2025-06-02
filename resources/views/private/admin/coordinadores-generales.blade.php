@extends('layouts.private.admin')

@section('title', 'Coordinadores Generales')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Coordinadores Generales</h1>
            <p class="text-gray-600">Gestiona los coordinadores generales de la empresa <strong>{{ $empresa->nombre }}</strong>.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="relative flex-1 min-w-64">
                <i data-lucide="search" class="absolute left-3 top-3 h-4 w-4 text-gray-400"></i>
                <input type="text" name="searchTerm" value="{{ request('searchTerm') }}" placeholder="Buscar por nombre" onkeyup="setTimeout(() => {this.form.submit()}, 500)" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div class="min-w-48 relative">
                <select
                    name="filters[area_id]"
                    onchange="this.form.submit()"
                    class="w-full pr-4 pl-3 py-2 border border-gray-300 rounded-lg
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        appearance-none"
                >
                    <option value="">Todas las áreas</option>
                    <option value="0" {{ request('filters.area_id')=='0'?'selected':'' }}>Sin área</option>
                    @foreach($areas as $area)
                    <option value="{{ $area->id }}" {{ request('filters.area_id') == $area->id ? 'selected' : '' }}>
                        {{ $area->nombre }}
                    </option>
                    @endforeach
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
            <button type="submit" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors cursor-pointe">
                <i data-lucide="filter" class="h-4 w-4 mr-2"></i>
                Filtrar
            </button>
        </form>
    </div>

    {{-- Table --}}
    @include('partials.admin.tablas.pag.coord-generales-tabla-pag')

</div>
@endsection

{{-- Scripts --}}
@push('scripts')
<script>
</script>
@endpush
