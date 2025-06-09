{{-- resources/views/admin/areas/index.blade.php --}}
@extends('layouts.private.colaborador')

@section('title', 'Mis Actividades')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mis Actividades</h1>
            <p class="text-gray-600">Puedes ver el estado de tus actividades. Para cambiar el estado, usa el botón "Ver Detalles" en cada tarjeta.</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="relative w-64">
                <i data-lucide="search" class="absolute left-3 top-3 h-4 w-4 text-gray-400"></i>
                <form method="GET">
                    <input
                        type="text"
                        name="search"
                        value="{{ $searchQuery }}"
                        placeholder="Buscar actividades..."
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        onchange="this.form.submit()"
                    />
                </form>
            </div>
        </div>
    </div>

    {{-- Board --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($kanbanColumns as $column)
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium uppercase tracking-wider text-gray-500">{{ $column['titulo'] }}</h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $column['color'] }}-100 text-{{ $column['color'] }}-700">
                        {{ $column['items']->count() }}
                    </span>
                </div>
                <div class="space-y-4">
                    @foreach($column['items'] as $actividad)
                        @include('partials.colaborador.activity-card', ['actividad' => $actividad])
                    @endforeach
                    @if($column['items']->isEmpty())
                        <div class="px-4 py-16 flex flex-col items-center justify-center">
                            <i data-lucide="activity" class="h-6 w-6 text-gray-400 mb-2"></i>
                            <h4 class="text-sm font-medium text-gray-700">Sin Actividades</h4>
                            <p class="text-sm text-gray-500 text-center">Para este estado aun no hay actividades asignadas.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
</script>
@endpush
