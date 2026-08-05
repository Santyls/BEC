@extends('layouts.admin')

@section('title', 'Mi Perfil')
@section('header_title', 'Mi Perfil')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 fade-in">

    <!-- FOTO + DATOS DE SOLO LECTURA -->
    <div class="glass rounded-2xl border border-slate-200 dark:border-slate-800 p-8">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <div class="flex flex-col items-center gap-3">
                <div class="relative w-28 h-28">
                    @if ($usuario['foto_url'])
                        <img id="foto-preview" src="{{ rtrim(config('services.bec_api.public_url'), '/') . $usuario['foto_url'] }}" alt="Foto de perfil" class="w-28 h-28 rounded-full object-cover border-4 border-slate-200 dark:border-slate-800">
                    @else
                        <div id="foto-preview-placeholder" class="w-28 h-28 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center font-bold text-white text-3xl border-4 border-slate-200 dark:border-slate-800">
                            {{ strtoupper(substr($usuario['nombre'], 0, 1)) }}
                        </div>
                        <img id="foto-preview" src="" alt="Foto de perfil" class="hidden w-28 h-28 rounded-full object-cover border-4 border-slate-200 dark:border-slate-800">
                    @endif
                    <label for="foto-input" class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-500 text-white p-1.5 rounded-full cursor-pointer shadow-lg transition-colors" title="Cambiar foto">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                    </label>
                </div>

                <form method="POST" action="{{ route('admin.perfil.foto') }}" data-confirm="¿Actualizar tu foto de perfil?" enctype="multipart/form-data" id="form-foto">
                    @csrf
                    <input type="file" id="foto-input" name="foto" accept="image/jpeg,image/png,image/webp" capture="user" class="hidden">
                </form>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 text-center">JPEG, PNG o WEBP · máx. 5MB</p>
            </div>

            <div class="flex-1 space-y-3 w-full">
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Nombre completo</p>
                    <p class="text-slate-800 dark:text-slate-200 font-medium">{{ trim($usuario['nombre'].' '.$usuario['apellido_paterno'].' '.$usuario['apellido_materno']) }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Correo electrónico</p>
                    <p class="text-slate-800 dark:text-slate-200 font-medium">{{ $usuario['correo'] ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Rol</p>
                    <span class="inline-block mt-0.5 px-3 py-1 text-xs font-medium rounded-full border bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20">
                        {{ $roles[$usuario['rol_id']]['nombre'] ?? 'Sin rol' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 pt-2 flex items-center">
                    <i data-lucide="info" class="w-3.5 h-3.5 mr-1.5 flex-shrink-0"></i>
                    Estos datos solo se pueden modificar desde Administración.
                </p>
            </div>
        </div>
    </div>

    <!-- TELÉFONO -->
    <div class="glass rounded-2xl border border-slate-200 dark:border-slate-800 p-8">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center">
            <i data-lucide="phone" class="w-5 h-5 mr-2 text-blue-500"></i> Teléfono de contacto
        </h3>
        <form method="POST" action="{{ route('admin.perfil.telefono') }}" class="flex flex-col sm:flex-row gap-3" data-confirm="¿Guardar el nuevo teléfono?">
            @csrf
            @method('PUT')
            <input type="text" name="telefono" value="{{ old('telefono', $usuario['telefono'] ?? '') }}" placeholder="10 dígitos" maxlength="10"
                class="flex-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-medium transition-all shadow-lg shadow-blue-500/20">
                Guardar
            </button>
        </form>
    </div>

    <!-- CONTRASEÑA -->
    <div class="glass rounded-2xl border border-slate-200 dark:border-slate-800 p-8">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center">
            <i data-lucide="lock" class="w-5 h-5 mr-2 text-blue-500"></i> Cambiar contraseña
        </h3>
        <form method="POST" action="{{ route('admin.perfil.password') }}" class="space-y-4" data-confirm="¿Confirmas el cambio de contraseña?">
            @csrf
            @method('PUT')
            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-300 block mb-1">Contraseña actual</label>
                <input type="password" name="password_actual" required
                    class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300 block mb-1">Nueva contraseña</label>
                    <input type="password" name="password_nueva" minlength="6" required
                        class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300 block mb-1">Confirmar nueva contraseña</label>
                    <input type="password" name="password_nueva_confirmation" minlength="6" required
                        class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                </div>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-medium transition-all shadow-lg shadow-blue-500/20">
                Actualizar contraseña
            </button>
        </form>
    </div>

</div>
@endsection

@push('styles')
<style>
    .fade-in {
        animation: fadeIn 0.4s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('foto-input').addEventListener('change', function () {
        if (!this.files || !this.files[0]) return;

        var lector = new FileReader();
        lector.onload = function (e) {
            var preview = document.getElementById('foto-preview');
            var placeholder = document.getElementById('foto-preview-placeholder');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        lector.readAsDataURL(this.files[0]);

        document.getElementById('form-foto').submit();
    });
</script>
@endpush
