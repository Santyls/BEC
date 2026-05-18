@extends('layouts.admin')

@section('title', 'Generación de Reportes')
@section('header_title', 'Reportes y Analíticas')

@section('content')

<div class="space-y-6">
    <div class="mb-8">
        <h3 class="text-xl font-bold text-white">Centro de Reportes</h3>
        <p class="text-sm text-slate-400">Genera documentos consolidados con las métricas de operación mensual.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- ── Donaciones ──────────────────────────────────── --}}
        <div class="glass rounded-3xl p-8 border border-slate-800 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
            <div class="flex items-center gap-4 mb-6 relative z-10">
                <div class="p-4 bg-blue-500/20 rounded-2xl border border-blue-500/30">
                    <i data-lucide="file-bar-chart-2" class="w-8 h-8 text-blue-400"></i>
                </div>
                <div>
                    <h4 class="text-xl font-bold text-white">Reporte de Donaciones</h4>
                    <p class="text-xs text-blue-300 uppercase tracking-widest font-semibold">Resumen Mensual</p>
                </div>
            </div>
            <div class="space-y-3 mb-8 relative z-10 text-sm text-slate-300">
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-blue-400"></i> Total de donaciones recibidas.</p>
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-blue-400"></i> Cantidad total de unidades donadas.</p>
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-blue-400"></i> Distribución por condición del artículo.</p>
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-blue-400"></i> Detalle por marca y categoría.</p>
            </div>
            <div class="flex gap-4 relative z-10">
                <button onclick="verReporte('donaciones')"
                    class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl border border-slate-700 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="eye" class="w-5 h-5"></i> Vista Previa
                </button>
                <button onclick="descargarPDF('donaciones')" id="btn-dl-donaciones"
                    class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/20 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="download" class="w-5 h-5"></i> Descargar PDF
                </button>
            </div>
        </div>

        {{-- ── Voluntariados ───────────────────────────────── --}}
        <div class="glass rounded-3xl p-8 border border-slate-800 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
            <div class="flex items-center gap-4 mb-6 relative z-10">
                <div class="p-4 bg-emerald-500/20 rounded-2xl border border-emerald-500/30">
                    <i data-lucide="users" class="w-8 h-8 text-emerald-400"></i>
                </div>
                <div>
                    <h4 class="text-xl font-bold text-white">Reporte de Voluntariados</h4>
                    <p class="text-xs text-emerald-300 uppercase tracking-widest font-semibold">Resumen Mensual</p>
                </div>
            </div>
            <div class="space-y-3 mb-8 relative z-10 text-sm text-slate-300">
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i> Voluntariados totales en el período.</p>
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i> Distribución activos vs inactivos.</p>
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i> Horarios y fechas programadas.</p>
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i> Cupos máximos y campañas asociadas.</p>
            </div>
            <div class="flex gap-4 relative z-10">
                <button onclick="verReporte('voluntariados')"
                    class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl border border-slate-700 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="eye" class="w-5 h-5"></i> Vista Previa
                </button>
                <button onclick="descargarPDF('voluntariados')" id="btn-dl-voluntariados"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-500/20 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="download" class="w-5 h-5"></i> Descargar PDF
                </button>
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════
     MODAL FLOTANTE
══════════════════════════════════════════════════ --}}
<div id="reporte-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     style="background:rgba(0,0,0,0.78);backdrop-filter:blur(4px);">

    <div class="relative w-full max-w-6xl flex flex-col rounded-2xl overflow-hidden border border-slate-700 shadow-2xl"
         style="height:90vh;background:#0f172a;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700 flex-shrink-0"
             style="background:#1e293b;">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg" style="background:rgba(59,130,246,0.2);">
                    <i data-lucide="file-text" class="w-5 h-5 text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-widest">Vista Previa</p>
                    <p id="modal-titulo" class="text-white font-bold text-lg leading-tight">—</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button id="btn-dl-modal"
                    class="hidden items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 transition-all">
                    <i data-lucide="download" class="w-4 h-4"></i> Descargar PDF
                </button>
                <button onclick="cerrarModal()"
                    class="p-2 rounded-xl text-slate-400 hover:text-white transition-all"
                    style="background:rgba(255,255,255,0.05);">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        {{-- Spinner --}}
        <div id="modal-loading" class="flex-1 flex flex-col items-center justify-center gap-4">
            <div class="w-12 h-12 rounded-full border-4 border-blue-500 border-t-transparent animate-spin"></div>
            <p class="text-slate-400 text-sm">Cargando datos...</p>
        </div>

        {{-- Error --}}
        <div id="modal-error" class="hidden flex-1 flex flex-col items-center justify-center gap-3 p-8">
            <i data-lucide="alert-triangle" class="w-10 h-10 text-red-400"></i>
            <p class="text-white font-bold text-lg">No se pudo cargar el reporte</p>
            <p id="modal-error-msg" class="text-slate-400 text-sm text-center max-w-md"></p>
            <button onclick="cerrarModal()" class="mt-2 px-6 py-2 rounded-xl text-sm font-bold text-white" style="background:rgba(255,255,255,0.1);">Cerrar</button>
        </div>

        {{-- Contenido HTML del reporte --}}
        <div id="modal-body" class="hidden flex-1 overflow-y-auto p-6">

            {{-- Stats cards --}}
            <div id="stats-container" class="grid gap-4 mb-6"></div>

            {{-- Tabla --}}
            <div class="rounded-xl overflow-hidden border border-slate-700">
                <table class="w-full text-sm" style="border-collapse:collapse;">
                    <thead id="tabla-head" style="background:#1e293b;"></thead>
                    <tbody id="tabla-body"></tbody>
                </table>
            </div>
            <p id="tabla-total" class="text-xs text-slate-500 mt-3 text-right"></p>
        </div>

    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
