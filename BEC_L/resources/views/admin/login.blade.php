@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="relative min-h-screen w-full flex flex-col items-center justify-center overflow-hidden bg-gray-900">

    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-b from-[#0f172a] via-[#1e3a5f] to-[#0f172a]"></div>

        <div class="absolute inset-0 opacity-20" 
             style="background-image: radial-gradient(#60A5FA 1px, transparent 1px); background-size: 40px 40px;"></div>
        
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-bec-primary/20 rounded-full blur-3xl animate-pulse"></div>
    </div>

    <div class="relative z-10 flex flex-col items-center w-full max-w-md px-4 animate-fade-in">
        
        <div class="mb-8 text-center">
            <h1 class="text-5xl font-display font-bold text-white tracking-widest drop-shadow-lg mb-2">BEC</h1>
            <p class="text-bec-light uppercase tracking-[0.2em] text-sm font-semibold">Panel de Administración</p>
        </div>

        <div class="w-full bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl p-8 md:p-10 relative overflow-hidden group">
            
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-bec-light to-transparent opacity-50"></div>

            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-bec-dark/50 border border-white/10 mb-4 shadow-inner">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h2 class="text-2xl text-white font-bold">Acceso Restringido</h2>
                <p class="text-blue-200/70 text-sm mt-2">Ingresa tus credenciales de administrador</p>
            </div>

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-2 text-left">
                    <label class="text-xs uppercase text-blue-300 font-bold tracking-wider ml-1">Correo Institucional</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-blue-300 group-focus-within:text-white transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                        </div>
                        <input type="email" name="email" 
                            class="w-full bg-black/20 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-white placeholder-white/20 focus:outline-none focus:bg-black/30 focus:border-bec-light/50 transition shadow-inner"
                            placeholder="admin@bec.org">
                    </div>
                </div>

                <div class="space-y-2 text-left">
                    <label class="text-xs uppercase text-blue-300 font-bold tracking-wider ml-1">Contraseña</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-blue-300 group-focus-within:text-white transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <input type="password" name="password" 
                            class="w-full bg-black/20 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-white placeholder-white/20 focus:outline-none focus:bg-black/30 focus:border-bec-light/50 transition shadow-inner"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-bec-primary to-blue-600 hover:from-blue-500 hover:to-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/20 transition transform hover:-translate-y-0.5 active:translate-y-0">
                    Entrar al Panel
                </button>
            </form>
        </div>
        
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="text-sm text-white/40 hover:text-white transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver al sitio público
            </a>
        </div>
    </div>
</div>
@endsection