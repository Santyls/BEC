<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Donaciones</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #1e293b; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        p.subtitulo { color: #64748b; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #1e293b; color: #ffffff; text-transform: uppercase; font-size: 9px; letter-spacing: 0.05em; }
        tr:nth-child(even) { background-color: #f1f5f9; }
        .resumen { margin-top: 16px; font-size: 10px; color: #64748b; }
    </style>
</head>
<body>
    <h1>Reporte de Donaciones</h1>
    <p class="subtitulo">Periodo: {{ \Illuminate\Support\Carbon::parse($desde)->format('d M, Y') }} — {{ \Illuminate\Support\Carbon::parse($hasta)->format('d M, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Folio</th>
                <th>Fecha</th>
                <th>Donante</th>
                <th>Categoría</th>
                <th>Condición</th>
                <th>Cantidad</th>
                <th>Marca</th>
                <th>Albergue destino</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($filas as $d)
                <tr>
                    <td>DON-{{ str_pad($d['id'], 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($d['fecha_donacion'])->format('d M, Y') }}</td>
                    <td>{{ $d['usuario'] ? trim($d['usuario']['nombre'].' '.$d['usuario']['apellido_paterno']) : 'Anónimo' }}</td>
                    <td>{{ $d['categoria']['nombre'] ?? '—' }}</td>
                    <td>{{ $d['condicion']['nombre'] ?? '—' }}</td>
                    <td>{{ $d['cantidad'] }}</td>
                    <td>{{ $d['marca'] ?? '—' }}</td>
                    <td>{{ $d['albergue']['nombre'] ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No hay donaciones en el periodo seleccionado.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="resumen">Total de registros: {{ count($filas) }} — Generado el {{ \Illuminate\Support\Carbon::now()->format('d M, Y H:i') }}</p>
</body>
</html>
