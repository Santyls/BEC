@extends('layouts.admin')

@section('title', 'Gestión de Donaciones')
@section('header_title', 'Donaciones Registradas')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Inventario Global de Donaciones</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Supervisa todos los insumos recibidos en los distintos albergues.</p>
        </div>
        <a href="{{ route('admin.donaciones.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-blue-500/20">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> NUEVA DONACIÓN
        </a>
    </div>

    <div class="glass rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800">
        <form method="GET" action="{{ route('admin.donaciones.index') }}" class="p-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="relative w-full sm:w-72">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <input type="text" name="q" value="{{ $busqueda }}" placeholder="Buscar por marca, categoría o albergue..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg pl-9 pr-4 py-2 focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex gap-2">
                <select name="categoria_id" onchange="this.form.submit()" class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 dark:[color-scheme:dark]">
                    <option value="">Todas las categorías</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria['id'] }}" @selected(request('categoria_id') == $categoria['id'])>{{ $categoria['nombre'] }}</option>
                    @endforeach
                </select>
                <button type="submit" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white p-2"><i data-lucide="search" class="w-4 h-4"></i></button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-widest border-b border-slate-200 dark:border-slate-800">
                        <th class="p-4 font-semibold">Folio / Fecha</th>
                        <th class="p-4 font-semibold">Donante</th>
                        <th class="p-4 font-semibold">Insumo</th>
                        <th class="p-4 font-semibold">Cantidad</th>
                        <th class="p-4 font-semibold">Albergue Destino</th>
                        <th class="p-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800/50">
                    @forelse ($donaciones as $d)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="p-4">
                                <p class="font-mono text-xs text-slate-500 dark:text-slate-400">#DON-{{ str_pad($d['id'], 3, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-slate-700 dark:text-slate-300">{{ \Illuminate\Support\Carbon::parse($d['fecha_donacion'])->format('d M, Y') }}</p>
                            </td>
                            <td class="p-4 font-bold text-slate-800 dark:text-slate-200">
                                @if ($d['usuario'])
                                    {{ $d['usuario']['nombre'] }} {{ $d['usuario']['apellido_paterno'] }}
                                    <span class="text-xs font-normal text-slate-500 dark:text-slate-400 block">{{ $d['usuario']['correo'] ?? 'sin correo' }}</span>
                                @else
                                    Anónimo <span class="text-xs font-normal text-slate-500 dark:text-slate-400 block">No registrado</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="bg-blue-500/10 text-blue-700 dark:text-blue-400 px-2 py-0.5 rounded text-[10px] uppercase font-bold border border-blue-500/20 mb-1 inline-block">{{ $categorias[$d['categoria_id']]['nombre'] ?? '—' }}</span>
                                <p class="text-slate-700 dark:text-slate-300">{{ $d['marca'] ?: $condiciones[$d['condicion_id']]['nombre'] ?? '' }}</p>
                            </td>
                            <td class="p-4 text-slate-800 dark:text-slate-200 font-bold">{{ number_format($d['cantidad'], 2) }} <span class="text-xs font-normal text-slate-500 dark:text-slate-400">{{ $unidades[$d['unidad_id']]['nombre'] ?? '' }}</span></td>
                            <td class="p-4 text-slate-500 dark:text-slate-400">{{ $d['albergue']['nombre'] ?? '—' }}</td>
                            <td class="p-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.donaciones.edit', $d['id']) }}" title="Editar donación"
                                       class="p-2 rounded-lg flex items-center text-slate-500 hover:bg-blue-500/10 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.donaciones.destroy', $d['id']) }}"
                                          data-confirm="¿Eliminar esta donación de {{ number_format($d['cantidad'], 2) }} {{ $unidades[$d['unidad_id']]['nombre'] ?? '' }}? Se descuenta del inventario y no se puede deshacer.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Eliminar donación"
                                                class="p-2 rounded-lg flex items-center text-slate-500 hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-slate-500 dark:text-slate-400">No hay donaciones que coincidan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.paginacion', ['paginador' => $donaciones])
    </div>
</div>
@endsection
