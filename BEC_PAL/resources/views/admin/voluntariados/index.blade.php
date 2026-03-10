@extends('layouts.admin')

@section('title', 'Gestión de Voluntariados')
@section('header_title', 'Voluntariados Activos')

@section('content')
<div class="space-y-6">
    
    <!-- Cabecera y Botón de Agregar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-white">Registro de Voluntariados</h3>
            <p class="text-sm text-slate-400">Administra las actividades y el cupo de voluntarios.</p>
        </div>
        
        <a href="{{ route('admin.voluntariados.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-blue-500/20">
    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> NUEVO VOLUNTARIADO
</a>
    </div>

    <!-- Tabla de Voluntariados -->
    <div class="glass rounded-2xl overflow-hidden border border-slate-800">
        <div class="p-4 bg-slate-800/30 border-b border-slate-800 flex justify-between items-center">
            <div class="relative w-72">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                <input type="text" placeholder="Buscar actividad..." class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg pl-9 pr-4 py-2 focus:outline-none focus:border-blue-500">
            </div>
            <button class="text-slate-400 hover:text-white p-2"><i data-lucide="filter" class="w-4 h-4"></i></button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 text-slate-400 text-xs uppercase tracking-widest border-b border-slate-800">
                        <th class="p-4 font-semibold">Actividad</th>
                        <th class="p-4 font-semibold">Albergue Asignado</th>
                        <th class="p-4 font-semibold">Fecha y Hora</th>
                        <th class="p-4 font-semibold">Cupo</th>
                        <th class="p-4 font-semibold">Estado</th>
                        <th class="p-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-800/50">
                    
                    <!-- Fila 1 -->
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <td class="p-4">
                            <p class="font-bold text-slate-200">Limpieza Profunda</p>
                            <p class="text-xs text-slate-500">Mantenimiento de áreas comunes</p>
                        </td>
                        <td class="p-4 text-slate-300">
                            <div class="flex items-center">
                                <i data-lucide="home" class="w-3 h-3 mr-2 text-slate-500"></i>
                                Albergue Esperanza
                            </div>
                        </td>
                        <td class="p-4 text-slate-300">
                            <p>15 Oct, 2026</p>
                            <p class="text-xs text-slate-500">08:00 AM - 12:00 PM</p>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-slate-700 rounded-full h-1.5 w-16">
                                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: 80%"></div>
                                </div>
                                <span class="text-xs text-slate-400">16/20</span>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="bg-emerald-500/10 text-emerald-400 px-2.5 py-1 rounded-full text-[10px] font-bold border border-emerald-500/20">
                                PRÓXIMO
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <button class="text-slate-500 hover:text-blue-400 p-1 transition-colors" title="Editar">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button class="text-slate-500 hover:text-rose-400 p-1 transition-colors ml-1" title="Eliminar">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Fila 2 -->
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <td class="p-4">
                            <p class="font-bold text-slate-200">Paseo de Mascotas</p>
                            <p class="text-xs text-slate-500">Actividad recreativa al aire libre</p>
                        </td>
                        <td class="p-4 text-slate-300">
                            <div class="flex items-center">
                                <i data-lucide="home" class="w-3 h-3 mr-2 text-slate-500"></i>
                                Refugio Huellas
                            </div>
                        </td>
                        <td class="p-4 text-slate-300">
                            <p>18 Oct, 2026</p>
                            <p class="text-xs text-slate-500">04:00 PM - 06:00 PM</p>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-slate-700 rounded-full h-1.5 w-16">
                                    <div class="bg-amber-500 h-1.5 rounded-full" style="width: 100%"></div>
                                </div>
                                <span class="text-xs text-amber-400 font-bold">10/10</span>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="bg-amber-500/10 text-amber-400 px-2.5 py-1 rounded-full text-[10px] font-bold border border-amber-500/20">
                                LLENO
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <button class="text-slate-500 hover:text-blue-400 p-1 transition-colors" title="Editar">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button class="text-slate-500 hover:text-rose-400 p-1 transition-colors ml-1" title="Eliminar">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection