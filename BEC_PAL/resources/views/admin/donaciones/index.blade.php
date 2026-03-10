@extends('layouts.admin')

@section('title', 'Gestión de Donaciones')
@section('header_title', 'Donaciones Registradas')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-white">Inventario Global de Donaciones</h3>
            <p class="text-sm text-slate-400">Supervisa todos los insumos recibidos en los distintos albergues.</p>
        </div>
        <a href="{{ route('admin.donaciones.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-blue-500/20">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> NUEVA DONACIÓN
        </a>
    </div>

    <div class="glass rounded-2xl overflow-hidden border border-slate-800">
        <!-- Barra de filtros -->
        <div class="p-4 bg-slate-800/30 border-b border-slate-800 flex justify-between items-center">
            <div class="relative w-72">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                <input type="text" placeholder="Buscar por donante o insumo..." class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg pl-9 pr-4 py-2 focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex gap-2">
                <select class="bg-slate-900 border border-slate-700 text-slate-400 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                    <option>Todas las categorías</option>
                    <option>Alimentos</option>
                    <option>Ropa</option>
                    <option>Higiene</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 text-slate-400 text-xs uppercase tracking-widest border-b border-slate-800">
                        <th class="p-4 font-semibold">Folio / Fecha</th>
                        <th class="p-4 font-semibold">Donante</th>
                        <th class="p-4 font-semibold">Insumo</th>
                        <th class="p-4 font-semibold">Cantidad</th>
                        <th class="p-4 font-semibold">Albergue Destino</th>
                        <th class="p-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-800/50">
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <td class="p-4">
                            <p class="font-mono text-xs text-slate-500">#DON-845</p>
                            <p class="text-slate-300">12 Oct, 2026</p>
                        </td>
                        <td class="p-4 font-bold text-slate-200">
                            JuanitoP <span class="text-xs font-normal text-slate-500 block">juan@bec.org</span>
                        </td>
                        <td class="p-4">
                            <span class="bg-blue-500/10 text-blue-400 px-2 py-0.5 rounded text-[10px] uppercase font-bold border border-blue-500/20 mb-1 inline-block">Alimentos</span>
                            <p class="text-slate-300">Arroz y Frijol (Verde Valle)</p>
                        </td>
                        <td class="p-4 text-slate-200 font-bold">20.00 <span class="text-xs font-normal text-slate-500">kg</span></td>
                        <td class="p-4 text-slate-400">Albergue Esperanza</td>
                        <td class="p-4 text-right">
                            <button class="text-slate-500 hover:text-blue-400 p-1"><i data-lucide="edit" class="w-4 h-4"></i></button>
                            <button class="text-slate-500 hover:text-rose-400 p-1 ml-1"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <td class="p-4">
                            <p class="font-mono text-xs text-slate-500">#DON-846</p>
                            <p class="text-slate-300">14 Oct, 2026</p>
                        </td>
                        <td class="p-4 font-bold text-slate-200">
                            Anonimo <span class="text-xs font-normal text-slate-500 block">No registrado</span>
                        </td>
                        <td class="p-4">
                            <span class="bg-purple-500/10 text-purple-400 px-2 py-0.5 rounded text-[10px] uppercase font-bold border border-purple-500/20 mb-1 inline-block">Ropa</span>
                            <p class="text-slate-300">Chamarras de invierno</p>
                        </td>
                        <td class="p-4 text-slate-200 font-bold">15.00 <span class="text-xs font-normal text-slate-500">piezas</span></td>
                        <td class="p-4 text-slate-400">Refugio Huellas</td>
                        <td class="p-4 text-right">
                            <button class="text-slate-500 hover:text-blue-400 p-1"><i data-lucide="edit" class="w-4 h-4"></i></button>
                            <button class="text-slate-500 hover:text-rose-400 p-1 ml-1"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection