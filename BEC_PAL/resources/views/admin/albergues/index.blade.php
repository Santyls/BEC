@extends('layouts.admin')

@section('title', 'Gestión de Albergues')
@section('header_title', 'Albergues')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .fade-in { animation: fadeIn 0.4s ease-in-out; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

    /* ── Modal ── */
    .modal-overlay {
        position: fixed; inset: 0; z-index: 50;
        background: rgba(0,0,0,.7); backdrop-filter: blur(5px);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none; transition: opacity .25s ease;
    }
    .modal-overlay.open { opacity: 1; pointer-events: all; }
    .modal-box {
        background: #1e293b; border: 1px solid rgba(255,255,255,.07);
        border-radius: 1.25rem; padding: 2rem;
        width: 100%; max-width: 660px;
        max-height: 92vh; overflow-y: auto;
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
    .field-input:focus { border-color:#10b981; box-shadow: 0 0 0 3px rgba(16,185,129,.12); }
    .field-input.error { border-color:#f43f5e; }
    .field-input:disabled { opacity:.45; cursor:not-allowed; }
    .field-error { font-size:.75rem; color:#f43f5e; margin-top:.3rem; display:none; }
    .field-error.show { display:block; }

    /* ── Section divider ── */
    .form-section {
        background: rgba(15,23,42,.5);
        border: 1px solid rgba(255,255,255,.06);
        border-radius:.75rem; padding:1rem 1.25rem;
    }
    .form-section-title {
        font-size:.7rem; font-weight:800; text-transform:uppercase;
        letter-spacing:.1em; color:#34d399; margin-bottom:1rem;
        display:flex; align-items:center; gap:.5rem;
    }
    .step-badge {
        display:inline-flex; align-items:center; justify-content:center;
        width:1.4rem; height:1.4rem; border-radius:50%;
        background:#065f46; color:#6ee7b7; font-size:.65rem; font-weight:800;
        flex-shrink:0;
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

    /* ── Filter ── */
    .filter-btn.active { background:rgba(16,185,129,.2); color:#34d399; border-color:rgba(16,185,129,.4); }
</style>
@endpush

@section('content')
<div class="space-y-6 fade-in">

    {{-- ===== MÉTRICA ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="home" class="w-5 h-5 text-emerald-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-widest">Total Albergues</p>
                <p id="stat-total" class="text-2xl font-black text-white">—</p>
            </div>
        </div>
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="users" class="w-5 h-5 text-indigo-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-widest">Cap. Total</p>
                <p id="stat-capacidad" class="text-2xl font-black text-white">—</p>
            </div>
        </div>
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-sky-500/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="map-pin" class="w-5 h-5 text-sky-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-widest">Cap. Promedio</p>
                <p id="stat-promedio" class="text-2xl font-black text-white">—</p>
            </div>
        </div>
    </div>

    {{-- ===== BARRA DE ACCIONES ===== --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="relative w-full sm:w-80">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
            <input id="search-input" type="text" placeholder="Buscar por nombre del albergue..."
                   class="w-full bg-slate-900/50 border border-slate-700 text-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
        </div>
        <button id="btn-nuevo-albergue"
                class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-xl font-medium text-sm flex items-center transition-all shadow-lg shadow-emerald-500/20">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Nuevo Albergue
        </button>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="glass rounded-2xl overflow-hidden border border-slate-800 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 text-slate-400 text-xs uppercase tracking-widest border-b border-slate-800">
                        <th class="px-6 py-4 font-semibold">#</th>
                        <th class="px-6 py-4 font-semibold">Nombre del Albergue</th>
                        <th class="px-6 py-4 font-semibold">Capacidad Máx.</th>
                        <th class="px-6 py-4 font-semibold">Teléfono</th>
                        <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="albergues-tbody" class="text-sm divide-y divide-slate-800/50">
                    <tr class="skeleton-row"><td colspan="5" class="px-6 py-4"><div class="skeleton h-6 w-full"></div></td></tr>
                    <tr class="skeleton-row"><td colspan="5" class="px-6 py-4"><div class="skeleton h-6 w-full"></div></td></tr>
                    <tr class="skeleton-row"><td colspan="5" class="px-6 py-4"><div class="skeleton h-6 w-full"></div></td></tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-800 text-sm text-slate-400">
            <span id="resultados-label">Cargando…</span>
        </div>
    </div>
</div>

{{-- =============================================
     MODAL: NUEVO / EDITAR ALBERGUE
     ============================================= --}}
<div id="modal-albergue" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-titulo">
    <div class="modal-box">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 id="modal-titulo" class="text-lg font-bold text-white">Nuevo Albergue</h3>
                <p id="modal-subtitulo" class="text-xs text-slate-500 mt-0.5">Todos los campos son obligatorios</p>
            </div>
            <button id="modal-close" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 transition-colors flex-shrink-0 ml-4">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-albergue" novalidate class="space-y-5">
            @csrf
            <input type="hidden" id="field-id" name="id">

            {{-- ── SECCIÓN 1: Datos del Albergue ── --}}
            <div class="form-section">
                <p class="form-section-title">
                    <span class="step-badge">1</span> Datos del Albergue
                </p>
                <div class="mb-4">
                    <label class="field-label">Nombre del Albergue *</label>
                    <input id="field-nombre" name="Nombre_Albergue" type="text" class="field-input" placeholder="Ej. Albergue Esperanza">
                    <p class="field-error" id="err-nombre">Campo requerido</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Capacidad Máxima *</label>
                        <input id="field-capacidad" name="Capacidad_Max" type="number" min="1" class="field-input" placeholder="Ej. 150">
                        <p class="field-error" id="err-capacidad">Debe ser un número mayor a 0</p>
                    </div>
                    <div>
                        <label class="field-label">Teléfono de Contacto *</label>
                        <input id="field-tel" name="Tel_Contacto" type="number" class="field-input" placeholder="Ej. 4421234567">
                        <p class="field-error" id="err-tel">Campo requerido</p>
                    </div>
                </div>
            </div>

            {{-- ── SECCIÓN 2: Dirección (OBLIGATORIA) ── --}}
            <div class="form-section">
                <p class="form-section-title">
                    <span class="step-badge">2</span> Dirección del Albergue
                    <span class="ml-auto text-[10px] font-normal text-rose-400 normal-case tracking-normal">* Todos los campos son obligatorios</span>
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="field-label">Estado *</label>
                        <select id="field-estado" class="field-input">
                            <option value="">— Selecciona un estado —</option>
                        </select>
                        <p id="loading-estados" class="text-xs text-slate-500 mt-1 hidden">Cargando estados…</p>
                        <p class="field-error" id="err-estado">Selecciona un estado</p>
                    </div>
                    <div>
                        <label class="field-label">Municipio *</label>
                        <select id="field-municipio" class="field-input" disabled>
                            <option value="">— Elige primero un Estado —</option>
                        </select>
                        <p id="loading-municipios" class="text-xs text-slate-500 mt-1 hidden">Cargando municipios…</p>
                        <p class="field-error" id="err-municipio">Selecciona un municipio</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="field-label">Colonia *</label>
                    <select id="field-colonia" class="field-input" disabled>
                        <option value="">— Elige primero un Municipio —</option>
                    </select>
                    <p id="loading-colonias" class="text-xs text-slate-500 mt-1 hidden">Cargando colonias…</p>
                    <p class="field-error" id="err-colonia">Selecciona una colonia</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Calle *</label>
                        <input id="field-calle" name="Calle" type="text" class="field-input" placeholder="Ej. Av. Hidalgo" disabled>
                        <p class="field-error" id="err-calle">Campo requerido</p>
                    </div>
                    <div>
                        <label class="field-label">Número Exterior *</label>
                        <input id="field-no-ext" name="No_exterior" type="text" class="field-input" placeholder="Ej. 145-B" disabled>
                        <p class="field-error" id="err-no-ext">Campo requerido</p>
                    </div>
                </div>
            </div>

            {{-- Error de API --}}
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
                        class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
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
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content
                || '{{ csrf_token() }}';

const ROUTE_INDEX       = '{{ route("admin.albergues.index") }}';
const ROUTE_STORE       = '{{ route("admin.albergues.store") }}';
const ROUTE_ESTADOS     = '{{ route("admin.catalogos.estados") }}';
const ROUTE_MUN_TPL     = '{{ url("admin/catalogos/estados") }}/{id}/municipios';
const ROUTE_COL_TPL     = '{{ url("admin/catalogos/municipios") }}/{id}/colonias';
const ROUTE_DIR_STORE   = '{{ route("admin.catalogos.direcciones.store") }}';
const ROUTE_PATCH_TPL   = '{{ url("admin/albergues") }}/{id}';

const routePatch   = (id) => ROUTE_PATCH_TPL.replace('{id}', id);
const routeDelete  = (id) => `${ROUTE_INDEX}/${id}`;
const routeMun     = (id) => ROUTE_MUN_TPL.replace('{id}', id);
const routeCol     = (id) => ROUTE_COL_TPL.replace('{id}', id);

// ═══════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════
let allAlbergues   = [];
let editingId      = null;
let estadosCargados = false;

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
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify(body),
    });
    return { ok: r.ok, status: r.status, data: await r.json() };
}
async function apiPatch(url, body) {
    const r = await fetch(url, {
        method: 'PATCH',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify(body),
    });
    return { ok: r.ok, status: r.status, data: await r.json() };
}
async function apiDelete(url) {
    const r = await fetch(url, {
        method: 'DELETE',
        headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
    });
    return { ok: r.ok, data: await r.json() };
}

// ═══════════════════════════════════════════════
// FETCH ALBERGUES (via Laravel proxy)
// ═══════════════════════════════════════════════
async function fetchAlbergues() {
    const data = await apiGet(ROUTE_INDEX + '?json=1');
    return Array.isArray(data) ? data : [];
}

// ═══════════════════════════════════════════════
// RENDER TABLE
// ═══════════════════════════════════════════════
function renderTabla(lista) {
    const tbody = document.getElementById('albergues-tbody');
    if (!lista.length) {
        tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">
            <i data-lucide="home" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
            <p>No se encontraron albergues.</p></td></tr>`;
        lucide.createIcons(); return;
    }
    tbody.innerHTML = lista.map(a => {
        const safeName = (a.Nombre_Albergue || '').replace(/'/g, "\\'");
        return `
        <tr class="hover:bg-slate-800/20 transition-colors group" data-id="${a.Id_Albergue}">
            <td class="px-6 py-4 text-slate-600 font-mono text-xs">#${a.Id_Albergue}</td>
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center mr-3 flex-shrink-0">
                        <i data-lucide="home" class="w-4 h-4"></i>
                    </div>
                    <span class="font-semibold text-slate-200">${a.Nombre_Albergue}</span>
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 text-[11px] font-semibold rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    ${a.Capacidad_Max} personas
                </span>
            </td>
            <td class="px-6 py-4 text-slate-400">${a.Tel_Contacto ?? '—'}</td>
            <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="abrirEditar(${a.Id_Albergue})"
                            class="p-2 text-slate-400 hover:text-blue-400 hover:bg-blue-400/10 rounded-lg transition-colors" title="Editar">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                    </button>
                    <button onclick="confirmarEliminar(${a.Id_Albergue}, '${safeName}')"
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
    document.getElementById('stat-total').textContent = lista.length;
    const totalCap = lista.reduce((s, a) => s + (a.Capacidad_Max || 0), 0);
    document.getElementById('stat-capacidad').textContent = totalCap.toLocaleString();
    const prom = lista.length ? Math.round(totalCap / lista.length) : 0;
    document.getElementById('stat-promedio').textContent = prom.toLocaleString();
}

// ═══════════════════════════════════════════════
// FILTROS / BÚSQUEDA
// ═══════════════════════════════════════════════
function aplicarFiltros() {
    const q = document.getElementById('search-input').value.toLowerCase().trim();
    const lista = q
        ? allAlbergues.filter(a => (a.Nombre_Albergue || '').toLowerCase().includes(q))
        : [...allAlbergues];
    renderTabla(lista);
    document.getElementById('resultados-label').textContent =
        `Mostrando ${lista.length} de ${allAlbergues.length} albergues`;
}

// ═══════════════════════════════════════════════
// CARGA INICIAL
// ═══════════════════════════════════════════════
async function cargarAlbergues() {
    try {
        allAlbergues = await fetchAlbergues();
        actualizarStats(allAlbergues);
        aplicarFiltros();
    } catch (e) {
        document.getElementById('albergues-tbody').innerHTML =
            `<tr><td colspan="5" class="px-6 py-10 text-center text-rose-400 text-sm">
                <i data-lucide="wifi-off" class="w-8 h-8 mx-auto mb-2"></i>
                <p>Error al cargar albergues desde la API.</p>
                <p class="text-slate-500 text-xs mt-1">${e.message}</p>
            </td></tr>`;
        lucide.createIcons();
        document.getElementById('resultados-label').textContent = 'Error de conexión';
    }
}

// ═══════════════════════════════════════════════
// MODAL — helpers
// ═══════════════════════════════════════════════
const modalOverlay = document.getElementById('modal-albergue');

function mostrarApiError(msg) {
    document.getElementById('modal-api-error-text').textContent = msg;
    document.getElementById('modal-api-error').classList.remove('hidden');
    lucide.createIcons();
}
function ocultarApiError() {
    document.getElementById('modal-api-error').classList.add('hidden');
}
function limpiarErrores() {
    document.querySelectorAll('#form-albergue .field-input').forEach(el => el.classList.remove('error'));
    document.querySelectorAll('#form-albergue .field-error').forEach(el => el.classList.remove('show'));
}
function abrirModal(titulo, subtitulo) {
    document.getElementById('modal-titulo').textContent    = titulo;
    document.getElementById('modal-subtitulo').textContent = subtitulo;
    ocultarApiError();
    limpiarErrores();
    modalOverlay.classList.add('open');
    lucide.createIcons();
}
function cerrarModal() {
    modalOverlay.classList.remove('open');
    document.getElementById('form-albergue').reset();
    editingId = null;
    resetDireccionCascade();
    // re-enable calle/no-ext on reset
    ['field-calle','field-no-ext'].forEach(id => document.getElementById(id).disabled = false);
}

// ═══════════════════════════════════════════════
// CASCADING SELECTS — Estados / Municipios / Colonias
// ═══════════════════════════════════════════════
function resetDireccionCascade() {
    const municipioSel = document.getElementById('field-municipio');
    const coloniaSel   = document.getElementById('field-colonia');

    municipioSel.innerHTML = '<option value="">— Elige primero un Estado —</option>';
    municipioSel.disabled = true;
    coloniaSel.innerHTML  = '<option value="">— Elige primero un Municipio —</option>';
    coloniaSel.disabled = true;

    ['field-calle','field-no-ext'].forEach(id => {
        document.getElementById(id).disabled = true;
        document.getElementById(id).value = '';
    });
    document.getElementById('field-estado').value = '';
}

async function cargarEstados() {
    if (estadosCargados) return;
    const sel    = document.getElementById('field-estado');
    const loader = document.getElementById('loading-estados');
    loader.classList.remove('hidden');
    try {
        const estados = await apiGet(ROUTE_ESTADOS);
        sel.innerHTML = '<option value="">— Selecciona un estado —</option>';
        estados.forEach(e => {
            const opt = document.createElement('option');
            opt.value = e.Id_Estado;
            opt.textContent = e.Nombre_Estado;
            sel.appendChild(opt);
        });
        estadosCargados = true;
    } catch {
        sel.innerHTML = '<option value="">Error al cargar estados</option>';
    } finally {
        loader.classList.add('hidden');
    }
}

document.getElementById('field-estado').addEventListener('change', async function () {
    const idEstado = this.value;
    const selMun   = document.getElementById('field-municipio');
    const loader   = document.getElementById('loading-municipios');

    selMun.innerHTML = '<option value="">— Cargando municipios… —</option>';
    selMun.disabled = true;
    document.getElementById('field-colonia').innerHTML = '<option value="">— Elige primero un Municipio —</option>';
    document.getElementById('field-colonia').disabled = true;
    ['field-calle','field-no-ext'].forEach(id => {
        document.getElementById(id).disabled = true;
        document.getElementById(id).value = '';
    });

    if (!idEstado) { selMun.innerHTML = '<option value="">— Elige primero un Estado —</option>'; return; }

    loader.classList.remove('hidden');
    try {
        const municipios = await apiGet(routeMun(idEstado));
        selMun.innerHTML = '<option value="">— Selecciona un municipio —</option>';
        municipios.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.Id_Municipio;
            opt.textContent = m.Nombre_Municipio;
            selMun.appendChild(opt);
        });
        selMun.disabled = false;
    } catch {
        selMun.innerHTML = '<option value="">Error al cargar municipios</option>';
    } finally { loader.classList.add('hidden'); }
});

document.getElementById('field-municipio').addEventListener('change', async function () {
    const idMun   = this.value;
    const selCol  = document.getElementById('field-colonia');
    const loader  = document.getElementById('loading-colonias');

    selCol.innerHTML = '<option value="">— Cargando colonias… —</option>';
    selCol.disabled = true;
    ['field-calle','field-no-ext'].forEach(id => {
        document.getElementById(id).disabled = true;
        document.getElementById(id).value = '';
    });

    if (!idMun) { selCol.innerHTML = '<option value="">— Elige primero un Municipio —</option>'; return; }

    loader.classList.remove('hidden');
    try {
        const colonias = await apiGet(routeCol(idMun));
        selCol.innerHTML = '<option value="">— Selecciona una colonia —</option>';
        colonias.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.Id_Colonia;
            opt.textContent = `${c.Nombre_Colonia} (CP ${c.Cp})`;
            selCol.appendChild(opt);
        });
        selCol.disabled = false;
    } catch {
        selCol.innerHTML = '<option value="">Error al cargar colonias</option>';
    } finally { loader.classList.add('hidden'); }
});

document.getElementById('field-colonia').addEventListener('change', function () {
    const enabled = !!this.value;
    ['field-calle','field-no-ext'].forEach(id => {
        document.getElementById(id).disabled = !enabled;
        if (!enabled) document.getElementById(id).value = '';
    });
});

// ═══════════════════════════════════════════════
// ABRIR NUEVO
// ═══════════════════════════════════════════════
document.getElementById('btn-nuevo-albergue').addEventListener('click', async () => {
    editingId = null;
    document.getElementById('form-albergue').reset();
    document.getElementById('field-id').value = '';
    resetDireccionCascade();
    document.getElementById('modal-submit-text').textContent = 'Crear Albergue';
    abrirModal('Nuevo Albergue', 'Todos los campos son obligatorios');
    await cargarEstados();
});

// ═══════════════════════════════════════════════
// ABRIR EDITAR
// ═══════════════════════════════════════════════
async function abrirEditar(id) {
    const a = allAlbergues.find(x => x.Id_Albergue === id);
    if (!a) return;
    editingId = id;

    document.getElementById('field-id').value        = a.Id_Albergue;
    document.getElementById('field-nombre').value    = a.Nombre_Albergue ?? '';
    document.getElementById('field-capacidad').value = a.Capacidad_Max   ?? '';
    document.getElementById('field-tel').value       = a.Tel_Contacto    ?? '';

    // En edición, la sección de dirección se muestra con nota informativa
    // (no recargamos los selects en cascada con valores viejos ya que requeriría
    // un GET extra para resolver Id_Direccion → Estado/Municipio/Colonia)
    resetDireccionCascade();
    // habilitamos calle y no-ext para que el usuario pueda editar si quiere nueva dir
    document.getElementById('field-calle').disabled   = false;
    document.getElementById('field-no-ext').disabled  = false;

    document.getElementById('modal-subtitulo').textContent =
        'Edita los datos. Si completas la dirección se creará una nueva; si la dejas vacía se conserva la actual.';
    document.getElementById('modal-submit-text').textContent = 'Guardar Cambios';
    abrirModal('Editar Albergue', 'Edita los datos del albergue');
    await cargarEstados();
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

    req('field-nombre',    'err-nombre');
    req('field-tel',       'err-tel');

    const cap = parseInt(document.getElementById('field-capacidad').value);
    if (isNaN(cap) || cap < 1) {
        document.getElementById('field-capacidad').classList.add('error');
        document.getElementById('err-capacidad').classList.add('show');
        valid = false;
    }

    // Dirección OBLIGATORIA (a menos que sea edición sin cambios de dir)
    const isDirSection = document.getElementById('field-estado').value
                      || document.getElementById('field-municipio').value
                      || document.getElementById('field-colonia').value
                      || document.getElementById('field-calle').value.trim()
                      || document.getElementById('field-no-ext').value.trim();

    // En creación SIEMPRE obligatoria. En edición solo si llenaron algo parcial.
    if (!editingId) {
        req('field-estado',    'err-estado',   'Selecciona un estado');
        req('field-municipio', 'err-municipio','Selecciona un municipio');
        req('field-colonia',   'err-colonia',  'Selecciona una colonia');
        req('field-calle',     'err-calle');
        req('field-no-ext',    'err-no-ext');
    } else if (isDirSection) {
        // Si llenaron algo de dirección en edición, deben completarla toda
        req('field-estado',    'err-estado',   'Completa todos los campos de dirección');
        req('field-municipio', 'err-municipio','Completa todos los campos de dirección');
        req('field-colonia',   'err-colonia',  'Completa todos los campos de dirección');
        req('field-calle',     'err-calle');
        req('field-no-ext',    'err-no-ext');
    }

    return valid;
}

// ═══════════════════════════════════════════════
// SUBMIT — flujo 2 pasos
// ═══════════════════════════════════════════════
document.getElementById('form-albergue').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!validarFormulario()) return;

    if (editingId) {
        const confirm = await Swal.fire({
            title: '¿Guardar cambios?',
            text: '¿Deseas guardar los cambios en este albergue?',
            icon: 'question', background: '#1e293b', color: '#e2e8f0', iconColor: '#10b981',
            showCancelButton: true, confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar', confirmButtonColor: '#10b981', cancelButtonColor: '#475569',
        });
        if (!confirm.isConfirmed) return;
    }

    const btn = document.getElementById('modal-submit');
    btn.disabled = true;
    ocultarApiError();

    // ── Datos base del albergue ──
    const payload = {
        Nombre_Albergue: document.getElementById('field-nombre').value.trim(),
        Capacidad_Max:   parseInt(document.getElementById('field-capacidad').value),
        Tel_Contacto:    parseInt(document.getElementById('field-tel').value),
    };

    // ── PASO 1: Crear dirección si hay datos ──
    const idColonia = document.getElementById('field-colonia').value;
    const calle     = document.getElementById('field-calle').value.trim();
    const noExt     = document.getElementById('field-no-ext').value.trim();
    const hayDir    = idColonia && calle && noExt;

    if (hayDir) {
        document.getElementById('modal-submit-text').textContent = 'Creando dirección…';
        const dirRes = await apiPost(ROUTE_DIR_STORE, {
            Id_Colonia:  parseInt(idColonia),
            Calle:       calle,
            No_exterior: noExt,
        });
        if (!dirRes.ok || !dirRes.data.success) {
            mostrarApiError(dirRes.data.message ?? 'Error al crear la dirección. El albergue no fue guardado.');
            btn.disabled = false;
            document.getElementById('modal-submit-text').textContent = editingId ? 'Guardar Cambios' : 'Crear Albergue';
            return; // DETENER — no continuar sin dirección
        }
        // ── PASO 2: Capturar Id_Direccion ──
        payload.Id_Direccion = dirRes.data.data?.Id_Direccion;
    }

    // CREAR: debe tener Id_Direccion
    if (!editingId && !payload.Id_Direccion) {
        mostrarApiError('No se pudo obtener el ID de la dirección. Intenta de nuevo.');
        btn.disabled = false;
        document.getElementById('modal-submit-text').textContent = 'Crear Albergue';
        return;
    }

    // ── PASO 3: POST/PATCH albergue ──
    document.getElementById('modal-submit-text').textContent = editingId ? 'Actualizando…' : 'Creando albergue…';

    try {
        const res = editingId
            ? await apiPatch(routePatch(editingId), payload)
            : await apiPost(ROUTE_STORE, payload);

        if (res.data.success ?? (res.ok)) {
            cerrarModal();
            Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: editingId ? 'Albergue actualizado' : 'Albergue creado exitosamente',
                showConfirmButton: false, timer: 3000,
                background: '#0f172a', color: '#e2e8f0', iconColor: '#10b981',
            });
            cargarAlbergues();
        } else {
            const msg = res.data.message ?? res.data.detail ?? 'Error desconocido en la API.';
            mostrarApiError(msg);
        }
    } catch (err) {
        mostrarApiError('Error de red al conectar con el servidor.');
    } finally {
        btn.disabled = false;
        document.getElementById('modal-submit-text').textContent = editingId ? 'Guardar Cambios' : 'Crear Albergue';
    }
});

