@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')
@section('header_title', 'Usuarios & Roles')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .fade-in { animation: fadeIn 0.4s ease-in-out; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

    /* Modal overlay */
    .modal-overlay {
        position: fixed; inset: 0; z-index: 50;
        background: rgba(0,0,0,.65); backdrop-filter: blur(4px);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none; transition: opacity .2s ease;
    }
    .modal-overlay.open { opacity: 1; pointer-events: all; }
    .modal-box {
        background: #1e293b; border: 1px solid rgba(255,255,255,.08);
        border-radius: 1rem; padding: 2rem; width: 100%; max-width: 580px;
        max-height: 90vh; overflow-y: auto;
        transform: translateY(20px) scale(.97); transition: transform .2s ease, opacity .2s ease;
    }
    .modal-overlay.open .modal-box { transform: translateY(0) scale(1); }

    /* Form fields */
    .field-label { display:block; font-size:.7rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.4rem; }
    .field-input {
        width:100%; background:#0f172a; border:1px solid #334155; color:#e2e8f0; font-size:.875rem;
        border-radius:.5rem; padding:.65rem 1rem; outline:none; transition: border-color .2s;
    }
    .field-input:focus { border-color:#3b82f6; }
    .field-input.error { border-color:#f43f5e; }
    .field-error { font-size:.75rem; color:#f43f5e; margin-top:.25rem; display:none; }
    .field-error.show { display:block; }

    /* Role badge colors */
    .badge-admin      { background:rgba(99,102,241,.15); color:#a5b4fc; border:1px solid rgba(99,102,241,.3); }
    .badge-recep      { background:rgba(14,165,233,.15); color:#7dd3fc; border:1px solid rgba(14,165,233,.3); }
    .badge-voluntario { background:rgba(168,85,247,.15); color:#d8b4fe; border:1px solid rgba(168,85,247,.3); }
    .badge-ciudadano  { background:rgba(59,130,246,.15);  color:#93c5fd; border:1px solid rgba(59,130,246,.3); }
    .badge-default    { background:rgba(100,116,139,.15); color:#94a3b8; border:1px solid rgba(100,116,139,.3); }

    /* Skeleton loader */
    .skeleton { background:linear-gradient(90deg,#1e293b 25%,#273349 50%,#1e293b 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; border-radius:.4rem; }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

    /* Filter active button */
    .filter-btn.active { background:rgba(59,130,246,.2); color:#60a5fa; border-color:rgba(59,130,246,.4); }
</style>
@endpush

@section('content')
<div class="space-y-6 fade-in">

    {{-- ===== MÉTRICAS ===== --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center">
                <i data-lucide="users" class="w-5 h-5 text-blue-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-widest">Total</p>
                <p id="stat-total" class="text-2xl font-black text-white">—</p>
            </div>
        </div>
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center">
                <i data-lucide="shield" class="w-5 h-5 text-indigo-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-widest">Admins</p>
                <p id="stat-admin" class="text-2xl font-black text-white">—</p>
            </div>
        </div>
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center">
                <i data-lucide="heart-handshake" class="w-5 h-5 text-purple-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-widest">Voluntarios</p>
                <p id="stat-voluntarios" class="text-2xl font-black text-white">—</p>
            </div>
        </div>
        <div class="glass rounded-xl p-4 border border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-sky-500/10 flex items-center justify-center">
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
        {{-- Búsqueda --}}
        <div class="relative w-full sm:w-80">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
            <input id="search-input" type="text" placeholder="Buscar por nombre o correo..."
                   class="w-full bg-slate-900/50 border border-slate-700 text-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            {{-- Filtros por rol --}}
            <div class="flex gap-1.5">
                <button data-role-filter="all"    class="filter-btn active text-xs px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 transition-all hover:text-blue-400">Todos</button>
                <button data-role-filter="1"      class="filter-btn text-xs px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 transition-all hover:text-blue-400">Admin</button>
                <button data-role-filter="2"      class="filter-btn text-xs px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 transition-all hover:text-blue-400">Recep.</button>
                <button data-role-filter="3"      class="filter-btn text-xs px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 transition-all hover:text-blue-400">Ciudadano</button>
                <button data-role-filter="4"      class="filter-btn text-xs px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 transition-all hover:text-blue-400">Voluntario</button>
            </div>

            {{-- Nuevo Usuario --}}
            <button id="btn-nuevo-usuario"
                    class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-medium text-sm flex items-center transition-all shadow-lg shadow-blue-500/20">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Nuevo Usuario
            </button>
        </div>
    </div>

    {{-- ===== MENSAJES FLASH (desde el controller, para fallback no-JS) ===== --}}
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ===== TABLA DE USUARIOS ===== --}}
    <div class="glass rounded-2xl overflow-hidden border border-slate-800 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 text-slate-400 text-xs uppercase tracking-widest border-b border-slate-800">
                        <th class="px-6 py-4 font-semibold">#</th>
                        <th class="px-6 py-4 font-semibold">Nombre</th>
                        <th class="px-6 py-4 font-semibold">Correo Electrónico</th>
                        <th class="px-6 py-4 font-semibold">Rol</th>
                        <th class="px-6 py-4 font-semibold">Tel.</th>
                        <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="usuarios-tbody" class="text-sm divide-y divide-slate-800/50">
                    {{-- Skeleton loader --}}
                    <tr class="skeleton-row"><td colspan="6" class="px-6 py-4"><div class="skeleton h-6 w-full"></div></td></tr>
                    <tr class="skeleton-row"><td colspan="6" class="px-6 py-4"><div class="skeleton h-6 w-full"></div></td></tr>
                    <tr class="skeleton-row"><td colspan="6" class="px-6 py-4"><div class="skeleton h-6 w-full"></div></td></tr>
                </tbody>
            </table>
        </div>

        {{-- Footer de resultados --}}
        <div class="px-6 py-4 border-t border-slate-800 flex items-center justify-between text-sm text-slate-400">
            <span id="resultados-label">Cargando…</span>
        </div>
    </div>
</div>

{{-- =============================================
     MODAL: NUEVO / EDITAR USUARIO
     ============================================= --}}
<div id="modal-usuario" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-titulo">
    <div class="modal-box">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 id="modal-titulo" class="text-lg font-bold text-white">Nuevo Usuario</h3>
                <p id="modal-subtitulo" class="text-xs text-slate-500 mt-0.5">Completa todos los campos obligatorios</p>
            </div>
            <button id="modal-close" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-usuario" novalidate class="space-y-4">
            @csrf
            <input type="hidden" id="field-id" name="id">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

            <div>
                <label class="field-label">Correo Electrónico *</label>
                <input id="field-correo" name="Correo" type="email" class="field-input" placeholder="usuario@ejemplo.com">
                <p class="field-error" id="err-correo">Ingresa un correo válido</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="field-label">Edad *</label>
                    <input id="field-edad" name="Edad" type="number" min="1" max="120" class="field-input" placeholder="25">
                    <p class="field-error" id="err-edad">Edad inválida</p>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="field-label">Rol *</label>
                    <select id="field-rol" name="Id_Rol" class="field-input">
                        <option value="">Seleccionar…</option>
                        <option value="1">Administrador</option>
                        <option value="2">Recepcionista</option>
                        <option value="3">Ciudadano</option>
                        <option value="4">Voluntario</option>
                    </select>
                    <p class="field-error" id="err-rol">Selecciona un rol</p>
                </div>
                <div>
                    <label class="field-label">ID Dirección *</label>
                    <input id="field-direccion" name="Id_Direccion" type="number" min="1" class="field-input" placeholder="1">
                    <p class="field-error" id="err-direccion">Campo requerido</p>
                </div>
            </div>

            <div>
                <label class="field-label">ID Albergue (opcional)</label>
                <input id="field-albergue" name="Id_Albergue" type="number" min="1" class="field-input" placeholder="Dejar vacío si no aplica">
            </div>

            {{-- Alerta de error de API --}}
            <div id="modal-api-error" class="hidden bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl px-4 py-3 text-sm"></div>

            <div class="pt-4 border-t border-slate-700 flex justify-end gap-3">
                <button type="button" id="modal-cancel" class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-white transition-colors">
                    Cancelar
                </button>
                <button type="submit" id="modal-submit"
                        class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50">
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
// =============================================
// CONFIG
// =============================================
const API_TOKEN = @json($api_token ?? '');
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content
                || document.querySelector('input[name="_token"]')?.value
                || '{{ csrf_token() }}';

// Rutas de Laravel (proxy → API)
const ROUTE_INDEX   = '{{ route("admin.usuarios.index") }}';
const ROUTE_STORE   = '{{ route("admin.usuarios.store") }}';

// Build PUT / DELETE urls on the fly:
const routeUpdate  = (id) => ROUTE_INDEX + '/' + id;
const routeDestroy = (id) => ROUTE_INDEX + '/' + id;

// =============================================
// STATE
// =============================================
let allUsuarios    = [];   // raw data from API
let activeRoleFilter = 'all';
let editingId      = null;

// =============================================
// ROLE HELPERS
// =============================================
const roleMap = {
    1: { label: 'Administrador', badge: 'badge-admin'      },
    2: { label: 'Recepcionista', badge: 'badge-recep'      },
    3: { label: 'Ciudadano',     badge: 'badge-ciudadano'  },
    4: { label: 'Voluntario',    badge: 'badge-voluntario' },
};

function getRoleInfo(id) {
    return roleMap[id] ?? { label: 'Desconocido', badge: 'badge-default' };
}

function avatarColor(id) {
    const colors = ['bg-blue-500/20 text-blue-400','bg-purple-500/20 text-purple-400',
                    'bg-emerald-500/20 text-emerald-400','bg-amber-500/20 text-amber-400',
                    'bg-rose-500/20 text-rose-400','bg-sky-500/20 text-sky-400'];
    return colors[id % colors.length];
}

// =============================================
// FETCH USUARIOS  (via Laravel proxy → BEC_API)
// =============================================
async function fetchUsuarios() {
    const res = await fetch(ROUTE_INDEX + '?json=1', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
}

// =============================================
// RENDER TABLE
// =============================================
function renderTabla(usuarios) {
    const tbody = document.getElementById('usuarios-tbody');

    if (!usuarios.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">
            <i data-lucide="users" class="w-10 h-10 mx-auto mb-3 opacity-30"></i>
            <p>No se encontraron usuarios.</p>
        </td></tr>`;
        lucide.createIcons();
        return;
    }

    tbody.innerHTML = usuarios.map(u => {
        const nombre    = `${u.Nombre} ${u.Apellido_P} ${u.Apellido_M}`;
        const inicial   = u.Nombre?.charAt(0).toUpperCase() ?? '?';
        const roleInfo  = getRoleInfo(u.Id_Rol);
        const avatarCls = avatarColor(u.id_Usuario);

        return `
        <tr class="hover:bg-slate-800/20 transition-colors group" data-id="${u.id_Usuario}" data-rol="${u.Id_Rol}">
            <td class="px-6 py-4 text-slate-500 font-mono text-xs">#${u.id_Usuario}</td>
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full ${avatarCls} flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0">
                        ${inicial}
                    </div>
                    <span class="font-medium text-slate-200">${nombre}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-slate-400">${u.Correo}</td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 text-[11px] font-semibold rounded-full ${roleInfo.badge}">
                    ${roleInfo.label}
                </span>
            </td>
            <td class="px-6 py-4 text-slate-400">${u.Tel ?? '—'}</td>
            <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="abrirEditar(${u.id_Usuario})"
                            class="p-2 text-slate-400 hover:text-blue-400 hover:bg-blue-400/10 rounded-lg transition-colors" title="Editar">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                    </button>
                    <button onclick="confirmarEliminar(${u.id_Usuario}, '${nombre.replace(/'/g,"\\'")}' )"
                            class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-400/10 rounded-lg transition-colors" title="Eliminar">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');

    lucide.createIcons();
}

// =============================================
// STATS
// =============================================
function actualizarStats(usuarios) {
    document.getElementById('stat-total').textContent      = usuarios.length;
    document.getElementById('stat-admin').textContent      = usuarios.filter(u => u.Id_Rol === 1).length;
    document.getElementById('stat-voluntarios').textContent = usuarios.filter(u => u.Id_Rol === 4).length;
    document.getElementById('stat-ciudadanos').textContent  = usuarios.filter(u => u.Id_Rol === 3).length;
}

// =============================================
// FILTER + SEARCH
// =============================================
function aplicarFiltros() {
    const q     = document.getElementById('search-input').value.toLowerCase().trim();
    let filtered = [...allUsuarios];

    // Role filter
    if (activeRoleFilter !== 'all') {
        filtered = filtered.filter(u => String(u.Id_Rol) === activeRoleFilter);
    }

    // Text search
    if (q) {
        filtered = filtered.filter(u => {
            const nombre = `${u.Nombre} ${u.Apellido_P} ${u.Apellido_M}`.toLowerCase();
            return nombre.includes(q) || (u.Correo ?? '').toLowerCase().includes(q);
        });
    }

    renderTabla(filtered);
    document.getElementById('resultados-label').textContent =
        `Mostrando ${filtered.length} de ${allUsuarios.length} usuarios`;
}

// =============================================
// LOAD DATA
// =============================================
async function cargarUsuarios() {
    try {
        const data = await fetchUsuarios();
        allUsuarios = Array.isArray(data) ? data : [];
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

// =============================================
// MODAL UTILS
// =============================================
const modalOverlay = document.getElementById('modal-usuario');

function abrirModal(titulo, subtitulo) {
    document.getElementById('modal-titulo').textContent    = titulo;
    document.getElementById('modal-subtitulo').textContent = subtitulo;
    modalOverlay.classList.add('open');
    limpiarErrores();
    document.getElementById('modal-api-error').classList.add('hidden');
    lucide.createIcons();
}

function cerrarModal() {
    modalOverlay.classList.remove('open');
    document.getElementById('form-usuario').reset();
    editingId = null;
}

function limpiarErrores() {
    document.querySelectorAll('.field-input').forEach(el => el.classList.remove('error'));
    document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));
}

// =============================================
// ABRIR NUEVO USUARIO
// =============================================
document.getElementById('btn-nuevo-usuario').addEventListener('click', () => {
    editingId = null;
    document.getElementById('form-usuario').reset();
    document.getElementById('field-id').value = '';
    document.getElementById('modal-submit-text').textContent = 'Crear Usuario';
    abrirModal('Nuevo Usuario', 'Completa todos los campos obligatorios marcados con *');
});

// =============================================
// ABRIR EDITAR USUARIO
// =============================================
function abrirEditar(id) {
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
    document.getElementById('field-direccion').value  = u.Id_Direccion ?? '';
    document.getElementById('field-albergue').value   = u.Id_Albergue ?? '';
    // Clear passwords (force re-entry for security)
    document.getElementById('field-password').value         = '';
    document.getElementById('field-password-confirm').value = '';

    document.getElementById('modal-submit-text').textContent = 'Guardar Cambios';
    abrirModal('Editar Usuario', `Editando: ${u.Nombre} ${u.Apellido_P}`);
}

// =============================================
// VALIDACIÓN
// =============================================
function validarFormulario() {
    let valid = true;
    limpiarErrores();

    const req = (id, errId) => {
        const val = document.getElementById(id).value.trim();
        if (!val) {
            document.getElementById(id).classList.add('error');
            document.getElementById(errId).classList.add('show');
            valid = false;
        }
        return val;
    };

    req('field-nombre',     'err-nombre');
    req('field-apellido-p', 'err-apellido-p');
    req('field-apellido-m', 'err-apellido-m');
    req('field-genero',     'err-genero');
    req('field-rol',        'err-rol');
    req('field-direccion',  'err-direccion');

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

    // Tel
    const tel = document.getElementById('field-tel').value.trim();
    if (!tel) {
        document.getElementById('field-tel').classList.add('error');
        document.getElementById('err-tel').classList.add('show');
        valid = false;
    }

    // Password (required on create, optional on edit)
    const pass    = document.getElementById('field-password').value;
    const confirm = document.getElementById('field-password-confirm').value;

    if (!editingId) {
        // New user: password required
        if (pass.length < 6) {
            document.getElementById('field-password').classList.add('error');
            document.getElementById('err-password').classList.add('show');
            valid = false;
        }
    }
    // If a password is provided (edit mode), confirm must match
    if (pass && pass !== confirm) {
        document.getElementById('field-password-confirm').classList.add('error');
        document.getElementById('err-password-confirm').classList.add('show');
        valid = false;
    }
    // On edit with blank password: provide a placeholder value (API requires it for PUT)
    // We'll handle this in the submit handler.

    return valid;
}

// =============================================
// SUBMIT FORM (CREATE / UPDATE)
// =============================================
document.getElementById('form-usuario').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!validarFormulario()) return;

    if (editingId) {
        // ASK CONFIRMATION before updating
        const result = await Swal.fire({
            title: '¿Aplicar cambios?',
            text: '¿Estás seguro de que deseas aplicar estos cambios al usuario?',
            icon: 'question',
            background: '#1e293b',
            color: '#e2e8f0',
            iconColor: '#3b82f6',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#475569',
        });
        if (!result.isConfirmed) return;
    }

    const btn = document.getElementById('modal-submit');
    btn.disabled = true;
    document.getElementById('modal-submit-text').textContent = 'Procesando…';

    const pass    = document.getElementById('field-password').value;
    const payload = {
        Nombre:       document.getElementById('field-nombre').value.trim(),
        Apellido_P:   document.getElementById('field-apellido-p').value.trim(),
        Apellido_M:   document.getElementById('field-apellido-m').value.trim(),
        Correo:       document.getElementById('field-correo').value.trim(),
        Password:     pass || 'UNCHANGED_' + Date.now(), // API requires it for PUT; will be ignored if unchanged logic is added
        Edad:         parseInt(document.getElementById('field-edad').value),
        Tel:          parseInt(document.getElementById('field-tel').value),
        Id_Rol:       parseInt(document.getElementById('field-rol').value),
        Id_Genero:    parseInt(document.getElementById('field-genero').value),
        Id_Direccion: parseInt(document.getElementById('field-direccion').value),
        _token:       CSRF_TOKEN,
    };

    const albergue = document.getElementById('field-albergue').value;
    if (albergue) payload.Id_Albergue = parseInt(albergue);

    const url    = editingId ? routeUpdate(editingId) : ROUTE_STORE;
    const method = editingId ? 'PUT' : 'POST';

    try {
        const res  = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            body: JSON.stringify(payload),
        });

        const data = await res.json();

        if (data.success) {
            cerrarModal();
            await Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: data.message, showConfirmButton: false, timer: 3000,
                background: '#0f172a', color: '#e2e8f0', iconColor: '#10b981',
            });
            cargarUsuarios();
        } else {
            document.getElementById('modal-api-error').textContent = data.message ?? 'Error desconocido en la API.';
            document.getElementById('modal-api-error').classList.remove('hidden');
        }
    } catch (err) {
        document.getElementById('modal-api-error').textContent = 'Error de red al conectar con el servidor.';
        document.getElementById('modal-api-error').classList.remove('hidden');
    } finally {
        btn.disabled = false;
        document.getElementById('modal-submit-text').textContent = editingId ? 'Guardar Cambios' : 'Crear Usuario';
    }
});

// =============================================
// ELIMINAR USUARIO
// =============================================
async function confirmarEliminar(id, nombre) {
    const result = await Swal.fire({
        title: '¿Eliminar usuario?',
        html: `¿Estás seguro de eliminar a <strong>${nombre}</strong>?<br><span class="text-sm opacity-70">Esta acción no se puede deshacer.</span>`,
        icon: 'warning',
        background: '#1e293b',
        color: '#e2e8f0',
        iconColor: '#f43f5e',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#475569',
    });

    if (!result.isConfirmed) return;

    try {
        const res  = await fetch(routeDestroy(id), {
            method: 'DELETE',
            headers: {
                'Accept':       'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
        });
        const data = await res.json();

        if (data.success) {
            await Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: data.message, showConfirmButton: false, timer: 3000,
                background: '#0f172a', color: '#e2e8f0', iconColor: '#10b981',
            });
            cargarUsuarios();
        } else {
            Swal.fire({
                icon: 'error', title: 'Error',
                text: data.message ?? 'No se pudo eliminar el usuario.',
                background: '#1e293b', color: '#e2e8f0', confirmButtonColor: '#3b82f6',
            });
        }
    } catch {
        Swal.fire({ icon: 'error', title: 'Error de red', text: 'No se pudo conectar con el servidor.', background: '#1e293b', color: '#e2e8f0' });
    }
}

// =============================================
// CLOSE MODAL
// =============================================
document.getElementById('modal-close').addEventListener('click', cerrarModal);
document.getElementById('modal-cancel').addEventListener('click', cerrarModal);
modalOverlay.addEventListener('click', (e) => { if (e.target === modalOverlay) cerrarModal(); });

// =============================================
// SEARCH + ROLE FILTER EVENTS
// =============================================
document.getElementById('search-input').addEventListener('input', aplicarFiltros);

document.querySelectorAll('[data-role-filter]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('[data-role-filter]').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        activeRoleFilter = btn.dataset.roleFilter;
        aplicarFiltros();
    });
});

// =============================================
// INIT
// =============================================
cargarUsuarios();
</script>
@endpush