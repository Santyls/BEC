@extends('layouts.admin')

@section('title', 'Editar Usuario')
@section('header_title', 'Usuarios')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="glass rounded-2xl p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <i data-lucide="user-cog" class="w-6 h-6 text-blue-500 mr-3"></i>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Editar Usuario</h2>
                </div>
                <a href="{{ route('admin.usuarios.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white text-sm flex items-center">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Volver
                </a>
            </div>

            <form method="POST" action="{{ route('admin.usuarios.update', $usuario['id']) }}" class="space-y-5" data-confirm="¿Los datos son correctos? Se guardarán los cambios de este usuario.">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Nombre(s) *</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $usuario['nombre']) }}" required
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $usuario['telefono']) }}" pattern="[0-9]{10}"
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Apellido paterno *</label>
                        <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno', $usuario['apellido_paterno']) }}" required
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Apellido materno *</label>
                        <input type="text" name="apellido_materno" value="{{ old('apellido_materno', $usuario['apellido_materno']) }}" required
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Correo</label>
                        <input type="email" name="correo" value="{{ old('correo', $usuario['correo']) }}"
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $usuario['fecha_nacimiento']) }}"
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Género</label>
                        <select name="genero_id" class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:[color-scheme:dark]">
                            <option value="">— Sin especificar —</option>
                            @foreach ($generos as $genero)
                                <option value="{{ $genero['id'] }}" @selected(old('genero_id', $usuario['genero_id']) == $genero['id'])>{{ $genero['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Rol *</label>
                        <select name="rol_id" required class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:[color-scheme:dark]">
                            @foreach ($roles as $rol)
                                <option value="{{ $rol['id'] }}" @selected(old('rol_id', $usuario['rol_id']) == $rol['id'])>{{ $rol['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest pt-2">Restablecer contraseña (opcional)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Nueva contraseña</label>
                        <input type="password" name="password" autocomplete="new-password"
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Déjalo vacío para no cambiar la contraseña actual.</p>
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest pt-2">Moderación</p>
                <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/30 p-4 space-y-3">
                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" id="vetado-checkbox" name="vetado" value="1" onchange="document.getElementById('motivo-veto-wrap').classList.toggle('hidden', !this.checked)"
                            {{ old('vetado', $usuario['vetado']) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-rose-500 focus:ring-rose-500">
                        Vetar a este usuario (no podrá inscribirse a ningún voluntariado)
                    </label>
                    <div id="motivo-veto-wrap" class="{{ old('vetado', $usuario['vetado']) ? '' : 'hidden' }}">
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Motivo del veto</label>
                        <textarea name="motivo_veto" rows="2" class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none">{{ old('motivo_veto', $usuario['motivo_veto']) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('admin.usuarios.index') }}" class="px-5 py-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-colors">Cancelar</a>
                    <button type="submit" class="bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-xl px-6 py-2.5 font-semibold shadow-lg shadow-blue-500/20 hover:opacity-90 transition-opacity">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