// ═══════════════════════════════════════════════
// ELIMINAR
// ═══════════════════════════════════════════════
async function confirmarEliminar(id, nombre) {
    const r = await Swal.fire({
        title: '¿Eliminar albergue?',
        html: `¿Estás seguro de eliminar <strong>${nombre}</strong>?<br><span class="text-sm opacity-70">Esta acción es irreversible.</span>`,
        icon: 'warning', background: '#1e293b', color: '#e2e8f0', iconColor: '#f43f5e',
        showCancelButton: true, confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar', confirmButtonColor: '#e11d48', cancelButtonColor: '#475569',
    });
    if (!r.isConfirmed) return;

    const res = await apiDelete(routeDelete(id));
    if (res.ok || res.data?.success) {
        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: 'Albergue eliminado', showConfirmButton: false, timer: 3000,
            background: '#0f172a', color: '#e2e8f0', iconColor: '#10b981',
        });
        cargarAlbergues();
    } else {
        Swal.fire({
            icon: 'error', title: 'Error',
            text: res.data?.message ?? res.data?.detail ?? 'No se pudo eliminar el albergue.',
            background: '#1e293b', color: '#e2e8f0', confirmButtonColor: '#10b981',
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
// BÚSQUEDA
// ═══════════════════════════════════════════════
document.getElementById('search-input').addEventListener('input', aplicarFiltros);

// ═══════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════
cargarAlbergues();
</script>
@endpush