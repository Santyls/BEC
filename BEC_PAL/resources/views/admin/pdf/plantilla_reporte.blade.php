<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de {{ ucfirst($tipo) }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 24px 30px 50px 30px;
        }

        /* ── Membrete ─────────────────────────── */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; border-bottom: 3px solid #1d4ed8; padding-bottom: 10px; }
        .header-org { font-size: 18px; font-weight: bold; color: #1d4ed8; text-transform: uppercase; letter-spacing: 1px; }
        .header-sub { font-size: 10px; color: #64748b; margin-top: 3px; }
        .header-fecha { text-align: right; font-size: 10px; color: #64748b; vertical-align: top; }
        .header-fecha strong { display: block; color: #1e293b; margin-bottom: 2px; }

        /* ── Título ───────────────────────────── */
        .titulo { text-align: center; font-size: 14px; font-weight: bold; text-transform: uppercase;
                  color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 14px; }
        .subtitulo { text-align: center; font-size: 10px; color: #94a3b8; margin-bottom: 16px; margin-top: -10px; }

        /* ── Stats ────────────────────────────── */
        .stats-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .stats-table td { border: 1px solid #e2e8f0; background: #f8fafc; text-align: center; padding: 10px 8px; }
        .stat-value { font-size: 22px; font-weight: bold; color: #1d4ed8; }
        .stat-value.verde { color: #15803d; }
        .stat-value.rojo  { color: #b91c1c; }
        .stat-label { font-size: 9px; text-transform: uppercase; color: #64748b; margin-top: 3px; letter-spacing: 0.4px; }

        /* ── Sección ──────────────────────────── */
        .section-label { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #1d4ed8;
                         border-left: 3px solid #1d4ed8; padding-left: 6px; margin-bottom: 8px; }

        /* ── Tabla de datos ───────────────────── */
        table.datos { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.datos th { background-color: #1d4ed8; color: #fff; font-size: 10px; text-transform: uppercase;
                         padding: 7px 8px; text-align: left; letter-spacing: 0.3px; }
        table.datos td { border: 1px solid #e2e8f0; padding: 6px 8px; color: #334155; vertical-align: middle; }
        table.datos tr.par td { background-color: #f8fafc; }

        /* ── Badges ───────────────────────────── */
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .badge-activo   { background: #dcfce7; color: #15803d; }
        .badge-inactivo { background: #fee2e2; color: #b91c1c; }
        .badge-nuevo    { background: #dbeafe; color: #1d4ed8; }
        .badge-bueno    { background: #fef9c3; color: #854d0e; }
        .badge-regular  { background: #fce7f3; color: #9d174d; }
        .id-cell { font-family: monospace; color: #64748b; font-size: 10px; }

        /* ── Estado vacío ─────────────────────── */
        .empty { text-align: center; padding: 30px; color: #64748b; font-size: 12px;
                 border: 1px dashed #cbd5e1; background: #f8fafc; }

        /* ── Footer ───────────────────────────── */
        .footer { text-align: center; font-size: 9px; color: #94a3b8;
                  border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 20px; }
    </style>
</head>
<body>

{{-- Membrete --}}
<table class="header-table">
    <tr>
        <td style="vertical-align: middle; padding-bottom: 10px;">
            <div class="header-org">BEC &mdash; Blue Earth Coders</div>
            <div class="header-sub">Sistema de Administración y Gestión &bull; BEC_PAL</div>
        </td>
        <td class="header-fecha" style="padding-bottom: 10px; width: 200px;">
            <strong>Generado el:</strong>
            {{ $fecha }}
        </td>
    </tr>
</table>

{{-- Título dinámico --}}
<div class="titulo">Consolidado de {{ ucfirst($tipo) }}</div>
<div class="subtitulo">Reporte oficial del período actual &bull; Confidencial</div>

{{-- Estadísticas de resumen --}}
@if($tipo === 'donaciones')
<table class="stats-table">
    <tr>
        <td style="width: 33%;">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total registros</div>
        </td>
        <td style="width: 33%;">
            <div class="stat-value">{{ number_format($stats['cantidad_total'], 0) }}</div>
            <div class="stat-label">Unidades totales</div>
        </td>
        <td style="width: 33%;">
            <div class="stat-value">{{ count($stats['por_condicion']) }}</div>
            <div class="stat-label">Tipos de condición</div>
        </td>
    </tr>
</table>
@elseif($tipo === 'voluntariados')
<table class="stats-table">
    <tr>
        <td style="width: 33%;">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total voluntariados</div>
        </td>
        <td style="width: 33%;">
            <div class="stat-value verde">{{ $stats['activos'] }}</div>
            <div class="stat-label">Activos</div>
        </td>
        <td style="width: 33%;">
            <div class="stat-value rojo">{{ $stats['inactivos'] }}</div>
            <div class="stat-label">Inactivos</div>
        </td>
    </tr>
</table>
@endif

{{-- Tabla de detalle --}}
<div class="section-label">Detalle de registros</div>

@if(empty($datos))
    <div class="empty">No hay registros disponibles para este reporte.</div>
@else
    <table class="datos">
        <thead>
            @if($tipo === 'donaciones')
            <tr>
                <th style="width:6%">#ID</th>
                <th style="width:13%">Categoría</th>
                <th style="width:14%">Cantidad</th>
                <th style="width:12%">U. Medida</th>
                <th style="width:14%">Condición</th>
                <th style="width:20%">Marca</th>
                <th style="width:21%">Albergue</th>
            </tr>
            @elseif($tipo === 'voluntariados')
            <tr>
                <th style="width:27%">Nombre</th>
                <th style="width:13%">Fecha Prog.</th>
                <th style="width:13%">Inicio</th>
                <th style="width:13%">Fin</th>
                <th style="width:14%">Estado</th>
                <th style="width:10%">Cupo Máx.</th>
                <th style="width:10%">Campaña</th>
            </tr>
            @endif
        </thead>
        <tbody>
            @if($tipo === 'donaciones')
                @foreach($datos as $i => $item)
                @php
                    $cond = strtolower($item['Id_Condicion'] ?? '');
                    $bc   = match($cond) { 'nuevo' => 'badge-nuevo', 'bueno' => 'badge-bueno', 'regular' => 'badge-regular', default => '' };
                @endphp
                <tr class="{{ $i % 2 === 1 ? 'par' : '' }}">
                    <td class="id-cell">#{{ $item['Id_Donacion'] ?? 'N/A' }}</td>
                    <td>{{ $item['id_Categoria'] ?? 'N/A' }}</td>
                    <td><strong>{{ number_format((float)($item['Cantidad'] ?? 0), 2) }}</strong></td>
                    <td>{{ $item['Id_Unidad'] ?? '-' }}</td>
                    <td><span class="badge {{ $bc }}">{{ $item['Id_Condicion'] ?? 'N/A' }}</span></td>
                    <td>{{ $item['Marca'] ?? '-' }}</td>
                    <td class="id-cell">{{ $item['Id_Albergue'] ?? '-' }}</td>
                </tr>
                @endforeach
            @elseif($tipo === 'voluntariados')
                @foreach($datos as $i => $item)
                @php
                    $est = strtolower($item['Estado_Voluntariado'] ?? '');
                    $bc  = $est === 'activo' ? 'badge-activo' : 'badge-inactivo';
                @endphp
                <tr class="{{ $i % 2 === 1 ? 'par' : '' }}">
                    <td><strong>{{ $item['Nombre_Voluntariado'] ?? 'Sin nombre' }}</strong></td>
                    <td>{{ $item['Fecha_prog'] ?? 'N/A' }}</td>
                    <td class="id-cell">{{ $item['Hora_inicio'] ?? '-' }}</td>
                    <td class="id-cell">{{ $item['Hora_Fin'] ?? '-' }}</td>
                    <td><span class="badge {{ $bc }}">{{ strtoupper($item['Estado_Voluntariado'] ?? 'N/A') }}</span></td>
                    <td style="text-align:center">{{ $item['Cupo_Max'] ?? '-' }}</td>
                    <td class="id-cell">{{ $item['id_campana'] ?? '-' }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
@endif

{{-- Footer --}}
<div class="footer">
    Documento generado automáticamente &bull; <strong>BEC_PAL &mdash; Blue Earth Coders</strong> &bull; {{ $fecha }} &bull; Confidencial
</div>

</body>
</html>
