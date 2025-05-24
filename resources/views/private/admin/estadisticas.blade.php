@extends('layouts.private.admin')

@section('title', 'Estadísticas - Admin')
@section('page-title', 'Estadísticas y Reportes')
@section('page-description', 'Análisis detallado del rendimiento de la empresa')

@section('content')
<div class="p-6">
    <!-- Time Period Selector -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option>Últimos 7 días</option>
                <option>Últimos 30 días</option>
                <option>Últimos 3 meses</option>
                <option>Último año</option>
            </select>
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Exportar Reporte</span>
        </button>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Productividad General</p>
                    <p class="text-2xl font-semibold text-gray-900">94%</p>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 text-sm font-medium">+5.2%</span>
                <span class="text-gray-600 text-sm">vs período anterior</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tareas Completadas</p>
                    <p class="text-2xl font-semibold text-gray-900">1,247</p>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 text-sm font-medium">+12.3%</span>
                <span class="text-gray-600 text-sm">vs período anterior</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tiempo Promedio</p>
                    <p class="text-2xl font-semibold text-gray-900">2.4h</p>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-red-600 text-sm font-medium">-8.1%</span>
                <span class="text-gray-600 text-sm">vs período anterior</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Colaboración</p>
                    <p class="text-2xl font-semibold text-gray-900">87%</p>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 text-sm font-medium">+3.7%</span>
                <span class="text-gray-600 text-sm">vs período anterior</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Performance Chart -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Rendimiento por Área</h3>
            </div>
            <div class="p-6">
                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                    <p class="text-gray-500">Gráfico de rendimiento por área</p>
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Actividad Reciente</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @php
                    $activities = [
                        ['time' => '10:30', 'user' => 'María García', 'action' => 'completó 3 tareas en Desarrollo'],
                        ['time' => '09:15', 'user' => 'Carlos López', 'action' => 'creó nuevo proyecto en Diseño'],
                        ['time' => '08:45', 'user' => 'Ana Martínez', 'action' => 'actualizó métricas de Marketing'],
                        ['time' => '08:20', 'user' => 'Luis Rodríguez', 'action' => 'generó reporte de Ventas']
                    ];
                    @endphp
                    
                    @foreach($activities as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mt-2"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium">{{ $activity['user'] }}</span>
                                {{ $activity['action'] }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Reportes Detallados</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Reporte de Productividad</h4>
                    <p class="text-gray-600 mb-4">Análisis completo del rendimiento por usuario y área</p>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Generar</button>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Reporte de Tiempo</h4>
                    <p class="text-gray-600 mb-4">Distribución de tiempo por proyectos y actividades</p>
                    <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Generar</button>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Reporte de Colaboración</h4>
                    <p class="text-gray-600 mb-4">Métricas de trabajo en equipo y comunicación</p>
                    <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">Generar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection