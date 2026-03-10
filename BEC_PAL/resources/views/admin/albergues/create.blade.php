@extends('layouts.admin')
@section('title', 'Nuevo Albergue')
@section('header_title', 'Crear Albergue')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.albergues.index') }}" class="w-10 h-10 rounded-full glass flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h3 class="text-xl font-bold text-white">Registrar Nuevo Albergue</h3>
            <p class="text-sm text-slate-400">Ingresa los datos generales y la dirección del nuevo albergue.</p>
        </div>
    </div>

    <form action="#" method="POST" class="glass rounded-2xl p-8 border border-slate-800 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Datos Generales -->
            <div class="md:col-span-2 border-b border-slate-800 pb-2 mb-2">
                <h4 class="text-blue-400 font-bold uppercase tracking-widest text-xs">Datos Generales</h4>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nombre del Albergue *</label>
                <input type="text" name="nombre" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Capacidad Máxima</label>
                <input type="number" name="capacidad" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Teléfono de Contacto *</label>
                <input type="text" name="telefono" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <!-- Dirección (Relaciones según el Diccionario) -->
            <div class="md:col-span-2 border-b border-slate-800 pb-2 mb-2 mt-4">
                <h4 class="text-blue-400 font-bold uppercase tracking-widest text-xs">Dirección</h4>
            </div>

            <div class="md:col-span-2 grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Calle *</label>
                    <input type="text" name="calle" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Número Ext.</label>
                    <input type="text" name="numero" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Código Postal *</label>
                <input type="text" name="codigo_postal" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Colonia *</label>
                <!-- Aquí idealmente iría un select dinámico cargado por el CP -->
                <select name="colonia_id" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>Seleccione colonia...</option>
                    <option value="1">Centro</option>
                    <option value="2">Juriquilla</option>
                </select>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-800 flex justify-end space-x-3">
            <a href="{{ route('admin.albergues.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-300 hover:text-white transition-colors">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg">
                Guardar Albergue
            </button>
        </div>
    </form>
</div>
@endsection