let tipoActivo = null;

/* ── Vista Previa (JSON → HTML) ──────────────────────────── */
async function verReporte(tipo) {
    tipoActivo = tipo;
    abrirModal(tipo);

    try {
        const res = await fetch(`/admin/reportes/datos?tipo=${tipo}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();

        if (!res.ok) throw new Error(json.error || 'HTTP ' + res.status);

        renderStats(tipo, json.stats);
        renderTabla(tipo, json.datos);

        document.getElementById('modal-loading').classList.add('hidden');
        document.getElementById('modal-body').classList.remove('hidden');
        document.getElementById('modal-body').classList.add('flex', 'flex-col');

        const btnDl = document.getElementById('btn-dl-modal');
        btnDl.classList.remove('hidden');
        btnDl.classList.add('flex');
        btnDl.onclick = () => descargarPDF(tipo);

    } catch (e) {
        mostrarError(e.message);
    }
}

/* ── Render stats ────────────────────────────────────────── */
function renderStats(tipo, stats) {
    const c = document.getElementById('stats-container');
    c.innerHTML = '';

    let items = [];

    if (tipo === 'donaciones') {
        c.className = 'grid grid-cols-3 gap-4 mb-6';
        items = [
            { val: stats.total,                              lbl: 'Total registros' },
            { val: Number(stats.cantidad_total).toLocaleString(), lbl: 'Unidades totales' },
            { val: Object.keys(stats.por_condicion).length,  lbl: 'Tipos de condición' },
        ];
        Object.entries(stats.por_condicion).forEach(([k, v]) => {
            items.push({ val: v, lbl: k });
        });
        const cols = Math.min(items.length, 4);
        c.className = `grid grid-cols-${cols} gap-4 mb-6`;
    } else {
        c.className = 'grid grid-cols-3 gap-4 mb-6';
        items = [
            { val: stats.total,     lbl: 'Total voluntariados', color: 'text-white' },
            { val: stats.activos,   lbl: 'Activos',             color: 'text-emerald-400' },
            { val: stats.inactivos, lbl: 'Inactivos',           color: 'text-red-400' },
        ];
    }

    items.forEach(item => {
        const div = document.createElement('div');
        div.className = 'rounded-xl p-4 text-center border border-slate-700';
        div.style.background = '#1e293b';
        div.innerHTML = `
            <div class="text-3xl font-bold ${item.color || 'text-blue-400'}">${item.val}</div>
            <div class="text-xs text-slate-400 uppercase tracking-wide mt-1">${item.lbl}</div>
        `;
        c.appendChild(div);
    });
}

/* ── Render tabla ────────────────────────────────────────── */
function renderTabla(tipo, datos) {
    const head = document.getElementById('tabla-head');
    const body = document.getElementById('tabla-body');
    const total = document.getElementById('tabla-total');

    const thStyle = 'padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:0.4px;color:#94a3b8;font-weight:600;';
    const tdStyle = (i) => `padding:9px 12px;border-top:1px solid #1e293b;color:#cbd5e1;font-size:12px;background:${i%2===0?'#0f172a':'#111827'};`;

    if (tipo === 'donaciones') {
        head.innerHTML = `<tr>${['#ID','Categoría','Cantidad','Unidad','Condición','Marca','Albergue'].map(h=>`<th style="${thStyle}">${h}</th>`).join('')}</tr>`;
        body.innerHTML = datos.map((r, i) => `<tr>
            <td style="${tdStyle(i)}"><span style="font-family:monospace;color:#64748b">#${r.Id_Donacion??'-'}</span></td>
            <td style="${tdStyle(i)}">${r.id_Categoria??'-'}</td>
            <td style="${tdStyle(i)}"><strong>${parseFloat(r.Cantidad??0).toLocaleString(undefined,{minimumFractionDigits:2})}</strong></td>
            <td style="${tdStyle(i)}">${r.Id_Unidad??'-'}</td>
            <td style="${tdStyle(i)}">${badgeCond(r.Id_Condicion)}</td>
            <td style="${tdStyle(i)}">${r.Marca??'-'}</td>
            <td style="${tdStyle(i)};color:#64748b;font-family:monospace">${r.Id_Albergue??'-'}</td>
        </tr>`).join('');
    } else {
        head.innerHTML = `<tr>${['Nombre','Fecha Prog.','Hora Inicio','Hora Fin','Estado','Cupo Máx.','Campaña'].map(h=>`<th style="${thStyle}">${h}</th>`).join('')}</tr>`;
        body.innerHTML = datos.map((r, i) => `<tr>
            <td style="${tdStyle(i)}"><strong>${r.Nombre_Voluntariado??'-'}</strong></td>
            <td style="${tdStyle(i)}">${r.Fecha_prog??'-'}</td>
            <td style="${tdStyle(i)};font-family:monospace">${r.Hora_inicio??'-'}</td>
            <td style="${tdStyle(i)};font-family:monospace">${r.Hora_Fin??'-'}</td>
            <td style="${tdStyle(i)}">${badgeEstado(r.Estado_Voluntariado)}</td>
            <td style="${tdStyle(i)};text-align:center">${r.Cupo_Max??'∞'}</td>
            <td style="${tdStyle(i)};color:#64748b">${r.id_campana??'-'}</td>
        </tr>`).join('');
    }

    total.textContent = `${datos.length} registro${datos.length !== 1 ? 's' : ''} encontrado${datos.length !== 1 ? 's' : ''}`;
}

function badgeCond(v) {
    const map = { 'Nuevo':'background:#dbeafe;color:#1d4ed8', 'Bueno':'background:#fef9c3;color:#854d0e', 'Regular':'background:#fce7f3;color:#9d174d' };
    const s = map[v] || 'background:#f1f5f9;color:#475569';
    return `<span style="padding:2px 8px;border-radius:10px;font-size:10px;font-weight:bold;${s}">${v??'-'}</span>`;
}
function badgeEstado(v) {
    const s = (v||'').toLowerCase() === 'activo'
        ? 'background:#dcfce7;color:#15803d'
        : 'background:#fee2e2;color:#b91c1c';
    return `<span style="padding:2px 8px;border-radius:10px;font-size:10px;font-weight:bold;text-transform:uppercase;${s}">${v??'-'}</span>`;
}

/* ── Descargar PDF (fetch → blob) ────────────────────────── */
async function descargarPDF(tipo) {
    const btn = document.getElementById(`btn-dl-${tipo}`) || document.getElementById('btn-dl-modal');
    const textoOrig = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '⏳ Generando PDF...'; }

    try {
        const fd = new FormData();
        fd.append('_token', CSRF);
        fd.append('tipo_reporte', tipo);
        fd.append('action', 'download');

        const res = await fetch('/admin/reportes/generar', { method: 'POST', body: fd });
        if (!res.ok) {
            const txt = await res.text();
            throw new Error('HTTP ' + res.status + ' — ' + txt.slice(0, 150));
        }

        const blob = await res.blob();
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        const fecha = new Date().toISOString().slice(0, 10);
        a.href     = url;
        a.download = `reporte_${tipo}_${fecha}.pdf`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(() => URL.revokeObjectURL(url), 5000);

    } catch (e) {
        alert('No se pudo descargar el PDF:\n' + e.message);
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = textoOrig; }
    }
}

