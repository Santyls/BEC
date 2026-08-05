@extends('layouts.admin')
@section('title', 'Editar Albergue')
@section('header_title', 'Editar Albergue')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.albergues.index') }}" class="w-10 h-10 rounded-full glass flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Editar Albergue</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Actualiza los datos generales y la dirección de este albergue.</p>
        </div>
    </div>

    <form action="{{ route('admin.albergues.update', $albergue['id']) }}" data-confirm="¿Los datos son correctos? Se guardarán los cambios de este albergue." method="POST" class="glass rounded-2xl p-8 border border-slate-200 dark:border-slate-800 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2 border-b border-slate-200 dark:border-slate-800 pb-2 mb-2">
                <h4 class="text-blue-400 font-bold uppercase tracking-widest text-xs">Datos Generales</h4>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Nombre del Albergue *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $albergue['nombre']) }}" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Capacidad Máxima *</label>
                <input type="number" name="capacidad_max" value="{{ old('capacidad_max', $albergue['capacidad_max']) }}" required min="1" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Teléfono de Contacto *</label>
                <input type="text" name="telefono" value="{{ old('telefono', $albergue['telefono']) }}" pattern="[0-9]{10}" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div class="md:col-span-2 border-b border-slate-200 dark:border-slate-800 pb-2 mb-2 mt-4">
                <h4 class="text-blue-400 font-bold uppercase tracking-widest text-xs">Dirección</h4>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Estado *</label>
                <select name="estado_id" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 dark:[color-scheme:dark]">
                    @foreach ($estados as $estado)
                        <option value="{{ $estado['id'] }}" @selected(old('estado_id', $albergue['direccion']['estado_id']) == $estado['id'])>{{ $estado['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Municipio *</label>
                <input type="text" name="municipio" value="{{ old('municipio', $albergue['direccion']['municipio']) }}" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Calle *</label>
                    <input type="text" name="calle" value="{{ old('calle', $albergue['direccion']['calle']) }}" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Número Ext. *</label>
                    <input type="text" name="numero_exterior" value="{{ old('numero_exterior', $albergue['direccion']['numero_exterior']) }}" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Número Int. (Opcional)</label>
                <input type="text" name="numero_interior" value="{{ old('numero_interior', $albergue['direccion']['numero_interior']) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Código Postal *</label>
                <input type="text" name="codigo_postal" value="{{ old('codigo_postal', $albergue['direccion']['codigo_postal']) }}" pattern="[0-9]{5}" required maxlength="5" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Colonia *</label>
                <input type="text" name="colonia" value="{{ old('colonia', $albergue['direccion']['colonia']) }}" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 flex justify-end space-x-3">
            <a href="{{ route('admin.albergues.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
