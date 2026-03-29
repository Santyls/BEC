@extends('layouts.admin')
@section('title', 'Nueva Donación')
@section('header_title', 'Registrar Donación')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.donaciones.index') }}" class="w-10 h-10 rounded-full glass flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h3 class="text-xl font-bold text-white">Ingreso de Donación Manual</h3>
            <p class="text-sm text-slate-400">Registra un donativo físico recibido en las instalaciones.</p>
        </div>
    </div>

    <form action="{{ route('admin.donaciones.store') }}" method="POST" class="glass rounded-2xl p-8 border border-slate-800 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Donante -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Usuario / Donante</label>
                <div class="relative">
                    <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                    <select name="usuario_id" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg pl-11 pr-4 py-3 focus:outline-none focus:border-blue-500 appearance-none">
                        <option value="">Anónimo (No registrado)</option>
                        <option value="2">JuanitoP (juan@bec.org)</option>
                        <option value="3">MariaG (maria@bec.org)</option>
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"></i>
                </div>
            </div>

            <!-- Categoría y Condición -->
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Categoría *</label>
                <select name="categoria_id" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>Seleccionar categoría...</option>
                    <option value="1">Alimentos</option>
                    <option value="2">Ropa</option>
                    <option value="3">Higiene Personal</option>
                    <option value="4">Medicamentos</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Condición del Artículo *</label>
                <select name="condicion_id" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                    <option value="1">Nuevo / Sellado</option>
                    <option value="2">Buen estado (Usado)</option>
                    <option value="3">Regular</option>
                </select>
            </div>

            <!-- Cantidad y Unidad -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Cantidad *</label>
                    <input type="number" step="0.01" name="cantidad" required placeholder="0.00" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Unidad *</label>
                    <select name="unidad_medicion" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                        <option value="piezas">Piezas</option>
                        <option value="kg">Kilogramos (kg)</option>
                        <option value="litros">Litros (L)</option>
                        <option value="cajas">Cajas</option>
                    </select>
                </div>
            </div>

            <!-- Marca y Albergue -->
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Marca (Opcional)</label>
                <input type="text" name="marca" placeholder="Ej. Verde Valle" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Albergue Destino *</label>
                <select name="albergue_destino_id" required class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>Selecciona dónde se almacenará...</option>
                    <option value="1">Albergue Esperanza (Centro)</option>
                    <option value="2">Refugio Huellas (Juriquilla)</option>
                </select>
            </div>

            <!-- Descripción -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Descripción Detallada *</label>
                <textarea name="descripcion" rows="3" required placeholder="Detalles específicos de la donación (ej. caducidad, tallas, etc.)" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 resize-none"></textarea>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-800 flex justify-end space-x-3">
            <a href="{{ route('admin.donaciones.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-300 hover:text-white transition-colors">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i> Registrar Donación
            </button>
        </div>
    </form>
</div>
@endsection