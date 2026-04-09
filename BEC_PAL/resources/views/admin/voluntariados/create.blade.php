@extends('layouts.admin')

@section('title', 'Nuevo Voluntariado')
@section('header_title', 'Crear Voluntariado')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Cabecera -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.voluntariados.index') }}" class="w-10 h-10 rounded-full glass flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h3 class="text-xl font-bold text-white">Registrar Nuevo Voluntariado</h3>
                <p class="text-sm text-slate-400">Completa los datos de la actividad y asigna su albergue correspondiente.</p>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('admin.voluntariados.store') }}" method="POST" class="glass rounded-2xl p-8 border border-slate-800">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Nombre de la Actividad -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nombre de la Actividad *</label>
                <input type="text" name="nombre" placeholder="Ej. Limpieza general, Paseo de perros..." required
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors">
            </div>

            <!-- Relación: Albergue -->
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Albergue Asignado *</label>
                <div class="relative">
                    <i data-lucide="home" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                    <select name="albergue_id" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg pl-11 pr-4 py-3 focus:outline-none focus:border-blue-500 appearance-none">
                        <option value="" disabled selected>Selecciona un albergue...</option>
                        <option value="1">Albergue Esperanza</option>
                        <option value="2">Refugio Huellas</option>
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"></i>
                </div>
            </div>

            <!-- Relación: Campaña (Opcional) -->
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Campaña (Opcional)</label>
                <div class="relative">
                    <i data-lucide="megaphone" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                    <select name="campana_id" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg pl-11 pr-4 py-3 focus:outline-none focus:border-blue-500 appearance-none">
                        <option value="" selected>Ninguna (Actividad independiente)</option>
                        <option value="1">Campaña Invierno 2026</option>
                        <option value="2">Adopta un amigo</option>
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"></i>
                </div>
            </div>

            <!-- Fecha Programada -->
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Fecha Programada *</label>
                <input type="date" name="fecha" required
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 [color-scheme:dark]">
            </div>

            <!-- Cupo de Voluntarios -->
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Cupo Máximo *</label>
                <input type="number" name="cupo" min="1" placeholder="Ej. 15" required
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <!-- Horarios -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Hora Inicio</label>
                    <input type="time" name="hora_inicio" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 [color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Hora Fin</label>
                    <input type="time" name="hora_fin" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 [color-scheme:dark]">
                </div>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Estado</label>
                <select name="estado" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                    <option value="activo">Activo / Próximo</option>
                    <option value="cancelado">Cancelado</option>
                    <option value="finalizado">Finalizado</option>
                </select>
            </div>

            <!-- Descripción -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Descripción y Requisitos</label>
                <textarea name="descripcion" rows="4" placeholder="Detalla las actividades a realizar, vestimenta requerida o cualquier nota importante para los voluntarios..."
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 resize-none"></textarea>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-800 flex justify-end space-x-3">
            <a href="{{ route('admin.voluntariados.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-blue-500/20">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                Guardar Voluntariado
            </button>
        </div>
    </form>
</div>
@endsection