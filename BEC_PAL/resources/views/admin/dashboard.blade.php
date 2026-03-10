@extends('layouts.admin')

<!-- Definimos las variables para el Layout -->
@section('title', 'Dashboard Resumen')
@section('header_title', 'Resumen General')

<!-- Inyectamos el contenido HTML específico del Dashboard -->
@section('content')
    <div class="space-y-8">
        
        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            
            <div class="dashboard-card bg-slate-800/40 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-slate-400">Total Albergues</p>
                        <h3 class="text-3xl font-bold text-white mt-2">24</h3>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-500/20 text-blue-400">
                        <i data-lucide="home" class="w-6 h-6"></i>
                    </div>
                </div>
                <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i data-lucide="home" class="w-32 h-32"></i>
                </div>
            </div>

            <div class="dashboard-card bg-slate-800/40 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-slate-400">Donaciones Activas</p>
                        <h3 class="text-3xl font-bold text-white mt-2">156</h3>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-500/20 text-emerald-400">
                        <i data-lucide="heart-handshake" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
            
            <!-- Puedes agregar más tarjetas aquí... -->
        </div>

        <!-- Sección de contenido más grande (Gráficos, Tablas recientes, etc.) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 dashboard-card bg-slate-800/40 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-white mb-4">Actividad Reciente</h3>
                <div class="h-64 flex items-center justify-center border-2 border-dashed border-slate-700/50 rounded-xl">
                    <p class="text-slate-500 flex flex-col items-center">
                        <i data-lucide="bar-chart-3" class="w-10 h-10 mb-2 opacity-50"></i>
                        El gráfico se renderizará aquí
                    </p>
                </div>
            </div>

            <div class="dashboard-card bg-slate-800/40 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-white mb-4">Últimos Registros</h3>
                <div class="space-y-4">
                    <!-- Ejemplo de lista de notificaciones -->
                    <div class="flex items-center p-3 rounded-lg bg-slate-900/50 border border-slate-800">
                        <div class="w-2 h-2 rounded-full bg-blue-500 mr-4"></div>
                        <p class="text-sm text-slate-300">Nuevo albergue "Esperanza" registrado.</p>
                    </div>
                    <div class="flex items-center p-3 rounded-lg bg-slate-900/50 border border-slate-800">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 mr-4"></div>
                        <p class="text-sm text-slate-300">Donación de ropa recibida.</p>
                    </div>
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