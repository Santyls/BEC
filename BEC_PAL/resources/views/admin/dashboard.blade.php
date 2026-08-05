@extends('layouts.admin')

<!-- Definimos las variables para el Layout -->
@section('title', 'Dashboard Resumen')
@section('header_title', 'Resumen General')

<!-- Inyectamos el contenido HTML específico del Dashboard -->
@section('content')
    <div class="space-y-8">

        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            <div class="dashboard-card bg-white dark:bg-slate-900 backdrop-blur-sm border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Albergues</p>
                        <h3 class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $totalAlbergues }}</h3>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-500/20 text-blue-700 dark:text-blue-400">
                        <i data-lucide="home" class="w-6 h-6"></i>
                    </div>
                </div>
                <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i data-lucide="home" class="w-32 h-32"></i>
                </div>
            </div>

            <div class="dashboard-card bg-white dark:bg-slate-900 backdrop-blur-sm border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Donaciones (Mes)</p>
                        <h3 class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $donacionesMes }}</h3>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-500/20 text-emerald-700 dark:text-emerald-400">
                        <i data-lucide="heart-handshake" class="w-6 h-6"></i>
                    </div>
                </div>
                <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i data-lucide="heart-handshake" class="w-32 h-32"></i>
                </div>
            </div>

            <div class="dashboard-card bg-white dark:bg-slate-900 backdrop-blur-sm border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Campañas Activas</p>
                        <h3 class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $campanasActivas }}</h3>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-500/20 text-amber-700 dark:text-amber-400">
                        <i data-lucide="megaphone" class="w-6 h-6"></i>
                    </div>
                </div>
                <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i data-lucide="megaphone" class="w-32 h-32"></i>
                </div>
            </div>

            <div class="dashboard-card bg-white dark:bg-slate-900 backdrop-blur-sm border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Voluntariados Activos</p>
                        <h3 class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $voluntariadosActivos }}</h3>
                    </div>
                    <div class="p-3 rounded-xl bg-purple-500/20 text-purple-700 dark:text-purple-400">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                </div>
                <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i data-lucide="users" class="w-32 h-32"></i>
                </div>
            </div>
        </div>

        <!-- Sección de contenido más grande (Gráficos, Tablas recientes, etc.) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 dashboard-card bg-white dark:bg-slate-900 backdrop-blur-sm border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Resumen del Mes</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Donaciones registradas</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $donacionesMes }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Voluntariados en curso</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $voluntariadosActivos }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Campañas activas</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $campanasActivas }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">Albergues operando</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalAlbergues }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.reportes.index') }}" class="mt-6 inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700">
                    Ver reportes detallados <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                </a>
            </div>

            <div class="dashboard-card bg-white dark:bg-slate-900 backdrop-blur-sm border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Últimos Registros</h3>
                <div class="space-y-4">
                    @forelse ($recientes as $item)
                        <div class="flex items-center p-3 rounded-lg bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800">
                            <div class="w-2 h-2 rounded-full {{ $item['color'] }} mr-4 flex-shrink-0"></div>
                            <p class="text-sm text-slate-700 dark:text-slate-300">{{ $item['texto'] }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-4">Sin actividad reciente.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
@endsection

<!-- Inyectamos el script específico solo al final del body del layout -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Animación sencilla de entrada para las tarjetas
        const cards = document.querySelectorAll('.dashboard-card');

        // Es importante llamar lucide aquí también por si hay iconos nuevos cargados
        lucide.createIcons();

        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease-out';

            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });
    });
</script>
@endpush
