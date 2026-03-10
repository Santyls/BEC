@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')
@section('header_title', 'Usuarios')

@section('content')
    <div class="space-y-6 fade-in">
        
        <!-- Barra de acciones superior -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="relative w-full sm:w-96">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500"></i>
                <input type="text" placeholder="Buscar por nombre, correo o rol..." 
                       class="w-full bg-slate-900/50 border border-slate-700 text-slate-200 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
            </div>
            
            <button class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-medium flex items-center transition-all shadow-lg shadow-blue-500/20">
                <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                Nuevo Usuario
            </button>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-800/50 border-b border-slate-700/50 text-slate-400 text-sm">
                            <th class="px-6 py-4 font-medium">Nombre</th>
                            <th class="px-6 py-4 font-medium">Correo Electrónico</th>
                            <th class="px-6 py-4 font-medium">Rol</th>
                            <th class="px-6 py-4 font-medium">Estado</th>
                            <th class="px-6 py-4 font-medium text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        <!-- Fila de ejemplo 1 -->
                        <tr class="hover:bg-slate-800/20 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold mr-3">
                                        C
                                    </div>
                                    <span class="font-medium text-slate-200">Carlos Mendoza</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-400">carlos.m@ejemplo.com</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                    Donador
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center text-emerald-400 text-sm">
                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-2"></div>
                                    Activo
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button class="p-2 text-slate-400 hover:text-blue-400 hover:bg-blue-400/10 rounded-lg transition-colors" title="Editar">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    <button class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-400/10 rounded-lg transition-colors" title="Eliminar">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Fila de ejemplo 2 -->
                        <tr class="hover:bg-slate-800/20 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold mr-3">
                                        L
                                    </div>
                                    <span class="font-medium text-slate-200">Laura Gómez</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-400">laura.g@ejemplo.com</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                    Voluntario
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center text-slate-500 text-sm">
                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-500 mr-2"></div>
                                    Inactivo
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button class="p-2 text-slate-400 hover:text-blue-400 hover:bg-blue-400/10 rounded-lg transition-colors" title="Editar">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    <button class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-400/10 rounded-lg transition-colors" title="Eliminar">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación Mockup -->
            <div class="px-6 py-4 border-t border-slate-800 flex items-center justify-between text-sm text-slate-400">
                <span>Mostrando 1 a 2 de 24 resultados</span>
                <div class="flex gap-1">
                    <button class="p-1 rounded hover:bg-slate-800 text-slate-500"><i data-lucide="chevron-left" class="w-5 h-5"></i></button>
                    <button class="px-3 py-1 rounded bg-blue-600/20 text-blue-400 font-medium">1</button>
                    <button class="px-3 py-1 rounded hover:bg-slate-800">2</button>
                    <button class="px-3 py-1 rounded hover:bg-slate-800">3</button>
                    <button class="p-1 rounded hover:bg-slate-800"><i data-lucide="chevron-right" class="w-5 h-5"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .fade-in {
        animation: fadeIn 0.4s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush