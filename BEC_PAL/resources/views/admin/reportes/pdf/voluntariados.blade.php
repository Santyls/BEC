<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Voluntariados</title>
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
    <h1>Reporte de Voluntariados</h1>
    <p class="subtitulo">Periodo: {{ \Illuminate\Support\Carbon::parse($desde)->format('d M, Y') }} — {{ \Illuminate\Support\Carbon::parse($hasta)->format('d M, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Actividad</th>
                <th>Albergue</th>
                <th>Campaña</th>
                <th>Fecha</th>
                <th>Horario</th>
                <th>Ubicación</th>
                <th>Cupo</th>
                <th>Inscritos</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @php $estados = [1 => 'Programado', 2 => 'Activo', 3 => 'Finalizado', 4 => 'Cancelado']; @endphp
            @forelse ($filas as $v)
                <tr>
                    <td>{{ $v['nombre_programa'] }}</td>
                    <td>{{ $v['albergue_nombre'] ?? '—' }}</td>
                    <td>{{ $v['campana_nombre'] ?? '—' }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($v['fecha_programada'])->format('d M, Y') }}</td>
                    <td>{{ substr($v['hora_inicio'],0,5) }} - {{ substr($v['hora_fin'],0,5) }}</td>
                    <td>{{ $v['ubicacion'] ?? '—' }}</td>
                    <td>{{ $v['cupo_maximo'] ?? 'Sin límite' }}</td>
                    <td>{{ $v['inscritos'] }}</td>
                    <td>{{ $estados[$v['estado_id']] ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="9">No hay voluntariados en el periodo seleccionado.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="resumen">Total de registros: {{ count($filas) }} — Generado el {{ \Illuminate\Support\Carbon::now()->format('d M, Y H:i') }}</p>
</body>
</html>
