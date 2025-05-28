{{-- resources/views/admin/estadisticas.blade.php --}}
@extends('layouts.private.admin')

@section('title', 'Estadísticas')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Estadísticas</h1>
            <p class="text-gray-600">Análisis y métricas de rendimiento de la empresa</p>
        </div>
        <div class="flex items-center gap-4">
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            aria-label="Seleccionar periodo">
                <option value="7">Últimos 7 días</option>
                <option value="30" selected>Últimos 30 días</option>
                <option value="90">Últimos 3 meses</option>
                <option value="365">Último año</option>
            </select>
            <button
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                Exportar Reporte
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            [
                'label' => 'Actividades Terminadas',
                'value' => $stats['actividades_terminadas'],
                'icon'  => 'check-circle',
                'text'  => "+{$stats['actividades_nuevas']} esta semana",
            ],
            [
                'label' => 'Metas',
                'value' => "{$stats['metas_completadas']}/{$stats['metas_total']}",
                'icon'  => 'target',
                'text'  => "{$stats['metas_completadas']} cumplidas de {$stats['metas_total']}",
            ],
            [
                'label' => 'Asistencias a Reuniones',
                'value' => $stats['asistencias_totales'],
                'icon'  => 'users',
                'text'  => "{$stats['asistencias_semana']} esta semana",
            ],
            [
                'label' => 'Avance de Metas',
                'value' => "{$stats['porcentaje_avance']}%",
                'icon'  => 'bar-chart-2',
                'text'  => null,
            ],
        ] as $card)
            <div 
                class="bg-white border border-gray-200 rounded-2xl p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow duration-200"
                role="region" aria-labelledby="card-{{ Str::slug($card['label']) }}">

                <header class="flex items-center justify-between mb-4">
                    <h3 id="card-{{ Str::slug($card['label']) }}" class="text-sm font-semibold text-gray-700">{{ $card['label'] }}</h3>
                    <i data-lucide="{{ $card['icon'] }}" class="w-5 h-5 text-blue-500"></i>
                </header>

                <div class="flex-1">
                    <p class="text-3xl font-bold text-gray-900">{{ $card['value'] }}</p>

                    {{-- barra de progreso solo para porcentaje --}}
                    @if($card['label'] === 'Avance de Metas')
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                            <div 
                            class="bg-blue-500 h-2 rounded-full" 
                            style="width: {{ $stats['porcentaje_avance'] }}%">
                            </div>
                        </div>
                    @endif
                </div>

                @if($card['text'])
                    <footer class="mt-4 text-xs text-gray-500">
                        {{ $card['text'] }}
                    </footer>
                @endif

            </div>
        @endforeach
    </div>


    {{-- Charts Section --}}
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Rendimiento por Área --}}
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium">Rendimiento por Área</h3>
                <select class="text-sm border-gray-300 rounded-md">
                    <option>Este mes</option>
                    <option>Mes anterior</option>
                    <option>Trimestre</option>
                </select>
            </div>
            <div class="space-y-4">
                @foreach($rendimiento_areas as $area)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $area['color'] }}"></div>
                        <span class="text-sm font-medium">{{ $area['nombre'] }}</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full" style="width: {{ $area['porcentaje'] }}%; background-color: {{ $area['color'] }}"></div>
                        </div>
                        <span class="text-sm font-medium w-12 text-right">{{ $area['porcentaje'] }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Actividad Semanal --}}
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium">Actividad Semanal</h3>
                <div class="flex space-x-2">
                    <div class="flex items-center space-x-1">
                        <div class="w-3 h-3 bg-purple-500 rounded"></div>
                        <span class="text-xs text-gray-600">Metas</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="w-3 h-3 bg-blue-500 rounded"></div>
                        <span class="text-xs text-gray-600">Actividades</span>
                    </div>
                </div>
            </div>
            <div class="h-64 flex items-end justify-between space-x-2">
                @foreach($actividad_semanal as $dia)
                <div class="flex flex-col items-center space-y-2 flex-1">
                    <div class="w-full flex flex-col space-y-1">
                        <div class="bg-purple-500 rounded-t" style="height: {{ ($dia['metas']/max(array_column($actividad_semanal, 'metas')))*60 }}px"></div>
                        <div class="bg-blue-500 rounded-b" style="height: {{ ($dia['actividades']/max(array_column($actividad_semanal, 'actividades')))*60 }}px"></div>
                    </div>
                    <div class="text-xs text-gray-600">{{ $dia['dia'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Detailed Stats --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Top Performers --}}
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-medium mb-4">Mejores Colaboradores</h3>
            <div class="space-y-4">
                @foreach($top_performers as $index => $performer)
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        @if($index === 0)
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <span class="text-yellow-600 font-bold">1</span>
                            </div>
                        @elseif($index === 1)
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                <span class="text-gray-600 font-bold">2</span>
                            </div>
                        @elseif($index === 2)
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <span class="text-orange-600 font-bold">3</span>
                            </div>
                        @else
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-bold">{{ $index + 1 }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $performer['nombre'] }}</p>
                        <p class="text-xs text-gray-500">{{ $performer['area'] }}</p>
                    </div>
                    <div class="text-sm font-medium text-green-600">{{ $performer['puntuacion'] }}%</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Metas por Estado --}}
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-medium mb-4">Estado de Metas</h3>
            <div class="space-y-4">
                @foreach($metas_estado as $estado)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-4 h-4 rounded-full" style="background-color: {{ $estado['color'] }}"></div>
                        <span class="text-sm">{{ $estado['nombre'] }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium">{{ $estado['cantidad'] }}</span>
                        <span class="text-xs text-gray-500">({{ $estado['porcentaje'] }}%)</span>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between text-sm">
                    <span class="font-medium">Total de Metas</span>
                    <span class="font-medium">{{ array_sum(array_column($metas_estado, 'cantidad')) }}</span>
                </div>
            </div>
        </div>

        {{-- Actividad Reciente --}}
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-medium mb-4">Actividad Reciente</h3>
            <div class="space-y-4">
                @foreach($actividad_reciente as $actividad)
                <div class="flex space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-{{ $actividad['color'] }}-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-{{ $actividad['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $actividad['icon'] !!}
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900">{{ $actividad['descripcion'] }}</p>
                        <p class="text-xs text-gray-500">{{ $actividad['usuario'] }} • {{ $actividad['tiempo'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tabla de Rendimiento Detallado --}}
    <div class="bg-white rounded-lg border border-gray-300">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium">Rendimiento Detallado por Área</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coordinador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Colaboradores</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metas Activas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completadas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rendimiento</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rendimiento_detallado as $area)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $area['color'] }}"></div>
                                <span class="text-sm font-medium text-gray-900">{{ $area['nombre'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $area['coordinador'] ?? 'Sin asignar' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $area['equipos'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $area['colaboradores'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $area['metas_activas'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $area['metas_completadas'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="h-2 rounded-full" style="width: {{ $area['rendimiento'] }}%; background-color: {{ $area['color'] }}"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $area['rendimiento'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection