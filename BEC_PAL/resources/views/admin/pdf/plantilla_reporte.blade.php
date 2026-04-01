<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte {{ ucfirst($tipo) }}</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; margin: 20px; }
h1   { font-size: 16px; color: #1d4ed8; margin: 0 0 2px 0; }
.sub { font-size: 9px; color: #666; margin-bottom: 14px; }
.hr  { border: none; border-top: 2px solid #1d4ed8; margin-bottom: 12px; }
.stat-wrap { margin-bottom: 14px; }
.stat-box  { display: inline-block; border: 1px solid #ccc; padding: 6px 12px; margin-right: 8px; text-align: center; min-width: 80px; background: #f8fafc; }
.stat-val  { font-size: 18px; font-weight: bold; color: #1d4ed8; }
.stat-lbl  { font-size: 8px; color: #666; text-transform: uppercase; }
table { width: 100%; border-collapse: collapse; margin-top: 6px; }
th    { background: #1d4ed8; color: #fff; padding: 6px 7px; text-align: left; font-size: 9px; text-transform: uppercase; }
td    { border: 1px solid #ddd; padding: 5px 7px; }
.par  { background: #f1f5f9; }
.foot { font-size: 8px; color: #999; text-align: center; margin-top: 18px; border-top: 1px solid #ddd; padding-top: 6px; }
</style>
</head>
<body>

<h1>BEC &mdash; Blue Earth Coders</h1>
<div class="sub">Sistema de Administración &bull; BEC_PAL &bull; Generado: {{ $fecha }}</div>
<hr class="hr">

<h2 style="font-size:13px;margin-bottom:10px;">Consolidado de {{ ucfirst($tipo) }}</h2>

{{-- Stats --}}
<div class="stat-wrap">
@if($tipo === 'donaciones')
  <span class="stat-box"><div class="stat-val">{{ $stats['total'] }}</div><div class="stat-lbl">Registros</div></span>
  <span class="stat-box"><div class="stat-val">{{ number_format($stats['cantidad_total'],0) }}</div><div class="stat-lbl">Unidades</div></span>
  @foreach($stats['por_condicion'] as $k => $v)
  <span class="stat-box"><div class="stat-val">{{ $v }}</div><div class="stat-lbl">{{ $k }}</div></span>
  @endforeach
@elseif($tipo === 'voluntariados')
  <span class="stat-box"><div class="stat-val">{{ $stats['total'] }}</div><div class="stat-lbl">Total</div></span>
  <span class="stat-box"><div class="stat-val">{{ $stats['activos'] }}</div><div class="stat-lbl">Activos</div></span>
  <span class="stat-box"><div class="stat-val">{{ $stats['inactivos'] }}</div><div class="stat-lbl">Inactivos</div></span>
@endif
</div>

{{-- Tabla --}}
@if(empty($datos))
  <p style="color:#666;text-align:center;padding:20px;">Sin registros disponibles.</p>
@else
<table>
  <thead>
  @if($tipo === 'donaciones')
    <tr><th>#</th><th>Categoria</th><th>Cantidad</th><th>Unidad</th><th>Condicion</th><th>Marca</th><th>Albergue</th></tr>
  @else
    <tr><th>Nombre</th><th>Fecha</th><th>Inicio</th><th>Fin</th><th>Estado</th><th>Cupo</th><th>Campana</th></tr>
  @endif
  </thead>
  <tbody>
  @if($tipo === 'donaciones')
    @foreach($datos as $i => $r)
    <tr class="{{ $i%2?'par':'' }}">
      <td>{{ $r['Id_Donacion'] ?? '-' }}</td>
      <td>{{ $r['id_Categoria'] ?? '-' }}</td>
      <td>{{ number_format((float)($r['Cantidad']??0),2) }}</td>
      <td>{{ $r['Id_Unidad'] ?? '-' }}</td>
      <td>{{ $r['Id_Condicion'] ?? '-' }}</td>
      <td>{{ $r['Marca'] ?? '-' }}</td>
      <td>{{ $r['Id_Albergue'] ?? '-' }}</td>
    </tr>
    @endforeach
  @else
    @foreach($datos as $i => $r)
    <tr class="{{ $i%2?'par':'' }}">
      <td>{{ $r['Nombre_Voluntariado'] ?? '-' }}</td>
      <td>{{ $r['Fecha_prog'] ?? '-' }}</td>
      <td>{{ $r['Hora_inicio'] ?? '-' }}</td>
      <td>{{ $r['Hora_Fin'] ?? '-' }}</td>
      <td>{{ $r['Estado_Voluntariado'] ?? '-' }}</td>
      <td>{{ $r['Cupo_Max'] ?? '-' }}</td>
      <td>{{ $r['id_campana'] ?? '-' }}</td>
    </tr>
    @endforeach
  @endif
  </tbody>
</table>
@if(count($datos) >= 200)
  <p style="color:#666;font-size:9px;margin-top:6px;">* Reporte limitado a 200 registros. Descarga el CSV para el listado completo.</p>
@endif
@endif

<div class="foot">BEC_PAL &mdash; Blue Earth Coders &bull; {{ $fecha }} &bull; Confidencial</div>
</body>
</html>
