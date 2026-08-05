@extends('layouts.admin')
@section('title', 'Nueva Donación')
@section('header_title', 'Registrar Donación')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.donaciones.index') }}" class="w-10 h-10 rounded-full glass flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Ingreso de Donación Manual</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Registra un donativo físico recibido en las instalaciones.</p>
        </div>
    </div>

    <form action="{{ route('admin.donaciones.store') }}" data-confirm="¿Registrar esta donación con los datos capturados?" method="POST" class="glass rounded-2xl p-8 border border-slate-200 dark:border-slate-800 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Usuario / Donante</label>
                <div class="relative">
                    <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    <select name="usuario_id" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg pl-11 pr-4 py-3 focus:outline-none focus:border-blue-500 appearance-none dark:[color-scheme:dark]">
                        <option value="">Anónimo (No registrado)</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario['id'] }}" @selected(old('usuario_id') == $usuario['id'])>
                                {{ trim($usuario['nombre'].' '.$usuario['apellido_paterno']) }} @if($usuario['correo']) ({{ $usuario['correo'] }}) @endif
                            </option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 dark:text-slate-400 pointer-events-none"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Categoría *</label>
                <select name="categoria_id" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 dark:[color-scheme:dark]">
                    <option value="" disabled selected>Seleccionar categoría...</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria['id'] }}" @selected(old('categoria_id') == $categoria['id'])>{{ $categoria['nombre'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Condición del Artículo *</label>
                <select name="condicion_id" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 dark:[color-scheme:dark]">
                    @foreach ($condiciones as $condicion)
                        <option value="{{ $condicion['id'] }}" @selected(old('condicion_id') == $condicion['id'])>{{ $condicion['nombre'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Cantidad *</label>
                    <input type="number" step="0.01" name="cantidad" value="{{ old('cantidad') }}" required placeholder="0.00" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Unidad *</label>
                    <select name="unidad_id" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 dark:[color-scheme:dark]">
                        @foreach ($unidades as $unidad)
                            <option value="{{ $unidad['id'] }}" @selected(old('unidad_id') == $unidad['id'])>{{ $unidad['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Marca (Opcional)</label>
                <input type="text" name="marca" value="{{ old('marca') }}" placeholder="Ej. Verde Valle" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Albergue Destino *</label>
                <select name="albergue_id" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 dark:[color-scheme:dark]">
                    <option value="" disabled selected>Selecciona dónde se almacenará...</option>
                    @foreach ($albergues as $albergue)
                        <option value="{{ $albergue['id'] }}" @selected(old('albergue_id') == $albergue['id'])>{{ $albergue['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 flex justify-end space-x-3">
            <a href="{{ route('admin.donaciones.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i> Registrar Donación
            </button>
        </div>
    </form>
</div>
@endsection
