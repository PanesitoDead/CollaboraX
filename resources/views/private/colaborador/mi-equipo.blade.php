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
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['label'=>'Miembros','icon'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m22 21-3-3m0 0a5 5 0 1 0-7-7 5 5 0 0 0 7 7z"/>','value'=>$estadisticas['miembros'],'sub'=>"{$estadisticas['miembros_nuevos']} nuevos este mes"],
            ['label'=>'Metas Activas','icon'=>'<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>','value'=>$estadisticas['metas_activas'],'sub'=>"{$estadisticas['metas_completadas_mes']} completada este mes"],
            ['label'=>'Actividades','icon'=>'<path d="M9 11H3l3-3"/><path d="m6 8 3 3"/><path d="M21 11h-6l3-3"/><path d="m18 8 3 3"/><path d="M9 19c-2.8 0-5-2.2-5-5v-4a2 2 0 0 1 2-2h2"/><path d="M15 19c2.8 0 5-2.2 5-5v-4a2 2 0 0 0-2-2h-2"/>','value'=>$estadisticas['actividades_total'],'sub'=> "{$estadisticas['actividades_progreso']} en progreso, {$estadisticas['actividades_completadas']} completadas"],
            ['label'=>'Rendimiento','icon'=>'<path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M14 9h1.5a2.5 2.5 0 0 1 0 5H14"/><path d="M6 9v6"/><path d="M14 9v6"/><path d="m6 9 7-3 7 3"/>','value'=> $estadisticas['rendimiento'].'%','sub'=> "+{$estadisticas['mejora_rendimiento']}% respecto al mes anterior"],
        ] as $stat)
        <div class="bg-white rounded-lg shadow-sm border border-gray-300">
            <div class="flex items-center justify-between p-6 pb-2">
                <h3 class="text-sm font-medium text-gray-900 tracking-tight">{{ $stat['label'] }}</h3>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $stat['icon'] !!}
                </svg>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold text-gray-900">{{ $stat['value'] }}</div>
                <p class="text-xs text-gray-500">{{ $stat['sub'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Información del Equipo --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">{{ $equipoInfo['nombre'] }}</h3>
                <p class="text-sm text-gray-500">Información general y miembros del equipo</p>
            </div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                Área: {{ $equipoInfo['area'] }}
            </div>
        </div>
        <div class="px-6 py-4 space-y-6">
            {{-- Progreso General --}}
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-700">Progreso general</span>
                    <span class="font-medium text-gray-900">{{ $equipoInfo['progreso_general'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $equipoInfo['progreso_general'] }}%"></div>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center text-sm text-gray-700">
                    <div>
                        <div class="font-medium text-gray-900">{{ $equipoInfo['metas_completadas'] }}/{{ $equipoInfo['metas_totales'] }}</div>
                        <div class="text-xs text-gray-500">Metas completadas</div>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $equipoInfo['actividades_completadas'] }}/{{ $equipoInfo['actividades_totales'] }}</div>
                        <div class="text-xs text-gray-500">Actividades completadas</div>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $equipoInfo['reuniones_pendientes'] }}</div>
                        <div class="text-xs text-gray-500">Reuniones pendientes</div>
                    </div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="bg-white rounded-lg border border-gray-300">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button 
                            onclick="switchTab('miembros')" 
                            id="tab-miembros" 
                            class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            data-tab="miembros"
                        >
                            Miembros del Equipo
                        </button>
                        <button 
                            onclick="switchTab('metas')" 
                            id="tab-metas" 
                            class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            data-tab="metas"
                        >
                            Metas del Equipo
                        </button>
                    </nav>
                </div>
                <div id="content-miembros" class="tab-content p-6 space-y-4">
                    @include('partials.colaborador.miembros-table', ['miembros' => $miembros])
                </div>
                <div id="content-metas" class="tab-content hidden p-6 space-y-4">
                    @include('partials.colaborador.metas-list', ['metas' => $metas])
                </div>
            </div>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.querySelectorAll('.tab-button').forEach(b => {
            b.classList.remove('border-blue-600','text-blue-600');
            b.classList.add('border-transparent','text-gray-500');
        });
        document.getElementById(`content-${tabName}`).classList.remove('hidden');
        const btn = document.getElementById(`tab-${tabName}`);
        btn.classList.add('border-blue-600','text-blue-600');
        btn.classList.remove('border-transparent','text-gray-500');
    }
    document.addEventListener('DOMContentLoaded', () => switchTab('miembros'));
</script>
@endpush
@endsection
