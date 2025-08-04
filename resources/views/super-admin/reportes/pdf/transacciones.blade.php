<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Transacciones - CollaboraX</title>
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
            border-bottom: 2px solid #7c3aed;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #7c3aed;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .summary {
            background-color: #faf5ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #7c3aed;
        }
        .summary-item {
            margin-bottom: 5px;
        }
        .summary-item strong {
            color: #374151;
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
            background-color: #faf5ff;
            font-weight: bold;
            color: #581c87;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .status-approved {
            color: #059669;
            font-weight: bold;
        }
        .status-pending {
            color: #d97706;
            font-weight: bold;
        }
        .status-rejected {
            color: #dc2626;
            font-weight: bold;
        }
        .currency {
            color: #7c3aed;
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
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CollaboraX - Reporte de Transacciones</h1>
        <p>Sistema de Gestión Empresarial</p>
        <p>Generado el: {{ $data['fecha_generacion'] ?? now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <h3 style="margin-top: 0; color: #581c87;">Resumen del Reporte</h3>
        <div class="summary-item">
            <strong>Total de Transacciones:</strong> {{ $data['total_transacciones'] ?? 0 }}
        </div>
        <div class="summary-item">
            <strong>Período de Consulta:</strong> Últimas transacciones disponibles
        </div>
    </div>

    <h3 style="color: #374151; margin-bottom: 10px;">Listado Detallado de Transacciones</h3>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">ID Pago</th>
                <th style="width: 12%;">Usuario ID</th>
                <th style="width: 20%;">Plan</th>
                <th style="width: 12%;">Monto</th>
                <th style="width: 12%;">Estado</th>
                <th style="width: 18%;">Método de Pago</th>
                <th style="width: 16%;">Fecha de Pago</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data['transacciones']) && count($data['transacciones']) > 0)
                @foreach($data['transacciones'] as $index => $transaccion)
                    @if($index > 0 && $index % 20 == 0)
                        </tbody>
                        </table>
                        <div class="page-break"></div>
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 10%;">ID Pago</th>
                                    <th style="width: 12%;">Usuario ID</th>
                                    <th style="width: 20%;">Plan</th>
                                    <th style="width: 12%;">Monto</th>
                                    <th style="width: 12%;">Estado</th>
                                    <th style="width: 18%;">Método de Pago</th>
                                    <th style="width: 16%;">Fecha de Pago</th>
                                </tr>
                            </thead>
                            <tbody>
                    @endif
                    <tr>
                        <td>{{ $transaccion['pago_id'] ?? 'N/A' }}</td>
                        <td>{{ $transaccion['usuario_id'] ?? 'N/A' }}</td>
                        <td>{{ $transaccion['plan_nombre'] ?? 'N/A' }}</td>
                        <td class="text-right currency">${{ $transaccion['monto'] ?? '0.00' }}</td>
                        <td>
                            @php
                                $estado = $transaccion['estado'] ?? 'unknown';
                                $estadoTexto = '';
                                $estadoClass = '';
                                
                                switch($estado) {
                                    case 'approved':
                                        $estadoTexto = 'Aprobado';
                                        $estadoClass = 'status-approved';
                                        break;
                                    case 'pending':
                                        $estadoTexto = 'Pendiente';
                                        $estadoClass = 'status-pending';
                                        break;
                                    case 'rejected':
                                        $estadoTexto = 'Rechazado';
                                        $estadoClass = 'status-rejected';
                                        break;
                                    default:
                                        $estadoTexto = ucfirst($estado);
                                        $estadoClass = '';
                                }
                            @endphp
                            <span class="{{ $estadoClass }}">{{ $estadoTexto }}</span>
                        </td>
                        <td>
                            @php
                                $metodo = $transaccion['metodo_pago'] ?? 'N/A';
                                $metodoTexto = '';
                                
                                switch($metodo) {
                                    case 'tarjeta_credito':
                                        $metodoTexto = 'Tarjeta de Crédito';
                                        break;
                                    case 'tarjeta_debito':
                                        $metodoTexto = 'Tarjeta de Débito';
                                        break;
                                    case 'transferencia':
                                        $metodoTexto = 'Transferencia';
                                        break;
                                    case 'manual':
                                        $metodoTexto = 'Manual';
                                        break;
                                    default:
                                        $metodoTexto = ucfirst(str_replace('_', ' ', $metodo));
                                }
                            @endphp
                            {{ $metodoTexto }}
                        </td>
                        <td>
                            @if(isset($transaccion['fecha_pago']))
                                {{ \Carbon\Carbon::parse($transaccion['fecha_pago'])->format('d/m/Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" style="text-align: center; color: #666; font-style: italic;">
                        No hay transacciones disponibles en este momento
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(isset($data['headers_excel']) && count($data['headers_excel']) > 0)
    <div style="margin-top: 20px; padding: 10px; background-color: #f8fafc; border-radius: 5px;">
        <h4 style="margin-top: 0; color: #374151;">Información Adicional</h4>
        <p style="margin: 5px 0; color: #666;">
            <strong>Formato de Exportación:</strong> Este reporte está optimizado para exportación a Excel
        </p>
        <p style="margin: 5px 0; color: #666;">
            <strong>Columnas disponibles:</strong> {{ implode(', ', $data['headers_excel']) }}
        </p>
    </div>
    @endif

    <div class="footer">
        <p>CollaboraX - Sistema de Gestión Empresarial | Reporte de Transacciones | Página {PAGE_NUM} de {PAGE_COUNT}</p>
    </div>
</body>
</html>
