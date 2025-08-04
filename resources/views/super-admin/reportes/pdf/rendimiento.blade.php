<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Rendimiento de Planes - CollaboraX</title>
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
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #f59e0b;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .summary {
            background-color: #fffbeb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #f59e0b;
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
        .highlight {
            color: #f59e0b;
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
            background-color: #fffbeb;
            font-weight: bold;
            color: #92400e;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .currency {
            color: #059669;
            font-weight: bold;
        }
        .percentage {
            color: #2563eb;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        .metric-good {
            color: #059669;
        }
        .metric-warning {
            color: #d97706;
        }
        .metric-bad {
            color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CollaboraX - Rendimiento de Planes</h1>
        <p>Sistema de Gestión Empresarial</p>
        <p>Generado el: {{ $data['fecha_generacion'] ?? now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @if(isset($data['resumen_general']))
    <div class="summary">
        <h3 style="margin-top: 0; color: #92400e;">Resumen General del Rendimiento</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <strong>Planes Activos:</strong><br>
                <span class="highlight">{{ $data['resumen_general']['total_planes_activos'] ?? 0 }}</span>
            </div>
            @if(isset($data['resumen_general']['plan_mejor_conversion']))
            <div class="summary-item">
                <strong>Mejor Conversión:</strong><br>
                {{ $data['resumen_general']['plan_mejor_conversion']['plan_nombre'] ?? 'N/A' }}<br>
                <span class="percentage">{{ $data['resumen_general']['plan_mejor_conversion']['tasa_conversion'] ?? '0.00' }}%</span>
            </div>
            @endif
            @if(isset($data['resumen_general']['plan_mas_rentable']))
            <div class="summary-item">
                <strong>Más Rentable:</strong><br>
                {{ $data['resumen_general']['plan_mas_rentable']['plan_nombre'] ?? 'N/A' }}<br>
                <span class="currency">${{ $data['resumen_general']['plan_mas_rentable']['ingresos_totales'] ?? '0.00' }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <h3 style="color: #374151; margin-bottom: 10px;">Análisis Detallado por Plan</h3>

    <table>
        <thead>
            <tr>
                <th style="width: 18%;">Plan</th>
                <th style="width: 10%;">Precio</th>
                <th style="width: 8%;">Intentos</th>
                <th style="width: 8%;">Exitosos</th>
                <th style="width: 8%;">Fallidos</th>
                <th style="width: 10%;">Conversión</th>
                <th style="width: 12%;">Ingresos</th>
                <th style="width: 8%;">Usuarios</th>
                <th style="width: 10%;">Valor/Usuario</th>
                <th style="width: 8%;">Días Activo</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data['planes_rendimiento']) && count($data['planes_rendimiento']) > 0)
                @foreach($data['planes_rendimiento'] as $plan)
                    <tr>
                        <td>
                            <strong>{{ $plan['plan_nombre'] ?? 'N/A' }}</strong>
                            @if(isset($plan['plan_frecuencia']))
                                <br><small>{{ ucfirst($plan['plan_frecuencia']) }}</small>
                            @endif
                        </td>
                        <td class="text-right currency">${{ $plan['plan_precio'] ?? '0.00' }}</td>
                        <td class="text-center">{{ $plan['total_intentos_pago'] ?? 0 }}</td>
                        <td class="text-center metric-good">{{ $plan['pagos_exitosos'] ?? 0 }}</td>
                        <td class="text-center metric-bad">{{ $plan['pagos_fallidos'] ?? 0 }}</td>
                        <td class="text-center">
                            @php
                                $conversion = $plan['tasa_conversion'] ?? null;
                                $conversionClass = '';
                                if ($conversion !== null) {
                                    if ($conversion >= 80) {
                                        $conversionClass = 'metric-good';
                                    } elseif ($conversion >= 50) {
                                        $conversionClass = 'metric-warning';
                                    } else {
                                        $conversionClass = 'metric-bad';
                                    }
                                }
                            @endphp
                            @if($conversion !== null)
                                <span class="percentage {{ $conversionClass }}">{{ $conversion }}%</span>
                            @else
                                <span style="color: #666;">-</span>
                            @endif
                        </td>
                        <td class="text-right currency">${{ $plan['ingresos_totales'] ?? '0.00' }}</td>
                        <td class="text-center">{{ $plan['usuarios_unicos'] ?? 0 }}</td>
                        <td class="text-right currency">
                            @if(isset($plan['valor_por_usuario']) && $plan['valor_por_usuario'] > 0)
                                ${{ number_format($plan['valor_por_usuario'], 2) }}
                            @else
                                $0.00
                            @endif
                        </td>
                        <td class="text-center">{{ $plan['dias_con_ventas'] ?? 0 }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="10" class="no-data">
                        No hay datos de rendimiento disponibles en este momento
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(isset($data['planes_rendimiento']) && count($data['planes_rendimiento']) > 0)
    <div style="margin-top: 30px;">
        <h4 style="color: #374151; margin-bottom: 15px;">Métricas Adicionales de Rendimiento</h4>
        
        <table style="font-size: 11px;">
            <thead>
                <tr>
                    <th style="width: 20%;">Plan</th>
                    <th style="width: 12%;">Tasa Rechazo</th>
                    <th style="width: 15%;">Ingresos en Riesgo</th>
                    <th style="width: 12%;">Primera Venta</th>
                    <th style="width: 12%;">Última Venta</th>
                    <th style="width: 12%;">Ingresos/Día</th>
                    <th style="width: 17%;">Frecuencia Venta (días)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['planes_rendimiento'] as $plan)
                    <tr>
                        <td><strong>{{ $plan['plan_nombre'] ?? 'N/A' }}</strong></td>
                        <td class="text-center">
                            @if(isset($plan['tasa_rechazo']) && $plan['tasa_rechazo'] !== null)
                                <span class="percentage metric-bad">{{ $plan['tasa_rechazo'] }}%</span>
                            @else
                                <span style="color: #666;">-</span>
                            @endif
                        </td>
                        <td class="text-right currency">
                            @if(isset($plan['ingresos_en_riesgo']))
                                ${{ $plan['ingresos_en_riesgo'] }}
                            @else
                                $0.00
                            @endif
                        </td>
                        <td class="text-center">
                            @if(isset($plan['primera_venta']) && $plan['primera_venta'])
                                {{ \Carbon\Carbon::parse($plan['primera_venta'])->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if(isset($plan['ultima_venta']) && $plan['ultima_venta'])
                                {{ \Carbon\Carbon::parse($plan['ultima_venta'])->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right currency">
                            @if(isset($plan['ingresos_por_dia']) && $plan['ingresos_por_dia'] > 0)
                                ${{ number_format($plan['ingresos_por_dia'], 2) }}
                            @else
                                $0.00
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $plan['frecuencia_venta'] ?? 0 }} días
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div style="margin-top: 20px; padding: 15px; background-color: #f8fafc; border-radius: 5px; border-left: 4px solid #2563eb;">
        <h4 style="margin-top: 0; color: #1e40af;">Interpretación del Reporte</h4>
        <div style="font-size: 11px; color: #374151;">
            <p style="margin: 5px 0;"><strong>Tasa de Conversión:</strong> 
                <span class="metric-good">Verde ≥80%</span>, 
                <span class="metric-warning">Amarillo 50-79%</span>, 
                <span class="metric-bad">Rojo &lt;50%</span>
            </p>
            <p style="margin: 5px 0;"><strong>Valor por Usuario:</strong> Ingresos totales dividido entre usuarios únicos</p>
            <p style="margin: 5px 0;"><strong>Frecuencia de Venta:</strong> Promedio de días entre ventas del plan</p>
            <p style="margin: 5px 0;"><strong>Ingresos por Día:</strong> Ingresos totales dividido entre días con actividad</p>
        </div>
    </div>

    <div class="footer">
        <p>CollaboraX - Sistema de Gestión Empresarial | Reporte de Rendimiento de Planes | Página {PAGE_NUM} de {PAGE_COUNT}</p>
    </div>
</body>
</html>
