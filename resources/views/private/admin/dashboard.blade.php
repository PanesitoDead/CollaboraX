{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.private.admin')

@section('title', 'Panel de Administrador')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Panel de Administrador</h1>
            <p class="text-gray-600">Gestión de la empresa TechSolutions S.A.</p>
        </div>
    </div>    {{-- Stats Cards --}}
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['label' => 'Áreas', 'value' => $stats['areas'], 'icon' => 'layers','text' => 'Marketing, Ventas, Operaciones, Finanzas, TI'],
            ['label' => 'Usuarios Totales', 'value' => $stats['usuarios_totales'], 'icon' => 'users', 'text' => "+{$stats['usuarios_nuevos']} desde la semana pasada"],
            ['label' => 'Metas Activas', 'value' => $stats['metas_activas'], 'icon' => 'target', 'text' => "{$stats['metas_progreso']} en progreso, {$stats['metas_pendientes']} pendientes"],
            ['label' => 'Cumplimiento', 'value' => "{$stats['cumplimiento']}%", 'icon' => 'check-square', 'text' => null],
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
                @if($card['label'] === 'Cumplimiento')
                <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $stats['cumplimiento'] }}%"></div>
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

    {{-- Tabs Content --}}
    <div class="bg-white rounded-lg border border-gray-300">
        <nav class="border-b border-gray-200">
            <div class="flex space-x-8 px-6">
            <button
                type="button"
                data-tab="coordinadores"
                class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-blue-500 py-4 px-1 font-medium text-sm text-blue-600 transition"
                onclick="showTab('coordinadores')"
            >
                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                Coordinadores
            </button>

            <button
                type="button"
                data-tab="areas"
                class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-transparent py-4 px-1 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition"
                onclick="showTab('areas')"
            >
                <i data-lucide="layers" class="w-4 h-4 mr-1"></i>
                Áreas
            </button>

            <button
                type="button"
                data-tab="rendimiento"
                class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-transparent py-4 px-1 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition"
                onclick="showTab('rendimiento')"
            >
                <i data-lucide="bar-chart-2" class="w-4 h-4 mr-1"></i>
                Rendimiento
            </button>
            </div>
        </nav>

        {{-- Coordinadores --}}
        <div id="tab-coordinadores" class="tab-content p-6 ">
            <h3 class="text-lg font-medium mb-1">Coordinadores Generales</h3>
            <p class="text-gray-600 mb-6">Responsables de las áreas de la empresa</p>

            <div class="space-y-4">
            @foreach($coordinadores as $c)
            <div class="flex items-center justify-between rounded-lg border border-gray-300 p-4">
                <div class="flex items-center gap-4">
                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                    <span class="text-sm font-medium">{{ $c['initials'] }}</span>
                </div>
                <div>
                    <p class="font-medium">{{ $c['name'] }}</p>
                    <p class="text-sm text-gray-500">{{ $c['email'] }}</p>
                </div>
                </div>
                <div class="flex items-center gap-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ $c['area'] }}
                </span>
                <div class="text-sm text-gray-500">{{ $c['last_active'] }}</div>
                <div class="flex gap-2">
                    <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                    Ver Perfil
                    </button>
                    <button class="px-3 py-1 text-sm border border-red-300 text-red-600 rounded-md hover:bg-red-50">
                    Desactivar
                    </button>
                </div>
                </div>
            </div>
            @endforeach
            </div>
        </div>

        {{-- Áreas --}}
        <div id="tab-areas" class="tab-content p-6 hidden">
            <h3 class="text-lg font-medium mb-1">Áreas de la Empresa</h3>
            <p class="text-gray-600 mb-6">Estructura organizativa de TechSolutions S.A.</p>

            <div class="space-y-4">
            @foreach($areas as $a)
            <div class="flex items-center justify-between rounded-lg border border-gray-300 p-4">
                <div>
                <p class="font-medium">{{ $a['name'] }}</p>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span>Coordinador: {{ $a['coordinator'] }}</span>
                    <span>•</span>
                    <span>{{ $a['groups'] }} grupos</span>
                    <span>•</span>
                    <span>{{ $a['users'] }} usuarios</span>
                </div>
                </div>
                <div class="flex items-center gap-4">
                <div class="flex flex-col items-end">
                    <span class="text-sm">Cumplimiento</span>
                    <div class="flex items-center gap-2">
                    <div class="w-24 bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full bg-blue-600" style="width: {{ $a['progress'] }}%"></div>
                    </div>
                    <span class="text-xs">{{ $a['progress'] }}%</span>
                    </div>
                </div>
                <button class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                    Gestionar
                </button>
                </div>
            </div>
            @endforeach
            </div>
        </div>

        {{-- Rendimiento --}}
        <div id="tab-rendimiento" class="tab-content p-6">
            <div class="bg-white rounded-lg border border-gray-300 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium flex items-center">
                    <i data-lucide="bar-chart-2" class="w-5 h-5 mr-2 text-gray-600"></i>
                    Rendimiento por Área
                </h3>
                <select id="rangoRendimiento" class="text-sm border-gray-300 rounded-md">
                    <option value="mes">Este mes</option>
                    <option value="trimestre">Trimestre</option>
                    <option value="anio">Año</option>
                </select>
                </div>
                <div class="w-full h-96">
                <canvas id="rendimientoChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.getElementById('rangoRendimiento').addEventListener('change', () => {
        updateRendimientoChart();
    });

    const ctxRend = document.getElementById('rendimientoChart').getContext('2d');
    let rendimientoChart;

    function renderRendimientoChart(data) {
    if (rendimientoChart) rendimientoChart.destroy();
    rendimientoChart = new Chart(ctxRend, {
        type: 'bar', 
        data: {
        labels: data.map(d => d.area),
        datasets: [{
            label: 'Cumplimiento (%)',
            data: data.map(d => d.cumplimiento),
            backgroundColor: data.map(d => d.color)
        }]
        },
        options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } }
        }
        }
    });
    }

    function updateRendimientoChart() {
    const rango = document.getElementById('rangoRendimiento').value;
    // Simulación de datos según el rango seleccionado
    let data;
    if (rango === 'mes') {
      data = [
        { area: 'Marketing', cumplimiento: 85, color: '#3B82F6' },
        { area: 'Ventas', cumplimiento: 78, color: '#10B981' },
        { area: 'Soporte', cumplimiento: 92, color: '#F59E0B' },
        { area: 'Desarrollo', cumplimiento: 88, color: '#EF4444' },
        { area: 'Recursos Humanos', cumplimiento: 80, color: '#8B5CF6' },
        { area: 'Finanzas', cumplimiento: 82, color: '#F472B6' },
        { area: 'Operaciones', cumplimiento: 76, color: '#F97316' },
      ];
    } else if (rango === 'trimestre') {
      data = [
        { area: 'Marketing', cumplimiento: 80, color: '#3B82F6' },
        { area: 'Ventas', cumplimiento: 75, color: '#10B981' },
        { area: 'Soporte', cumplimiento: 90, color: '#F59E0B' },
        { area: 'Desarrollo', cumplimiento: 85, color: '#EF4444' },
        { area: 'Recursos Humanos', cumplimiento: 78, color: '#8B5CF6' },
        { area: 'Finanzas', cumplimiento: 80, color: '#F472B6' },
        { area: 'Operaciones', cumplimiento: 74, color: '#F97316' },
      ];
    } else { // año
      data = [
        { area: 'Marketing', cumplimiento: 82, color: '#3B82F6' },
        { area: 'Ventas', cumplimiento: 79, color: '#10B981' },
        { area: 'Soporte', cumplimiento: 91, color: '#F59E0B' },
        { area: 'Desarrollo', cumplimiento: 87, color: '#EF4444' },
        { area: 'Recursos Humanos', cumplimiento: 81, color: '#8B5CF6' },
        { area: 'Finanzas', cumplimiento: 83, color: '#F472B6' },
        { area: 'Operaciones', cumplimiento: 77, color: '#F97316' },
      ];
    }
    // Renderizar con datos simulados
    renderRendimientoChart(data);
  }

    // Inicializar con el valor por defecto
    updateRendimientoChart();
    function showTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
        });
        document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
        });
        document.getElementById(`tab-${tab}`).classList.remove('hidden');
        const activeBtn = document.querySelector(`.tab-button[data-tab="${tab}"]`);
        activeBtn.classList.add('border-blue-500', 'text-blue-600');
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
    }
</script>
@endpush