@extends('layouts.super-admin.super-admin')

@section('title', 'Dashboard Super Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
        <!-- Header del Dashboard -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Super Admin</h1>
                <p class="mt-2 text-gray-600">Panel de control general del sistema</p>
                <div id="realDataIndicator" class="mt-2 text-blue-600 text-sm font-medium">
                    🔄 Cargando datos reales de la API...
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <select id="rangoTiempo" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="generales">Resumen General</option>
                    <option value="mes-actual">Mes Actual</option>
                    <option value="por-mes">Por Mes</option>
                    <option value="por-planes">Por Planes</option>
                </select>
                <button id="btnRefresh" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Empresas --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
            <header class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Empresas</h3>
                <i data-lucide="building" class="w-5 h-5 text-blue-500"></i>
            </header>
            <div class="flex-1">
                <p class="text-3xl font-bold text-gray-900">{{ $estadisticasBasicas['total_empresas'] ?? 0 }}</p>
                <div class="mt-2 flex items-center text-sm">
                    <span class="text-green-600 font-medium">{{ $estadisticasBasicas['empresas_activas'] ?? 0 }} activas</span>
                    <span class="text-gray-500 ml-2">de {{ $estadisticasBasicas['total_empresas'] ?? 0 }} totales</span>
                </div>
            </div>
        </div>

        {{-- Usuarios --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
            <header class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Usuarios</h3>
                <i data-lucide="users" class="w-5 h-5 text-green-500"></i>
            </header>
            <div class="flex-1">
                <p class="text-3xl font-bold text-gray-900">{{ $estadisticasBasicas['usuarios_totales'] ?? 0 }}</p>
                <div class="mt-2 flex items-center text-sm">
                    <span class="text-green-600 font-medium">{{ $estadisticasBasicas['usuarios_activos'] ?? 0 }} activos</span>
                    <span class="text-gray-500 ml-2">de {{ $estadisticasBasicas['usuarios_totales'] ?? 0 }} totales</span>
                </div>
            </div>
        </div>

        {{-- Ingresos Mensuales --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
            <header class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Ingresos del Mes</h3>
                <i data-lucide="dollar-sign" class="w-5 h-5 text-yellow-500"></i>
            </header>
            <div class="flex-1">
                <p id="ingresosMensuales" class="text-3xl font-bold text-gray-900">S/ 0.00</p>
                <div class="mt-2 flex items-center text-sm">
                    <span id="pagosMensuales" class="text-blue-600 font-medium">0 pagos</span>
                    <span id="promedioMensual" class="text-gray-500 ml-2">S/ 0.00 promedio</span>
                </div>
            </div>
        </div>

        {{-- Crecimiento --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
            <header class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Crecimiento</h3>
                <i data-lucide="trending-up" class="w-5 h-5 text-purple-500"></i>
            </header>
            <div class="flex-1">
                <p id="crecimientoValue" class="text-3xl font-bold text-gray-900">0%</p>
                <div class="mt-2 flex items-center text-sm">
                    <span id="crecimientoStatus" class="text-green-600 font-medium">Sin datos</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Content --}}
    <div class="bg-white rounded-lg border border-gray-300">
        <nav class="border-b border-gray-200">
            <div class="flex space-x-8 px-6">
                <button
                    type="button"
                    data-tab="ingresos"
                    class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-blue-500 py-4 px-1 font-medium text-sm text-blue-600 transition"
                    onclick="showTab('ingresos')"
                >
                    <i data-lucide="bar-chart-3" class="w-4 h-4 mr-1"></i>
                    Estadísticas de Ingresos
                </button>

                <button
                    type="button"
                    data-tab="empresas"
                    class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-transparent py-4 px-1 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition"
                    onclick="showTab('empresas')"
                >
                    <i data-lucide="building" class="w-4 h-4 mr-1"></i>
                    Empresas Recientes
                </button>

                <button
                    type="button"
                    data-tab="planes"
                    class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-transparent py-4 px-1 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition"
                    onclick="showTab('planes')"
                >
                    <i data-lucide="package" class="w-4 h-4 mr-1"></i>
                    Estadísticas por Planes
                </button>
            </div>
        </nav>

        {{-- Estadísticas de Ingresos --}}
        <div id="tab-ingresos" class="tab-content p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Gráfico de Ingresos --}}
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4 flex items-center">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 mr-2 text-gray-600"></i>
                        Ingresos por Período
                    </h3>
                    <div class="h-64">
                        <canvas id="ingresosChart"></canvas>
                    </div>
                </div>

                {{-- Resumen de Estadísticas --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-medium flex items-center">
                        <i data-lucide="pie-chart" class="w-5 h-5 mr-2 text-gray-600"></i>
                        Resumen General
                    </h3>
                    
                    <div id="resumenIngresos" class="space-y-3">
                        {{-- Se llenará dinámicamente --}}
                        <div class="text-center text-gray-500 py-8">
                            <i data-lucide="loader" class="w-8 h-8 mx-auto mb-2 animate-spin"></i>
                            <p>Cargando estadísticas...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Empresas Recientes --}}
        <div id="tab-empresas" class="tab-content p-6 hidden">
            <h3 class="text-lg font-medium mb-1">Empresas Registradas Recientemente</h3>
            <p class="text-gray-600 mb-6">Últimas 5 empresas registradas en el sistema (ordenadas por ID)</p>
            <div class="space-y-4">
                @forelse($empresasRecientes as $empresa)
                    <div class="flex items-center justify-between rounded-lg border border-gray-300 p-4">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-sm font-medium">{{ strtoupper(substr($empresa['nombre'], 0, 2)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium">{{ $empresa['nombre'] }}</p>
                                <p class="text-sm text-gray-500">Plan: {{ $empresa['plan'] }} • {{ $empresa['fecha'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $empresa['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $empresa['activo'] ? 'Activa' : 'Inactiva' }}
                            </span>
                            <a href="{{ route('super-admin.empresas.show', $empresa['id']) }}" class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                Ver Detalle
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i data-lucide="building" class="w-12 h-12 mx-auto text-gray-400 mb-3"></i>
                        <p class="text-gray-500">No hay empresas registradas</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Estadísticas por Planes --}}
        <div id="tab-planes" class="tab-content p-6 hidden">
            <h3 class="text-lg font-medium mb-1">Rendimiento por Planes</h3>
            <p class="text-gray-600 mb-6">Estadísticas detalladas de cada plan de suscripción</p>
            
            <div id="estadisticasPlanes" class="space-y-4">
                {{-- Se llenará dinámicamente --}}
                <div class="text-center text-gray-500 py-8">
                    <i data-lucide="loader" class="w-8 h-8 mx-auto mb-2 animate-spin"></i>
                    <p>Cargando estadísticas de planes...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Variables globales
    let ingresosChart;
    const apiBaseUrl = '{{ route("super-admin.dashboard.api.ingresos") }}';
    
    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Iniciando dashboard superadmin...');
        
        initializeCharts();
        
        // Cargar datos iniciales con logging
        console.log('Cargando datos iniciales...');
        loadIngresosData();
        
        // Cargar específicamente datos del mes actual para verificación
        setTimeout(() => {
            console.log('Verificando datos del mes actual...');
            cargarDatos('mes-actual');
        }, 2000);
        
        // Event listeners
        document.getElementById('rangoTiempo').addEventListener('change', function() {
            const tipo = this.value;
            console.log(`Selector cambiado a: ${tipo}`);
            cargarDatos(tipo);
        });
        
        document.getElementById('btnRefresh').addEventListener('click', function() {
            this.classList.add('animate-spin');
            const tipoActual = document.getElementById('rangoTiempo').value;
            console.log(`Refrescando datos tipo: ${tipoActual}`);
            cargarDatos(tipoActual).finally(() => {
                this.classList.remove('animate-spin');
            });
        });
    });

    // Inicializar gráficos
    function initializeCharts() {
        const ctx = document.getElementById('ingresosChart').getContext('2d');
        ingresosChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Ingresos',
                    data: [],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'S/ ' + value.toLocaleString('es-PE');
                            }
                        }
                    }
                }
            }
        });
    }

    // Función específica para cargar datos por tipo
    async function cargarDatos(tipo = 'generales') {
        console.log(`Cargando datos tipo: ${tipo}`);
        
        try {
            const response = await fetch(`${apiBaseUrl}?tipo=${tipo}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log(`Respuesta API ${tipo}:`, result);
            
            if (result.success) {
                updateDashboard(result.data, tipo);
                
                // Mostrar indicador de datos reales
                const indicator = document.getElementById('realDataIndicator');
                if (indicator) {
                    indicator.textContent = `✅ Datos reales (${tipo}) - Última actualización: ${new Date().toLocaleTimeString()}`;
                    indicator.className = 'text-green-600 text-sm font-medium';
                }
            } else {
                console.error(`Error en respuesta ${tipo}:`, result.message);
            }
        } catch (error) {
            console.error(`Error cargando datos ${tipo}:`, error);
        }
    }

    // Cargar datos de ingresos
    async function loadIngresosData() {
        const tipo = document.getElementById('rangoTiempo').value;
        
        try {
            const response = await fetch(`${apiBaseUrl}?tipo=${tipo}`);
            const result = await response.json();
            
            if (result.success) {
                updateDashboard(result.data, tipo);
            } else {
                console.error('Error cargando datos:', result.message);
                showError('Error cargando los datos de ingresos');
            }
        } catch (error) {
            console.error('Error en la petición:', error);
            showError('Error de conexión al cargar los datos');
        }
    }

    // Actualizar dashboard con nuevos datos
    function updateDashboard(data, tipo) {
        switch (tipo) {
            case 'mes-actual':
                updateMesActual(data);
                break;
            case 'por-mes':
                updatePorMes(data);
                break;
            case 'por-planes':
                updatePorPlanes(data);
                break;
            case 'generales':
            default:
                updateGenerales(data);
                break;
        }
    }

    // Actualizar datos generales
    function updateGenerales(data) {
        if (data && data.comparacion_mensual && data.comparacion_mensual.mes_actual) {
            const mesActual = data.comparacion_mensual.mes_actual;
            
            // Actualizar cards principales con datos reales
            const ingresos = parseFloat(mesActual.ingresos) || 0;
            const pagos = parseInt(mesActual.total_pagos) || 0;
            
            document.getElementById('ingresosMensuales').textContent = 
                'S/ ' + ingresos.toLocaleString('es-PE', {minimumFractionDigits: 2});
            document.getElementById('pagosMensuales').textContent = 
                pagos + ' pagos';
            
            const promedio = pagos > 0 ? ingresos / pagos : 0;
            document.getElementById('promedioMensual').textContent = 
                'S/ ' + promedio.toLocaleString('es-PE', {minimumFractionDigits: 2}) + ' promedio';

            // Actualizar crecimiento
            if (data.comparacion_mensual.crecimiento_ingresos) {
                document.getElementById('crecimientoValue').textContent = data.comparacion_mensual.crecimiento_ingresos;
                document.getElementById('crecimientoStatus').textContent = 'vs mes anterior';
            }
        } else if (data && data.total_ingresos) {
            // Fallback para estructura de datos simulados
            const ingresos = data.total_ingresos || 0;
            const pagos = data.total_pagos || 0;
            
            document.getElementById('ingresosMensuales').textContent = 
                'S/ ' + ingresos.toLocaleString('es-PE', {minimumFractionDigits: 2});
            document.getElementById('pagosMensuales').textContent = 
                pagos + ' pagos';
            
            const promedio = pagos > 0 ? ingresos / pagos : 0;
            document.getElementById('promedioMensual').textContent = 
                'S/ ' + promedio.toLocaleString('es-PE', {minimumFractionDigits: 2}) + ' promedio';

            if (data.crecimiento_mensual) {
                document.getElementById('crecimientoValue').textContent = data.crecimiento_mensual + '%';
                document.getElementById('crecimientoStatus').textContent = 'crecimiento mensual';
            }
        }
    }

    // Actualizar datos del mes actual
    function updateMesActual(data) {
        console.log('Datos mes actual recibidos:', data);
        
        // Si los datos vienen con estructura resumen_mes_actual (datos reales de API)
        if (data && data.resumen_mes_actual) {
            const resumen = data.resumen_mes_actual;
            
            console.log('Procesando datos reales del mes actual:', resumen);
            
            // Actualizar cards principales con datos reales
            const ingresos = parseFloat(resumen.ingresos_aprobados) || 0;
            const pagos = parseInt(resumen.pagos_aprobados) || 0;
            
            document.getElementById('ingresosMensuales').textContent = 
                'S/ ' + ingresos.toLocaleString('es-PE', {minimumFractionDigits: 2});
            document.getElementById('pagosMensuales').textContent = 
                pagos + ' pagos';
            
            const promedio = pagos > 0 ? ingresos / pagos : 0;
            document.getElementById('promedioMensual').textContent = 
                'S/ ' + promedio.toLocaleString('es-PE', {minimumFractionDigits: 2}) + ' promedio';
            
            // Actualizar estado con datos reales
            document.getElementById('crecimientoValue').textContent = 'REAL: S/ ' + ingresos.toLocaleString('es-PE');
            document.getElementById('crecimientoStatus').textContent = `${resumen.nombre_mes} ${resumen.año} - Datos reales API`;
            
            // Mostrar resumen de estados si existen datos por plan
            if (data.ingresos_por_plan && data.ingresos_por_plan.length > 0) {
                const resumenContainer = document.getElementById('resumenIngresos');
                if (resumenContainer) {
                    resumenContainer.innerHTML = `
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-green-700">Ingresos Aprobados (REAL)</span>
                                <span class="text-lg font-bold text-green-900">S/ ${ingresos.toLocaleString('es-PE', {minimumFractionDigits: 2})}</span>
                            </div>
                            <div class="text-xs text-green-600 mt-1">${pagos} pagos procesados</div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-blue-700">Planes Activos</span>
                                <span class="text-lg font-bold text-blue-900">${data.ingresos_por_plan.length}</span>
                            </div>
                            <div class="text-xs text-blue-600 mt-1">Tipos de planes con pagos</div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Fuente de Datos</span>
                                <span class="text-lg font-bold text-gray-900">API Real</span>
                            </div>
                            <div class="text-xs text-gray-600 mt-1">Datos en tiempo real</div>
                        </div>
                    `;
                }
            }
            
            // Mostrar planes si existen
            if (data.ingresos_por_plan) {
                const planesContainer = document.getElementById('estadisticasPlanes');
                if (planesContainer) {
                    planesContainer.innerHTML = data.ingresos_por_plan.map(plan => `
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">${plan.plan_nombre}</h4>
                                    <p class="text-sm text-gray-500">S/ ${plan.plan_precio} • Datos Reales</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-gray-900">S/ ${parseFloat(plan.ingresos_plan).toLocaleString('es-PE', {minimumFractionDigits: 2})}</p>
                                    <p class="text-sm text-gray-500">${plan.total_pagos} suscripciones</p>
                                </div>
                            </div>
                            <div class="bg-green-100 rounded-lg p-3 text-center">
                                <p class="text-lg font-bold text-green-600">${plan.total_pagos}</p>
                                <p class="text-xs text-green-700">Pagos reales del API</p>
                            </div>
                        </div>
                    `).join('');
                }
            }
            
        } else if (Array.isArray(data)) {
            // Si es un array de pagos (estructura alternativa)
            let totalIngresos = 0;
            let totalPagos = data.length;
            
            data.forEach(pago => {
                const monto = parseFloat(pago.monto) || 0;
                totalIngresos += monto;
            });
            
            document.getElementById('ingresosMensuales').textContent = 
                'S/ ' + totalIngresos.toLocaleString('es-PE', {minimumFractionDigits: 2});
            document.getElementById('pagosMensuales').textContent = 
                totalPagos + ' pagos';
            
            const promedio = totalPagos > 0 ? totalIngresos / totalPagos : 0;
            document.getElementById('promedioMensual').textContent = 
                'S/ ' + promedio.toLocaleString('es-PE', {minimumFractionDigits: 2}) + ' promedio';
            
            document.getElementById('crecimientoValue').textContent = 'ARRAY: S/ ' + totalIngresos.toLocaleString('es-PE');
            document.getElementById('crecimientoStatus').textContent = 'datos array de API';
        }
    }

    // Actualizar datos por mes
    function updatePorMes(data) {
        if (data && data.ingresos_por_mes) {
            const ingresos = data.ingresos_por_mes;
            
            // Actualizar gráfico
            const labels = ingresos.map(item => item.nombre_mes + ' ' + item.año);
            const valores = ingresos.map(item => item.ingresos_totales || 0);
            
            ingresosChart.data.labels = labels;
            ingresosChart.data.datasets[0].data = valores;
            ingresosChart.update();
            
            // Calcular totales
            const totalIngresos = valores.reduce((sum, val) => sum + val, 0);
            const totalPagos = ingresos.reduce((sum, item) => sum + (item.total_pagos || 0), 0);
            
            document.getElementById('ingresosMensuales').textContent = 
                'S/ ' + totalIngresos.toLocaleString('es-PE', {minimumFractionDigits: 2});
            document.getElementById('pagosMensuales').textContent = totalPagos + ' pagos';
            
            const promedio = totalPagos > 0 ? totalIngresos / totalPagos : 0;
            document.getElementById('promedioMensual').textContent = 
                'S/ ' + promedio.toLocaleString('es-PE', {minimumFractionDigits: 2}) + ' promedio';
        }
    }

    // Actualizar datos por planes
    function updatePorPlanes(data) {
        if (data && data.estadisticas_por_plan) {
            updateEstadisticasPlanes(data);
            
            // Calcular totales de todos los planes
            const planes = data.estadisticas_por_plan;
            const totalIngresos = planes.reduce((sum, plan) => sum + (plan.ingresos_totales || 0), 0);
            const totalPagos = planes.reduce((sum, plan) => sum + (plan.pagos_aprobados || 0), 0);
            
            document.getElementById('ingresosMensuales').textContent = 
                'S/ ' + totalIngresos.toLocaleString('es-PE', {minimumFractionDigits: 2});
            document.getElementById('pagosMensuales').textContent = totalPagos + ' pagos';
            
            const promedio = totalPagos > 0 ? totalIngresos / totalPagos : 0;
            document.getElementById('promedioMensual').textContent = 
                'S/ ' + promedio.toLocaleString('es-PE', {minimumFractionDigits: 2}) + ' promedio';
        }
    }

    // Actualizar resumen de ingresos
    function updateResumenIngresos(data) {
        const container = document.getElementById('resumenIngresos');
        
        if (data && data.resumen_mes_actual) {
            const resumen = data.resumen_mes_actual;
            
            container.innerHTML = `
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-green-700">Ingresos Aprobados</span>
                        <span class="text-lg font-bold text-green-900">S/ ${(resumen.ingresos_aprobados || 0).toLocaleString('es-PE', {minimumFractionDigits: 2})}</span>
                    </div>
                    <div class="text-xs text-green-600 mt-1">${resumen.pagos_aprobados || 0} pagos procesados</div>
                </div>
                
                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-yellow-700">Ingresos Pendientes</span>
                        <span class="text-lg font-bold text-yellow-900">S/ ${(resumen.ingresos_pendientes || 0).toLocaleString('es-PE', {minimumFractionDigits: 2})}</span>
                    </div>
                    <div class="text-xs text-yellow-600 mt-1">${resumen.pagos_pendientes || 0} pagos en proceso</div>
                </div>
                
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-red-700">Ingresos Rechazados</span>
                        <span class="text-lg font-bold text-red-900">S/ ${(resumen.ingresos_rechazados || 0).toLocaleString('es-PE', {minimumFractionDigits: 2})}</span>
                    </div>
                    <div class="text-xs text-red-600 mt-1">${resumen.pagos_rechazados || 0} pagos fallidos</div>
                </div>
            `;
        } else {
            container.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-2"></i>
                    <p>No hay datos disponibles</p>
                </div>
            `;
        }
    }

    // Actualizar estadísticas de planes
    function updateEstadisticasPlanes(data) {
        const container = document.getElementById('estadisticasPlanes');
        
        if (data && data.estadisticas_por_plan && data.estadisticas_por_plan.length > 0) {
            const planes = data.estadisticas_por_plan;
            
            container.innerHTML = planes.map(plan => `
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">${plan.plan_nombre}</h4>
                            <p class="text-sm text-gray-500">S/ ${plan.plan_precio} • ${plan.plan_frecuencia}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">S/ ${(plan.ingresos_totales || 0).toLocaleString('es-PE', {minimumFractionDigits: 2})}</p>
                            <p class="text-sm text-gray-500">${plan.pagos_aprobados || 0} suscripciones</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-lg font-bold text-green-600">${plan.pagos_aprobados || 0}</p>
                            <p class="text-xs text-gray-500">Aprobados</p>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-lg font-bold text-yellow-600">${plan.pagos_pendientes || 0}</p>
                            <p class="text-xs text-gray-500">Pendientes</p>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-lg font-bold text-red-600">${plan.pagos_rechazados || 0}</p>
                            <p class="text-xs text-gray-500">Rechazados</p>
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <i data-lucide="package-x" class="w-8 h-8 mx-auto mb-2"></i>
                    <p>No hay datos de planes disponibles</p>
                </div>
            `;
        }
    }

    // Mostrar error
    function showError(message) {
        // Aquí podrías implementar un sistema de notificaciones
        console.error(message);
    }

    // Cambiar tabs
    function showTab(tab) {
        // Ocultar todos los tabs
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
        });
        
        // Resetear estilos de botones
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Mostrar tab seleccionado
        document.getElementById(`tab-${tab}`).classList.remove('hidden');
        
        // Activar botón seleccionado
        const activeBtn = document.querySelector(`.tab-button[data-tab="${tab}"]`);
        activeBtn.classList.add('border-blue-500', 'text-blue-600');
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        
        // Si es el tab de planes, cargar datos específicos
        if (tab === 'planes') {
            loadPlanesData();
        }
    }

    // Cargar datos específicos de planes
    async function loadPlanesData() {
        try {
            const response = await fetch(`${apiBaseUrl}?tipo=por-planes`);
            const result = await response.json();
            
            if (result.success) {
                updateEstadisticasPlanes(result.data);
            }
        } catch (error) {
            console.error('Error cargando datos de planes:', error);
        }
    }
</script>
@endpush