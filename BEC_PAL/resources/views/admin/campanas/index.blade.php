@extends('layouts.admin')

@section('title', 'Gestión de Campañas')
@section('header_title', 'Campañas Activas')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-white">Campañas Estratégicas</h3>
            <p class="text-sm text-slate-400">Agrupa voluntariados y donaciones bajo objetivos en común.</p>
        </div>
        <a href="{{ route('admin.campanas.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-blue-500/20">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> NUEVA CAMPAÑA
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Tarjeta de Campaña 1 -->
        <div class="glass rounded-2xl p-6 border border-slate-800 hover:border-blue-500/50 transition-all group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4">
                <span class="bg-emerald-500/10 text-emerald-400 px-2 py-1 rounded text-[10px] font-bold border border-emerald-500/20">ACTIVA</span>
            </div>
            <i data-lucide="megaphone" class="w-8 h-8 text-blue-500 mb-4"></i>
            <h4 class="text-lg font-bold text-white">Invierno Cálido 2026</h4>
            <p class="text-xs text-slate-400 mt-2 line-clamp-2">Recolección de cobijas y ropa de invierno para albergues de la sierra.</p>
            
            <div class="mt-4 pt-4 border-t border-slate-800 flex justify-between items-center text-sm text-slate-400">
                <span title="Fecha de fin"><i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i> 31 Dic</span>
                <div>
                    <button class="hover:text-blue-400 mr-2"><i data-lucide="edit" class="w-4 h-4"></i></button>
                    <button class="hover:text-rose-400"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Campaña 2 -->
        <div class="glass rounded-2xl p-6 border border-slate-800 hover:border-blue-500/50 transition-all group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4">
                <span class="bg-slate-500/10 text-slate-400 px-2 py-1 rounded text-[10px] font-bold border border-slate-500/20">PROGRAMADA</span>
            </div>
            <i data-lucide="megaphone" class="w-8 h-8 text-slate-500 mb-4 group-hover:text-blue-500 transition-colors"></i>
            <h4 class="text-lg font-bold text-white">Adopta un Amigo</h4>
            <p class="text-xs text-slate-400 mt-2 line-clamp-2">Campaña intensiva de fin de semana para fomentar la adopción de mascotas rescatadas.</p>
            
            <div class="mt-4 pt-4 border-t border-slate-800 flex justify-between items-center text-sm text-slate-400">
                <span title="Fecha de inicio"><i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i> Inicia: 15 Ene</span>
                <div>
                    <button class="hover:text-blue-400 mr-2"><i data-lucide="edit" class="w-4 h-4"></i></button>
                    <button class="hover:text-rose-400"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection