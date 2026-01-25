@extends('layouts.app')

@section('title', 'Completar Perfil')

@section('content')
<div class="relative min-h-screen w-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-bec-dark overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-bec-dark via-[#152a45] to-black"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-20" style="background-image: radial-gradient(#3B82F6 1px, transparent 1px); background-size: 30px 30px;"></div>
    </div>

    <div class="relative z-10 w-full max-w-4xl bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row animate-fade-in">
        

        <div class="w-full md:w-1/3 bg-bec-primary/20 p-8 text-white flex flex-col justify-center items-center text-center border-b md:border-b-0 md:border-r border-white/10">
            <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center mb-4 shadow-inner border border-white/30">

                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <h2 class="text-2xl font-bold font-display mb-2">Tu Perfil</h2>
            <p class="text-blue-100 text-sm leading-relaxed">
                Completa tu información para una mejor experiencia.
            </p>
            

            <div class="mt-6 w-full bg-black/20 rounded-full h-2 overflow-hidden">
                <div class="bg-bec-light h-2 rounded-full transition-all duration-1000 ease-out" style="width: {{ $user->profile_completeness }}%"></div>
            </div>
            <p class="text-xs text-blue-200 mt-2">Completado al {{ $user->profile_completeness }}%</p>
        </div>

        <div class="w-full md:w-2/3 p-8 md:p-12">
            <h3 class="text-xl font-bold text-white mb-6 border-b border-white/10 pb-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-bec-light" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .667.455 1 1 1h2c.545 0 1-.333 1-1m-4 0h2m-2 0v1m0-1h-2m2 0h2"></path></svg>
                Información Personal
            </h3>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <div class="col-span-2">
                        <label class="block text-sm font-medium text-blue-200 mb-1">Nombre Completo *</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $user->nombre) }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1">Alias (Usuario) *</label>
                        <input type="text" name="alias" value="{{ old('alias', $user->alias) }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1">Edad *</label>
                        <input type="number" name="edad" value="{{ old('edad', $user->edad) }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1">Género *</label>
                        <select name="genero_id" class="w-full bg-bec-dark border border-white/10 rounded-xl px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition">
                            @foreach($generos as $genero)
                                <option value="{{ $genero->id }}" {{ old('genero_id', $user->genero_id) == $genero->id ? 'selected' : '' }}>{{ $genero->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1">Nacionalidad *</label>
                        <select name="nacionalidad_id" class="w-full bg-bec-dark border border-white/10 rounded-xl px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition">
                            @foreach($nacionalidades as $nac)
                                <option value="{{ $nac->id }}" {{ old('nacionalidad_id', $user->nacionalidad_id) == $nac->id ? 'selected' : '' }}>{{ $nac->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1">País *</label>
                        <select name="pais_id" class="w-full bg-bec-dark border border-white/10 rounded-xl px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition">
                            @foreach($paises as $pais)
                                <option value="{{ $pais->id }}" {{ old('pais_id', $user->pais_id) == $pais->id ? 'selected' : '' }}>{{ $pais->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1">Teléfono</label>
                        <input type="tel" name="telefono" value="{{ old('telefono', $user->telefono) }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-white/10 mt-6">
                    <button type="submit" class="flex-1 bg-bec-primary hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 text-center">
                        Guardar y continuar
                    </button>
                    <a href="{{ route('home') }}" class="flex-1 bg-white/5 hover:bg-white/10 border border-white/20 text-blue-100 font-semibold py-3 px-6 rounded-xl transition text-center">
                        Omitir por ahora
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection