@extends('layouts.admin')

@section('title', 'Gestión de Voluntariados')
@section('header_title', 'Voluntariados')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .fade-in { animation: fadeIn 0.4s ease-in-out; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

    /* ── Modal ── */
    .modal-overlay {
        position: fixed; inset: 0; z-index: 50;
        background: rgba(0,0,0,.75); backdrop-filter: blur(5px);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none; transition: opacity .25s ease;
    }
    .modal-overlay.open { opacity: 1; pointer-events: all; }
    .modal-box {
        background: #1e293b; border: 1px solid rgba(255,255,255,.07);
        border-radius: 1.25rem; padding: 2rem;
        width: 100%; max-width: 700px;
        max-height: 94vh; overflow-y: auto;
        transform: translateY(24px) scale(.97);
        transition: transform .25s ease;
        scrollbar-width: thin; scrollbar-color: #334155 transparent;
    }
    .modal-overlay.open .modal-box { transform: translateY(0) scale(1); }

    /* ── Form fields ── */
    .field-label {
        display:block; font-size:.7rem; font-weight:700;
        color:#94a3b8; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.4rem;
    }
    .field-input {
        width:100%; background:#0f172a; border:1px solid #334155;
        color:#e2e8f0; font-size:.875rem; border-radius:.5rem;
        padding:.65rem 1rem; outline:none; transition: border-color .2s;
        appearance: none;
    }
    .field-input:focus { border-color:#8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,.12); }
    .field-input.error { border-color:#f43f5e; }
    .field-input:disabled { opacity:.45; cursor:not-allowed; }
    .field-error { font-size:.75rem; color:#f43f5e; margin-top:.3rem; display:none; }
    .field-error.show { display:block; }
    textarea.field-input { resize: vertical; min-height: 80px; }

    /* ── Section divider ── */
    .form-section {
        background: rgba(15,23,42,.5);
        border: 1px solid rgba(255,255,255,.06);
        border-radius:.75rem; padding:1rem 1.25rem;
    }
    .form-section-title {
        font-size:.7rem; font-weight:800; text-transform:uppercase;
        letter-spacing:.1em; color:#a78bfa; margin-bottom:1rem;
        display:flex; align-items:center; gap:.5rem;
    }
    .step-badge {
        display:inline-flex; align-items:center; justify-content:center;
        width:1.4rem; height:1.4rem; border-radius:50%;
        background:#3b0764; color:#c4b5fd; font-size:.65rem; font-weight:800; flex-shrink:0;
    }

    /* ── Select arrow ── */
    select.field-input {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .85rem center;
        padding-right: 2.25rem;
    }

    /* ── Skeleton ── */
    .skeleton { background:linear-gradient(90deg,#1e293b 25%,#273349 50%,#1e293b 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; border-radius:.4rem; }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

    /* ── Estado badges ── */
    .badge-activo     { background:rgba(16,185,129,.12); color:#34d399; border:1px solid rgba(16,185,129,.3); }
    .badge-completado { background:rgba(99,102,241,.12);  color:#a5b4fc; border:1px solid rgba(99,102,241,.3); }
    .badge-cancelado  { background:rgba(244,63,94,.12);   color:#fb7185; border:1px solid rgba(244,63,94,.3); }
    .badge-default    { background:rgba(100,116,139,.12); color:#94a3b8; border:1px solid rgba(100,116,139,.3); }
</style>
@endpush

@section('content')
<div class="space-y-6 fade-in">

    {{-- ===== MÉTRICAS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-violet-500/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="heart-handshake" class="w-5 h-5 text-violet-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-widest">Total</p>
                <p id="stat-total" class="text-2xl font-black text-white">—</p>
            </div>
        </div>
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="calendar-check" class="w-5 h-5 text-emerald-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-widest">Activos</p>
                <p id="stat-activos" class="text-2xl font-black text-white">—</p>
            </div>
        </div>
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-sky-500/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="users" class="w-5 h-5 text-sky-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-widest">Cupo Total</p>
                <p id="stat-cupo" class="text-2xl font-black text-white">—</p>
            </div>
        </div>
    </div>

    {{-- ===== BARRA DE ACCIONES ===== --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="relative w-full sm:w-80">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
            <input id="search-input" type="text" placeholder="Buscar por nombre de voluntariado..."
                   class="w-full bg-slate-900/50 border border-slate-700 text-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-all">
        </div>
        <button id="btn-nuevo"
                class="bg-violet-600 hover:bg-violet-500 text-white px-5 py-2.5 rounded-xl font-medium text-sm flex items-center transition-all shadow-lg shadow-violet-500/20">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Nuevo Voluntariado
        </button>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="glass rounded-2xl overflow-hidden border border-slate-800 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 text-slate-400 text-xs uppercase tracking-widest border-b border-slate-800">
                        <th class="px-6 py-4 font-semibold">#</th>
                        <th class="px-6 py-4 font-semibold">Actividad</th>
                        <th class="px-6 py-4 font-semibold">Fecha</th>
                        <th class="px-6 py-4 font-semibold">Horario</th>
                        <th class="px-6 py-4 font-semibold">Cupo Máx.</th>
                        <th class="px-6 py-4 font-semibold">Estado</th>
                        <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="voluntariados-tbody" class="text-sm divide-y divide-slate-800/50">
                    <tr class="skeleton-row"><td colspan="7" class="px-6 py-4"><div class="skeleton h-6 w-full"></div></td></tr>
                    <tr class="skeleton-row"><td colspan="7" class="px-6 py-4"><div class="skeleton h-6 w-full"></div></td></tr>
                    <tr class="skeleton-row"><td colspan="7" class="px-6 py-4"><div class="skeleton h-6 w-full"></div></td></tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-800 text-sm text-slate-400">
            <span id="resultados-label">Cargando…</span>
        </div>
    </div>
</div>

{{-- =============================================
     MODAL: NUEVO / EDITAR VOLUNTARIADO
     ============================================= --}}
<div id="modal-voluntariado" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-titulo">
    <div class="modal-box">

        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 id="modal-titulo" class="text-lg font-bold text-white">Nuevo Voluntariado</h3>
                <p id="modal-subtitulo" class="text-xs text-slate-500 mt-0.5">Campos marcados con * son obligatorios</p>
            </div>
            <button id="modal-close" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 transition-colors flex-shrink-0 ml-4">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-voluntariado" novalidate class="space-y-5">
            @csrf
            <input type="hidden" id="field-id">

            {{-- ── SECCIÓN 1: Datos Principales ── --}}
            <div class="form-section">
                <p class="form-section-title"><span class="step-badge">1</span> Datos del Voluntariado</p>

                <div class="mb-4">
                    <label class="field-label">Nombre del Voluntariado *</label>
                    <input id="field-nombre" type="text" class="field-input" placeholder="Ej. Limpieza comunitaria">
                    <p class="field-error" id="err-nombre">Campo requerido</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="field-label">Fecha *</label>
                        <input id="field-fecha" type="date" class="field-input">
                        <p class="field-error" id="err-fecha">Campo requerido</p>
                    </div>
                    <div>
                        <label class="field-label">Hora Inicio *</label>
                        <input id="field-hora-inicio" type="time" class="field-input">
                        <p class="field-error" id="err-hora-inicio">Campo requerido</p>
                    </div>
                    <div>
                        <label class="field-label">Hora Fin *</label>
                        <input id="field-hora-fin" type="time" class="field-input">
                        <p class="field-error" id="err-hora-fin">Campo requerido</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Estado *</label>
                        <select id="field-estado" class="field-input">
                            <option value="">— Selecciona —</option>
                            <option value="Activo">Activo</option>
                            <option value="Completado">Completado</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                        <p class="field-error" id="err-estado">Selecciona un estado</p>
                    </div>
                    <div>
                        <label class="field-label">Cupo Máximo (opcional)</label>
                        <input id="field-cupo" type="number" min="1" class="field-input" placeholder="Ej. 30">
                    </div>
                </div>
            </div>

            {{-- ── SECCIÓN 2: Descripción ── --}}
            <div class="form-section">
                <p class="form-section-title"><span class="step-badge">2</span> Descripción y Requisitos</p>
                <div>
                    <label class="field-label">Descripción / Requisitos *</label>
                    <textarea id="field-descripcion" class="field-input" placeholder="Describe los requisitos y objetivos del voluntariado…" rows="3"></textarea>
                    <p class="field-error" id="err-descripcion">Campo requerido</p>
                </div>
            </div>

            {{-- ── SECCIÓN 3: Relaciones Opcionales ── --}}
            <div class="form-section">
                <p class="form-section-title">
                    <span class="step-badge">3</span> Albergue y Campaña
                    <span class="ml-auto text-[10px] font-normal text-slate-500 normal-case tracking-normal">(Opcionales)</span>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Albergue</label>
                        <select id="field-albergue" class="field-input">
                            <option value="">— Sin Albergue —</option>
                        </select>
                        <p id="loading-albergues" class="text-xs text-slate-500 mt-1 hidden">Cargando albergues…</p>
                    </div>
                    <div>
                        <label class="field-label">Campaña</label>
                        <select id="field-campana" class="field-input">
                            <option value="">— Sin Campaña —</option>
                        </select>
                        <p id="loading-campanas" class="text-xs text-slate-500 mt-1 hidden">Cargando campañas…</p>
                    </div>
                </div>
            </div>

            {{-- Error API --}}
            <div id="modal-api-error" class="hidden bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl px-4 py-3 text-sm flex items-start gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                <span id="modal-api-error-text"></span>
            </div>

            {{-- Botones --}}
            <div class="pt-4 border-t border-slate-700 flex justify-end gap-3">
                <button type="button" id="modal-cancel"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-white transition-colors">
                    Cancelar
                </button>
                <button type="submit" id="modal-submit"
                        class="bg-violet-600 hover:bg-violet-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-violet-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    <span id="modal-submit-text">Guardar</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ═══════════════════════════════════════════════
// CONFIG
// ═══════════════════════════════════════════════
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

const ROUTE_INDEX     = '{{ route("admin.voluntariados.index") }}';
const ROUTE_STORE     = '{{ route("admin.voluntariados.store") }}';
const ROUTE_PATCH_TPL = '{{ url("admin/voluntariados") }}/{id}';
const ROUTE_ALBERGUES = '{{ route("admin.catalogos.albergues") }}';
const ROUTE_CAMPANAS  = '{{ route("admin.catalogos.campanas") }}';

const routePatch  = (id) => ROUTE_PATCH_TPL.replace('{id}', id);
const routeDelete = (id) => `${ROUTE_INDEX}/${id}`;

// ═══════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════
let allVoluntariados  = [];
let editingId         = null;
let alberguesCargados = false;
let campanasCargadas  = false;

// ═══════════════════════════════════════════════
// HTTP HELPERS
// ═══════════════════════════════════════════════
async function apiGet(url) {
    const r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
    if (!r.ok) throw new Error(`HTTP ${r.status}`);
    return r.json();
}
async function apiPost(url, body) {
    const r = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify(body),
    });
    return { ok: r.ok, status: r.status, data: await r.json() };
}
async function apiPatch(url, body) {
    const r = await fetch(url, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify(body),
    });
    return { ok: r.ok, status: r.status, data: await r.json() };
}
async function apiDelete(url) {
    const r = await fetch(url, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
    });
    return { ok: r.ok, data: await r.json() };
}

// ═══════════════════════════════════════════════
// BADGE DE ESTADO
// ═══════════════════════════════════════════════
function estadoBadge(estado) {
    const s = (estado || '').toLowerCase();
    if (s === 'activo')     return `<span class="px-2.5 py-1 rounded-full text-[10px] font-bold badge-activo">${estado}</span>`;
    if (s === 'completado') return `<span class="px-2.5 py-1 rounded-full text-[10px] font-bold badge-completado">${estado}</span>`;
    if (s === 'cancelado')  return `<span class="px-2.5 py-1 rounded-full text-[10px] font-bold badge-cancelado">${estado}</span>`;
    return `<span class="px-2.5 py-1 rounded-full text-[10px] font-bold badge-default">${estado ?? '—'}</span>`;
}

// ═══════════════════════════════════════════════
// RENDER TABLE
// ═══════════════════════════════════════════════
function renderTabla(lista) {
    const tbody = document.getElementById('voluntariados-tbody');
    if (!lista.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-12 text-center text-slate-500">
            <i data-lucide="heart-handshake" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
            <p>No se encontraron voluntariados.</p></td></tr>`;
        lucide.createIcons(); return;
    }
    tbody.innerHTML = lista.map(v => {
        const safeName = (v.Nombre_Voluntariado || '').replace(/'/g, "\\'");
        const fecha    = v.Fecha_prog    ? new Date(v.Fecha_prog + 'T12:00:00').toLocaleDateString('es-MX', { day:'2-digit', month:'short', year:'numeric' }) : '—';
        const hInicio  = v.Hora_inicio   ? v.Hora_inicio.substring(0,5) : '—';
        const hFin     = v.Hora_Fin      ? v.Hora_Fin.substring(0,5)    : '—';
        const cupo     = v.Cupo_Max      ? v.Cupo_Max : '∞';
        return `
        <tr class="hover:bg-slate-800/20 transition-colors group" data-id="${v.Id_Voluntariado}">
            <td class="px-6 py-4 text-slate-600 font-mono text-xs">#${v.Id_Voluntariado}</td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-violet-500/10 text-violet-400 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="heart-handshake" class="w-4 h-4"></i>
                    </div>
                    <span class="font-semibold text-slate-200">${v.Nombre_Voluntariado}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-slate-300">${fecha}</td>
            <td class="px-6 py-4 text-slate-400 text-xs">${hInicio} – ${hFin}</td>
            <td class="px-6 py-4">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/20">${cupo}</span>
            </td>
            <td class="px-6 py-4">${estadoBadge(v.Estado_Voluntariado)}</td>
            <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="abrirEditar(${v.Id_Voluntariado})"
                            class="p-2 text-slate-400 hover:text-blue-400 hover:bg-blue-400/10 rounded-lg transition-colors" title="Editar">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                    </button>
                    <button onclick="confirmarEliminar(${v.Id_Voluntariado}, '${safeName}')"
                            class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-400/10 rounded-lg transition-colors" title="Eliminar">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
    lucide.createIcons();
}

// ═══════════════════════════════════════════════
// STATS
// ═══════════════════════════════════════════════
function actualizarStats(lista) {
    document.getElementById('stat-total').textContent   = lista.length;
    document.getElementById('stat-activos').textContent = lista.filter(v => (v.Estado_Voluntariado || '').toLowerCase() === 'activo').length;
    const cupoTotal = lista.reduce((s, v) => s + (v.Cupo_Max || 0), 0);
    document.getElementById('stat-cupo').textContent    = cupoTotal > 0 ? cupoTotal.toLocaleString() : '—';
}

// ═══════════════════════════════════════════════
// FILTROS
// ═══════════════════════════════════════════════
function aplicarFiltros() {
    const q = document.getElementById('search-input').value.toLowerCase().trim();
    const lista = q
        ? allVoluntariados.filter(v => (v.Nombre_Voluntariado || '').toLowerCase().includes(q))
        : [...allVoluntariados];
    renderTabla(lista);
    document.getElementById('resultados-label').textContent =
        `Mostrando ${lista.length} de ${allVoluntariados.length} voluntariados`;
}

// ═══════════════════════════════════════════════
// CARGA INICIAL
// ═══════════════════════════════════════════════
async function cargarVoluntariados() {
    try {
        const data = await apiGet(ROUTE_INDEX + '?json=1');
        allVoluntariados = Array.isArray(data) ? data : [];
        actualizarStats(allVoluntariados);
        aplicarFiltros();
    } catch (e) {
        document.getElementById('voluntariados-tbody').innerHTML =
            `<tr><td colspan="7" class="px-6 py-10 text-center text-rose-400 text-sm">
                <i data-lucide="wifi-off" class="w-8 h-8 mx-auto mb-2"></i>
                <p>Error al cargar voluntariados desde la API.</p>
                <p class="text-slate-500 text-xs mt-1">${e.message}</p>
            </td></tr>`;
        lucide.createIcons();
        document.getElementById('resultados-label').textContent = 'Error de conexión';
    }
}

// ═══════════════════════════════════════════════
// CARGA DE SELECTS — Albergues y Campañas
// ═══════════════════════════════════════════════
async function cargarAlbergues() {
    if (alberguesCargados) return;
    const sel    = document.getElementById('field-albergue');
    const loader = document.getElementById('loading-albergues');
    loader.classList.remove('hidden');
    try {
        const albergues = await apiGet(ROUTE_ALBERGUES);
        sel.innerHTML = '<option value="">— Sin Albergue —</option>';
        albergues.forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.Id_Albergue;
            opt.textContent = a.Nombre_Albergue;
            sel.appendChild(opt);
        });
        alberguesCargados = true;
    } catch { sel.innerHTML = '<option value="">Error al cargar</option>'; }
    finally  { loader.classList.add('hidden'); }
}

async function cargarCampanas() {
    if (campanasCargadas) return;
    const sel    = document.getElementById('field-campana');
    const loader = document.getElementById('loading-campanas');
    loader.classList.remove('hidden');
    try {
        const campanas = await apiGet(ROUTE_CAMPANAS);
        sel.innerHTML = '<option value="">— Sin Campaña —</option>';
        campanas.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id_Campana;
            opt.textContent = c.Nombre_Campana;
            sel.appendChild(opt);
        });
        campanasCargadas = true;
    } catch { sel.innerHTML = '<option value="">Error al cargar</option>'; }
    finally  { loader.classList.add('hidden'); }
}

// ═══════════════════════════════════════════════
// MODAL — helpers
// ═══════════════════════════════════════════════
const modalOverlay = document.getElementById('modal-voluntariado');

function mostrarApiError(msg) {
    document.getElementById('modal-api-error-text').textContent = msg;
    document.getElementById('modal-api-error').classList.remove('hidden');
    lucide.createIcons();
}
function ocultarApiError() { document.getElementById('modal-api-error').classList.add('hidden'); }
function limpiarErrores() {
    document.querySelectorAll('#form-voluntariado .field-input').forEach(el => el.classList.remove('error'));
    document.querySelectorAll('#form-voluntariado .field-error').forEach(el => el.classList.remove('show'));
}
function abrirModal(titulo, subtitulo) {
    document.getElementById('modal-titulo').textContent    = titulo;
    document.getElementById('modal-subtitulo').textContent = subtitulo;
    ocultarApiError(); limpiarErrores();
    modalOverlay.classList.add('open');
    lucide.createIcons();
}
function cerrarModal() {
    modalOverlay.classList.remove('open');
    document.getElementById('form-voluntariado').reset();
    editingId = null;
}

// ═══════════════════════════════════════════════
// ABRIR NUEVO
// ═══════════════════════════════════════════════
document.getElementById('btn-nuevo').addEventListener('click', async () => {
    editingId = null;
    document.getElementById('form-voluntariado').reset();
    document.getElementById('field-id').value = '';
    document.getElementById('modal-submit-text').textContent = 'Crear Voluntariado';
    abrirModal('Nuevo Voluntariado', 'Campos marcados con * son obligatorios');
    await Promise.all([cargarAlbergues(), cargarCampanas()]);
});

// ═══════════════════════════════════════════════
// ABRIR EDITAR
// ═══════════════════════════════════════════════
async function abrirEditar(id) {
    const v = allVoluntariados.find(x => x.Id_Voluntariado === id);
    if (!v) return;
    editingId = id;

    // Cargar selects primero
    await Promise.all([cargarAlbergues(), cargarCampanas()]);

    document.getElementById('field-id').value          = v.Id_Voluntariado;
    document.getElementById('field-nombre').value      = v.Nombre_Voluntariado   ?? '';
    document.getElementById('field-fecha').value       = v.Fecha_prog             ?? '';
    document.getElementById('field-hora-inicio').value = v.Hora_inicio            ? v.Hora_inicio.substring(0,5)  : '';
    document.getElementById('field-hora-fin').value    = v.Hora_Fin               ? v.Hora_Fin.substring(0,5)     : '';
    document.getElementById('field-estado').value      = v.Estado_Voluntariado   ?? '';
    document.getElementById('field-cupo').value        = v.Cupo_Max               ?? '';
    document.getElementById('field-descripcion').value = v.Descripcion_Requisitos ?? '';
    // Relaciones opcionales — deben establecerse DESPUÉS de que las options ya existan
    document.getElementById('field-albergue').value    = v.Id_albergue ?? '';
    document.getElementById('field-campana').value     = v.id_campana  ?? '';

    document.getElementById('modal-submit-text').textContent = 'Guardar Cambios';
    abrirModal('Editar Voluntariado', `Editando: ${v.Nombre_Voluntariado}`);
}

// ═══════════════════════════════════════════════
// VALIDACIÓN
// ═══════════════════════════════════════════════
function validarFormulario() {
    let valid = true;
    limpiarErrores();

    const req = (id, errId, msg) => {
        const val = (document.getElementById(id).value ?? '').trim();
        if (!val) {
            document.getElementById(id).classList.add('error');
            const errEl = document.getElementById(errId);
            if (msg) errEl.textContent = msg;
            errEl.classList.add('show');
            valid = false;
        }
        return val;
    };

    req('field-nombre',      'err-nombre');
    req('field-fecha',       'err-fecha');
    req('field-hora-inicio', 'err-hora-inicio');
    req('field-hora-fin',    'err-hora-fin');
    req('field-estado',      'err-estado', 'Selecciona un estado');
    req('field-descripcion', 'err-descripcion');

    return valid;
}

// ═══════════════════════════════════════════════
// SUBMIT
// ═══════════════════════════════════════════════
document.getElementById('form-voluntariado').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!validarFormulario()) return;

    if (editingId) {
        const conf = await Swal.fire({
            title: '¿Guardar cambios?',
            text: '¿Deseas guardar los cambios en este voluntariado?',
            icon: 'question', background: '#1e293b', color: '#e2e8f0', iconColor: '#8b5cf6',
            showCancelButton: true, confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar', confirmButtonColor: '#7c3aed', cancelButtonColor: '#475569',
        });
        if (!conf.isConfirmed) return;
    }

    const btn = document.getElementById('modal-submit');
    btn.disabled = true;
    ocultarApiError();

    // Leer campos — respetar null en opcionales
    const albergueVal = document.getElementById('field-albergue').value;
    const campanaVal  = document.getElementById('field-campana').value;
    const cupoVal     = document.getElementById('field-cupo').value;

    const payload = {
        Nombre_Voluntariado:   document.getElementById('field-nombre').value.trim(),
        Fecha_prog:            document.getElementById('field-fecha').value,
        Hora_inicio:           document.getElementById('field-hora-inicio').value,
        Hora_Fin:              document.getElementById('field-hora-fin').value,
        Estado_Voluntariado:   document.getElementById('field-estado').value,
        Descripcion_Requisitos: document.getElementById('field-descripcion').value.trim(),
        // Opcionales — null si vacío
        Id_albergue: albergueVal ? parseInt(albergueVal) : null,
        id_campana:  campanaVal  ? parseInt(campanaVal)  : null,
        Cupo_Max:    cupoVal     ? parseInt(cupoVal)     : null,
    };

    document.getElementById('modal-submit-text').textContent = editingId ? 'Actualizando…' : 'Creando…';

    try {
        const res = editingId
            ? await apiPatch(routePatch(editingId), payload)
            : await apiPost(ROUTE_STORE, payload);

        if (res.data.success ?? res.ok) {
            cerrarModal();
            Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: editingId ? 'Voluntariado actualizado' : 'Voluntariado creado exitosamente',
                showConfirmButton: false, timer: 3000,
                background: '#0f172a', color: '#e2e8f0', iconColor: '#8b5cf6',
            });
            cargarVoluntariados();
        } else {
            const msg = res.data.message ?? res.data.detail ?? 'Error desconocido en la API.';
            mostrarApiError(msg);
        }
    } catch {
        mostrarApiError('Error de red al conectar con el servidor.');
    } finally {
        btn.disabled = false;
        document.getElementById('modal-submit-text').textContent = editingId ? 'Guardar Cambios' : 'Crear Voluntariado';
    }
});

// ═══════════════════════════════════════════════
// ELIMINAR
// ═══════════════════════════════════════════════
async function confirmarEliminar(id, nombre) {
    const r = await Swal.fire({
        title: '¿Estás seguro?',
        html: `¿Deseas eliminar <strong>${nombre}</strong>?<br><span class="text-sm opacity-70">Esta acción no se puede deshacer.</span>`,
        icon: 'warning', background: '#1e293b', color: '#e2e8f0', iconColor: '#f43f5e',
        showCancelButton: true, confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar', confirmButtonColor: '#e11d48', cancelButtonColor: '#475569',
    });
    if (!r.isConfirmed) return;

    const res = await apiDelete(routeDelete(id));
    if (res.ok || res.data?.success) {
        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: 'Voluntariado eliminado', showConfirmButton: false, timer: 3000,
            background: '#0f172a', color: '#e2e8f0', iconColor: '#8b5cf6',
        });
        cargarVoluntariados();
    } else {
        Swal.fire({
            icon: 'error', title: 'Error',
            text: res.data?.message ?? res.data?.detail ?? 'No se pudo eliminar.',
            background: '#1e293b', color: '#e2e8f0', confirmButtonColor: '#7c3aed',
        });
    }
}

// ═══════════════════════════════════════════════
// CERRAR MODAL
// ═══════════════════════════════════════════════
document.getElementById('modal-close').addEventListener('click', cerrarModal);
document.getElementById('modal-cancel').addEventListener('click', cerrarModal);
modalOverlay.addEventListener('click', e => { if (e.target === modalOverlay) cerrarModal(); });

// ═══════════════════════════════════════════════
// BÚSQUEDA + INIT
// ═══════════════════════════════════════════════
document.getElementById('search-input').addEventListener('input', aplicarFiltros);
cargarVoluntariados();
</script>
@endpush