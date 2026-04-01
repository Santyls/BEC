@extends('layouts.admin')

@section('title', 'Modelo de Predicciones')
@section('header_title', 'Analítica Predictiva — ML')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<style>
    .gradient-border {
        border: 1px solid transparent;
        background-clip: padding-box;
    }
    .pulse-dot {
        animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
    }
    @keyframes pulse-ring {
        0%   { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4); }
        70%  { box-shadow: 0 0 0 8px rgba(99, 102, 241, 0); }
        100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
    }
    .fade-in {
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    select option { background-color: #1e293b; color: #f8fafc; }
</style>
@endpush

@section('content')
<div class="space-y-8">

    {{-- ===== CABECERA ===== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-2xl font-black text-white tracking-tight">
                Modelo Predictivo de Demanda
            </h3>
            <p class="text-sm text-slate-400 mt-1">
                Análisis de series de tiempo (Holt-Winters) sobre <span class="text-indigo-400 font-semibold">Ropa &amp; Cobijas</span> donadas.
            </p>
        </div>

        {{-- Badge de estado del modelo --}}
        <div class="flex items-center gap-2 px-4 py-2 bg-indigo-500/10 border border-indigo-500/30 rounded-xl self-start md:self-auto">
            <span class="w-2 h-2 rounded-full bg-indigo-400 pulse-dot"></span>
            <span class="text-xs font-semibold text-indigo-300 uppercase tracking-widest">Modelo Activo</span>
        </div>
    </div>

    {{-- ===== SELECTOR DE ALBERGUE ===== --}}
    <div class="glass rounded-2xl p-6 border border-slate-800">
        <label for="albergue_select" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">
            <i data-lucide="home" class="inline w-3.5 h-3.5 mr-1 text-indigo-400"></i>
            Filtrar por Albergue
        </label>
        <div class="flex flex-col sm:flex-row gap-3">
            <select
                id="albergue_select"
                class="flex-1 bg-slate-800/80 text-white border border-slate-700 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all cursor-pointer"
            >
                <option value="">— Selecciona un albergue —</option>
                @foreach ($albergues as $albergue)
                    <option value="{{ $albergue['Id_Albergue'] }}">
                        {{ $albergue['Nombre_Albergue'] }}
                    </option>
                @endforeach
            </select>
            <button
                id="btn_cargar"
                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 active:scale-95 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed"
                disabled
            >
                <i data-lucide="brain-circuit" class="w-4 h-4"></i>
                Generar Predicción
            </button>
        </div>
    </div>

    {{-- ===== PANEL PRINCIPAL DE GRÁFICOS ===== --}}
    <div id="panel_graficos" class="hidden fade-in">

        {{-- KPIs rápidos --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" id="kpi_row">
            <div class="glass rounded-2xl p-4 border border-slate-800 text-center">
                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-1">Días Histórico</p>
                <p id="kpi_hist_dias" class="text-2xl font-black text-white">—</p>
            </div>
            <div class="glass rounded-2xl p-4 border border-slate-800 text-center">
                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-1">Prom. Histórico/día</p>
                <p id="kpi_hist_prom" class="text-2xl font-black text-blue-400">—</p>
            </div>
            <div class="glass rounded-2xl p-4 border border-slate-800 text-center">
                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-1">Total Predicho (30d)</p>
                <p id="kpi_pred_total" class="text-2xl font-black text-indigo-400">—</p>
            </div>
            <div class="glass rounded-2xl p-4 border border-slate-800 text-center">
                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-1">Prom. Predicho/día</p>
                <p id="kpi_pred_prom" class="text-2xl font-black text-emerald-400">—</p>
            </div>
        </div>

        {{-- Gráfico principal --}}
        <div class="glass rounded-3xl p-6 border border-slate-800 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
            <div class="flex items-center justify-between mb-6 relative z-10">
                <div>
                    <h4 class="text-lg font-bold text-white" id="grafico_titulo">Predicción de Demanda</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Últimos 60 días históricos + 30 días proyectados</p>
                </div>
                <div class="flex items-center gap-4 text-xs text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block w-3 h-0.5 bg-blue-400 rounded"></span> Histórico
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block w-3 h-0.5 bg-indigo-400 rounded" style="border-top: 2px dashed #818cf8; background:transparent;"></span> Predicción
                    </span>
                </div>
            </div>
            <div class="relative" style="height: 360px;">
                <canvas id="chart_prediccion"></canvas>
            </div>
        </div>
    </div>

    {{-- ===== ESTADO VACÍO (inicial) ===== --}}
    <div id="panel_vacio" class="glass rounded-3xl p-16 border border-slate-800 border-dashed flex flex-col items-center justify-center text-center">
        <div class="w-20 h-20 rounded-full bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center mb-6">
            <i data-lucide="line-chart" class="w-10 h-10 text-indigo-400/70"></i>
        </div>
        <h4 class="text-xl font-bold text-slate-300 mb-2">Selecciona un Albergue</h4>
        <p class="text-sm text-slate-500 max-w-sm">
            Elige un albergue del selector superior y presiona <strong class="text-slate-400">Generar Predicción</strong> para visualizar el análisis de series de tiempo.
        </p>
    </div>

    {{-- ===== ESTADO DE CARGA ===== --}}
    <div id="panel_cargando" class="hidden glass rounded-3xl p-16 border border-slate-800 flex flex-col items-center justify-center text-center">
        <div class="relative w-16 h-16 mb-6">
            <div class="w-16 h-16 rounded-full border-4 border-indigo-500/20 border-t-indigo-500 animate-spin"></div>
        </div>
        <p class="text-base font-semibold text-slate-300">Ejecutando Modelo Predictivo…</p>
        <p class="text-xs text-slate-500 mt-1">Procesando series de tiempo y ajustando Holt-Winters</p>
    </div>

    {{-- ===== ESTADO DE ERROR ===== --}}
    <div id="panel_error" class="hidden fade-in glass rounded-2xl p-6 border border-rose-500/30 bg-rose-500/5">
        <div class="flex items-start gap-4">
            <div class="p-2.5 rounded-xl bg-rose-500/20 shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-400"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-rose-300">No se pudo generar la predicción</p>
                <p id="error_msg" class="text-xs text-rose-400/80 mt-1"></p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Refs DOM ──────────────────────────────────────────────────────────
    const selectAlbergue = document.getElementById('albergue_select');
    const btnCargar      = document.getElementById('btn_cargar');
    const panelVacio     = document.getElementById('panel_vacio');
    const panelCargando  = document.getElementById('panel_cargando');
    const panelGraficos  = document.getElementById('panel_graficos');
    const panelError     = document.getElementById('panel_error');
    const errorMsg       = document.getElementById('error_msg');

    // KPIs
    const kpiHistDias  = document.getElementById('kpi_hist_dias');
    const kpiHistProm  = document.getElementById('kpi_hist_prom');
    const kpiPredTotal = document.getElementById('kpi_pred_total');
    const kpiPredProm  = document.getElementById('kpi_pred_prom');
    const graficoTitulo = document.getElementById('grafico_titulo');

    // ── Instancia del Gráfico (CRÍTICO: destruir antes de redibujar) ──────
    let miGrafico = null;

    // ── Helpers de visibilidad ────────────────────────────────────────────
    function mostrar(panel) {
        [panelVacio, panelCargando, panelGraficos, panelError].forEach(p => {
            p.classList.add('hidden');
            p.classList.remove('fade-in');
        });
        panel.classList.remove('hidden');
        // micro-animación sólo en resultados y error
        if (panel === panelGraficos || panel === panelError) {
            panel.classList.add('fade-in');
        }
    }

    // ── Habilitar botón solo si hay selección ─────────────────────────────
    selectAlbergue.addEventListener('change', () => {
        btnCargar.disabled = selectAlbergue.value === '';
    });

    // ── Disparar fetch al hacer clic ──────────────────────────────────────
    btnCargar.addEventListener('click', () => {
        const albergueId = selectAlbergue.value;
        if (!albergueId) return;

        const albergueNombre = selectAlbergue.options[selectAlbergue.selectedIndex].text.trim();
        cargarPrediccion(albergueId, albergueNombre);
    });

    // ── Función principal de carga ────────────────────────────────────────
    function cargarPrediccion(albergueId, nombreAlbergue) {
        mostrar(panelCargando);
        btnCargar.disabled = true;

        const url = `{{ route('admin.predicciones.datos') }}?albergue_id=${encodeURIComponent(albergueId)}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(res => res.json().then(data => ({ ok: res.ok, data })))
        .then(({ ok, data }) => {
            if (!ok || data.error) {
                throw new Error(data.error || 'Error desconocido del servidor.');
            }
            renderizarGrafico(data, nombreAlbergue);
            actualizarKPIs(data);
            mostrar(panelGraficos);
        })
        .catch(err => {
            errorMsg.textContent = err.message;
            mostrar(panelError);
        })
        .finally(() => {
            btnCargar.disabled = selectAlbergue.value === '';
        });
    }

    // ── Renderizado del gráfico ───────────────────────────────────────────
    function renderizarGrafico(datos, nombreAlbergue) {
        // ¡CRÍTICO! Destruir instancia previa para liberar el canvas
        if (miGrafico) {
            miGrafico.destroy();
            miGrafico = null;
        }

        const ctx = document.getElementById('chart_prediccion').getContext('2d');

        // Parsear a numérico (evitar strings)
        const histFechas  = datos.historico.fechas;
        const histValores = datos.historico.valores.map(v => parseFloat(v));
        const predFechas  = datos.prediccion.fechas;
        const predValores = datos.prediccion.valores.map(v => parseFloat(v));

        // Combinar etiquetas del eje X
        const todasLasFechas = [...histFechas, ...predFechas];

        // Relleno para alinear los datasets: histórico lleva null en zona de predicción
        const dataHistorico  = [...histValores, ...new Array(predFechas.length).fill(null)];
        const dataPrediccion = [...new Array(histFechas.length - 1).fill(null), histValores[histValores.length - 1], ...predValores];

        graficoTitulo.textContent = `Predicción: ${nombreAlbergue}`;

        // Gradiente para el histórico
        const gradHist = ctx.createLinearGradient(0, 0, 0, 360);
        gradHist.addColorStop(0, 'rgba(96, 165, 250, 0.35)');
        gradHist.addColorStop(1, 'rgba(96, 165, 250, 0.0)');

        // Gradiente para la predicción
        const gradPred = ctx.createLinearGradient(0, 0, 0, 360);
        gradPred.addColorStop(0, 'rgba(129, 140, 248, 0.30)');
        gradPred.addColorStop(1, 'rgba(129, 140, 248, 0.0)');

        miGrafico = new Chart(ctx, {
            type: 'line',
            data: {
                labels: todasLasFechas,
                datasets: [
                    {
                        label: 'Histórico (Donaciones)',
                        data: dataHistorico,
                        borderColor: '#60a5fa',
                        backgroundColor: gradHist,
                        borderWidth: 2,
                        pointRadius: 2,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#60a5fa',
                        tension: 0.35,
                        fill: true,
                        spanGaps: false,
                    },
                    {
                        label: 'Predicción (30 días)',
                        data: dataPrediccion,
                        borderColor: '#818cf8',
                        backgroundColor: gradPred,
                        borderWidth: 2.5,
                        borderDash: [6, 4],
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#818cf8',
                        tension: 0.35,
                        fill: true,
                        spanGaps: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        borderColor: 'rgba(99, 102, 241, 0.4)',
                        borderWidth: 1,
                        titleColor: '#e2e8f0',
                        bodyColor: '#94a3b8',
                        padding: 12,
                        callbacks: {
                            label: ctx => {
                                const v = ctx.raw;
                                if (v === null) return null;
                                return ` ${ctx.dataset.label}: ${v.toFixed(2)} uds.`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(148, 163, 184, 0.06)', drawBorder: false },
                        ticks: {
                            color: '#64748b',
                            font: { size: 10 },
                            maxTicksLimit: 12,
                            maxRotation: 40,
                        }
                    },
                    y: {
                        grid: { color: 'rgba(148, 163, 184, 0.08)', drawBorder: false },
                        ticks: {
                            color: '#64748b',
                            font: { size: 11 },
                            callback: v => `${v} ud.`,
                        },
                        beginAtZero: true,
                    }
                }
            }
        });
    }

    // ── Actualizar tarjetas KPI ───────────────────────────────────────────
    function actualizarKPIs(datos) {
        const hv = datos.historico.valores.map(v => parseFloat(v));
        const pv = datos.prediccion.valores.map(v => parseFloat(v));

        const histDias  = hv.length;
        const histProm  = hv.reduce((a, b) => a + b, 0) / (histDias || 1);
        const predTotal = pv.reduce((a, b) => a + b, 0);
        const predProm  = predTotal / (pv.length || 1);

        kpiHistDias.textContent  = histDias;
        kpiHistProm.textContent  = histProm.toFixed(1);
        kpiPredTotal.textContent = predTotal.toFixed(0);
        kpiPredProm.textContent  = predProm.toFixed(1);
    }

    // Inicialización: lucide icons se regenera desde el layout, no hace falta llamarlo aquí.
})();
</script>
@endpush
