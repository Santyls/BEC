@extends('layouts.admin')

@section('title', 'Gestión de Albergues')
@section('header_title', 'Albergues en Operación (API)')

@section('content')
    <div class="space-y-6 flex flex-col items-center">
        <div class="w-full max-w-5xl flex justify-between items-center bg-slate-800/40 p-6 rounded-2xl border border-slate-700/50 shadow-xl">
            <div>
                <h3 class="text-xl font-bold text-white">Directorio de Albergues</h3>
                <p class="text-sm text-slate-400 mt-1">Lista obtenida en tiempo real desde la API BEC</p>
            </div>
            <a href="{{ route('admin.albergues.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-medium transition-colors shadow-lg shadow-emerald-500/20 flex items-center">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Nuevo Albergue
            </a>
        </div>

        @if(isset($error))
            <div class="w-full max-w-5xl bg-red-500/10 border border-red-500/50 text-red-500 px-6 py-4 rounded-xl text-sm font-medium">
                <i data-lucide="alert-triangle" class="w-5 h-5 inline mr-2 -mt-1"></i> {{ $error }}
            </div>
        @endif

        <div class="w-full max-w-5xl bg-slate-800/40 border border-slate-700/50 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-900/60 text-slate-400 text-sm uppercase tracking-wider border-b border-slate-700">
                            <th class="py-4 px-6 font-semibold">ID</th>
                            <th class="py-4 px-6 font-semibold">Nombre del Albergue</th>
                            <th class="py-4 px-6 font-semibold">Capacidad Max.</th>
                            <th class="py-4 px-6 font-semibold">Teléfono</th>
                            <th class="py-4 px-6 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50 text-slate-300">
                        @forelse($albergues as $albergue)
                            <tr class="hover:bg-slate-700/30 transition-colors">
                                <td class="py-4 px-6 font-medium text-slate-500">#{{ $albergue['Id_Albergue'] }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex justify-center items-center mr-3">
                                            <i data-lucide="home" class="w-4 h-4"></i>
                                        </div>
                                        <span class="font-semibold text-white">{{ $albergue['Nombre_Albergue'] }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                        {{ $albergue['Capacidad_Max'] }} personas
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm">{{ $albergue['Tel_Contacto'] }}</td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <button class="p-2 text-slate-400 hover:text-blue-400 hover:bg-blue-400/10 rounded-lg transition-colors" title="Editar">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    <button class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-400/10 rounded-lg transition-colors" title="Eliminar">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 px-6 text-center text-slate-500 italic">
                                    @if(isset($error))
                                        Error de conexión. No hay datos para mostrar.
                                    @else
                                        No se encontraron albergues registrados.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection