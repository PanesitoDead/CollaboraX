<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ingresos - CollaboraX</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #059669;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .summary {
            background-color: #f0fdf4;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #059669;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        .summary-item {
            margin-bottom: 5px;
        }
        .summary-item strong {
            color: #374151;
        }
        .currency {
            color: #059669;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0fdf4;
            font-weight: bold;
            color: #065f46;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .config-info {
            background-color: #eff6ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #2563eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CollaboraX - Reporte de Ingresos</h1>
        <p>Sistema de Gestión Empresarial</p>
        <p>Generado el: {{ $data['fecha_generacion'] ?? now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @if(isset($data['configuracion']))
    <div class="config-info">
        <h4 style="margin-top: 0; color: #1e40af;">Configuración del Reporte</h4>
        <p style="margin: 0;">
            <strong>Formato:</strong> {{ ucfirst($data['configuracion']['formato'] ?? 'tabla') }} | 
            <strong>Período:</strong> {{ ucfirst(str_replace('-', ' ', $data['configuracion']['periodo'] ?? 'mes-actual')) }} | 
            <strong>Agrupación:</strong> {{ ucfirst($data['configuracion']['agrupacion'] ?? $agrupacion) }}
        </p>
    </div>
    @endif

    @if(isset($data['resumen_periodo']))
    <div class="summary">
        <h3 style="margin-top: 0; color: #065f46;">Resumen del Período</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <strong>Total Transacciones:</strong><br>
                {{ $data['resumen_periodo']['total_transacciones_periodo'] ?? 0 }}
            </div>
            <div class="summary-item">
                <strong>Pagos Aprobados:</strong><br>
                {{ $data['resumen_periodo']['total_pagos_aprobados'] ?? 0 }}
            </div>
            <div class="summary-item">
                <strong>Ingresos Totales:</strong><br>
                <span class="currency">${{ $data['resumen_periodo']['ingresos_totales_periodo'] ?? '0.00' }}</span>
            </div>
            @if(isset($data['resumen_periodo']['ticket_promedio_periodo']))
            <div class="summary-item">
                <strong>Ticket Promedio:</strong><br>
                <span class="currency">${{ $data['resumen_periodo']['ticket_promedio_periodo'] }}</span>
            </div>
            @endif
            @if(isset($data['resumen_periodo']['usuarios_unicos_periodo']))
            <div class="summary-item">
                <strong>Usuarios Únicos:</strong><br>
                {{ $data['resumen_periodo']['usuarios_unicos_periodo'] }}
            </div>
            @endif
        </div>
    </div>
    @endif

    <h3 style="color: #374151; margin-bottom: 10px;">
        Detalle de Ingresos 
        @if($agrupacion === 'plan')
            por Plan
        @elseif($agrupacion === 'fecha')
            por Fecha
        @elseif($agrupacion === 'mes')
            por Mes
        @else
            por {{ ucfirst($agrupacion) }}
        @endif
    </h3>

    <table>
        <thead>
            <tr>
                @if($agrupacion === 'plan')
                    <th style="width: 20%;">Plan</th>
                    <th style="width: 12%;">Precio</th>
                    <th style="width: 12%;">Frecuencia</th>
                    <th style="width: 10%;">Trans.</th>
                    <th style="width: 10%;">Aprob.</th>
                    <th style="width: 10%;">Pend.</th>
                    <th style="width: 10%;">Rechaz.</th>
                    <th style="width: 16%;">Ingresos</th>
                @else
                    <th style="width: 20%;">
                        @if($agrupacion === 'fecha')
                            Fecha
                        @elseif($agrupacion === 'mes')
                            Mes
                        @else
                            {{ ucfirst($agrupacion) }}
                        @endif
                    </th>
                    <th style="width: 12%;">Trans. Total</th>
                    <th style="width: 12%;">Aprobados</th>
                    <th style="width: 12%;">Pendientes</th>
                    <th style="width: 12%;">Rechazados</th>
                    <th style="width: 16%;">Ingresos Aprob.</th>
                    <th style="width: 16%;">Ticket Promedio</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if(isset($data['filas']) && count($data['filas']) > 0)
                @foreach($data['filas'] as $fila)
                    <tr>
                        @if($agrupacion === 'plan')
                            <td>{{ $fila['plan_nombre'] ?? 'N/A' }}</td>
                            <td class="text-right currency">${{ $fila['plan_precio'] ?? '0.00' }}</td>
                            <td>{{ ucfirst($fila['plan_frecuencia'] ?? 'N/A') }}</td>
                            <td class="text-right">{{ $fila['total_transacciones'] ?? 0 }}</td>
                            <td class="text-right">{{ $fila['pagos_aprobados'] ?? 0 }}</td>
                            <td class="text-right">{{ $fila['pagos_pendientes'] ?? 0 }}</td>
                            <td class="text-right">{{ $fila['pagos_rechazados'] ?? 0 }}</td>
                            <td class="text-right currency">${{ $fila['ingresos_aprobados'] ?? '0.00' }}</td>
                        @else
                            <td>
                                @if($agrupacion === 'fecha' && isset($fila['fecha']))
                                    {{ \Carbon\Carbon::parse($fila['fecha'])->format('d/m/Y') }}
                                    @if(isset($fila['dia_semana']))
                                        <br><small>{{ $fila['dia_semana'] }}</small>
                                    @endif
                                @else
                                    {{ $fila['fecha'] ?? $fila['mes'] ?? $fila[$agrupacion] ?? 'N/A' }}
                                @endif
                            </td>
                            <td class="text-right">{{ $fila['total_transacciones'] ?? 0 }}</td>
                            <td class="text-right">{{ $fila['pagos_aprobados'] ?? 0 }}</td>
                            <td class="text-right">{{ $fila['pagos_pendientes'] ?? 0 }}</td>
                            <td class="text-right">{{ $fila['pagos_rechazados'] ?? 0 }}</td>
                            <td class="text-right currency">${{ $fila['ingresos_aprobados'] ?? '0.00' }}</td>
                            <td class="text-right currency">${{ $fila['ticket_promedio'] ?? '0.00' }}</td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="{{ $agrupacion === 'plan' ? '8' : '7' }}" style="text-align: center; color: #666; font-style: italic;">
                        No hay datos disponibles para el período seleccionado
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(isset($data['metadatos_tabla']) && isset($data['metadatos_tabla']['total_filas']))
    <div style="margin-top: 20px; padding: 10px; background-color: #f8fafc; border-radius: 5px;">
        <p style="margin: 0; color: #374151;">
            <strong>Total de registros:</strong> {{ $data['metadatos_tabla']['total_filas'] }} | 
            <strong>Columnas:</strong> {{ $data['metadatos_tabla']['total_columnas'] ?? 'N/A' }}
        </p>
    </div>
    @endif

    <div class="footer">
        <p>CollaboraX - Sistema de Gestión Empresarial | Reporte de Ingresos | Página {PAGE_NUM} de {PAGE_COUNT}</p>
    </div>
</body>
</html>
