@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')
@section('header_title', 'Usuarios & Roles')

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
        width: 100%; max-width: 640px;
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
    .field-input:focus { border-color:#3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
    .field-input.error { border-color:#f43f5e; }
    .field-input:disabled { opacity:.45; cursor:not-allowed; }
    .field-error { font-size:.75rem; color:#f43f5e; margin-top:.3rem; display:none; }
    .field-error.show { display:block; }

    /* ── Section divider inside modal ── */
    .form-section {
        background: rgba(15,23,42,.5);
        border: 1px solid rgba(255,255,255,.06);
        border-radius:.75rem; padding:1rem 1.25rem;
    }
    .form-section-title {
        font-size:.7rem; font-weight:800; text-transform:uppercase;
        letter-spacing:.1em; color:#60a5fa; margin-bottom:1rem;
        display:flex; align-items:center; gap:.5rem;
    }

    /* ── Role badges ── */
    .badge-admin    { background:rgba(99,102,241,.15); color:#a5b4fc; border:1px solid rgba(99,102,241,.3); }
    .badge-recep    { background:rgba(14,165,233,.15);  color:#7dd3fc; border:1px solid rgba(14,165,233,.3); }
    .badge-ciudadano{ background:rgba(59,130,246,.15);  color:#93c5fd; border:1px solid rgba(59,130,246,.3); }
    .badge-default  { background:rgba(100,116,139,.15); color:#94a3b8; border:1px solid rgba(100,116,139,.3); }

    /* ── Skeleton ── */
    .skeleton { background:linear-gradient(90deg,#1e293b 25%,#273349 50%,#1e293b 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; border-radius:.4rem; }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

    /* ── Filter buttons ── */
    .filter-btn.active { background:rgba(59,130,246,.2); color:#60a5fa; border-color:rgba(59,130,246,.4); }

    /* ── Step indicator in modal ── */
    .step-badge {
        display:inline-flex; align-items:center; justify-content:center;
        width:1.4rem; height:1.4rem; border-radius:50%;
        background:#1e40af; color:#bfdbfe; font-size:.65rem; font-weight:800;
        flex-shrink:0;
    }

    /* ── Select arrow ── */
    select.field-input {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .85rem center;
        padding-right: 2.25rem;
    }
</style>
@endpush

@section('content')
<div class="space-y-6 fade-in">

    {{-- ===== MÉTRICAS (3 roles) ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="users" class="w-5 h-5 text-blue-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-widest">Total Usuarios</p>
                <p id="stat-total" class="text-2xl font-black text-white">—</p>
            </div>
        </div>
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="shield" class="w-5 h-5 text-indigo-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-widest">Administradores</p>
                <p id="stat-admin" class="text-2xl font-black text-white">—</p>
            </div>
        </div>
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-sky-500/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="user" class="w-5 h-5 text-sky-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-widest">Ciudadanos</p>
                <p id="stat-ciudadanos" class="text-2xl font-black text-white">—</p>
            </div>
        </div>
    </div>

    {{-- ===== BARRA DE ACCIONES ===== --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="relative w-full sm:w-80">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
            <input id="search-input" type="text" placeholder="Buscar por nombre o correo..."
                   class="w-full bg-slate-900/50 border border-slate-700 text-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            {{-- Filtros por rol (solo 3) --}}
            <div class="flex gap-1.5">
                <button data-role-filter="all" class="filter-btn active text-xs px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 transition-all hover:text-blue-400">Todos</button>
                <button data-role-filter="1"   class="filter-btn text-xs px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 transition-all hover:text-blue-400">Admin</button>
                <button data-role-filter="2"   class="filter-btn text-xs px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 transition-all hover:text-blue-400">Recep.</button>
                <button data-role-filter="3"   class="filter-btn text-xs px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 transition-all hover:text-blue-400">Ciudadano</button>
            </div>

            <button id="btn-nuevo-usuario"
                    class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-medium text-sm flex items-center transition-all shadow-lg shadow-blue-500/20">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Nuevo Usuario
            </button>
        </div>
    </div>

    {{-- Flash messages (no-JS fallback) --}}
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    {{-- ===== TABLA ===== --}}
    <div class="glass rounded-2xl overflow-hidden border border-slate-800 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 text-slate-400 text-xs uppercase tracking-widest border-b border-slate-800">
                        <th class="px-6 py-4 font-semibold">#</th>
                        <th class="px-6 py-4 font-semibold">Nombre</th>
                        <th class="px-6 py-4 font-semibold">Correo</th>
                        <th class="px-6 py-4 font-semibold">Rol</th>
                        <th class="px-6 py-4 font-semibold">Tel.</th>
                        <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="usuarios-tbody" class="text-sm divide-y divide-slate-800/50">
                    <tr class="skeleton-row"><td colspan="6" class="px-6 py-4"><div class="skeleton h-6 w-full"></div></td></tr>
                    <tr class="skeleton-row"><td colspan="6" class="px-6 py-4"><div class="skeleton h-6 w-full"></div></td></tr>
                    <tr class="skeleton-row"><td colspan="6" class="px-6 py-4"><div class="skeleton h-6 w-full"></div></td></tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-800 text-sm text-slate-400">
            <span id="resultados-label">Cargando…</span>
        </div>
    </div>
</div>

{{-- =============================================
     MODAL: NUEVO / EDITAR USUARIO
     ============================================= --}}
<div id="modal-usuario" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-titulo">
    <div class="modal-box">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 id="modal-titulo" class="text-lg font-bold text-white">Nuevo Usuario</h3>
                <p id="modal-subtitulo" class="text-xs text-slate-500 mt-0.5">Campos marcados con * son obligatorios</p>
            </div>
            <button id="modal-close" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 transition-colors flex-shrink-0 ml-4">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-usuario" novalidate class="space-y-5">
            @csrf
            <input type="hidden" id="field-id" name="id">

            {{-- ── SECCIÓN 1: Datos personales ── --}}
            <div class="form-section">
                <p class="form-section-title">
                    <span class="step-badge">1</span> Datos Personales
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="field-label">Nombre *</label>
                        <input id="field-nombre" name="Nombre" type="text" class="field-input" placeholder="Juan">
                        <p class="field-error" id="err-nombre">Campo requerido</p>
                    </div>
                    <div>
                        <label class="field-label">Apellido Paterno *</label>
                        <input id="field-apellido-p" name="Apellido_P" type="text" class="field-input" placeholder="García">
                        <p class="field-error" id="err-apellido-p">Campo requerido</p>
                    </div>
                    <div>
                        <label class="field-label">Apellido Materno *</label>
                        <input id="field-apellido-m" name="Apellido_M" type="text" class="field-input" placeholder="López">
                        <p class="field-error" id="err-apellido-m">Campo requerido</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="field-label">Edad *</label>
                        <input id="field-edad" name="Edad" type="number" min="1" max="120" class="field-input" placeholder="25">
                        <p class="field-error" id="err-edad">Edad inválida (1-120)</p>
                    </div>
                    <div>
                        <label class="field-label">Teléfono *</label>
                        <input id="field-tel" name="Tel" type="number" class="field-input" placeholder="4421234567">
                        <p class="field-error" id="err-tel">Campo requerido</p>
                    </div>
                    <div>
                        <label class="field-label">Género *</label>
                        <select id="field-genero" name="Id_Genero" class="field-input">
                            <option value="">Seleccionar…</option>
                            <option value="1">Masculino</option>
                            <option value="2">Femenino</option>
                            <option value="3">Otro</option>
                        </select>
                        <p class="field-error" id="err-genero">Selecciona un género</p>
                    </div>
                </div>
            </div>

            {{-- ── SECCIÓN 2: Cuenta ── --}}
            <div class="form-section">
                <p class="form-section-title">
                    <span class="step-badge">2</span> Cuenta y Acceso
                </p>
                <div class="mb-4">
                    <label class="field-label">Correo Electrónico *</label>
                    <input id="field-correo" name="Correo" type="email" class="field-input" placeholder="usuario@ejemplo.com">
                    <p class="field-error" id="err-correo">Ingresa un correo válido</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="field-label">Contraseña *</label>
                        <input id="field-password" name="Password" type="password" class="field-input" placeholder="Mínimo 6 caracteres">
                        <p class="field-error" id="err-password">Mínimo 6 caracteres</p>
                    </div>
                    <div>
                        <label class="field-label">Confirmar Contraseña *</label>
                        <input id="field-password-confirm" type="password" class="field-input" placeholder="Repite la contraseña">
                        <p class="field-error" id="err-password-confirm">Las contraseñas no coinciden</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Rol *</label>
                        <select id="field-rol" name="Id_Rol" class="field-input">
                            <option value="">Seleccionar…</option>
                            <option value="1">Administrador</option>
                            <option value="2">Recepcionista</option>
                            <option value="3">Ciudadano</option>
                        </select>
                        <p class="field-error" id="err-rol">Selecciona un rol</p>
                    </div>
                    <div>
                        <label class="field-label">ID Albergue (opcional)</label>
                        <input id="field-albergue" name="Id_Albergue" type="number" min="1" class="field-input" placeholder="Dejar vacío si no aplica">
                    </div>
                </div>
            </div>

            {{-- ── SECCIÓN 3: Dirección (opcional, en cascada) ── --}}
            <div class="form-section">
                <p class="form-section-title">
                    <span class="step-badge">3</span> Datos de Dirección
                    <span class="ml-auto text-[10px] font-normal text-slate-500 normal-case tracking-normal">(Opcional — se crea antes del usuario)</span>
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    {{-- Estado --}}
                    <div>
                        <label class="field-label">Estado</label>
                        <select id="field-estado" class="field-input">
                            <option value="">— Selecciona un estado —</option>
                        </select>
                        <p id="loading-estados" class="text-xs text-slate-500 mt-1 hidden">Cargando estados…</p>
                    </div>
                    {{-- Municipio --}}
                    <div>
                        <label class="field-label">Municipio</label>
                        <select id="field-municipio" class="field-input" disabled>
                            <option value="">— Elige primero un Estado —</option>
                        </select>
                        <p id="loading-municipios" class="text-xs text-slate-500 mt-1 hidden">Cargando municipios…</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="field-label">Colonia</label>
                    <select id="field-colonia" class="field-input" disabled>
                        <option value="">— Elige primero un Municipio —</option>
                    </select>
                    <p id="loading-colonias" class="text-xs text-slate-500 mt-1 hidden">Cargando colonias…</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Calle</label>
                        <input id="field-calle" name="Calle" type="text" class="field-input" placeholder="Ej. Av. Hidalgo" disabled>
                    </div>
                    <div>
                        <label class="field-label">Número Exterior</label>
                        <input id="field-no-ext" name="No_exterior" type="text" class="field-input" placeholder="Ej. 145-B" disabled>
                    </div>
                </div>

                <p class="text-xs text-slate-600 mt-3 flex items-center gap-1.5">
                    <i data-lucide="info" class="w-3 h-3 flex-shrink-0 text-slate-500"></i>
                    Si llenas Estado + Municipio + Colonia + Calle + No. Ext., se creará la dirección automáticamente al guardar.
                </p>
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
                        class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
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

// Laravel proxy routes
const ROUTE_INDEX          = '{{ route("admin.usuarios.index") }}';
const ROUTE_STORE          = '{{ route("admin.usuarios.store") }}';
const ROUTE_ESTADOS        = '{{ route("admin.catalogos.estados") }}';
const ROUTE_MUNICIPIOS_TPL = '{{ url("admin/catalogos/estados") }}/{id}/municipios';
const ROUTE_COLONIAS_TPL   = '{{ url("admin/catalogos/municipios") }}/{id}/colonias';
const ROUTE_DIR_STORE      = '{{ route("admin.catalogos.direcciones.store") }}';

const ROUTE_PATCH = '{{ url("admin/usuarios") }}/{id}';

const routeUpdate  = (id) => ROUTE_PATCH.replace('{id}', id);
const routeDestroy = (id) => `${ROUTE_INDEX}/${id}`;
const routeMunicipios = (idEstado)    => ROUTE_MUNICIPIOS_TPL.replace('{id}', idEstado);
const routeColonias   = (idMunicipio) => ROUTE_COLONIAS_TPL.replace('{id}', idMunicipio);

// ═══════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════
let allUsuarios      = [];
let activeRoleFilter = 'all';
let editingId        = null;

// ═══════════════════════════════════════════════
// ROLE MAP  (solo 3 roles)
// ═══════════════════════════════════════════════
const roleMap = {
    1: { label: 'Administrador', badge: 'badge-admin'     },
    2: { label: 'Recepcionista', badge: 'badge-recep'     },
    3: { label: 'Ciudadano',     badge: 'badge-ciudadano' },
};
function getRoleInfo(id) {
    return roleMap[id] ?? { label: 'Desconocido', badge: 'badge-default' };
}
function avatarColor(id) {
    const c = ['bg-blue-500/20 text-blue-400','bg-purple-500/20 text-purple-400',
               'bg-emerald-500/20 text-emerald-400','bg-amber-500/20 text-amber-400',
               'bg-rose-500/20 text-rose-400','bg-sky-500/20 text-sky-400'];
    return c[id % c.length];
}

// ═══════════════════════════════════════════════
// HTTP HELPER
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
async function apiPut(url, body) {
    const r = await fetch(url, {
        method: 'PUT',
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
// FETCH USUARIOS (via Laravel proxy)
// ═══════════════════════════════════════════════
async function fetchUsuarios() {
    const data = await apiGet(ROUTE_INDEX + '?json=1');
    return Array.isArray(data) ? data : [];
}

// ═══════════════════════════════════════════════
// RENDER TABLE
// ═══════════════════════════════════════════════
function renderTabla(lista) {
    const tbody = document.getElementById('usuarios-tbody');
    if (!lista.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">
            <i data-lucide="users" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
            <p>No se encontraron usuarios.</p></td></tr>`;
        lucide.createIcons(); return;
    }
    tbody.innerHTML = lista.map(u => {
        const nombre   = `${u.Nombre} ${u.Apellido_P} ${u.Apellido_M}`;
        const inicial  = u.Nombre?.charAt(0).toUpperCase() ?? '?';
        const role     = getRoleInfo(u.Id_Rol);
        const avatar   = avatarColor(u.id_Usuario);
        const safeName = nombre.replace(/'/g, "\\'");
        return `
        <tr class="hover:bg-slate-800/20 transition-colors group" data-id="${u.id_Usuario}" data-rol="${u.Id_Rol}">
            <td class="px-6 py-4 text-slate-600 font-mono text-xs">#${u.id_Usuario}</td>
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full ${avatar} flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0">${inicial}</div>
                    <span class="font-medium text-slate-200">${nombre}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-slate-400">${u.Correo}</td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 text-[11px] font-semibold rounded-full ${role.badge}">${role.label}</span>
            </td>
            <td class="px-6 py-4 text-slate-400">${u.Tel ?? '—'}</td>
            <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="abrirEditar(${u.id_Usuario})"
                            class="p-2 text-slate-400 hover:text-blue-400 hover:bg-blue-400/10 rounded-lg transition-colors" title="Editar">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                    </button>
                    <button onclick="confirmarEliminar(${u.id_Usuario}, '${safeName}')"
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
// STATS (solo Total + Admins + Ciudadanos)
// ═══════════════════════════════════════════════
function actualizarStats(lista) {
    document.getElementById('stat-total').textContent      = lista.length;
    document.getElementById('stat-admin').textContent      = lista.filter(u => u.Id_Rol === 1).length;
    document.getElementById('stat-ciudadanos').textContent = lista.filter(u => u.Id_Rol === 3).length;
}

// ═══════════════════════════════════════════════
// FILTROS
// ═══════════════════════════════════════════════
function aplicarFiltros() {
    const q = document.getElementById('search-input').value.toLowerCase().trim();
    let lista = [...allUsuarios];
    if (activeRoleFilter !== 'all') lista = lista.filter(u => String(u.Id_Rol) === activeRoleFilter);
    if (q) lista = lista.filter(u => {
        const n = `${u.Nombre} ${u.Apellido_P} ${u.Apellido_M}`.toLowerCase();
        return n.includes(q) || (u.Correo ?? '').toLowerCase().includes(q);
    });
    renderTabla(lista);
    document.getElementById('resultados-label').textContent =
        `Mostrando ${lista.length} de ${allUsuarios.length} usuarios`;
}

// ═══════════════════════════════════════════════
// CARGA INICIAL
// ═══════════════════════════════════════════════
async function cargarUsuarios() {
    try {
        allUsuarios = await fetchUsuarios();
        actualizarStats(allUsuarios);
        aplicarFiltros();
    } catch (e) {
        document.getElementById('usuarios-tbody').innerHTML =
            `<tr><td colspan="6" class="px-6 py-10 text-center text-rose-400 text-sm">
                <i data-lucide="wifi-off" class="w-8 h-8 mx-auto mb-2"></i>
                <p>Error al cargar usuarios desde la API.</p>
                <p class="text-slate-500 text-xs mt-1">${e.message}</p>
            </td></tr>`;
        lucide.createIcons();
        document.getElementById('resultados-label').textContent = 'Error de conexión';
    }
}

// ═══════════════════════════════════════════════
// MODAL  — helpers
// ═══════════════════════════════════════════════
const modalOverlay = document.getElementById('modal-usuario');

function mostrarApiError(msg) {
    document.getElementById('modal-api-error-text').textContent = msg;
    document.getElementById('modal-api-error').classList.remove('hidden');
    lucide.createIcons();
}
function ocultarApiError() {
    document.getElementById('modal-api-error').classList.add('hidden');
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
    document.getElementById('form-usuario').reset();
    editingId = null;
    resetDireccionCascade();
}
function limpiarErrores() {
    document.querySelectorAll('.field-input').forEach(el => el.classList.remove('error'));
    document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));
}

// ═══════════════════════════════════════════════
// CASCADING SELECTS — Estados / Municipios / Colonias
// ═══════════════════════════════════════════════
let estadosCargados = false;

function resetDireccionCascade() {
    ['field-municipio','field-colonia'].forEach(id => {
        const el = document.getElementById(id);
        el.innerHTML = id === 'field-municipio'
            ? '<option value="">— Elige primero un Estado —</option>'
            : '<option value="">— Elige primero un Municipio —</option>';
        el.disabled = true;
    });
    ['field-calle','field-no-ext'].forEach(id => {
        document.getElementById(id).disabled = true;
        document.getElementById(id).value = '';
    });
}

async function cargarEstados() {
    if (estadosCargados) return;
    const sel = document.getElementById('field-estado');
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

document.getElementById('field-estado').addEventListener('change', async function() {
    const idEstado = this.value;
    const selMun = document.getElementById('field-municipio');
    const loader  = document.getElementById('loading-municipios');

    // reset downstream
    selMun.innerHTML = '<option value="">— Cargando municipios… —</option>';
    selMun.disabled = true;
    document.getElementById('field-colonia').innerHTML = '<option value="">— Elige primero un Municipio —</option>';
    document.getElementById('field-colonia').disabled = true;
    ['field-calle','field-no-ext'].forEach(id => { document.getElementById(id).disabled = true; document.getElementById(id).value = ''; });

    if (!idEstado) {
        selMun.innerHTML = '<option value="">— Elige primero un Estado —</option>';
        return;
    }
    loader.classList.remove('hidden');
    try {
        const municipios = await apiGet(routeMunicipios(idEstado));
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
    } finally {
        loader.classList.add('hidden');
    }
});

document.getElementById('field-municipio').addEventListener('change', async function() {
    const idMun = this.value;
    const selCol = document.getElementById('field-colonia');
    const loader  = document.getElementById('loading-colonias');

    selCol.innerHTML = '<option value="">— Cargando colonias… —</option>';
    selCol.disabled = true;
    ['field-calle','field-no-ext'].forEach(id => { document.getElementById(id).disabled = true; document.getElementById(id).value = ''; });

    if (!idMun) {
        selCol.innerHTML = '<option value="">— Elige primero un Municipio —</option>';
        return;
    }
    loader.classList.remove('hidden');
    try {
        const colonias = await apiGet(routeColonias(idMun));
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
    } finally {
        loader.classList.add('hidden');
    }
});

document.getElementById('field-colonia').addEventListener('change', function() {
    const enabled = !!this.value;
    ['field-calle','field-no-ext'].forEach(id => {
        document.getElementById(id).disabled = !enabled;
        if (!enabled) document.getElementById(id).value = '';
    });
});

// ═══════════════════════════════════════════════
// ABRIR NUEVO USUARIO
// ═══════════════════════════════════════════════
document.getElementById('btn-nuevo-usuario').addEventListener('click', async () => {
    editingId = null;
    document.getElementById('form-usuario').reset();
    document.getElementById('field-id').value = '';
    resetDireccionCascade();
    document.getElementById('modal-submit-text').textContent = 'Crear Usuario';
    abrirModal('Nuevo Usuario', 'Campos marcados con * son obligatorios');
    await cargarEstados();
});

// ═══════════════════════════════════════════════
// ABRIR EDITAR
// ═══════════════════════════════════════════════
async function abrirEditar(id) {
    const u = allUsuarios.find(x => x.id_Usuario === id);
    if (!u) return;
    editingId = id;

    document.getElementById('field-id').value         = u.id_Usuario;
    document.getElementById('field-nombre').value     = u.Nombre      ?? '';
    document.getElementById('field-apellido-p').value = u.Apellido_P  ?? '';
    document.getElementById('field-apellido-m').value = u.Apellido_M  ?? '';
    document.getElementById('field-correo').value     = u.Correo      ?? '';
    document.getElementById('field-edad').value       = u.Edad        ?? '';
    document.getElementById('field-tel').value        = u.Tel         ?? '';
    document.getElementById('field-genero').value     = u.Id_Genero   ?? '';
    document.getElementById('field-rol').value        = u.Id_Rol      ?? '';
    document.getElementById('field-albergue').value   = u.Id_Albergue ?? '';
    document.getElementById('field-password').value         = '';
    document.getElementById('field-password-confirm').value = '';

    resetDireccionCascade();
    document.getElementById('modal-submit-text').textContent = 'Guardar Cambios';
    abrirModal('Editar Usuario', `Editando: ${u.Nombre} ${u.Apellido_P}`);
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

    req('field-nombre',     'err-nombre');
    req('field-apellido-p', 'err-apellido-p');
    req('field-apellido-m', 'err-apellido-m');
    req('field-genero',     'err-genero');
    req('field-rol',        'err-rol');

    // Correo
    const correo = document.getElementById('field-correo').value.trim();
    if (!correo || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
        document.getElementById('field-correo').classList.add('error');
        document.getElementById('err-correo').classList.add('show');
        valid = false;
    }

    // Edad
    const edad = parseInt(document.getElementById('field-edad').value);
    if (isNaN(edad) || edad < 1 || edad > 120) {
        document.getElementById('field-edad').classList.add('error');
        document.getElementById('err-edad').classList.add('show');
        valid = false;
    }

    // Teléfono
    if (!document.getElementById('field-tel').value.trim()) {
        document.getElementById('field-tel').classList.add('error');
        document.getElementById('err-tel').classList.add('show');
        valid = false;
    }

    // Password
    const pass    = document.getElementById('field-password').value;
    const confirm = document.getElementById('field-password-confirm').value;
    if (!editingId && pass.length < 6) {
        document.getElementById('field-password').classList.add('error');
        document.getElementById('err-password').classList.add('show');
        valid = false;
    }
    if (pass && pass !== confirm) {
        document.getElementById('field-password-confirm').classList.add('error');
        document.getElementById('err-password-confirm').classList.add('show');
        valid = false;
    }

    return valid;
}

// ═══════════════════════════════════════════════
// HELPERS — leer dirección del formulario
// ═══════════════════════════════════════════════
function getDireccionData() {
    const idColonia = document.getElementById('field-colonia').value;
    const calle     = document.getElementById('field-calle').value.trim();
    const noExt     = document.getElementById('field-no-ext').value.trim();
    // Consideramos que la dirección está "llena" si hay colonia + calle + no ext.
    const llena = idColonia && calle && noExt;
    return llena ? { Id_Colonia: parseInt(idColonia), Calle: calle, No_exterior: noExt } : null;
}

// ═══════════════════════════════════════════════
// SUBMIT — flujo de 2 pasos
// ═══════════════════════════════════════════════
document.getElementById('form-usuario').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!validarFormulario()) return;

    // Confirmación antes de actualizar
    if (editingId) {
        const r = await Swal.fire({
            title: '¿Aplicar cambios?',
            text: '¿Estás seguro de que deseas aplicar estos cambios al usuario?',
            icon: 'question', background: '#1e293b', color: '#e2e8f0', iconColor: '#3b82f6',
            showCancelButton: true, confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar', confirmButtonColor: '#3b82f6', cancelButtonColor: '#475569',
        });
        if (!r.isConfirmed) return;
    }

    const btn = document.getElementById('modal-submit');
    btn.disabled = true;
    ocultarApiError();

    // ── PASO 1: Evaluar si hay dirección ──
    const dirData = getDireccionData();
    let idDireccion = null;

    if (dirData) {
        document.getElementById('modal-submit-text').textContent = 'Creando dirección…';
        // ── PASO 2: POST /direcciones ──
        const dirRes = await apiPost(ROUTE_DIR_STORE, dirData);
        if (!dirRes.ok || !dirRes.data.success) {
            mostrarApiError(dirRes.data.message ?? 'Error al crear la dirección. El usuario no fue creado.');
            btn.disabled = false;
            document.getElementById('modal-submit-text').textContent = editingId ? 'Guardar Cambios' : 'Crear Usuario';
            return; // DETENER — no crear usuario sin dirección
        }
        // ── PASO 3: Capturar ID ──
        idDireccion = dirRes.data.data?.Id_Direccion ?? null;
    }

    // ── PASO 4: POST / PATCH usuario ──
    document.getElementById('modal-submit-text').textContent = editingId ? 'Actualizando…' : 'Creando usuario…';

    const pass = document.getElementById('field-password').value;
    const payload = {
        Nombre:       document.getElementById('field-nombre').value.trim(),
        Apellido_P:   document.getElementById('field-apellido-p').value.trim(),
        Apellido_M:   document.getElementById('field-apellido-m').value.trim(),
        Correo:       document.getElementById('field-correo').value.trim(),
        Edad:         parseInt(document.getElementById('field-edad').value),
        Tel:          parseInt(document.getElementById('field-tel').value),
        Id_Rol:       parseInt(document.getElementById('field-rol').value),
        Id_Genero:    parseInt(document.getElementById('field-genero').value),
    };

    // Include Password only if provided (on create it is required by UI validation, on edit it is optional)
    if (pass) {
        payload.Password = pass;
    }

    // Include Address ID if we just created one OR if we are in edit mode and want to preserve current one (handled by API)
    if (idDireccion) {
        payload.Id_Direccion = idDireccion;
    } else if (!editingId) {
        payload.Id_Direccion = 1; // Default
    }

    const albergue = document.getElementById('field-albergue').value;
    if (albergue) payload.Id_Albergue = parseInt(albergue);

    try {
        const res = editingId
            ? await apiPatch(routeUpdate(editingId), payload)
            : await apiPost(ROUTE_STORE, payload);

        if (res.data.success) {
            cerrarModal();
            Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: res.data.message, showConfirmButton: false, timer: 3000,
                background: '#0f172a', color: '#e2e8f0', iconColor: '#10b981',
            });
            cargarUsuarios();
        } else {
            mostrarApiError(res.data.message ?? 'Error desconocido en la API.');
        }
    } catch (err) {
        mostrarApiError('Error de red al conectar con el servidor.');
    } finally {
        btn.disabled = false;
        document.getElementById('modal-submit-text').textContent = editingId ? 'Guardar Cambios' : 'Crear Usuario';
    }
});

// ═══════════════════════════════════════════════
// ELIMINAR
// ═══════════════════════════════════════════════
async function confirmarEliminar(id, nombre) {
    const r = await Swal.fire({
        title: '¿Eliminar usuario?',
        html: `¿Eliminar a <strong>${nombre}</strong>?<br><span class="text-sm opacity-70">Esta acción no se puede deshacer.</span>`,
        icon: 'warning', background: '#1e293b', color: '#e2e8f0', iconColor: '#f43f5e',
        showCancelButton: true, confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar', confirmButtonColor: '#e11d48', cancelButtonColor: '#475569',
    });
    if (!r.isConfirmed) return;

    const res = await apiDelete(routeDestroy(id));
    if (res.data.success) {
        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: res.data.message, showConfirmButton: false, timer: 3000,
            background: '#0f172a', color: '#e2e8f0', iconColor: '#10b981',
        });
        cargarUsuarios();
    } else {
        Swal.fire({ icon:'error', title:'Error', text: res.data.message ?? 'No se pudo eliminar.',
                    background:'#1e293b', color:'#e2e8f0', confirmButtonColor:'#3b82f6' });
    }
}

// ═══════════════════════════════════════════════
// CERRAR MODAL
// ═══════════════════════════════════════════════
document.getElementById('modal-close').addEventListener('click', cerrarModal);
document.getElementById('modal-cancel').addEventListener('click', cerrarModal);
modalOverlay.addEventListener('click', e => { if (e.target === modalOverlay) cerrarModal(); });

// ═══════════════════════════════════════════════
// BÚSQUEDA + FILTROS
// ═══════════════════════════════════════════════
document.getElementById('search-input').addEventListener('input', aplicarFiltros);

document.querySelectorAll('[data-role-filter]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('[data-role-filter]').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        activeRoleFilter = btn.dataset.roleFilter;
        aplicarFiltros();
    });
});

// ═══════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════
cargarUsuarios();
</script>
@endpush