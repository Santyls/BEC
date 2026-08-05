@extends('layouts.admin')

@section('title', 'Gestión de Albergues')
@section('header_title', 'Albergues Registrados')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Albergues y Sedes</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Administra las instalaciones disponibles para voluntariados y donaciones.</p>
        </div>
        <a href="{{ route('admin.albergues.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-blue-500/20">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> NUEVO ALBERGUE
        </a>
    </div>

    <div class="glass rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800">
        <form method="GET" action="{{ route('admin.albergues.index') }}" class="p-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800 flex flex-wrap gap-3 justify-between items-center">
            <div class="relative w-full sm:w-72">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <input type="text" name="q" value="{{ $busqueda }}" placeholder="Buscar por nombre, colonia o municipio..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg pl-9 pr-4 py-2 focus:outline-none focus:border-blue-500">
            </div>
            <button type="submit" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white p-2"><i data-lucide="search" class="w-4 h-4"></i></button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-widest border-b border-slate-200 dark:border-slate-800">
                        <th class="p-4 font-semibold">Nombre del Albergue</th>
                        <th class="p-4 font-semibold">Ubicación (Colonia)</th>
                        <th class="p-4 font-semibold">Capacidad</th>
                        <th class="p-4 font-semibold">Contacto</th>
                        <th class="p-4 font-semibold">Estado</th>
                        <th class="p-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800/50">
                    @forelse ($albergues as $albergue)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="p-4 font-bold text-slate-800 dark:text-slate-200">{{ $albergue['nombre'] }}</td>
                            <td class="p-4 text-slate-500 dark:text-slate-400"><i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i> {{ $albergue['direccion']['colonia'] }}</td>
                            <td class="p-4 text-slate-700 dark:text-slate-300">{{ $albergue['capacidad_max'] }} personas</td>
                            <td class="p-4 text-slate-500 dark:text-slate-400">{{ $albergue['telefono'] }}</td>
                            <td class="p-4">
                                @if ($albergue['activo'])
                                    <span class="text-emerald-700 dark:text-emerald-400 text-xs">● Activo</span>
                                @else
                                    @php
                                        $diasRestantes = $albergue['fecha_desactivacion']
                                            ? max(0, 30 - \Illuminate\Support\Carbon::parse($albergue['fecha_desactivacion'])->diffInDays(now()))
                                            : null;
                                    @endphp
                                    <span class="text-slate-500 dark:text-slate-400 text-xs">● Inactivo</span>
                                    @if (!is_null($diasRestantes))
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Se elimina en {{ $diasRestantes }} día(s)</p>
                                    @endif
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                @if ($albergue['activo'])
                                    <a href="{{ route('admin.albergues.edit', $albergue['id']) }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 p-1 inline-block"><i data-lucide="edit" class="w-4 h-4"></i></a>
                                    <form method="POST" action="{{ route('admin.albergues.destroy', $albergue['id']) }}" class="inline" data-confirm="¿Desactivar {{ $albergue['nombre'] }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 p-1 ml-1"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.albergues.reactivar', $albergue['id']) }}" class="inline" data-confirm="¿Reactivar {{ $albergue['nombre'] }}?">
                                        @csrf
                                        <button type="submit" class="text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 p-1 inline-block" title="Reactivar"><i data-lucide="rotate-ccw" class="w-4 h-4"></i></button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.albergues.eliminarPermanente', $albergue['id']) }}" class="inline" data-confirm="¿Eliminar PERMANENTEMENTE {{ $albergue['nombre'] }}? Esta acción no se puede deshacer.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 p-1 ml-1" title="Eliminar permanentemente"><i data-lucide="x-circle" class="w-4 h-4"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-slate-500 dark:text-slate-400">No hay albergues que coincidan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.paginacion', ['paginador' => $albergues])
    </div>
</div>
@endsection
