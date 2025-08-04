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
            <select id="periodo-select" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            aria-label="Seleccionar periodo">
                <option value="7" {{ request('periodo') == 7 ? 'selected' : '' }}>Últimos 7 días</option>
                <option value="30" {{ request('periodo', 30) == 30 ? 'selected' : '' }}>Últimos 30 días</option>
                <option value="90" {{ request('periodo') == 90 ? 'selected' : '' }}>Últimos 3 meses</option>
                <option value="365" {{ request('periodo') == 365 ? 'selected' : '' }}>Último año</option>
            </select>
            {{-- <button
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                Exportar Reporte
            </button> --}}
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

    {{-- Información del Plan --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <i data-lucide="crown" class="w-5 h-5 mr-2 text-yellow-500"></i>
                <h3 class="text-lg font-semibold text-gray-900">Plan Actual</h3>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-white {{ $infoDelPlan['color_estado'] ?? 'bg-gray-500' }}">
                {{ $infoDelPlan['estado'] ?? 'Sin información' }}
            </span>
        </div>

        @if(($infoDelPlan['estado'] ?? '') === 'Sin suscripción')
            {{-- Mostrar información cuando no hay suscripción activa --}}
            <div class="text-center py-8">
                <div class="mb-4">
                    <i data-lucide="alert-circle" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-900 mb-2">Sin Suscripción Activa</h4>
                    <p class="text-gray-600 mb-6">No tienes ningún plan de suscripción activo en este momento</p>
                </div>
                
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-orange-800">
                        <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                        Para acceder a todas las funcionalidades de CollaboraX, necesitas suscribirte a uno de nuestros planes.
                    </p>
                </div>
            </div>
        @else
            {{-- Mostrar información para plan con suscripción activa --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ $infoDelPlan['nombre'] }}</p>
                    <p class="text-sm text-gray-600">Plan Activo</p>
                </div>

                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">
                        S/. {{ number_format($infoDelPlan['plan']['precio'] ?? 0, 2) }}
                    </p>
                    <p class="text-sm text-gray-600">Precio {{ ucfirst($infoDelPlan['plan']['frecuencia'] ?? 'mensual') }}</p>
                </div>

                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-purple-600">
                        {{ (isset($infoDelPlan['limites']['trabajadores']) && $infoDelPlan['limites']['trabajadores'] == -1) ? '∞' : ($infoDelPlan['limites']['trabajadores'] ?? 'N/A') }}
                    </p>
                    <p class="text-sm text-gray-600">Límite Usuarios</p>
                </div>

                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-orange-600">
                        {{ (isset($infoDelPlan['funciones_avanzadas']) && $infoDelPlan['funciones_avanzadas']) ? 'Sí' : 'No' }}
                    </p>
                    <p class="text-sm text-gray-600">Funciones Avanzadas</p>
                </div>
            </div>

            {{-- Información adicional del plan --}}
            @if(isset($infoDelPlan['plan']) && $infoDelPlan['plan'])
                <div class="mt-4">
                    {{-- Descripción del plan --}}
                    @if(isset($infoDelPlan['plan']['descripcion']))
                        <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">Descripción del Plan</h4>
                            <p class="text-sm text-blue-800">{{ $infoDelPlan['plan']['descripcion'] }}</p>
                        </div>
                    @endif

                    {{-- Información de tiempo y renovación --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if(isset($infoDelPlan['dias_restantes']))
                            <div class="text-center p-3 bg-yellow-50 rounded-lg">
                                <p class="text-lg font-bold text-yellow-600">{{ $infoDelPlan['dias_restantes'] }}</p>
                                <p class="text-sm text-gray-600">Días Restantes</p>
                            </div>
                        @endif

                        @if(isset($infoDelPlan['renovacion_automatica']))
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <p class="text-lg font-bold text-green-600">
                                    {{ $infoDelPlan['renovacion_automatica'] ? 'Activada' : 'Desactivada' }}
                                </p>
                                <p class="text-sm text-gray-600">Renovación Automática</p>
                            </div>
                        @endif
                    </div>

                    {{-- Beneficios del plan --}}
                    @if(isset($infoDelPlan['plan']['beneficios']) && is_array($infoDelPlan['plan']['beneficios']) && !empty($infoDelPlan['plan']['beneficios']))
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Beneficios Incluidos</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($infoDelPlan['plan']['beneficios'] as $beneficio)
                                    <div class="flex items-center text-sm text-gray-700">
                                        <i data-lucide="check" class="w-4 h-4 mr-2 text-green-500"></i>
                                        {{ $beneficio }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @if(isset($infoDelPlan['fecha_vencimiento']) && $infoDelPlan['fecha_vencimiento'])
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                        Vence el {{ \Carbon\Carbon::parse($infoDelPlan['fecha_vencimiento'])->format('d/m/Y') }}
                        @if(isset($infoDelPlan['renovacion_automatica']) && $infoDelPlan['renovacion_automatica'])
                            <span class="text-green-600">(Renovación automática activada)</span>
                        @endif
                    </p>
                </div>
            @endif
        @endif
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
    // Manejar cambio de período
    const periodoSelect = document.getElementById('periodo-select');
    if (periodoSelect) {
      periodoSelect.addEventListener('change', function() {
        const periodo = this.value;
        const url = new URL(window.location);
        url.searchParams.set('periodo', periodo);
        window.location.href = url.toString();
      });
    }

    // Gráfico circular Distribución de Roles
    const rolesCtx = document.getElementById('rolesChart');
    if (rolesCtx) {
      const rolesData = @json($roles_dist);
      new Chart(rolesCtx.getContext('2d'), {
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
    }

    // Gráfico de Actividad Semanal
    const actividadCtx = document.getElementById('actividadSemanalChart');
    if (actividadCtx) {
      const actividadSemanalData = @json($actividad_semanal);
      const labels = actividadSemanalData.map(d => d.dia);
      const metas = actividadSemanalData.map(d => d.metas);
      const actividades = actividadSemanalData.map(d => d.actividades);

      new Chart(actividadCtx.getContext('2d'), {
          type: 'bar',
          data: {
          labels: labels,
          datasets: [
              {
              label: 'Metas',
              data: metas,
              backgroundColor: '#8b5cf6'
              },
              {
              label: 'Actividades',
              data: actividades,
              backgroundColor: '#3b82f6'
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
    }
  });
</script>
@endpush