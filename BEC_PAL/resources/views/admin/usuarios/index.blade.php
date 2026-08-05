@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')
@section('header_title', 'Usuarios')

@section('content')
    <div class="space-y-6 fade-in">

        <!-- Barra de acciones superior -->
        <form method="GET" action="{{ route('admin.usuarios.index') }}" class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="relative w-full sm:w-96">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500 dark:text-slate-400"></i>
                <input type="text" name="q" value="{{ $busqueda }}" placeholder="Buscar por nombre, correo o rol..."
                       class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
            </div>

            <a href="{{ route('admin.usuarios.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-medium flex items-center transition-all shadow-lg shadow-blue-500/20">
                <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                Nuevo Usuario
            </a>
        </form>

        <!-- Tabla de Usuarios -->
        <div class="bg-white dark:bg-slate-900 backdrop-blur-sm border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-sm">
                            <th class="px-6 py-4 font-medium">Nombre</th>
                            <th class="px-6 py-4 font-medium">Correo Electrónico</th>
                            <th class="px-6 py-4 font-medium">Rol</th>
                            <th class="px-6 py-4 font-medium">Estado</th>
                            <th class="px-6 py-4 font-medium text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @forelse ($usuarios as $usuario)
                            @php
                                $nombreCompleto = trim($usuario['nombre'].' '.$usuario['apellido_paterno'].' '.$usuario['apellido_materno']);
                                $rolNombre = $roles[$usuario['rol_id']]['nombre'] ?? 'Sin rol';
                                $colorRol = match ($usuario['rol_id']) {
                                    1 => 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20',
                                    2 => 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20',
                                    default => 'bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/20',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if ($usuario['foto_url'])
                                            <img src="{{ rtrim(config('services.bec_api.public_url'), '/') . $usuario['foto_url'] }}" alt="" class="w-8 h-8 rounded-full object-cover mr-3">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-700 dark:text-indigo-400 flex items-center justify-center font-bold mr-3">
                                                {{ strtoupper(substr($usuario['nombre'], 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $nombreCompleto }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $usuario['correo'] ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full border {{ $colorRol }}">
                                        {{ $rolNombre }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($usuario['activo'])
                                        <div class="flex items-center text-emerald-700 dark:text-emerald-400 text-sm">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></div> Activo
                                        </div>
                                    @else
                                        @php
                                            $diasRestantes = $usuario['fecha_desactivacion']
                                                ? max(0, 30 - \Illuminate\Support\Carbon::parse($usuario['fecha_desactivacion'])->diffInDays(now()))
                                                : null;
                                        @endphp
                                        <div class="flex items-center text-slate-500 dark:text-slate-400 text-sm" title="Se elimina en definitiva si nadie la reactiva">
                                            <div class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-600 mr-2"></div> Inactivo
                                        </div>
                                        @if (!is_null($diasRestantes))
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Se elimina en {{ $diasRestantes }} día(s)</p>
                                        @endif
                                    @endif
                                    @if ($usuario['vetado'])
                                        <span class="mt-1 inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 dark:text-rose-400 bg-rose-500/10 border border-rose-500/20 px-2 py-0.5 rounded-full" title="{{ $usuario['motivo_veto'] ?? 'Sin motivo especificado' }}">
                                            <i data-lucide="shield-off" class="w-3 h-3"></i> VETADO
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if ($usuario['activo'])
                                            <a href="{{ route('admin.usuarios.edit', $usuario['id']) }}" class="p-2 text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-400/10 rounded-lg transition-colors" title="Editar">
                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario['id']) }}" data-confirm="¿Desactivar a {{ $nombreCompleto }}?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-400/10 rounded-lg transition-colors" title="Desactivar">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.usuarios.reactivar', $usuario['id']) }}" data-confirm="¿Reactivar a {{ $nombreCompleto }}?">
                                                @csrf
                                                <button type="submit" class="p-2 text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-400/10 rounded-lg transition-colors" title="Reactivar">
                                                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.usuarios.eliminarPermanente', $usuario['id']) }}" data-confirm="¿Eliminar PERMANENTEMENTE a {{ $nombreCompleto }}? Esta acción no se puede deshacer.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-400/10 rounded-lg transition-colors" title="Eliminar permanentemente">
                                                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">No hay usuarios que coincidan con la búsqueda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('partials.paginacion', ['paginador' => $usuarios])
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
