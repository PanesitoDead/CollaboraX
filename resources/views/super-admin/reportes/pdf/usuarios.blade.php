<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Usuarios - CollaboraX</title>
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
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .summary {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
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
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .status-active {
            color: #059669;
            font-weight: bold;
        }
        .status-inactive {
            color: #dc2626;
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
        <h1>CollaboraX - Reporte de Usuarios</h1>
        <p>Sistema de Gestión Empresarial</p>
        <p>Generado el: {{ $data['fecha_generacion'] }}</p>
    </div>

    <div class="summary">
        <h3 style="margin-top: 0; color: #374151;">Resumen del Reporte</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <strong>Total de Usuarios:</strong> {{ $data['total_usuarios'] }}
            </div>
            <div class="summary-item">
                <strong>Período:</strong> {{ ucfirst(str_replace('-', ' ', $data['periodo'])) }}
            </div>
        </div>
    </div>

    <h3 style="color: #374151; margin-bottom: 10px;">Listado Detallado de Usuarios</h3>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 20%;">Nombre</th>
                <th style="width: 25%;">Correo Electrónico</th>
                <th style="width: 12%;">Rol</th>
                <th style="width: 15%;">Empresa</th>
                <th style="width: 10%;">Estado</th>
                <th style="width: 10%;">Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['usuarios'] as $index => $usuario)
                @if($index > 0 && $index % 25 == 0)
                    </tbody>
                    </table>
                    <div class="page-break"></div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 8%;">ID</th>
                                <th style="width: 20%;">Nombre</th>
                                <th style="width: 25%;">Correo Electrónico</th>
                                <th style="width: 12%;">Rol</th>
                                <th style="width: 15%;">Empresa</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;">Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                @endif
                <tr>
                    <td>{{ $usuario['id'] }}</td>
                    <td>{{ $usuario['nombre'] }}</td>
                    <td>{{ $usuario['correo'] }}</td>
                    <td>{{ $usuario['rol'] }}</td>
                    <td>{{ $usuario['empresa'] }}</td>
                    <td>
                        <span class="{{ $usuario['activo'] === 'Activo' ? 'status-active' : 'status-inactive' }}">
                            {{ $usuario['activo'] }}
                        </span>
                    </td>
                    <td>{{ $usuario['fecha_registro'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>CollaboraX - Sistema de Gestión Empresarial | Página {PAGE_NUM} de {PAGE_COUNT}</p>
    </div>
</body>
</html>
