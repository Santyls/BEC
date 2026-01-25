@extends('layouts.app')

@section('title', 'Agregar Dirección')

@section('content')
<div class="relative min-h-screen w-full flex items-center justify-center py-24 px-4 bg-bec-dark overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-bec-dark via-[#152a45] to-black"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-20" style="background-image: radial-gradient(#3B82F6 1px, transparent 1px); background-size: 30px 30px;"></div>
    </div>

    <div class="relative z-10 w-full max-w-3xl bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl shadow-2xl overflow-hidden p-8 md:p-12 animate-slide-up">
        
        <div class="flex items-center gap-4 mb-8 border-b border-white/10 pb-4">
            <div class="w-12 h-12 bg-bec-primary/20 rounded-xl flex items-center justify-center text-white border border-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white font-display">Agregar Dirección</h2>
                <p class="text-blue-200 text-sm">Necesaria para la recolección de donaciones físicas.</p>
            </div>
        </div>

        <form action="{{ route('address.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- País -->
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-1">País *</label>
                    <select name="pais_id" class="w-full bg-bec-dark border border-white/10 rounded-xl px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition">
                        @foreach($paises as $pais)
                            <option value="{{ $pais->id }}">{{ $pais->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-1">Estado *</label>
                    <input type="text" name="estado" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition" placeholder="Ej. Querétaro">
                </div>

                <!-- Municipio -->
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-1">Municipio *</label>
                    <input type="text" name="municipio" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition" placeholder="Ej. Santiago de Querétaro">
                </div>

                <!-- Colonia -->
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-1">Colonia *</label>
                    <input type="text" name="colonia" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition" placeholder="Ej. Centro Histórico">
                </div>

                <!-- Código Postal -->
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-1">Código Postal *</label>
                    <input type="text" name="codigo_postal" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition" placeholder="76000">
                </div>

                <!-- Calle -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-blue-200 mb-1">Calle *</label>
                    <input type="text" name="calle" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition" placeholder="Ej. Av. Universidad">
                </div>

                <!-- Números -->
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-1">Número Exterior *</label>
                    <input type="text" name="num_exterior" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition" placeholder="#123">
                </div>
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-1">Número Interior (Opcional)</label>
                    <input type="text" name="num_interior" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-bec-light/50 transition" placeholder="Depto 4B">
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-white/10">
                <button type="submit" class="bg-bec-primary hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                    Guardar Dirección
                </button>
            </div>
        </form>
    </div>
</div>
@endsection