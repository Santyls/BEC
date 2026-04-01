@extends('layouts.admin')

@section('title', 'Dashboard Resumen')
@section('header_title', 'Resumen General')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<style>
    .dashboard-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .dashboard-card:hover { transform: translateY(-3px); box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-in { animation: fadeSlideUp 0.5s ease-out forwards; opacity: 0; }
</style>
@endpush

@section('content')
<div class="space-y-8">

    {{-- ===== TARJETAS DE ESTADÍSTICAS ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <div class="dashboard-card anim-in bg-slate-800/40 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl relative overflow-hidden group" style="animation-delay:0.05s">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-sm font-medium text-slate-400">Total Albergues</p>
                    <h3 class="text-3xl font-bold text-white mt-2">{{ count($albergues) > 0 ? count($albergues) : '—' }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-blue-500/20 text-blue-400">
                    <i data-lucide="home" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:opacity-20 transition-opacity">
                <i data-lucide="home" class="w-32 h-32"></i>
            </div>
        </div>

        <div class="dashboard-card anim-in bg-slate-800/40 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl relative overflow-hidden group" style="animation-delay:0.10s">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-sm font-medium text-slate-400">Donaciones Activas</p>
                    <h3 class="text-3xl font-bold text-white mt-2">156</h3>
                </div>
                <div class="p-3 rounded-xl bg-emerald-500/20 text-emerald-400">
                    <i data-lucide="heart-handshake" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:opacity-20 transition-opacity">
                <i data-lucide="heart-handshake" class="w-32 h-32"></i>
            </div>
        </div>

        <div class="dashboard-card anim-in bg-slate-800/40 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl relative overflow-hidden group" style="animation-delay:0.15s">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-sm font-medium text-slate-400">Voluntarios Activos</p>
                    <h3 class="text-3xl font-bold text-white mt-2">48</h3>
                </div>
                <div class="p-3 rounded-xl bg-violet-500/20 text-violet-400">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="dashboard-card anim-in bg-slate-800/40 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl relative overflow-hidden group" style="animation-delay:0.20s">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-sm font-medium text-slate-400">Campañas Activas</p>
                    <h3 class="text-3xl font-bold text-white mt-2">7</h3>
                </div>
                <div class="p-3 rounded-xl bg-amber-500/20 text-amber-400">
                    <i data-lucide="megaphone" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== GRÁFICO DE PREDICCIONES + ÚLTIMOS REGISTROS ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Gráfico principal --}}
        <div class="lg:col-span-2 dashboard-card anim-in bg-slate-800/40 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl" style="animation-delay:0.25s">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-white">Proyección de Insumos Invernales</h3>
                    <p class="text-xs text-slate-400 mt-1">Análisis Causal (Series de Tiempo) de ropa y cobijas necesarias.</p>
                </div>
                <select
                    id="albergueSelect"
                    class="bg-slate-900 border border-slate-700 text-emerald-400 font-medium text-sm rounded-lg px-4 py-2 focus:outline-none focus:border-emerald-500 shadow-lg cursor-pointer transition-colors shrink-0"
                >
                    @forelse ($albergues as $albergue)
                        <option value="{{ $albergue['Id_Albergue'] }}">{{ $albergue['Nombre_Albergue'] }}</option>
                    @empty
                        <option value="">Sin albergues registrados</option>
                    @endforelse
                </select>
            </div>

            {{-- Contenedor del canvas + loading + error --}}
            <div class="relative rounded-xl overflow-hidden bg-slate-900/30" style="height: 300px;">

                {{-- Loading overlay --}}
                <div id="chartLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/80 z-10 backdrop-blur-sm">
                    <div class="w-10 h-10 rounded-full border-4 border-emerald-500/20 border-t-emerald-500 animate-spin mb-3"></div>
                    <p class="text-sm font-bold text-slate-300 uppercase tracking-widest">Calculando Algoritmo...</p>
                    <p class="text-[10px] text-slate-500 mt-2">Ajustando modelo de Suavizado Exponencial</p>
                </div>

                {{-- Error overlay --}}
                <div id="chartError" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-slate-900/80 z-10 backdrop-blur-sm p-6 text-center">
                    <i data-lucide="alert-triangle" class="w-10 h-10 text-rose-400 mb-3"></i>
                    <p class="text-sm font-bold text-rose-300 uppercase tracking-widest">Sin datos suficientes</p>
                    <p id="chartErrorMsg" class="text-[11px] text-slate-500 mt-2 max-w-xs"></p>
                    <a href="{{ route('admin.predicciones.index') }}" class="mt-4 px-4 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-lg border border-slate-700 transition-all inline-flex items-center gap-1.5">
                        <i data-lucide="line-chart" class="w-3.5 h-3.5"></i>
                        Ver módulo completo
                    </a>
                </div>

                {{-- El canvas debe estar fuera de los overlays para que Chart.js acceda a él --}}
                <canvas id="demandChart" style="position:absolute; inset:0; width:100%; height:100%;"></canvas>
            </div>
        </div>

        {{-- Últimos Registros --}}
        <div class="dashboard-card anim-in bg-slate-800/40 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl" style="animation-delay:0.30s">
            <h3 class="text-lg font-semibold text-white mb-4">Últimos Registros</h3>
            <div class="space-y-3">
                <div class="flex items-center p-3 rounded-lg bg-slate-900/50 border border-slate-800">
                    <div class="w-2 h-2 rounded-full bg-blue-500 mr-4 shrink-0"></div>
                    <p class="text-sm text-slate-300">Nuevo albergue "Esperanza" registrado.</p>
                </div>
                <div class="flex items-center p-3 rounded-lg bg-slate-900/50 border border-slate-800">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 mr-4 shrink-0"></div>
                    <p class="text-sm text-slate-300">Donación de ropa recibida.</p>
                </div>
                <div class="flex items-center p-3 rounded-lg bg-slate-900/50 border border-slate-800">
                    <div class="w-2 h-2 rounded-full bg-violet-500 mr-4 shrink-0"></div>
                    <p class="text-sm text-slate-300">Nuevo voluntario registrado.</p>
                </div>
                <div class="flex items-center p-3 rounded-lg bg-slate-900/50 border border-slate-800">
                    <div class="w-2 h-2 rounded-full bg-amber-500 mr-4 shrink-0"></div>
                    <p class="text-sm text-slate-300">Campaña "Invierno 2026" iniciada.</p>
                </div>
            </div>

            <a href="{{ route('admin.predicciones.index') }}" class="mt-6 w-full flex items-center justify-center gap-2 py-2.5 border border-indigo-500/40 text-indigo-400 hover:bg-indigo-500/10 text-xs font-semibold rounded-xl transition-all">
                <i data-lucide="brain-circuit" class="w-3.5 h-3.5"></i>
                Módulo de Predicciones Completo
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Instancia global del gráfico ──────────────────────────────────────
    let demandChart = null;

    // ── Refs DOM ──────────────────────────────────────────────────────────
    const selectEl    = document.getElementById('albergueSelect');
    const loadingEl   = document.getElementById('chartLoading');
    const errorEl     = document.getElementById('chartError');
    const errorMsgEl  = document.getElementById('chartErrorMsg');
    const canvasEl    = document.getElementById('demandChart');

    // ── Helpers de estado ─────────────────────────────────────────────────
    function mostrarLoading() {
        loadingEl.classList.remove('hidden');
        errorEl.classList.add('hidden');
    }
    function mostrarError(msg) {
        loadingEl.classList.add('hidden');
        errorEl.classList.remove('hidden');
        errorMsgEl.textContent = msg;
        lucide.createIcons();
    }
    function mostrarGrafico() {
        loadingEl.classList.add('hidden');
        errorEl.classList.add('hidden');
    }

    // ── Función principal de fetch (usa el proxy Laravel, no la API directo) ──
    async function fetchPrediccion(idAlbergue) {
        if (!idAlbergue) {
            mostrarError('No hay albergues registrados en el sistema.');
            return;
        }

        mostrarLoading();

        // Ruta proxy Laravel — evita CORS y pasa el JWT desde sesión
        const url = `{{ route('admin.predicciones.datos') }}?albergue_id=${encodeURIComponent(idAlbergue)}`;

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });

            const data = await response.json();

            if (!response.ok || data.error) {
                throw new Error(data.error || `Error del servidor (${response.status})`);
            }

            renderChart(data.historico, data.prediccion);
            mostrarGrafico();

        } catch (err) {
            console.error('[Dashboard Chart]', err);
            mostrarError(err.message);
        }
    }

    // ── Renderizado del gráfico Chart.js ──────────────────────────────────
    function renderChart(historico, prediccion) {
        // ¡CRÍTICO! Destruir instancia previa antes de redibujar
        if (demandChart) {
            demandChart.destroy();
            demandChart = null;
        }

        const ctx = canvasEl.getContext('2d');

        // Parsear como números (nunca strings al eje Y)
        const histValores = historico.valores.map(v => parseFloat(v));
        const predValores = prediccion.valores.map(v => parseFloat(v));

        // Eje X combinado
        const labels = [...historico.fechas, ...prediccion.fechas];

        // Dataset 1: histórico + nulos al final (para no "pintar" zona de predicción)
        const datosHistorico = [...histValores, ...Array(predValores.length).fill(null)];

        // Dataset 2: nulos al inicio + último punto del histórico como puente + predicción
        const datosPrediccion = [
            ...Array(histValores.length - 1).fill(null),
            histValores[histValores.length - 1],
            ...predValores
        ];

        // Gradientes
        const gradHist = ctx.createLinearGradient(0, 0, 0, 300);
        gradHist.addColorStop(0, 'rgba(59, 130, 246, 0.30)');
        gradHist.addColorStop(1, 'rgba(59, 130, 246, 0.00)');

        demandChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Tendencia Histórica',
                        data: datosHistorico,
                        borderColor: '#3b82f6',
                        backgroundColor: gradHist,
                        borderWidth: 2,
                        pointRadius: 2,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#3b82f6',
                        fill: true,
                        tension: 0.4,
                        spanGaps: false,
                    },
                    {
                        label: 'Proyección Predictiva (30 días)',
                        data: datosPrediccion,
                        borderColor: '#10b981',
                        borderWidth: 2,
                        borderDash: [6, 4],
                        pointRadius: 2,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#10b981',
                        fill: false,
                        tension: 0.4,
                        spanGaps: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        display: true,
                        labels: { color: '#94a3b8', font: { size: 11, weight: 'bold' }, boxWidth: 12, padding: 16 }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(59, 130, 246, 0.3)',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: ctx => {
                                if (ctx.raw === null) return null;
                                return ` ${ctx.dataset.label}: ${ctx.raw.toFixed(2)} uds.`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(51, 65, 85, 0.12)', drawBorder: false },
                        ticks: { color: '#64748b', maxTicksLimit: 10, font: { size: 10 } }
                    },
                    y: {
                        grid: { color: 'rgba(51, 65, 85, 0.12)', drawBorder: false },
                        ticks: { color: '#64748b', callback: v => `${v}` },
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // ── Inicialización ────────────────────────────────────────────────────
    // Cargar predicción del primer albergue disponible al entrar
    if (selectEl && selectEl.value) {
        fetchPrediccion(selectEl.value);
    } else {
        mostrarError('No hay albergues disponibles para mostrar predicciones.');
    }

    // Recargar al cambiar albergue
    if (selectEl) {
        selectEl.addEventListener('change', e => fetchPrediccion(e.target.value));
    }
})();
</script>
@endpush