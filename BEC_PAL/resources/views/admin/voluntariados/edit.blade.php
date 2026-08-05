@extends('layouts.admin')

@section('title', 'Editar Voluntariado')
@section('header_title', 'Editar Voluntariado')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.voluntariados.index') }}" class="w-10 h-10 rounded-full glass flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Editar Voluntariado</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $voluntariado['inscritos'] }} persona(s) ya inscritas en esta actividad.</p>
        </div>
    </div>

    <form action="{{ route('admin.voluntariados.update', $voluntariado['id']) }}" data-confirm="¿Los datos son correctos? Se guardarán los cambios de este voluntariado." method="POST" class="glass rounded-2xl p-8 border border-slate-200 dark:border-slate-800">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Nombre de la Actividad *</label>
                <input type="text" name="nombre_programa" value="{{ old('nombre_programa', $voluntariado['nombre_programa']) }}" required
                    class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Albergue Asignado</label>
                <select name="albergue_id" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 dark:[color-scheme:dark]">
                    <option value="">Sin albergue asignado</option>
                    @foreach ($albergues as $albergue)
                        <option value="{{ $albergue['id'] }}" @selected(old('albergue_id', $voluntariado['albergue_id']) == $albergue['id'])>{{ $albergue['nombre'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Campaña (Opcional)</label>
                <select name="campana_id" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 dark:[color-scheme:dark]">
                    <option value="">Ninguna (Actividad independiente)</option>
                    @foreach ($campanas as $campana)
                        <option value="{{ $campana['id'] }}" @selected(old('campana_id', $voluntariado['campana_id']) == $campana['id'])>{{ $campana['nombre'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Fecha Programada *</label>
                <input type="date" name="fecha_programada" value="{{ old('fecha_programada', $voluntariado['fecha_programada']) }}" required
                    class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 [color-scheme:light] dark:[color-scheme:dark]">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Cupo Máximo</label>
                <input type="number" name="cupo_maximo" value="{{ old('cupo_maximo', $voluntariado['cupo_maximo']) }}" min="{{ $voluntariado['inscritos'] }}"
                    class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Hora Inicio *</label>
                    <input type="time" name="hora_inicio" value="{{ old('hora_inicio', substr($voluntariado['hora_inicio'],0,5)) }}" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 [color-scheme:light] dark:[color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Hora Fin *</label>
                    <input type="time" name="hora_fin" value="{{ old('hora_fin', substr($voluntariado['hora_fin'],0,5)) }}" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 [color-scheme:light] dark:[color-scheme:dark]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Estado *</label>
                <select name="estado_id" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 dark:[color-scheme:dark]">
                    @foreach ($estados as $estado)
                        <option value="{{ $estado['id'] }}" @selected(old('estado_id', $voluntariado['estado_id']) == $estado['id'])>{{ $estado['nombre'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Ubicación (Opcional)</label>
                <div class="relative">
                    <i data-lucide="map-pin" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    <input type="text" name="ubicacion" value="{{ old('ubicacion', $voluntariado['ubicacion']) }}" placeholder="Ej. Parque Alameda, entrada norte (déjalo vacío si es en el albergue asignado)"
                        class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg pl-11 pr-4 py-3 focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Descripción y Requisitos *</label>
                <textarea name="descripcion_requisitos" rows="4" required
                    class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 resize-none">{{ old('descripcion_requisitos', $voluntariado['descripcion_requisitos']) }}</textarea>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 flex justify-end space-x-3">
            <a href="{{ route('admin.voluntariados.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center transition-all shadow-lg shadow-blue-500/20">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
