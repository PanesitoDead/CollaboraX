{{-- resources/views/admin/estadisticas.blade.php --}}
@extends('layouts.private.admin')

@section('title', 'Estadísticas')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Estadísticas</h1>
            <p class="text-gray-600">Análisis y métricas de rendimiento de la empresa</p>
        </div>
        <div class="flex gap-2">
            <select class="border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                <option value="7">Últimos 7 días</option>
                <option value="30" selected>Últimos 30 días</option>
                <option value="90">Últimos 3 meses</option>
                <option value="365">Último año</option>
            </select>
            <button class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar Reporte
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Productividad General</h3>
                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $kpis['productividad'] }}%</div>
            <div class="flex items-center text-sm">
                <svg class="h-4 w-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                <span class="text-green-600">+{{ $kpis['productividad_cambio'] }}%</span>
                <span class="text-gray-500 ml-1">vs mes anterior</span>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Metas Completadas</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $kpis['metas_completadas'] }}/{{ $kpis['metas_total'] }}</div>
            <div class="flex items-center text-sm">
                <span class="text-blue-600">{{ number_format(($kpis['metas_completadas']/$kpis['metas_total'])*100, 1) }}%</span>
                <span class="text-gray-500 ml-1">de cumplimiento</span>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Colaboradores Activos</h3>
                <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $kpis['colaboradores_activos'] }}</div>
            <div class="flex items-center text-sm">
                <span class="text-purple-600">{{ $kpis['colaboradores_conectados'] }}</span>
                <span class="text-gray-500 ml-1">conectados hoy</span>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-600">Tiempo Promedio</h3>
                <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold">{{ $kpis['tiempo_promedio'] }}h</div>
            <div class="flex items-center text-sm">
                <span class="text-orange-600">{{ $kpis['tiempo_tareas'] }}h</span>
                <span class="text-gray-500 ml-1">por tarea</span>
            </div>
        </div>
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