@extends('layouts.app')

@section('title', 'Crear Cuenta')

@section('content')
<div class="relative min-h-screen w-full overflow-hidden flex">

    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-bec-dark/60 z-10"></div>
        <img src="https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?q=80&w=1920&auto=format&fit=crop" class="w-full h-full object-cover absolute top-0 left-0 opacity-100" alt="Fondo Registro">
    </div>

    <div class="relative z-20 w-full flex flex-col md:flex-row-reverse">
        
        <div class="w-full md:w-[60%] p-8 md:p-16 flex flex-col justify-between min-h-[200px] md:min-h-screen text-right animate-slide-in-right delay-100">
            <div>
                <h1 class="text-4xl font-display font-bold text-white tracking-wider drop-shadow-lg">
                    Únete a <span class="text-bec-light">BEC</span>
                </h1>
                <p class="text-blue-200 mt-2 text-lg">Tu primer paso para cambiar el mundo.</p>
            </div>
            <div class="mt-auto hidden md:block">
                <blockquote class="border-r-4 border-bec-light pr-4 py-2">
                    <p class="text-2xl text-white font-light leading-snug italic">
                        "Mucha gente pequeña, en lugares pequeños, haciendo cosas pequeñas, puede cambiar el mundo."
                    </p>
                </blockquote>
            </div>
        </div>

        <div class="w-full md:w-[40%] flex items-center justify-center p-6 md:p-12 animate-slide-in-left">
            
            <div class="w-full max-w-md bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl shadow-2xl p-8 transform transition duration-500 hover:scale-[1.01] relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-bold text-white mb-6 text-center font-display">Crear Cuenta</h2>


                    <form action="{{ route('register.process') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-blue-100 ml-1">Alias</label>
                            <input type="text" name="alias" value="{{ old('alias') }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-bec-light/50 focus:border-transparent transition backdrop-blur-sm" placeholder="Tu apodo público" required>
                            @error('alias') <span class="text-red-300 text-xs pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-blue-100 ml-1">Correo</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-bec-light/50 focus:border-transparent transition backdrop-blur-sm" placeholder="correo@ejemplo.com" required>
                            @error('email') <span class="text-red-300 text-xs pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-blue-100 ml-1">Contraseña</label>
                                <input type="password" name="password" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition backdrop-blur-sm" required>
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-blue-100 ml-1">Confirmar</label>
                                <input type="password" name="password_confirmation" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition backdrop-blur-sm" required>
                            </div>
                        </div>
                        @error('password') <span class="text-red-300 text-xs pl-1">{{ $message }}</span> @enderror

                        <div class="flex items-start mt-2">
                            <div class="flex items-center h-5">
                                <input id="terms" name="terms" type="checkbox" required class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300">
                            </div>
                            <label for="terms" class="ml-2 text-sm font-medium text-blue-200">
                                Acepto los <a href="#" class="text-white hover:underline">Términos y Condiciones</a>.
                            </label>
                        </div>
                        @error('terms') <span class="text-red-300 text-xs pl-1">{{ $message }}</span> @enderror

                        <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-bec-primary/80 to-blue-600/80 hover:from-bec-primary hover:to-blue-600 text-white font-bold tracking-wide shadow-lg border border-white/10 transition-all duration-300 transform hover:-translate-y-0.5 backdrop-blur-md">
                            Registrarse
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-blue-200">
                            ¿Ya tienes cuenta? 
                            <a href="{{ route('login') }}" class="font-bold text-white hover:underline transition-colors">Inicia Sesión aquí</a>
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