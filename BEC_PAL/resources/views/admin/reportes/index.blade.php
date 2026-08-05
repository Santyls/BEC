@extends('layouts.admin')

@section('title', 'Generación de Reportes')
@section('header_title', 'Reportes y Analíticas')

@section('content')
<div class="space-y-6">
    <div class="mb-4">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Centro de Reportes</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">Filtra el periodo y los criterios antes de consultar o exportar.</p>
    </div>

    <form method="GET" action="{{ route('admin.reportes.index') }}" class="glass rounded-2xl p-6 border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}" max="{{ now()->toDateString() }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 [color-scheme:light] dark:[color-scheme:dark]">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}" max="{{ now()->toDateString() }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 [color-scheme:light] dark:[color-scheme:dark]">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Albergue (Donaciones)</label>
                <select name="donacion_albergue_id" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 dark:[color-scheme:dark]">
                    <option value="">Todos</option>
                    @foreach ($albergues as $albergue)
                        <option value="{{ $albergue['id'] }}" @selected($albergueSeleccionado == $albergue['id'])>{{ $albergue['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Campaña (Voluntariados)</label>
                <select name="campana_id" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 dark:[color-scheme:dark]">
                    <option value="">Todas</option>
                    @foreach ($campanas as $campana)
                        <option value="{{ $campana['id'] }}" @selected($campanaSeleccionada == $campana['id'])>{{ $campana['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Voluntariado específico</label>
                <select name="voluntariado_id" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 dark:[color-scheme:dark]">
                    <option value="">Todos</option>
                    @foreach ($catalogoVoluntariados as $vol)
                        <option value="{{ $vol['id'] }}" @selected($voluntariadoSeleccionado == $vol['id'])>{{ $vol['nombre_programa'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.reportes.index') }}" class="px-4 py-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white text-sm transition-colors">Limpiar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-lg font-bold text-sm flex items-center transition-all">
                <i data-lucide="filter" class="w-4 h-4 mr-2"></i> Aplicar filtros
            </button>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- Tarjeta de Reporte de Donaciones -->
        <div class="glass rounded-3xl p-8 border border-slate-200 dark:border-slate-800 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl -mr-10 -mt-10"></div>

            <div class="flex items-center gap-4 mb-6 relative z-10">
                <div class="p-4 bg-blue-500/20 rounded-2xl border border-blue-500/30">
                    <i data-lucide="file-bar-chart-2" class="w-8 h-8 text-blue-700 dark:text-blue-400"></i>
                </div>
                <div>
                    <h4 class="text-xl font-bold text-slate-900 dark:text-white">Reporte de Donaciones</h4>
                    <p class="text-xs text-blue-700 dark:text-blue-400 uppercase tracking-widest font-semibold">Periodo seleccionado</p>
                </div>
            </div>

            <div class="space-y-3 relative z-10 text-sm text-slate-700 dark:text-slate-300">
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800">
                    <span class="flex items-center gap-2"><i data-lucide="package" class="w-4 h-4 text-blue-600"></i> Total de donaciones (periodo)</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $totalDonacionesPeriodo }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800">
                    <span class="flex items-center gap-2"><i data-lucide="award" class="w-4 h-4 text-blue-600"></i> Top donador (periodo)</span>
                    <span class="font-bold text-slate-900 dark:text-white text-right">{{ $topDonadorPeriodo }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800">
                    <span class="flex items-center gap-2"><i data-lucide="trophy" class="w-4 h-4 text-amber-600"></i> Top donador (histórico general)</span>
                    <span class="font-bold text-slate-900 dark:text-white text-right">{{ $topDonadorGeneral }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800">
                    <span class="flex items-center gap-2"><i data-lucide="tag" class="w-4 h-4 text-blue-600"></i> Categoría más donada</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $categoriaTop }}</span>
                </div>
            </div>

            <div class="mt-6 flex gap-3 relative z-10">
                <a href="{{ route('admin.reportes.donaciones.xlsx', request()->query()) }}" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl transition-all flex justify-center items-center gap-2 text-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Exportar XLSX
                </a>
                <a href="{{ route('admin.reportes.donaciones.pdf', request()->query()) }}" class="flex-1 bg-rose-600 hover:bg-rose-500 text-white font-bold py-3 rounded-xl transition-all flex justify-center items-center gap-2 text-sm">
                    <i data-lucide="file-text" class="w-4 h-4"></i> Exportar PDF
                </a>
            </div>
        </div>

        <!-- Tarjeta de Reporte de Voluntariados -->
        <div class="glass rounded-3xl p-8 border border-slate-200 dark:border-slate-800 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl -mr-10 -mt-10"></div>

            <div class="flex items-center gap-4 mb-6 relative z-10">
                <div class="p-4 bg-emerald-500/20 rounded-2xl border border-emerald-500/30">
                    <i data-lucide="users" class="w-8 h-8 text-emerald-700 dark:text-emerald-400"></i>
                </div>
                <div>
                    <h4 class="text-xl font-bold text-slate-900 dark:text-white">Reporte de Voluntariados</h4>
                    <p class="text-xs text-emerald-700 dark:text-emerald-400 uppercase tracking-widest font-semibold">Periodo seleccionado</p>
                </div>
            </div>

            <div class="space-y-3 relative z-10 text-sm text-slate-700 dark:text-slate-300">
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800">
                    <span class="flex items-center gap-2"><i data-lucide="calendar-check" class="w-4 h-4 text-emerald-600"></i> Voluntariados (periodo)</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $totalVoluntariadosPeriodo }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800">
                    <span class="flex items-center gap-2"><i data-lucide="user-check" class="w-4 h-4 text-emerald-600"></i> Total de inscritos</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $totalInscritosPeriodo }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800">
                    <span class="flex items-center gap-2"><i data-lucide="megaphone" class="w-4 h-4 text-emerald-600"></i> Campaña con más voluntariados</span>
                    <span class="font-bold text-slate-900 dark:text-white text-right">{{ $campanaTop }}</span>
                </div>
            </div>

            <div class="mt-6 flex gap-3 relative z-10">
                <a href="{{ route('admin.reportes.voluntariados.xlsx', request()->query()) }}" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl transition-all flex justify-center items-center gap-2 text-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Exportar XLSX
                </a>
                <a href="{{ route('admin.reportes.voluntariados.pdf', request()->query()) }}" class="flex-1 bg-rose-600 hover:bg-rose-500 text-white font-bold py-3 rounded-xl transition-all flex justify-center items-center gap-2 text-sm">
                    <i data-lucide="file-text" class="w-4 h-4"></i> Exportar PDF
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
