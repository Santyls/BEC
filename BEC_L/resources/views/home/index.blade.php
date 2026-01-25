@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="relative min-h-screen bg-gradient-to-br from-bec-dark to-gray-900 overflow-hidden text-white">

    <div class="absolute top-0 left-0 w-96 h-96 bg-bec-primary rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

    <main class="flex flex-col items-center justify-center min-h-screen text-center px-4 relative z-10">
        <h1 class="text-5xl md:text-7xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-200 to-white animate-fade-in">
            Conéctate. Dona. <br> Cambia una vida.
        </h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl mb-10 leading-relaxed font-light animate-slide-up">
            Una plataforma tecnológica para conectar corazones con necesidades reales.
            Gestión inteligente de donaciones y voluntariado.
        </p>
        
        <div class="flex gap-4 animate-slide-up">
            @auth
                <a href="{{ route('profile.edit') }}" class="px-8 py-3 bg-white/10 backdrop-blur-sm border border-white/30 rounded-xl font-semibold hover:bg-white/20 transition transform hover:scale-105">
                    Ir a mi Perfil
                </a>
            @else
                <a href="{{ route('login') }}" class="px-8 py-3 bg-white/10 backdrop-blur-sm border border-white/30 rounded-xl font-semibold hover:bg-white/20 transition transform hover:scale-105">
                    Comenzar ahora
                </a>
            @endauth
        </div>
    </main>


    <footer class="absolute bottom-0 w-full bg-black/20 backdrop-blur-sm border-t border-white/10 py-6 text-center text-sm text-gray-400">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center">
            <p>&copy; {{ date('Y') }} BEC: Blue Earth Coders. Todos los derechos reservados.</p>
            <div class="flex gap-4 mt-2 md:mt-0">
                <a href="#" class="hover:text-white">Privacidad</a>
                <a href="#" class="hover:text-white">Términos</a>
            </div>
        </div>
    </footer>

</div>
@endsection