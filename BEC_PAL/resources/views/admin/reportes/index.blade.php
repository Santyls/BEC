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
        
        <!-- Tarjeta de Reporte de Donaciones -->
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
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-blue-400"></i> Top donador del mes.</p>
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-blue-400"></i> Semana con mayor afluencia.</p>
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-blue-400"></i> Categoría más donada.</p>
            </div>

            <button class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/20 transition-all flex justify-center items-center gap-2 relative z-10 group-hover:scale-[1.02]">
                <i data-lucide="download" class="w-5 h-5"></i>
                Generar Reporte Mensual
            </button>
        </div>

        <!-- Tarjeta de Reporte de Voluntariados -->
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
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i> Voluntariados totales en el mes.</p>
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i> Voluntario con mayor participación.</p>
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i> Edades promedio de asistentes.</p>
                <p class="flex items-center gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i> Campaña con más voluntarios.</p>
            </div>

            <button class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-500/20 transition-all flex justify-center items-center gap-2 relative z-10 group-hover:scale-[1.02]">
                <i data-lucide="download" class="w-5 h-5"></i>
                Generar Reporte Mensual
            </button>
        </div>

    </div>
</div>
@endsection