@extends('layouts.private.colaborador')

@section('title', 'Mi Equipo')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Mi Equipo</h1>
        <p class="text-gray-600">Información sobre el equipo al que perteneces.</p>
    </div>

    {{-- Estadísticas --}}
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 auto-rows-fr">
        @foreach ([
            ['label' => 'Miembros',    'icon' => 'users',      'value' => $estadisticas['miembros'],         'sub' => "{$estadisticas['miembros_nuevos']} nuevos este mes"],
            ['label' => 'Metas Activas','icon' => 'target',     'value' => $estadisticas['metas_activas'],     'sub' => "{$estadisticas['metas_completadas_mes']} completadas este mes"],
            ['label' => 'Actividades',  'icon' => 'activity',   'value' => $estadisticas['actividades_total'], 'sub' => "{$estadisticas['actividades_progreso']} en progreso, {$estadisticas['actividades_completadas']} completadas"],
            ['label' => 'Rendimiento',  'icon' => 'trending-up', 'value' => $estadisticas['rendimiento'] . '%', 'sub' => null],
        ] as $stat)
            <div 
                class="bg-white border border-gray-200 rounded-2xl p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow duration-200 h-full"
                role="region"
                aria-labelledby="card-{{ Str::slug($stat['label']) }}"
            >
                <header class="flex items-center justify-between mb-4">
                    <h3 id="card-{{ Str::slug($stat['label']) }}" class="text-sm font-semibold text-gray-700">
                        {{ $stat['label'] }}
                    </h3>
                    <i data-lucide="{{ $stat['icon'] }}" class="w-5 h-5 text-blue-500"></i>
                </header>

                <div class="flex-1">
                    <p class="text-3xl font-bold text-gray-900">{{ $stat['value'] }}</p>

                    @if ($stat['label'] === 'Rendimiento')
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                            <div
                                class="bg-blue-500 h-2 rounded-full"
                                style="width: {{ $estadisticas['rendimiento'] }}%"
                            ></div>
                        </div>
                    @endif
                </div>

                @if ($stat['sub'])
                    <footer class="mt-4 text-xs text-gray-500">
                        {{ $stat['sub'] }}
                    </footer>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Información del Equipo --}}
    <div class="bg-white rounded-lg border border-gray-300 overflow-hidden">
    {{-- Cabecera --}}
    <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
        <h3 class="text-xl font-semibold text-gray-900">{{ $equipoInfo['nombre'] }}</h3>
        <p class="text-sm text-gray-500">Información general y miembros del equipo</p>
        </div>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
        Área: {{ $equipoInfo['area'] }}
        </span>
    </div>

    {{-- Progreso General --}}
    <div class="px-6 py-4 space-y-4 border-b border-gray-200">
        <div class="flex justify-between text-sm">
            <span class="text-gray-700">Progreso general</span>
            <span class="font-medium text-gray-900">{{ $equipoInfo['progreso_general'] }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
            <div
                class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                style="width: {{ $equipoInfo['progreso_general'] }}%">
            </div>
            </div>

            <div class="grid grid-cols-3 gap-4 text-center text-sm text-gray-700">
            <div>
                <div class="font-medium text-gray-900">
                {{ $equipoInfo['metas_completadas'] }}/{{ $equipoInfo['metas_totales'] }}
                </div>
                <div class="text-xs text-gray-500">Metas completadas</div>
            </div>
            <div>
                <div class="font-medium text-gray-900">
                {{ $equipoInfo['actividades_completadas'] }}/{{ $equipoInfo['actividades_totales'] }}
                </div>
                <div class="text-xs text-gray-500">Actividades completadas</div>
            </div>
            <div>
                <div class="font-medium text-gray-900">{{ $equipoInfo['reuniones_pendientes'] }}</div>
                <div class="text-xs text-gray-500">Reuniones pendientes</div>
            </div>
            </div>
        </div>

        {{-- Tabs --}}
        <nav class="border-b border-gray-200">
            <div class="flex space-x-8 px-6">
            <button
                type="button"
                data-tab="miembros"
                class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-blue-500 py-4 px-1 font-medium text-sm text-blue-600 transition"
                onclick="showTab('miembros')"
            >
                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                Miembros
            </button>
            <button
                type="button"
                data-tab="metas"
                class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-transparent py-4 px-1 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition"
                onclick="showTab('metas')"
            >
                <i data-lucide="target" class="w-4 h-4 mr-1"></i>
                Metas
            </button>
            </div>
        </nav>

        {{-- Contenidos de pestañas --}}
        <div id="tab-miembros" class="tab-content p-6">
            @include('partials.colaborador.miembros-table', ['miembros' => $miembros])
        </div>
        <div id="tab-metas" class="tab-content p-6 hidden">
            @include('partials.colaborador.metas-list', ['metas' => $metas])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showTab(tab) {
    // Oculta contenidos
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    // Reset estilos de botones
    document.querySelectorAll('.tab-button').forEach(btn => {
      btn.classList.remove('border-blue-500', 'text-blue-600');
      btn.classList.add('border-transparent', 'text-gray-500');
    });
    // Muestra contenido activo
    document.getElementById(`tab-${tab}`).classList.remove('hidden');
    // Activa botón
    const active = document.querySelector(`.tab-button[data-tab="${tab}"]`);
    active.classList.add('border-blue-500', 'text-blue-600');
    active.classList.remove('border-transparent', 'text-gray-500');
  }

  // Inicializa con pestaña miembros activa
  document.addEventListener('DOMContentLoaded', () => showTab('miembros'))
</script>
@endpush
