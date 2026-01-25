@extends('layouts.app')

@section('title', 'Panel Administrativo')

@section('content')
<div class="relative min-h-screen w-full bg-bec-dark overflow-hidden font-sans text-white">

    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0f172a] via-[#1e3a5f] to-black"></div>
        <div class="absolute top-[-10%] left-[-10%] w-[600px] h-[600px] bg-bec-primary/20 rounded-full blur-[100px] animate-blob"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-purple-600/20 rounded-full blur-[100px] animate-blob animation-delay-2000"></div>
        <div class="absolute inset-0 opacity-10" 
             style="background-image: radial-gradient(#60A5FA 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    <nav class="fixed w-full z-50 top-0 transition-all duration-300 p-4">
        <div class="max-w-7xl mx-auto bg-white/5 backdrop-blur-xl border border-white/10 rounded-full px-6 py-3 flex flex-wrap justify-between items-center shadow-2xl">
            
            <div class="flex items-center gap-3">
                <div class="bg-bec-primary/80 p-1.5 rounded-lg shadow-lg shadow-bec-primary/20">
                    <span class="text-lg font-bold text-white font-display tracking-wide">BEC</span>
                </div>
                <span class="text-xs font-bold text-blue-200 uppercase tracking-[0.2em] hidden sm:block">Admin Panel</span>
            </div>
            

            <div class="hidden lg:flex items-center gap-1 p-1 bg-black/20 rounded-full border border-white/5">
                <a href="#" class="px-4 py-1.5 text-xs font-medium text-white bg-white/10 rounded-full shadow-sm border border-white/10 transition hover:bg-white/20">
                    Dashboard
                </a>
                <a href="#" class="px-4 py-1.5 text-xs font-medium text-blue-200 hover:text-white hover:bg-white/5 rounded-full transition">
                    + Usuario
                </a>
                <a href="#" class="px-4 py-1.5 text-xs font-medium text-blue-200 hover:text-white hover:bg-white/5 rounded-full transition">
                    Donaciones
                </a>
                <a href="#" class="px-4 py-1.5 text-xs font-medium text-blue-200 hover:text-white hover:bg-white/5 rounded-full transition">
                    Voluntariados
                </a>
                <a href="#" class="px-4 py-1.5 text-xs font-medium text-blue-200 hover:text-white hover:bg-white/5 rounded-full transition">
                    Programas
                </a>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-bec-light to-bec-primary p-[1px]">
                        <div class="w-full h-full rounded-full bg-bec-dark flex items-center justify-center">
                            <span class="text-xs font-bold">AD</span>
                        </div>
                    </div>
                </div>
                <div class="h-6 w-[1px] bg-white/10"></div>
                <form action="{{ route('home') }}" method="GET">
                    <button class="text-xs font-bold text-red-300 hover:text-red-100 transition flex items-center gap-1">
                        <span>Salir</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="relative z-10 flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-12">
        
        <div class="mb-10 flex justify-between items-end animate-fade-in">
            <div>
                <h1 class="text-4xl font-bold text-white font-display mb-2 drop-shadow-lg">Vista General</h1>
                <p class="text-blue-200 text-sm font-light">Resumen de actividad del sistema</p>
            </div>
          
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 animate-slide-up">
            
            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition duration-300 group relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/20 rounded-full blur-2xl group-hover:bg-blue-500/30 transition"></div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-blue-500/20 rounded-lg text-blue-300 border border-blue-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-green-400 bg-green-400/10 px-2 py-1 rounded-full border border-green-400/20">+12%</span>
                </div>
                <p class="text-3xl font-bold text-white font-display mb-1">{{ $stats['usuarios'] }}</p>
                <p class="text-xs text-blue-200 uppercase tracking-wider font-semibold">Usuarios Registrados</p>
            </div>

            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition duration-300 group relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/20 rounded-full blur-2xl group-hover:bg-purple-500/30 transition"></div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-purple-500/20 rounded-lg text-purple-300 border border-purple-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-white/40 bg-white/5 px-2 py-1 rounded-full">Activos</span>
                </div>
                <p class="text-3xl font-bold text-white font-display mb-1">{{ $stats['programas'] }}</p>
                <p class="text-xs text-blue-200 uppercase tracking-wider font-semibold">Programas</p>
            </div>

            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition duration-300 group relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-500/20 rounded-full blur-2xl group-hover:bg-green-500/30 transition"></div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-green-500/20 rounded-lg text-green-300 border border-green-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-green-400 bg-green-400/10 px-2 py-1 rounded-full border border-green-400/20">+5 hoy</span>
                </div>
                <p class="text-3xl font-bold text-white font-display mb-1">{{ $stats['donaciones'] }}</p>
                <p class="text-xs text-blue-200 uppercase tracking-wider font-semibold">Donaciones</p>
            </div>


            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition duration-300 group relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-500/20 rounded-full blur-2xl group-hover:bg-orange-500/30 transition"></div>
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-orange-500/20 rounded-lg text-orange-300 border border-orange-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-white font-display mb-1">{{ $stats['voluntariados'] }}</p>
                <p class="text-xs text-blue-200 uppercase tracking-wider font-semibold">Voluntariados</p>
            </div>
        </div>

        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden shadow-2xl animate-fade-in animation-delay-500">
            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                <div>
                    <h2 class="text-lg font-bold text-white">Usuarios Registrados</h2>
                    <p class="text-xs text-blue-200">Actividad reciente</p>
                </div>
                <div class="flex gap-2">
                    <input type="text" placeholder="Buscar..." class="bg-black/20 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-bec-light/50 transition">
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs uppercase text-blue-300 font-bold tracking-wider bg-black/20">
                            <th class="p-4 border-b border-white/10">ID</th>
                            <th class="p-4 border-b border-white/10">Usuario</th>
                            <th class="p-4 border-b border-white/10">Email</th>
                            <th class="p-4 border-b border-white/10">Estado</th>
                            <th class="p-4 border-b border-white/10 text-center">Donaciones</th>
                            <th class="p-4 border-b border-white/10 text-center">Voluntariados</th>
                            <th class="p-4 border-b border-white/10 text-right">Fecha Reg.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-sm text-blue-100">
                        @foreach($usuarios as $user)
                        <tr class="hover:bg-white/5 transition duration-150">
                            <td class="p-4 font-mono text-white/50 text-xs">#{{ $user->id }}</td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-bec-primary to-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-lg border border-white/20">
                                        {{ substr($user->alias, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-white">{{ $user->alias }}</span>
                                </div>
                            </td>
                            <td class="p-4 text-white/70">{{ $user->email }}</td>
                            <td class="p-4">
                                @if($user->verificado)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Verificado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <span class="font-bold text-white">{{ $user->donaciones }}</span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="font-bold text-white">{{ $user->voluntariados }}</span>
                            </td>
                            <td class="p-4 text-right text-white/60 font-mono text-xs">{{ $user->fecha }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-white/10 bg-white/5 flex justify-center">
                <button class="text-xs text-blue-300 hover:text-white transition flex items-center gap-1">
                    Ver todos los usuarios
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
        </div>

    </main>
</div>
@endsection