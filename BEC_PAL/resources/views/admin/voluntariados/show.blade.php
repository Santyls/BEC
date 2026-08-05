@extends('layouts.admin')

@section('title', $voluntariado['nombre_programa'])
@section('header_title', 'Detalle del Voluntariado')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.voluntariados.index') }}" class="w-10 h-10 rounded-full glass flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $voluntariado['nombre_programa'] }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $inscritos ? count($inscritos) : 0 }} inscripción(es) registradas en total.</p>
            </div>
        </div>
        <a href="{{ route('admin.voluntariados.edit', $voluntariado['id']) }}" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg">
            <i data-lucide="edit" class="w-4 h-4 mr-2"></i> Editar
        </a>
    </div>

    <!-- Detalles -->
    <div class="glass rounded-2xl p-6 border border-slate-200 dark:border-slate-800 grid grid-cols-1 md:grid-cols-4 gap-6 text-sm">
        <div>
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Fecha y hora</p>
            <p class="text-slate-800 dark:text-slate-200">{{ \Illuminate\Support\Carbon::parse($voluntariado['fecha_programada'])->format('d M, Y') }}</p>
            <p class="text-slate-500 dark:text-slate-400 text-xs">{{ substr($voluntariado['hora_inicio'],0,5) }} - {{ substr($voluntariado['hora_fin'],0,5) }}</p>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Albergue</p>
            <p class="text-slate-800 dark:text-slate-200">{{ $albergue['nombre'] ?? 'Sin albergue asignado' }}</p>
            @if (!empty($voluntariado['ubicacion']))
                <p class="text-slate-500 dark:text-slate-400 text-xs"><i data-lucide="map-pin" class="w-3 h-3 inline"></i> {{ $voluntariado['ubicacion'] }}</p>
            @endif
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Campaña</p>
            <p class="text-slate-800 dark:text-slate-200">{{ $campana['nombre'] ?? 'Ninguna (actividad independiente)' }}</p>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Estado / Cupo</p>
            <p class="text-slate-800 dark:text-slate-200">{{ $estados[$voluntariado['estado_id']]['nombre'] ?? '—' }}</p>
            <p class="text-slate-500 dark:text-slate-400 text-xs">{{ $voluntariado['inscritos'] }} / {{ $voluntariado['cupo_maximo'] ?? '∞' }}</p>
        </div>
        <div class="md:col-span-4 pt-2 border-t border-slate-200 dark:border-slate-800">
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Descripción y requisitos</p>
            <p class="text-slate-700 dark:text-slate-300">{{ $voluntariado['descripcion_requisitos'] }}</p>
        </div>
    </div>

    <!-- Inscritos -->
    <div class="glass rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
            <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-widest">Personas inscritas</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-widest border-b border-slate-200 dark:border-slate-800">
                        <th class="p-4 font-semibold">Nombre</th>
                        <th class="p-4 font-semibold">Contacto</th>
                        <th class="p-4 font-semibold">Fecha de inscripción</th>
                        <th class="p-4 font-semibold">Estado</th>
                        <th class="p-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800/50">
                    @forelse ($inscritos as $i)
                        @php
                            $colorEstado = match ($i['estado_id']) {
                                2 => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20',
                                3 => 'bg-slate-500/10 text-slate-700 dark:text-slate-300 border-slate-500/20',
                                default => 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="p-4">
                                @if ($i['usuario'])
                                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ $i['usuario']['nombre'] }} {{ $i['usuario']['apellido_paterno'] }} {{ $i['usuario']['apellido_materno'] }}</p>
                                    @if ($i['usuario']['vetado'])
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 dark:text-rose-400 bg-rose-500/10 border border-rose-500/20 px-2 py-0.5 rounded-full mt-1">
                                            <i data-lucide="shield-off" class="w-3 h-3"></i> VETADO
                                        </span>
                                    @endif
                                @else
                                    <p class="text-slate-500 dark:text-slate-400 italic">Usuario eliminado</p>
                                @endif
                            </td>
                            <td class="p-4 text-slate-500 dark:text-slate-400">
                                {{ $i['usuario']['correo'] ?? '—' }}
                                <p class="text-xs">{{ $i['usuario']['telefono'] ?? '' }}</p>
                            </td>
                            <td class="p-4 text-slate-700 dark:text-slate-300">{{ \Illuminate\Support\Carbon::parse($i['fecha_inscripcion'])->format('d M, Y H:i') }}</td>
                            <td class="p-4">
                                <span class="{{ $colorEstado }} px-2.5 py-1 rounded-full text-[10px] font-bold border">
                                    {{ mb_strtoupper($estadosInscripcion[$i['estado_id']]['nombre'] ?? '—') }}
                                </span>
                            </td>
                            <td class="p-4 text-right whitespace-nowrap">
                                @if ($i['estado_id'] === 1)
                                    <form method="POST" action="{{ route('admin.voluntariados.inscripciones.cancelar', [$voluntariado['id'], $i['id']]) }}" class="inline" data-confirm="¿Cancelar esta inscripción?">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="text-slate-500 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 p-1 transition-colors" title="Cancelar inscripción">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                                @if ($i['usuario'] && !$i['usuario']['vetado'])
                                    <form method="POST" action="{{ route('admin.usuarios.vetar', $i['usuario']['id']) }}" class="inline" data-confirm="¿Vetar a {{ $i['usuario']['nombre'] }}? No podrá inscribirse a ningún voluntariado futuro.">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="vetado" value="1">
                                        <input type="hidden" name="motivo_veto" value="Vetado desde el detalle de '{{ $voluntariado['nombre_programa'] }}'">
                                        <button type="submit" class="text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 p-1 transition-colors ml-1" title="Vetar usuario">
                                            <i data-lucide="shield-off" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @elseif ($i['usuario'] && $i['usuario']['vetado'])
                                    <form method="POST" action="{{ route('admin.usuarios.vetar', $i['usuario']['id']) }}" class="inline" data-confirm="¿Quitar el veto a {{ $i['usuario']['nombre'] }}?">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="vetado" value="0">
                                        <button type="submit" class="text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 p-1 transition-colors ml-1" title="Quitar veto">
                                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-8 text-center text-slate-500 dark:text-slate-400">Nadie se ha inscrito todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
