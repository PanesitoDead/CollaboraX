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
    {{-- Distribución de Roles --}}
    <div class="bg-white rounded-lg border border-gray-300 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium flex items-center">
            <i data-lucide="users" class="w-5 h-5 mr-2 text-gray-600"></i>
            Distribución de Roles
        </h3>
        <select class="text-sm border-gray-300 rounded-md">
            <option>Este mes</option>
            <option>Trimestre</option>
            <option>Año</option>
        </select>
        </div>
        <div class="w-full h-64">
            <canvas id="rolesChart"></canvas>
        </div> 
    </div>

        {{-- Actividad Semanal --}}
        <div class="bg-white rounded-lg border border-gray-300 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium flex items-center">
                <i data-lucide="calendar" class="w-5 h-5 mr-2 text-gray-600"></i>
                Actividad Semanal
                </h3>
                <select id="semanaSelector" class="text-sm border-gray-300 rounded-md">
                <option value="1">Última semana</option>
                <option value="2">2 Semanas</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="actividadSemanalChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3 mt-6">
    {{-- Áreas con Más Rendimiento --}}
        <div class="bg-white rounded-lg border border-gray-300 p-6 shadow-sm">
            <h3 class="text-lg font-medium mb-4 flex items-center">
            <i data-lucide="bar-chart-2" class="w-5 h-5 mr-2 text-gray-600"></i>
            Áreas con Más Rendimiento
            </h3>
            <div class="space-y-4">
            @foreach($top_performers as $i => $p)
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 flex-shrink-0">
                <div class="rounded-full flex items-center justify-center 
                    @if($i===0) bg-yellow-100 @elseif($i===1) bg-gray-100 @elseif($i===2) bg-orange-100 @else bg-blue-100 @endif">
                    <span class="font-bold 
                    @if($i===0) text-yellow-600 
                    @elseif($i===1) text-gray-600 
                    @elseif($i===2) text-orange-600 
                    @else text-blue-600 @endif">
                    {{ $i+1 }}
                    </span>
                </div>
                </div>
                <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ $p['area'] }}</p>
                <p class="text-xs text-gray-500">Cumplimiento: {{ $p['puntuacion'] }}%</p>
                </div>
            </div>
            @endforeach
            </div>
        </div>

        {{-- Estado de Metas --}}
        <div class="bg-white rounded-lg border border-gray-300 p-6 shadow-sm">
            <h3 class="text-lg font-medium mb-4 flex items-center">
            <i data-lucide="check-square" class="w-5 h-5 mr-2 text-gray-600"></i>
            Estado de Metas
            </h3>
            <div class="space-y-4">
            @foreach($metas_estado as $e)
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                <div class="w-4 h-4 rounded-full" style="background-color: {{ $e['color'] }}"></div>
                <span class="text-sm">{{ $e['nombre'] }}</span>
                </div>
                <div class="flex items-center space-x-2">
                <span class="text-sm font-medium">{{ $e['cantidad'] }}</span>
                <span class="text-xs text-gray-500">({{ $e['porcentaje'] }}%)</span>
                </div>
            </div>
            @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex justify-between text-sm font-medium">
                <span>Total de Metas</span>
                <span>{{ array_sum(array_column($metas_estado, 'cantidad')) }}</span>
            </div>
            </div>
        </div>

        {{-- Reuniones por Semana --}}
        <div class="bg-white rounded-lg border border-gray-300 p-6 shadow-sm">
            <h3 class="text-lg font-medium mb-4 flex items-center">
            <i data-lucide="clock" class="w-5 h-5 mr-2 text-gray-600"></i>
            Reuniones por Semana
            </h3>
            <div class="space-y-4">
            @foreach($reuniones_semana as $dia)
            <div class="flex items-center justify-between">
                <span class="text-sm">{{ $dia['dia'] }}</span>
                <span class="text-sm font-medium">{{ $dia['conteo'] }}</span>
            </div>
            @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 text-sm flex justify-between font-medium">
            <span>Total</span>
            <span>{{ array_sum(array_column($reuniones_semana, 'conteo')) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Gráfico circular Distribución de Roles
    const ctx = document.getElementById('rolesChart').getContext('2d');
    const rolesData = @json($roles_dist);
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: rolesData.map(r => r.nombre),
        datasets: [{
          data: rolesData.map(r => r.porcentaje),
          backgroundColor: rolesData.map(r => r.color)
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        aspectRatio: 1,
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });

    const actividadSemanalData = @json($actividad_semanal);
    const ctxActividad = document.getElementById('actividadSemanalChart').getContext('2d');
    const labels = actividadSemanalData.map(d => d.dia);
    const metas = actividadSemanalData.map(d => d.metas);
    const actividades = actividadSemanalData.map(d => d.actividades);

    const actividadChart = new Chart(ctxActividad, {
        type: 'bar',
        data: {
        labels: labels,
        datasets: [
            {
            label: 'Metas',
            data: metas,
            backgroundColor: '#8b5cf6' // purple-500
            },
            {
            label: 'Actividades',
            data: actividades,
            backgroundColor: '#3b82f6' // blue-500
            }
        ]
        },
        options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1
            }
            }
        },
        plugins: {
            legend: {
            position: 'bottom'
            }
        }
        }
    });

  });
</script>
@endpush