/* ── Helpers del modal ───────────────────────────────────── */
function abrirModal(tipo) {
    const nombres = { donaciones: 'Donaciones', voluntariados: 'Voluntariados' };
    document.getElementById('modal-titulo').textContent = 'Consolidado de ' + (nombres[tipo] || tipo);

    document.getElementById('modal-loading').classList.remove('hidden');
    document.getElementById('modal-loading').classList.add('flex');
    document.getElementById('modal-error').classList.add('hidden');
    document.getElementById('modal-error').classList.remove('flex');
    document.getElementById('modal-body').classList.add('hidden');
    document.getElementById('modal-body').classList.remove('flex');

    const btnDl = document.getElementById('btn-dl-modal');
    btnDl.classList.add('hidden');
    btnDl.classList.remove('flex');

    const modal = document.getElementById('reporte-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function mostrarError(msg) {
    document.getElementById('modal-loading').classList.add('hidden');
    document.getElementById('modal-loading').classList.remove('flex');
    document.getElementById('modal-error-msg').textContent = msg;
    document.getElementById('modal-error').classList.remove('hidden');
    document.getElementById('modal-error').classList.add('flex');
}

function cerrarModal() {
    const modal = document.getElementById('reporte-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    tipoActivo = null;
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModal(); });
document.getElementById('reporte-modal').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>

@endsection
