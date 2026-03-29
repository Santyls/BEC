@extends('layouts.admin')

@section('title', 'Gestión de Campañas')
@section('header_title', 'Campañas Activas e Históricas (API)')

@section('content')
    <div class="space-y-6 flex flex-col items-center">
        <div class="w-full max-w-5xl flex justify-between items-center bg-slate-800/40 p-6 rounded-2xl border border-slate-700/50 shadow-xl">
            <div>
                <h3 class="text-xl font-bold text-white">Registro de Campañas</h3>
                <p class="text-sm text-slate-400 mt-1">Lista obtenida en tiempo real desde la API BEC</p>
            </div>
            <a href="{{ route('admin.campanas.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-medium transition-colors shadow-lg shadow-indigo-500/20 flex items-center">
                <i data-lucide="megaphone" class="w-4 h-4 mr-2"></i> Nueva Campaña
            </a>
        </div>

        @if(isset($error))
            <div class="w-full max-w-5xl bg-red-500/10 border border-red-500/50 text-red-500 px-6 py-4 rounded-xl text-sm font-medium">
                <i data-lucide="alert-triangle" class="w-5 h-5 inline mr-2 -mt-1"></i> {{ $error }}
            </div>
        @endif

        <div class="w-full max-w-5xl bg-slate-800/40 border border-slate-700/50 rounded-2xl overflow-hidden shadow-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @forelse($campanas as $campana)
                    <div class="bg-slate-900/50 rounded-xl border border-slate-700 p-5 hover:border-blue-500/50 transition-colors group relative">
                        <!-- Etiquetas de Estado -->
                        <div class="absolute top-4 right-4">
                            @if($campana['id_Estado_campana'] == 2)
                                <span class="bg-emerald-500/20 text-emerald-400 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border border-emerald-500/30 shadow-sm shadow-emerald-500/20 flex items-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span> Activa
                                </span>
                            @elseif($campana['id_Estado_campana'] == 1)
                                <span class="bg-blue-500/20 text-blue-400 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border border-blue-500/30">
                                    Programada
                                </span>
                            @else
                                <span class="bg-slate-700/50 text-slate-400 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border border-slate-600">
                                    Finalizada
                                </span>
                            @endif
                        </div>

                        <div class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400 mb-4">
                            <i data-lucide="flag" class="w-5 h-5"></i>
                        </div>
                        
                        <h4 class="text-lg font-bold text-white leading-tight mb-2 pr-16">{{ $campana['Nombre_Campana'] }}</h4>
                        
                        <p class="text-sm text-slate-400 mb-4 line-clamp-2" title="{{ $campana['Descripcion_Objetivos'] }}">
                            {{ $campana['Descripcion_Objetivos'] }}
                        </p>

                        <div class="flex items-center text-xs text-slate-500 font-medium mb-4 bg-slate-800/50 p-2 rounded-lg">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 mr-2 text-slate-400"></i>
                            <span>{{ \Carbon\Carbon::parse($campana['Fecha_Inicio'])->format('d M y') }}</span>
                            <i data-lucide="arrow-right" class="w-3 h-3 mx-2"></i>
                            <span>{{ \Carbon\Carbon::parse($campana['Fecha_Fin'])->format('d M y') }}</span>
                        </div>

                        <div class="flex space-x-2 border-t border-slate-800 pt-4 mt-2">
                            <button class="flex-1 bg-slate-800 hover:bg-blue-600 hover:text-white text-slate-300 py-2 rounded-lg text-sm transition-colors text-center font-medium">
                                Ver Detalles
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center">
                        <i data-lucide="inbox" class="w-12 h-12 text-slate-600 mx-auto mb-3"></i>
                        <p class="text-slate-500">
                            @if(isset($error)) Error de conexión. @else No hay campañas registradas. @endif
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection