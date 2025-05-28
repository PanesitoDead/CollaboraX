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
                class="tab-button inline-flex items-center whitespace-nowrap border-b-2 py-4 px-1 font-medium text-sm transition"
            >
                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                Coordinadores
            </button>

            <button
                type="button"
                data-tab="areas"
                class="tab-button inline-flex items-center whitespace-nowrap border-b-2 py-4 px-1 font-medium text-sm transition"
            >
                <i data-lucide="layers" class="w-4 h-4 mr-1"></i>
                Áreas
            </button>

            <button
                type="button"
                data-tab="rendimiento"
                class="tab-button inline-flex items-center whitespace-nowrap border-b-2 py-4 px-1 font-medium text-sm transition"
            >
                <i data-lucide="bar-chart-2" class="w-4 h-4 mr-1"></i>
                Rendimiento
            </button>
            </div>
        </nav>

        {{-- Coordinadores --}}
        <div id="tab-coordinadores" class="tab-content p-6 hidden">
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
        <div id="tab-rendimiento" class="tab-content p-6 hidden">
            <h3 class="text-lg font-medium mb-1">Rendimiento por Área</h3>
            <p class="text-gray-600 mb-6">Métricas de cumplimiento y productividad</p>

            <div class="h-96 flex items-center justify-center bg-gray-50 rounded-lg">
            <p class="text-gray-500">Aquí iría el gráfico de rendimiento por área</p>
            </div>
        </div>
        </div>
</div>
@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', () => {
        // lucide.replace();

        const buttons = document.querySelectorAll('.tab-button');
        const contents = document.querySelectorAll('.tab-content');

        function activateTab(tab) {
            buttons.forEach(btn => {
                const isActive = btn.dataset.tab === tab;
                btn.classList.toggle('border-blue-600', isActive);
                btn.classList.toggle('text-blue-600', isActive);
                btn.classList.toggle('border-transparent', !isActive);
                btn.classList.toggle('text-gray-500', !isActive);
            });
            contents.forEach(content => {
                content.classList.toggle('hidden', content.id !== `tab-${tab}`);
            });
        }

        buttons.forEach(btn => {
            btn.addEventListener('click', () => activateTab(btn.dataset.tab));
        });

        // Activar primer tab por defecto
        activateTab(buttons[0].dataset.tab);
    });
</script>
@endpush