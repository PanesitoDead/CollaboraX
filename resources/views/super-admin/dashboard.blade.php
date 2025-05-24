@extends('layouts.super-admin.super-admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Empresas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalEmpresas ?? 156 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Usuarios Activos</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $usuariosActivos ?? 2847 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Ingresos Mensuales</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($ingresosMensuales ?? 89750) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Crecimiento</p>
                    <p class="text-2xl font-semibold text-gray-900">+{{ $crecimiento ?? 23.5 }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ingresos por Mes</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <p class="text-gray-500">Gráfico de ingresos aquí</p>
            </div>
        </div>

        <!-- Recent Companies -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Empresas Recientes</h3>
            <div class="space-y-4">
                @foreach($empresasRecientes ?? [
                    ['nombre' => 'TechCorp Solutions', 'plan' => 'Enterprise', 'fecha' => '2024-01-15'],
                    ['nombre' => 'Digital Innovations', 'plan' => 'Professional', 'fecha' => '2024-01-14'],
                    ['nombre' => 'StartUp Hub', 'plan' => 'Basic', 'fecha' => '2024-01-13'],
                    ['nombre' => 'Global Systems', 'plan' => 'Enterprise', 'fecha' => '2024-01-12']
                ] as $empresa)
                <div class="flex items-center justify-between py-2">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $empresa['nombre'] }}</p>
                        <p class="text-xs text-gray-500">Plan {{ $empresa['plan'] }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $empresa['fecha'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Estado del Sistema</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                <span class="text-sm text-gray-600">Servidores: Operativo</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                <span class="text-sm text-gray-600">Base de Datos: Operativo</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-yellow-400 rounded-full mr-3"></div>
                <span class="text-sm text-gray-600">API: Mantenimiento</span>
            </div>
        </div>
    </div>
</div>
@endsection