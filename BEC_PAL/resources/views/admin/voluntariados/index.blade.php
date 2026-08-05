@extends('layouts.admin')

@section('title', 'Gestión de Voluntariados')
@section('header_title', 'Voluntariados Activos')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Registro de Voluntariados</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Administra las actividades y el cupo de voluntarios.</p>
        </div>
        <a href="{{ route('admin.voluntariados.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-blue-500/20">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> NUEVO VOLUNTARIADO
        </a>
    </div>

    <div class="glass rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800">
        <form method="GET" action="{{ route('admin.voluntariados.index') }}" class="p-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800 flex flex-wrap gap-3 justify-between items-center">
            <div class="relative w-full sm:w-72">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <input type="text" name="q" value="{{ $busqueda }}" placeholder="Buscar actividad..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg pl-9 pr-4 py-2 focus:outline-none focus:border-blue-500">
            </div>
            <button type="submit" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white p-2"><i data-lucide="search" class="w-4 h-4"></i></button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-widest border-b border-slate-200 dark:border-slate-800">
                        <th class="p-4 font-semibold">Actividad</th>
                        <th class="p-4 font-semibold">Albergue Asignado</th>
                        <th class="p-4 font-semibold">Fecha y Hora</th>
                        <th class="p-4 font-semibold">Cupo</th>
                        <th class="p-4 font-semibold">Estado</th>
                        <th class="p-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800/50">
                    @forelse ($voluntariados as $v)
                        @php
                            $albergueNombre = $v['albergue_id'] ? ($albergues[$v['albergue_id']]['nombre'] ?? '—') : '—';
                            $nombreEstado = $estados[$v['estado_id']]['nombre'] ?? 'Sin estado';
                            $colorEstado = match ($v['estado_id']) {
                                2 => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20',
                                4 => 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20',
                                3 => 'bg-slate-500/10 text-slate-700 dark:text-slate-300 border-slate-500/20',
                                default => 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20',
                            };
                            $lleno = $v['cupo_maximo'] && $v['inscritos'] >= $v['cupo_maximo'];
                            $porcentaje = $v['cupo_maximo'] ? min(100, round($v['inscritos'] / $v['cupo_maximo'] * 100)) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="p-4">
                                <a href="{{ route('admin.voluntariados.show', $v['id']) }}" class="font-bold text-slate-800 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ $v['nombre_programa'] }}</a>
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1">{{ $v['descripcion_requisitos'] }}</p>
                            </td>
                            <td class="p-4 text-slate-700 dark:text-slate-300">
                                <div class="flex items-center">
                                    <i data-lucide="home" class="w-3 h-3 mr-2 text-slate-500 dark:text-slate-400"></i>
                                    {{ $albergueNombre }}
                                </div>
                                @if (!empty($v['ubicacion']))
                                    <div class="flex items-center mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        <i data-lucide="map-pin" class="w-3 h-3 mr-2 flex-shrink-0"></i>
                                        {{ $v['ubicacion'] }}
                                    </div>
                                @endif
                            </td>
                            <td class="p-4 text-slate-700 dark:text-slate-300">
                                <p>{{ \Illuminate\Support\Carbon::parse($v['fecha_programada'])->format('d M, Y') }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ substr($v['hora_inicio'],0,5) }} - {{ substr($v['hora_fin'],0,5) }}</p>
                            </td>
                            <td class="p-4">
                                @if ($v['cupo_maximo'])
                                    <div class="flex items-center gap-2">
                                        <div class="bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 w-16">
                                            <div class="{{ $lleno ? 'bg-amber-500' : 'bg-blue-500' }} h-1.5 rounded-full" style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                        <span class="text-xs {{ $lleno ? 'text-amber-700 dark:text-amber-400 font-bold' : 'text-slate-500 dark:text-slate-400' }}">{{ $v['inscritos'] }}/{{ $v['cupo_maximo'] }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $v['inscritos'] }} inscritos (sin límite)</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="{{ $colorEstado }} px-2.5 py-1 rounded-full text-[10px] font-bold border">
                                    {{ mb_strtoupper($lleno ? 'LLENO' : $nombreEstado) }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('admin.voluntariados.show', $v['id']) }}" class="text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 p-1 transition-colors inline-block" title="Ver inscritos">
                                    <i data-lucide="users" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.voluntariados.edit', $v['id']) }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 p-1 transition-colors inline-block" title="Editar">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.voluntariados.destroy', $v['id']) }}" class="inline" data-confirm="¿Cancelar '{{ $v['nombre_programa'] }}'?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 p-1 transition-colors ml-1" title="Cancelar">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-slate-500 dark:text-slate-400">No hay voluntariados que coincidan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.paginacion', ['paginador' => $voluntariados])
    </div>
</div>
@endsection
