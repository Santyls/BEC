@extends('layouts.admin')

@section('title', 'Gestión de Campañas')
@section('header_title', 'Campañas Activas')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Campañas Estratégicas</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Agrupa voluntariados y donaciones bajo objetivos en común.</p>
        </div>
        <a href="{{ route('admin.campanas.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-blue-500/20">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> NUEVA CAMPAÑA
        </a>
    </div>

    <form method="GET" action="{{ route('admin.campanas.index') }}" class="glass rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex flex-wrap gap-3 justify-between items-center">
        <div class="relative w-full sm:w-72">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 dark:text-slate-400"></i>
            <input type="text" name="q" value="{{ $busqueda }}" placeholder="Buscar por nombre u objetivos..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg pl-9 pr-4 py-2 focus:outline-none focus:border-blue-500">
        </div>
        <button type="submit" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white p-2"><i data-lucide="search" class="w-4 h-4"></i></button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($campanas as $campana)
            @php
                $nombreEstado = $estados[$campana['estado_id']]['nombre'] ?? 'Sin estado';
                $colorEstado = match ($campana['estado_id']) {
                    2 => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20',
                    3 => 'bg-slate-500/10 text-slate-700 dark:text-slate-300 border-slate-500/20',
                    default => 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20',
                };
            @endphp
            <div class="glass rounded-2xl p-6 border border-slate-200 dark:border-slate-800 hover:border-blue-500/50 transition-all group relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4">
                    <span class="{{ $colorEstado }} px-2 py-1 rounded text-[10px] font-bold border">{{ mb_strtoupper($nombreEstado) }}</span>
                </div>
                <i data-lucide="megaphone" class="w-8 h-8 text-blue-500 mb-4"></i>
                <h4 class="text-lg font-bold text-slate-900 dark:text-white pr-24">{{ $campana['nombre'] }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2">{{ $campana['descripcion_objetivos'] }}</p>

                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800 flex flex-wrap gap-3 justify-between items-center text-sm text-slate-500 dark:text-slate-400">
                    <span title="Vigencia"><i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i> {{ \Illuminate\Support\Carbon::parse($campana['fecha_inicio'])->format('d M') }} – {{ \Illuminate\Support\Carbon::parse($campana['fecha_fin'])->format('d M') }}</span>
                    <!-- flex + gap: los iconos de lucide son SVG en display:block y
                         dentro de elementos inline se encimaban entre sí. -->
                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.campanas.edit', $campana['id']) }}" title="Editar campaña"
                           class="p-2 rounded-lg flex items-center hover:bg-blue-500/10 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.campanas.destroy', $campana['id']) }}" data-confirm="¿Marcar '{{ $campana['nombre'] }}' como finalizada?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Marcar como finalizada"
                                    class="p-2 rounded-lg flex items-center hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full glass rounded-2xl p-10 border border-slate-200 dark:border-slate-800 text-center text-slate-500 dark:text-slate-400">
                No hay campañas que coincidan.
            </div>
        @endforelse
    </div>

    <div class="glass rounded-2xl border border-slate-200 dark:border-slate-800">
        @include('partials.paginacion', ['paginador' => $campanas])
    </div>
</div>
@endsection
