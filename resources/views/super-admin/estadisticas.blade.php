{{-- resources/views/super-admin/estadisticas.blade.php --}}
@extends('layouts.super-admin.super-admin')

@section('title', 'Estadísticas del Sistema')
@section('page-title', 'Estadísticas del Sistema')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Estadísticas del Sistema</h1>
            <p class="text-gray-600">Visión general y métricas claves operativas</p>
        </div>
        <div class="flex items-center gap-4">
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" aria-label="Seleccionar periodo">
                <option value="7">Últimos 7 días</option>
                <option value="30" selected>Últimos 30 días</option>
                <option value="90">Últimos 3 meses</option>
                <option value="365">Último año</option>
            </select>
            <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                Exportar Reporte
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $cards = [
                [
                    'label' => 'Crecimiento Mensual',
                    'value' => "+{$growth}%",       // variable $growth
                    'icon'  => 'trending-up',
                    'text'  => "↗ {$growth_change}% vs mes anterior"
                ],
                [
                    'label' => 'Ingresos Totales',
                    'value' => '$' . number_format($total_income, 0),
                    'icon'  => 'dollar-sign',
                    'text'  => "↗ {$income_change}% vs mes anterior"
                ],
                [
                    'label' => 'Retención de Usuarios',
                    'value' => "{$user_retention}%",
                    'icon'  => 'users',
                    'text'  => "↗ {$retention_change}% vs mes anterior"
                ],
                [
                    'label' => 'Actividad Promedio',
                    'value' => "{$avg_activity}%",
                    'icon'  => 'activity',
                    'text'  => "↘ {$activity_change}% vs mes anterior"
                ],
            ];
        @endphp
        @foreach($cards as $card)
            <div class="bg-white border border-gray-200 rounded-2xl p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow duration-200">
                <header class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700">{{ $card['label'] }}</h3>
                    <i data-lucide="{{ $card['icon'] }}" class="w-5 h-5 text-blue-500"></i>
                </header>
                <div class="flex-1">
                    <p class="text-3xl font-bold text-gray-900">{{ $card['value'] }}</p>
                </div>
                @if($card['text'])
                    <footer class="mt-4 text-xs text-gray-500">{{ $card['text'] }}</footer>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Charts Section --}}
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Ingresos por Mes --}}
        <div class="bg-white rounded-lg border border-gray-300 p-6 shadow-sm">
            <h3 class="text-lg font-medium mb-4 flex items-center">
                <i data-lucide="bar-chart-2" class="w-5 h-5 mr-2 text-gray-600"></i>
                Ingresos por Mes
            </h3>
            <div class="w-full h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Crecimiento de Usuarios --}}
        <div class="bg-white rounded-lg border border-gray-300 p-6 shadow-sm">
            <h3 class="text-lg font-medium mb-4 flex items-center">
                <i data-lucide="user-plus" class="w-5 h-5 mr-2 text-gray-600"></i>
                Crecimiento de Usuarios
            </h3>
            <div class="w-full h-64">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Distribución de Planes --}}
    <div class="bg-white rounded-lg border border-gray-300 p-6 shadow-sm">
        <h3 class="text-lg font-medium mb-4 flex items-center">
            <i data-lucide="pie-chart" class="w-5 h-5 mr-2 text-gray-600"></i>
            Distribución de Planes
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-gray-600">{{ $plan['percent'] }}%</span>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900">{{ $plan['name'] }}</h4>
                    <p class="text-sm text-gray-500">{{ $plan['count'] }} empresas</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Actividad Reciente --}}
    <div class="bg-white rounded-lg border border-gray-300 p-6 shadow-sm">
        <h3 class="text-lg font-medium mb-4 flex items-center">
            <i data-lucide="clock" class="w-5 h-5 mr-2 text-gray-600"></i>
            Actividad Reciente del Sistema
        </h3>
        <div class="space-y-4">
            @foreach($recent_activities as $activity)
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $activity['bg'] }}">
                            {!! $activity['icon_svg'] !!}
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">{{ $activity['message'] }}</p>
                        <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = @json($monthly_revenue);
    new Chart(revenueCtx, {
      type: 'line',
      data: {
        labels: revenueData.map(r => r.month),
        datasets: [{ label: 'Ingresos', data: revenueData.map(r => r.value), backgroundColor: revenueData.map(r => r.color), fill: false }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });

    // User Growth Chart
    const userCtx = document.getElementById('userGrowthChart').getContext('2d');
    const userData = @json($user_growth);
    new Chart(userCtx, {
      type: 'bar',
      data: {
        labels: userData.map(u => u.month),
        datasets: [{ label: 'Usuarios', data: userData.map(u => u.count) }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });
  });
</script>
@endpush
