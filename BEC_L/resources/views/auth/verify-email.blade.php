@extends('layouts.app')

@section('title', 'Verificar Correo')

@section('content')
<div class="relative min-h-screen w-full overflow-hidden flex items-center justify-center bg-bec-dark">

    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-bec-dark to-gray-900"></div>

        <div class="absolute top-20 left-20 w-72 h-72 bg-bec-primary/20 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute bottom-20 right-20 w-72 h-72 bg-purple-600/20 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
    </div>

    <div class="relative z-10 w-full max-w-md p-8 mx-4 text-center bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl shadow-2xl animate-fade-in">
        
        <div class="mx-auto mb-6 flex items-center justify-center w-16 h-16 rounded-full bg-bec-primary/20 border border-bec-primary/50 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>

        <h2 class="text-2xl font-display font-bold text-white mb-4">Verifica tu correo electrónico</h2>
        
        <p class="text-blue-100 mb-8 leading-relaxed">
            ¡Casi listo! Te hemos enviado un enlace de verificación a: <br>
            <span class="font-semibold text-white">{{ Auth::user()->email }}</span>. <br>
            Por favor revisa tu bandeja de entrada para activar todas las funciones.
        </p>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-500/20 border border-green-500/50 text-green-200 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-4">

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full py-3 px-6 rounded-xl bg-bec-primary hover:bg-blue-600 text-white font-semibold shadow-lg transition transform hover:-translate-y-0.5">
                    Reenviar correo de verificación
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-blue-300 hover:text-white underline transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>

</div>
@endsection