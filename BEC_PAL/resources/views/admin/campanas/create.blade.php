@extends('layouts.admin')
@section('title', 'Nueva Campaña')
@section('header_title', 'Crear Campaña')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.campanas.index') }}" class="w-10 h-10 rounded-full glass flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h3 class="text-xl font-bold text-white">Registrar Nueva Campaña</h3>
            <p class="text-sm text-slate-400">Define el periodo y los objetivos de esta iniciativa.</p>
        </div>
    </div>

    <form action="{{ route('admin.campanas.store') }}" method="POST" class="glass rounded-2xl p-8 border border-slate-800 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nombre de la Campaña *</label>
                <input type="text" name="nombre" required placeholder="Ej. Invierno Cálido 2026" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Fecha de Inicio *</label>
                <input type="date" name="fecha_inicio" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 [color-scheme:dark]">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Fecha de Finalización *</label>
                <input type="date" name="fecha_fin" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 [color-scheme:dark]">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Estado</label>
                <select name="estado" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                    <option value="1">Programada (Futura)</option>
                    <option value="2">Activa</option>
                    <option value="3">Finalizada</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Descripción y Objetivos *</label>
                <textarea name="descripcion" rows="4" required placeholder="Describe cuál es la meta principal de esta campaña..." class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 resize-none"></textarea>
            </div>

        </div>

        <div class="mt-8 pt-6 border-t border-slate-800 flex justify-end space-x-3">
            <a href="{{ route('admin.campanas.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-300 hover:text-white transition-colors">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i> Crear Campaña
            </button>
        </div>
    </form>
</div>
@endsection