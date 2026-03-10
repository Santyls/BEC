@extends('layouts.admin')

@section('title', 'Gestión de Albergues')
@section('header_title', 'Albergues Registrados')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-white">Albergues y Sedes</h3>
            <p class="text-sm text-slate-400">Administra las instalaciones disponibles para voluntariados y donaciones.</p>
        </div>
        <a href="{{ route('admin.albergues.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-blue-500/20">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> NUEVO ALBERGUE
        </a>
    </div>

    <div class="glass rounded-2xl overflow-hidden border border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 text-slate-400 text-xs uppercase tracking-widest border-b border-slate-800">
                        <th class="p-4 font-semibold">Nombre del Albergue</th>
                        <th class="p-4 font-semibold">Ubicación (Colonia)</th>
                        <th class="p-4 font-semibold">Capacidad</th>
                        <th class="p-4 font-semibold">Contacto</th>
                        <th class="p-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-800/50">
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <td class="p-4 font-bold text-slate-200">Albergue Esperanza</td>
                        <td class="p-4 text-slate-400"><i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i> Centro Histórico</td>
                        <td class="p-4 text-slate-300">50 personas</td>
                        <td class="p-4 text-slate-400">442 123 4567</td>
                        <td class="p-4 text-right">
                            <button class="text-slate-500 hover:text-blue-400 p-1"><i data-lucide="edit" class="w-4 h-4"></i></button>
                            <button class="text-slate-500 hover:text-rose-400 p-1 ml-1"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <td class="p-4 font-bold text-slate-200">Refugio Huellas</td>
                        <td class="p-4 text-slate-400"><i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i> Juriquilla</td>
                        <td class="p-4 text-slate-300">120 animales</td>
                        <td class="p-4 text-slate-400">442 987 6543</td>
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