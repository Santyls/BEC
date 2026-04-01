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

        {{-- ── Donaciones ──────────────────────────────────────── --}}
        <div class="glass rounded-3xl p-8 border border-slate-800 relative overflow-hidden group">
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
                <button onclick="generarReporte('donaciones', 'preview')"
                    class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-black/20 transition-all flex justify-center items-center gap-2 border border-slate-700">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                    Vista Previa
                </button>
                <button onclick="generarReporte('donaciones', 'download')"
                    class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/20 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    Descargar PDF
                </button>
            </div>
        </div>

        {{-- ── Voluntariados ───────────────────────────────────── --}}
        <div class="glass rounded-3xl p-8 border border-slate-800 relative overflow-hidden group">
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
                <button onclick="generarReporte('voluntariados', 'preview')"
                    class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-black/20 transition-all flex justify-center items-center gap-2 border border-slate-700">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                    Vista Previa
                </button>
                <button onclick="generarReporte('voluntariados', 'download')"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-500/20 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    Descargar PDF
                </button>
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL FLOTANTE DE VISTA PREVIA
═══════════════════════════════════════════════════════════ --}}
<div id="reporte-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     style="background: rgba(0,0,0,0.75); backdrop-filter: blur(4px);">

    <div class="relative w-full max-w-5xl flex flex-col rounded-2xl overflow-hidden border border-slate-700 shadow-2xl"
         style="height: 90vh; background: #0f172a;">

        {{-- Header del modal --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700 flex-shrink-0"
             style="background: #1e293b;">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg" style="background: rgba(59,130,246,0.2);">
                    <i data-lucide="file-text" class="w-5 h-5 text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-widest">Vista Previa</p>
                    <p id="modal-titulo" class="text-white font-bold text-lg leading-tight">Reporte</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button id="btn-descargar-modal"
                    class="hidden items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white transition-all"
                    style="background: #2563eb;"
                    onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Descargar PDF
                </button>
                <button onclick="cerrarModal()"
                    class="p-2 rounded-xl text-slate-400 hover:text-white transition-all"
                    style="background: rgba(255,255,255,0.05);"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        {{-- Estado: cargando --}}
        <div id="modal-loading" class="flex-1 flex flex-col items-center justify-center gap-4">
            <div class="w-12 h-12 rounded-full border-4 border-blue-500 border-t-transparent animate-spin"></div>
            <p class="text-slate-400 text-sm">Generando reporte, por favor espera...</p>
        </div>

        {{-- Estado: error --}}
        <div id="modal-error" class="hidden flex-1 flex flex-col items-center justify-center gap-4 p-8">
            <div class="p-4 rounded-2xl" style="background: rgba(239,68,68,0.15);">
                <i data-lucide="alert-triangle" class="w-10 h-10 text-red-400"></i>
            </div>
            <p class="text-white font-bold text-lg">No se pudo generar el reporte</p>
            <p id="modal-error-msg" class="text-slate-400 text-sm text-center max-w-sm">
                Verifica que la API esté en línea e intenta de nuevo.
            </p>
            <button onclick="cerrarModal()"
                class="mt-2 px-6 py-2 rounded-xl text-sm font-bold text-white"
                style="background: rgba(255,255,255,0.1);">
                Cerrar
            </button>
        </div>

        {{-- Estado: PDF cargado --}}
        <iframe id="pdf-frame"
                class="hidden flex-1 w-full border-0"
                title="Vista previa del reporte">
        </iframe>

    </div>
</div>

<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
    let tipoActivo = null;

    async function generarReporte(tipo, accion) {
        const fd = new FormData();
        fd.append('_token', CSRF_TOKEN);
        fd.append('tipo_reporte', tipo);
        fd.append('action', accion);

        if (accion === 'preview') {
            abrirModal(tipo);
            try {
                const res = await fetch('/admin/reportes/generar', { method: 'POST', body: fd });

                if (!res.ok) {
                    const texto = await res.text();
                    mostrarError('Error HTTP ' + res.status + '. ' + (texto.length < 200 ? texto : ''));
                    return;
                }

                const contentType = res.headers.get('Content-Type') || '';
                if (!contentType.includes('application/pdf')) {
                    const texto = await res.text();
                    mostrarError('La respuesta no es un PDF. ' + texto.slice(0, 200));
                    return;
                }

                const blob = await res.blob();
                const url  = URL.createObjectURL(blob);

                const frame = document.getElementById('pdf-frame');
                frame.src = url;
                frame.classList.remove('hidden');
                frame.classList.add('flex');

                document.getElementById('modal-loading').classList.add('hidden');

                // Activar botón de descarga en el modal
                const btnDesc = document.getElementById('btn-descargar-modal');
                btnDesc.classList.remove('hidden');
                btnDesc.classList.add('flex');
                btnDesc.onclick = () => descargarBlob(blob, tipo);

            } catch (e) {
                mostrarError('Error de red: ' + e.message);
            }

        } else {
            // Descarga directa vía fetch → blob → anchor
            const btnDesc = document.querySelector(`button[onclick="generarReporte('${tipo}', 'download')"]`);
            if (btnDesc) {
                btnDesc.disabled = true;
                btnDesc.innerHTML = '<svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Generando...';
            }
            try {
                const res = await fetch('/admin/reportes/generar', { method: 'POST', body: fd });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const blob = await res.blob();
                descargarBlob(blob, tipo);
            } catch (e) {
                alert('No se pudo descargar el reporte: ' + e.message);
            } finally {
                if (btnDesc) {
                    btnDesc.disabled = false;
                    btnDesc.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Descargar PDF';
                }
            }
        }
    }

    function descargarBlob(blob, tipo) {
        const fecha = new Date().toISOString().slice(0, 10);
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href     = url;
        a.download = `reporte_${tipo}_${fecha}.pdf`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(() => URL.revokeObjectURL(url), 5000);
    }

    function abrirModal(tipo) {
        tipoActivo = tipo;
        const nombres = { donaciones: 'Donaciones', voluntariados: 'Voluntariados' };
        document.getElementById('modal-titulo').textContent = 'Consolidado de ' + (nombres[tipo] || tipo);

        document.getElementById('modal-loading').classList.remove('hidden');
        document.getElementById('modal-error').classList.add('hidden');

        const frame = document.getElementById('pdf-frame');
        frame.classList.add('hidden');
        frame.classList.remove('flex');
        frame.src = '';

        const btnDesc = document.getElementById('btn-descargar-modal');
        btnDesc.classList.add('hidden');
        btnDesc.classList.remove('flex');

        const modal = document.getElementById('reporte-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function mostrarError(msg) {
        document.getElementById('modal-loading').classList.add('hidden');
        document.getElementById('modal-error-msg').textContent = msg;
        document.getElementById('modal-error').classList.remove('hidden');
        document.getElementById('modal-error').classList.add('flex');
    }

    function cerrarModal() {
        const modal = document.getElementById('reporte-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        const frame = document.getElementById('pdf-frame');
        if (frame.src.startsWith('blob:')) URL.revokeObjectURL(frame.src);
        frame.src = '';
        tipoActivo = null;
    }

    // Cerrar con Escape
    document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModal(); });

    // Cerrar al click en el fondo
    document.getElementById('reporte-modal').addEventListener('click', function(e) {
        if (e.target === this) cerrarModal();
    });
</script>

@endsection
