@extends('layouts.app')

@section('title', 'Admin | Voluntariados')

@section('content')
<div class="relative min-h-screen w-full bg-bec-dark overflow-hidden font-sans text-white">

    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0f172a] via-[#1e3a5f] to-black"></div>
        <div class="absolute top-[-10%] left-[20%] w-[600px] h-[600px] bg-purple-500/10 rounded-full blur-[100px] animate-blob"></div>
    </div>

    <nav class="fixed w-full z-50 top-0 transition-all duration-300 p-4">
        <div class="max-w-7xl mx-auto bg-white/5 backdrop-blur-xl border border-white/10 rounded-full px-6 py-3 flex flex-wrap justify-between items-center shadow-2xl">
            <div class="flex items-center gap-3">
                <div class="bg-bec-primary/80 p-1.5 rounded-lg shadow-lg">
                    <span class="text-lg font-bold text-white font-display tracking-wide">BEC</span>
                </div>
                <span class="text-xs font-bold text-blue-200 uppercase tracking-[0.2em] hidden sm:block">Admin Panel</span>
            </div>
            <div class="hidden lg:flex items-center gap-1 p-1 bg-black/20 rounded-full border border-white/5">
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-1.5 text-xs font-medium text-blue-200 hover:text-white hover:bg-white/5 rounded-full transition">Dashboard</a>
                <a href="#" class="px-4 py-1.5 text-xs font-medium text-blue-200 hover:text-white hover:bg-white/5 rounded-full transition">+ Usuario</a>
                <a href="{{ route('admin.donations') }}" class="px-4 py-1.5 text-xs font-medium text-blue-200 hover:text-white hover:bg-white/5 rounded-full transition">Donaciones</a>
                <a href="{{ route('admin.volunteering') }}" class="px-4 py-1.5 text-xs font-medium text-white bg-white/10 rounded-full shadow-sm border border-white/10 transition">Voluntariados</a>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-bec-light to-bec-primary p-[1px]"><div class="w-full h-full rounded-full bg-bec-dark flex items-center justify-center"><span class="text-xs font-bold">AD</span></div></div>
            </div>
        </div>
    </nav>

    <main class="relative z-10 flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-12">
        

        <div class="flex justify-between items-end mb-8 animate-fade-in">
            <div>
                <h1 class="text-3xl font-bold text-white font-display mb-1">Programas y Voluntarios</h1>
                <p class="text-blue-200 text-sm font-light">Gestión de eventos activos en Querétaro</p>
            </div>
            <button class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg transition transform hover:-translate-y-0.5 border border-white/10 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Crear Programa
            </button>
        </div>

        <div class="mb-12 animate-slide-up">
            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-purple-400"></span> Programas Activos
            </h2>
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs uppercase text-blue-300 font-bold tracking-wider bg-black/20">
                                <th class="p-5 border-b border-white/10">Organización</th>
                                <th class="p-5 border-b border-white/10">Programa</th>
                                <th class="p-5 border-b border-white/10 w-1/4">Ubicación (Qro)</th>
                                <th class="p-5 border-b border-white/10">Fecha / Horario</th>
                                <th class="p-5 border-b border-white/10 text-center">Cupo</th>
                                <th class="p-5 border-b border-white/10 text-center">Inscritos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm text-blue-100">
                            @foreach($programas as $prog)
                            <tr class="hover:bg-white/5 transition duration-150">
                                <td class="p-5 text-white font-medium">{{ $prog->org }}</td>
                                <td class="p-5">
                                    <p class="font-bold text-white">{{ $prog->nombre }}</p>
                                    <p class="text-xs text-blue-300/70 truncate max-w-[200px]">{{ $prog->descripcion }}</p>
                                </td>
                                <td class="p-5 text-xs">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                        {{ $prog->ubicacion }}
                                    </div>
                                </td>
                                <td class="p-5">
                                    <p class="text-white">{{ $prog->fecha }}</p>
                                    <p class="text-xs text-blue-300/70">{{ $prog->horario }}</p>
                                </td>
                                <td class="p-5 text-center font-mono">{{ $prog->max_part }}</td>
                                <td class="p-5 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold 
                                        {{ $prog->inscritos >= $prog->max_part ? 'bg-red-500/20 text-red-300' : 'bg-green-500/20 text-green-300' }}">
                                        {{ $prog->inscritos }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="animate-slide-up animation-delay-500">
            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-orange-400"></span> Personas Inscritas
            </h2>
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs uppercase text-blue-300 font-bold tracking-wider bg-black/20">
                                <th class="p-5 border-b border-white/10">ID</th>
                                <th class="p-5 border-b border-white/10">Voluntario</th>
                                <th class="p-5 border-b border-white/10">Programa Asignado</th>
                                <th class="p-5 border-b border-white/10">Rol</th>
                                <th class="p-5 border-b border-white/10 text-right">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm text-blue-100">
                            @foreach($voluntarios as $vol)
                            <tr class="hover:bg-white/5 transition duration-150">
                                <td class="p-5 font-mono text-white/50 text-xs">#{{ $vol->id }}</td>
                                <td class="p-5 font-bold text-white">{{ $vol->nombre }}</td>
                                <td class="p-5 text-blue-200">{{ $vol->programa }}</td>
                                <td class="p-5">
                                    <span class="px-2 py-1 rounded border border-white/10 bg-white/5 text-xs">{{ $vol->rol }}</span>
                                </td>
                                <td class="p-5 text-right">
                                    @if($vol->estado == 'Confirmado')
                                        <span class="text-green-400 text-xs font-bold">● Confirmado</span>
                                    @elseif($vol->estado == 'Asistió')
                                        <span class="text-blue-400 text-xs font-bold">● Asistió</span>
                                    @else
                                        <span class="text-yellow-400 text-xs font-bold">● Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div>
@endsection