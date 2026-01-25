@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="relative min-h-screen w-full overflow-hidden flex">

    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-bec-dark/60 z-10"></div>
        <div class="absolute inset-0" id="bg-slider">
            <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=1920&auto=format&fit=crop" class="w-full h-full object-cover absolute top-0 left-0 opacity-100" alt="Voluntariado 1">
        </div>
    </div>

    <div class="relative z-20 w-full flex flex-col md:flex-row">
        
        <div class="w-full md:w-[60%] p-8 md:p-16 flex flex-col justify-between min-h-[300px] md:min-h-screen animate-slide-in-left delay-100">
            <div>
                <h1 class="text-4xl font-display font-bold text-white tracking-wider drop-shadow-lg">
                    BEC <span class="text-bec-light text-2xl font-normal block sm:inline">| Blue Earth Coders</span>
                </h1>
            </div>
            <div class="mt-auto">
                <blockquote class="border-l-4 border-bec-primary pl-4 py-2">
                    <p class="text-xl md:text-3xl text-white font-light leading-snug">
                        "La tecnología es solo una herramienta. La gente usa las herramientas para mejorar la vida de los demás."
                    </p>
                </blockquote>
            </div>
        </div>

        <div class="w-full md:w-[40%] flex items-center justify-center p-6 md:p-12 animate-slide-in-right">
            
            <div class="w-full max-w-md bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl shadow-2xl p-8 transform transition duration-500 hover:scale-[1.01] relative">
                <div class="relative z-10">
                    <h2 class="text-3xl font-bold text-white mb-2 text-center font-display">Bienvenido</h2>
                    <p class="text-blue-200 text-center mb-8 text-sm">Ingresa tus credenciales</p>

                    <form action="{{ route('login.process') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-blue-100 ml-1">Correo</label>
                            <input type="email" name="email" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-bec-primary/50 transition backdrop-blur-sm" placeholder="ejemplo@bec.org" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-blue-100 ml-1">Contraseña</label>
                            <input type="password" name="password" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-bec-primary/50 transition backdrop-blur-sm" placeholder="••••••••" required>
                        </div>

                        <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-bec-primary/80 to-blue-600/80 hover:from-bec-primary hover:to-blue-600 text-white font-bold tracking-wide shadow-lg border border-white/10 transition-all duration-300 transform hover:-translate-y-0.5 backdrop-blur-md">
                            Iniciar Sesión
                        </button>
                    </form>

                    <div class="mt-8 text-center">
                        <p class="text-sm text-blue-200">
                            ¿Aún no tienes cuenta? 
                            <a href="{{ route('register') }}" class="font-bold text-white hover:underline transition-colors">Regístrate aquí</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-slide-in-left { animation: slideInLeft 0.7s cubic-bezier(0.25, 1, 0.5, 1) forwards; opacity: 0; }
    .animate-slide-in-right { animation: slideInRight 0.7s cubic-bezier(0.25, 1, 0.5, 1) forwards; opacity: 0; }
    .delay-100 { animation-delay: 0.1s; }

    @keyframes slideInLeft {
        from { transform: translateX(-40px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideInRight {
        from { transform: translateX(40px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
@endsection