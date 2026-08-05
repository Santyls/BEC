@extends('layouts.admin')

@section('title', 'Nuevo Usuario')
@section('header_title', 'Usuarios')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="glass rounded-2xl p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <i data-lucide="user-plus" class="w-6 h-6 text-blue-500 mr-3"></i>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Nuevo Usuario</h2>
                </div>
                <a href="{{ route('admin.usuarios.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white text-sm flex items-center">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Volver
                </a>
            </div>

            <form method="POST" action="{{ route('admin.usuarios.store') }}" class="space-y-5" data-confirm="¿Crear este usuario con los datos capturados?">
                @csrf

                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Datos generales</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Nombre(s) *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Teléfono *</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" pattern="[0-9]{10}" required
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Apellido paterno *</label>
                        <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno') }}" required
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Apellido materno *</label>
                        <input type="text" name="apellido_materno" value="{{ old('apellido_materno') }}" required
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest pt-2">Cuenta y acceso (opcional)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Correo</label>
                        <input type="email" name="correo" value="{{ old('correo') }}"
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Contraseña</label>
                        <input type="password" name="password" autocomplete="new-password"
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Mínimo 6 caracteres.</p>
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                            class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Género</label>
                        <select name="genero_id" class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:[color-scheme:dark]">
                            <option value="">— Sin especificar —</option>
                            @foreach ($generos as $genero)
                                <option value="{{ $genero['id'] }}" @selected(old('genero_id') == $genero['id'])>{{ $genero['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest pt-2">Rol en el sistema</p>
                <div>
                    <select name="rol_id" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:[color-scheme:dark]">
                        @foreach ($roles as $rol)
                            <option value="{{ $rol['id'] }}" @selected(old('rol_id', 3) == $rol['id'])>{{ $rol['nombre'] }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                        Si dejas correo y contraseña vacíos, el usuario queda registrado sin acceso propio al sistema
                        (útil para el alta rápida de un ciudadano en mostrador).
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('admin.usuarios.index') }}" class="px-5 py-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-colors">Cancelar</a>
                    <button type="submit" class="bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-xl px-6 py-2.5 font-semibold shadow-lg shadow-blue-500/20 hover:opacity-90 transition-opacity">
                        Registrar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
