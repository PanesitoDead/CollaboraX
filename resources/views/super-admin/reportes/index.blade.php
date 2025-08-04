@extends('layouts.super-admin.super-admin')

@section('title', 'Reportes - Super Admin')
@section('page-title', 'Reportes')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Reportes del Sistema</h1>
                <p class="mt-2 text-gray-600">Genera y visualiza reportes detallados del sistema</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <button onclick="refreshAllData()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150">
                    <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-2"></i>
                    Actualizar Datos
                </button>
            </div>
        </div>
    </div>

    {{-- Resumen Básico --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="building" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Empresas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $resumenBasico['total_empresas'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Empresas Activas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $resumenBasico['empresas_activas'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Usuarios</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $resumenBasico['usuarios_totales'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i data-lucide="user-check" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Usuarios Activos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $resumenBasico['usuarios_activos'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Secciones de Reportes --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Reporte de Usuarios --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Reporte de Usuarios</h3>
                <i data-lucide="users" class="w-5 h-5 text-gray-400"></i>
            </div>
            <p class="text-gray-600 mb-4">Lista detallada de todos los usuarios del sistema con filtros de período</p>
            
            <div class="space-y-3 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                    <select id="usuariosPeriodo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="mes-actual">Mes Actual</option>
                        <option value="ultimos-3-meses">Últimos 3 Meses</option>
                        <option value="ultimos-6-meses">Últimos 6 Meses</option>
                        <option value="anual">Año Actual</option>
                        <option value="todo">Todos los Tiempos</option>
                    </select>
                </div>
            </div>

            <div class="flex space-x-2">
                <button onclick="generarReporteUsuarios('tabla')" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-150">
                    <i data-lucide="eye" class="w-4 h-4 inline mr-2"></i>
                    Ver Reporte
                </button>
                <button onclick="generarReporteUsuarios('pdf')" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-150">
                    <i data-lucide="download" class="w-4 h-4 inline mr-2"></i>
                    Descargar PDF
                </button>
            </div>
        </div>

        {{-- Reporte de Ingresos --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Reporte de Ingresos</h3>
                <i data-lucide="dollar-sign" class="w-5 h-5 text-gray-400"></i>
            </div>
            <p class="text-gray-600 mb-4">Análisis detallado de ingresos por fechas y planes de suscripción</p>
            
            <div class="space-y-3 mb-4">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                        <select id="ingresosPeriodo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="mes-actual">Mes Actual</option>
                            <option value="ultimos-3-meses">Últimos 3 Meses</option>
                            <option value="ultimos-6-meses">Últimos 6 Meses</option>
                            <option value="anual">Año Actual</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Agrupar por</label>
                        <select id="ingresosAgrupacion" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="fecha">Fecha</option>
                            <option value="plan">Plan</option>
                            <option value="estado">Estado</option>
                            <option value="mes">Mes</option>
                        </select>
                    </div>
                </div>
                <div id="fechasPersonalizadas" class="hidden grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                        <input type="date" id="fechaInicio" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                        <input type="date" id="fechaFin" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="flex space-x-2">
                <button onclick="generarReporteIngresos('tabla')" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-150">
                    <i data-lucide="eye" class="w-4 h-4 inline mr-2"></i>
                    Ver Reporte
                </button>
                <button onclick="generarReporteIngresos('pdf')" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-150">
                    <i data-lucide="download" class="w-4 h-4 inline mr-2"></i>
                    Descargar PDF
                </button>
            </div>
        </div>

        {{-- Reporte de Transacciones --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Reporte de Transacciones</h3>
                <i data-lucide="credit-card" class="w-5 h-5 text-gray-400"></i>
            </div>
            <p class="text-gray-600 mb-4">Listado detallado de todas las transacciones para exportación</p>
            
            <div class="space-y-3 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Límite de registros</label>
                    <select id="transaccionesLimite" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="10">10 registros</option>
                        <option value="25">25 registros</option>
                        <option value="50">50 registros</option>
                        <option value="100">100 registros</option>
                        <option value="250">250 registros</option>
                    </select>
                </div>
            </div>

            <div class="flex space-x-2">
                <button onclick="generarReporteTransacciones('tabla')" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition duration-150">
                    <i data-lucide="eye" class="w-4 h-4 inline mr-2"></i>
                    Ver Reporte
                </button>
                <button onclick="generarReporteTransacciones('pdf')" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-150">
                    <i data-lucide="download" class="w-4 h-4 inline mr-2"></i>
                    Descargar PDF
                </button>
            </div>
        </div>

        {{-- Reporte de Rendimiento de Planes --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Rendimiento de Planes</h3>
                <i data-lucide="trending-up" class="w-5 h-5 text-gray-400"></i>
            </div>
            <p class="text-gray-600 mb-4">Análisis completo del rendimiento y conversión de todos los planes</p>
            
            <div class="mb-4">
                <div class="bg-blue-50 p-3 rounded-md">
                    <p class="text-sm text-blue-800">
                        <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                        Este reporte incluye tasas de conversión, ingresos por plan y análisis de rendimiento
                    </p>
                </div>
            </div>

            <div class="flex space-x-2">
                <button onclick="generarReporteRendimiento('tabla')" class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition duration-150">
                    <i data-lucide="eye" class="w-4 h-4 inline mr-2"></i>
                    Ver Reporte
                </button>
                <button onclick="generarReporteRendimiento('pdf')" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-150">
                    <i data-lucide="download" class="w-4 h-4 inline mr-2"></i>
                    Descargar PDF
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Modal para mostrar reportes --}}
<div id="reporteModal" class="fixed inset-0 hidden z-50" style="background-color: rgba(0, 0, 0, 0.5) !important;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-hidden shadow-xl">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 id="reporteModalTitle" class="text-lg font-semibold text-gray-900">Reporte</h3>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <div id="reporteModalContent" class="p-6 overflow-auto max-h-[calc(90vh-140px)]">
                <!-- Contenido del reporte se carga aquí -->
            </div>
        </div>
    </div>
</div>

{{-- Loading Overlay --}}
<div id="loadingOverlay" class="fixed inset-0 hidden z-40" style="background-color: rgba(0, 0, 0, 0.5) !important;">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-900">Generando reporte...</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Función para mostrar/ocultar fechas personalizadas
    document.getElementById('ingresosPeriodo').addEventListener('change', function() {
        const fechasDiv = document.getElementById('fechasPersonalizadas');
        if (this.value === 'personalizado') {
            fechasDiv.classList.remove('hidden');
        } else {
            fechasDiv.classList.add('hidden');
        }
    });

    // Función para mostrar loading
    function showLoading() {
        document.getElementById('loadingOverlay').classList.remove('hidden');
    }

    // Función para ocultar loading
    function hideLoading() {
        document.getElementById('loadingOverlay').classList.add('hidden');
    }

    // Función para mostrar modal
    function mostrarModal(titulo, contenido) {
        document.getElementById('reporteModalTitle').textContent = titulo;
        document.getElementById('reporteModalContent').innerHTML = contenido;
        document.getElementById('reporteModal').classList.remove('hidden');
    }

    // Función para cerrar modal
    function cerrarModal() {
        document.getElementById('reporteModal').classList.add('hidden');
    }

    // Función para generar reporte de usuarios
    async function generarReporteUsuarios(tipo) {
        const periodo = document.getElementById('usuariosPeriodo').value;
        
        if (tipo === 'pdf') {
            showLoading();
            window.location.href = `{{ route('super-admin.reportes.usuarios') }}?periodo=${periodo}&export=pdf`;
            setTimeout(hideLoading, 2000);
            return;
        }

        showLoading();
        try {
            const response = await fetch(`{{ route('super-admin.reportes.usuarios') }}?periodo=${periodo}`);
            const result = await response.json();
            
            if (result.success) {
                const contenido = generarTablaUsuarios(result.data);
                mostrarModal('Reporte de Usuarios', contenido);
            } else {
                alert('Error al generar el reporte: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar el reporte');
        } finally {
            hideLoading();
        }
    }

    // Función para generar reporte de ingresos
    async function generarReporteIngresos(tipo) {
        const periodo = document.getElementById('ingresosPeriodo').value;
        const agrupacion = document.getElementById('ingresosAgrupacion').value;
        let url = `{{ route('super-admin.reportes.ingresos') }}?periodo=${periodo}&agrupacion=${agrupacion}`;
        
        if (periodo === 'personalizado') {
            const fechaInicio = document.getElementById('fechaInicio').value;
            const fechaFin = document.getElementById('fechaFin').value;
            if (!fechaInicio || !fechaFin) {
                alert('Por favor selecciona las fechas de inicio y fin');
                return;
            }
            url += `&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
        }

        if (tipo === 'pdf') {
            showLoading();
            window.location.href = url + '&export=pdf';
            setTimeout(hideLoading, 2000);
            return;
        }

        showLoading();
        try {
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
                const contenido = generarTablaIngresos(result.data, agrupacion);
                mostrarModal('Reporte de Ingresos', contenido);
            } else {
                alert('Error al generar el reporte: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar el reporte');
        } finally {
            hideLoading();
        }
    }

    // Función para generar reporte de transacciones
    async function generarReporteTransacciones(tipo) {
        const limite = document.getElementById('transaccionesLimite').value;
        let url = `{{ route('super-admin.reportes.transacciones') }}?limite=${limite}`;

        if (tipo === 'pdf') {
            showLoading();
            window.location.href = url + '&export=pdf';
            setTimeout(hideLoading, 2000);
            return;
        }

        showLoading();
        try {
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
                const contenido = generarTablaTransacciones(result.data);
                mostrarModal('Reporte de Transacciones', contenido);
            } else {
                alert('Error al generar el reporte: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar el reporte');
        } finally {
            hideLoading();
        }
    }

    // Función para generar reporte de rendimiento
    async function generarReporteRendimiento(tipo) {
        let url = `{{ route('super-admin.reportes.rendimiento-planes') }}`;

        if (tipo === 'pdf') {
            showLoading();
            window.location.href = url + '?export=pdf';
            setTimeout(hideLoading, 2000);
            return;
        }

        showLoading();
        try {
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
                const contenido = generarTablaRendimiento(result.data);
                mostrarModal('Reporte de Rendimiento de Planes', contenido);
            } else {
                alert('Error al generar el reporte: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar el reporte');
        } finally {
            hideLoading();
        }
    }

    // Función para generar tabla de usuarios
    function generarTablaUsuarios(data) {
        let html = `
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    <strong>Total de usuarios:</strong> ${data.total_usuarios} | 
                    <strong>Período:</strong> ${data.periodo} | 
                    <strong>Generado:</strong> ${data.fecha_generacion}
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
        `;

        data.usuarios.forEach(usuario => {
            html += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${usuario.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${usuario.nombre}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${usuario.correo}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${usuario.rol}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${usuario.empresa}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${usuario.activo === 'Activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${usuario.activo}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${usuario.fecha_registro}</td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        return html;
    }

    // Función para generar tabla de ingresos
    function generarTablaIngresos(data, agrupacion) {
        let html = `
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    <strong>Configuración:</strong> ${data.configuracion?.formato || 'tabla'} - ${data.configuracion?.periodo || 'mes-actual'} - ${data.configuracion?.agrupacion || agrupacion} | 
                    <strong>Generado:</strong> ${data.fecha_generacion || 'Ahora'}
                </p>
            </div>
        `;

        // Mostrar resumen si está disponible
        if (data.resumen_periodo) {
            html += `
                <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-900 mb-2">Resumen del Período</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-blue-700">Total Transacciones:</span>
                            <span class="font-medium">${data.resumen_periodo.total_transacciones_periodo || 0}</span>
                        </div>
                        <div>
                            <span class="text-blue-700">Pagos Aprobados:</span>
                            <span class="font-medium">${data.resumen_periodo.total_pagos_aprobados || 0}</span>
                        </div>
                        <div>
                            <span class="text-blue-700">Ingresos Totales:</span>
                            <span class="font-medium">$${data.resumen_periodo.ingresos_totales_periodo || '0.00'}</span>
                        </div>
                        <div>
                            <span class="text-blue-700">Ticket Promedio:</span>
                            <span class="font-medium">$${data.resumen_periodo.ticket_promedio_periodo || '0.00'}</span>
                        </div>
                    </div>
                </div>
            `;
        }

        html += `
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
        `;

        // Headers dinámicos basados en la agrupación
        if (agrupacion === 'plan') {
            html += `
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frecuencia</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transacciones</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aprobados</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasa Aprob.</th>
            `;
        } else {
            html += `
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transacciones</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aprobados</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket Prom.</th>
            `;
        }

        html += `
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
        `;

        if (data.filas && data.filas.length > 0) {
            data.filas.forEach(fila => {
                html += '<tr>';
                if (agrupacion === 'plan') {
                    html += `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${fila.plan_nombre || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${fila.plan_precio || '0.00'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${fila.plan_frecuencia || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${fila.total_transacciones || 0}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${fila.pagos_aprobados || 0}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${fila.ingresos_aprobados || '0.00'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${fila.tasa_aprobacion || '0.00'}%</td>
                    `;
                } else {
                    html += `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${fila.fecha || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${fila.total_transacciones || 0}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${fila.pagos_aprobados || 0}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${fila.ingresos_aprobados || '0.00'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${fila.ticket_promedio || '0.00'}</td>
                    `;
                }
                html += '</tr>';
            });
        } else {
            html += `
                <tr>
                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        No hay datos disponibles para el período seleccionado
                    </td>
                </tr>
            `;
        }

        html += `
                    </tbody>
                </table>
            </div>
        `;

        return html;
    }

    // Función para generar tabla de transacciones
    function generarTablaTransacciones(data) {
        let html = `
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    <strong>Total transacciones:</strong> ${data.total_transacciones || 0} | 
                    <strong>Generado:</strong> ${data.fecha_generacion || 'Ahora'}
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pago</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
        `;

        if (data.transacciones && data.transacciones.length > 0) {
            data.transacciones.forEach(transaccion => {
                let estadoClass = 'bg-gray-100 text-gray-800';
                if (transaccion.estado === 'approved') {
                    estadoClass = 'bg-green-100 text-green-800';
                } else if (transaccion.estado === 'pending') {
                    estadoClass = 'bg-yellow-100 text-yellow-800';
                } else if (transaccion.estado === 'rejected') {
                    estadoClass = 'bg-red-100 text-red-800';
                }

                html += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${transaccion.pago_id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${transaccion.usuario_id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${transaccion.plan_nombre}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${transaccion.monto}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${estadoClass}">
                                ${transaccion.estado}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${transaccion.metodo_pago}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${transaccion.fecha_pago}</td>
                    </tr>
                `;
            });
        } else {
            html += `
                <tr>
                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        No hay transacciones disponibles
                    </td>
                </tr>
            `;
        }

        html += `
                    </tbody>
                </table>
            </div>
        `;

        return html;
    }

    // Función para generar tabla de rendimiento
    function generarTablaRendimiento(data) {
        let html = `
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    <strong>Generado:</strong> ${data.fecha_generacion || 'Ahora'}
                </p>
            </div>
        `;

        // Mostrar resumen general si está disponible
        if (data.resumen_general) {
            html += `
                <div class="mb-6 bg-green-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-green-900 mb-2">Resumen General</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-green-700">Planes Activos:</span>
                            <span class="font-medium">${data.resumen_general.total_planes_activos || 0}</span>
                        </div>
                        <div>
                            <span class="text-green-700">Mejor Conversión:</span>
                            <span class="font-medium">${data.resumen_general.plan_mejor_conversion?.plan_nombre || 'N/A'} (${data.resumen_general.plan_mejor_conversion?.tasa_conversion || '0'}%)</span>
                        </div>
                        <div>
                            <span class="text-green-700">Más Rentable:</span>
                            <span class="font-medium">${data.resumen_general.plan_mas_rentable?.plan_nombre || 'N/A'} ($${data.resumen_general.plan_mas_rentable?.ingresos_totales || '0.00'})</span>
                        </div>
                    </div>
                </div>
            `;
        }

        html += `
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intentos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exitosos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conversión</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuarios</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
        `;

        if (data.planes_rendimiento && data.planes_rendimiento.length > 0) {
            data.planes_rendimiento.forEach(plan => {
                html += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${plan.plan_nombre}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${plan.plan_precio}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${plan.total_intentos_pago || 0}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${plan.pagos_exitosos || 0}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${plan.tasa_conversion || '0.00'}%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${plan.ingresos_totales || '0.00'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${plan.usuarios_unicos || 0}</td>
                    </tr>
                `;
            });
        } else {
            html += `
                <tr>
                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        No hay datos de rendimiento disponibles
                    </td>
                </tr>
            `;
        }

        html += `
                    </tbody>
                </table>
            </div>
        `;

        return html;
    }

    // Función para refrescar todos los datos
    function refreshAllData() {
        location.reload();
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('reporteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });
</script>
@endpush
