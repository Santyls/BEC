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
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Proyección de Insumos Invernales</h3>
                        <p class="text-xs text-slate-400 mt-1">Análisis Causal (Series de Tiempo) de ropa y cobijas necesarias.</p>
                    </div>
                    <select id="albergueSelect" class="bg-slate-900 border border-slate-700 text-emerald-400 font-medium text-sm rounded-lg px-4 py-2 focus:outline-none focus:border-emerald-500 shadow-lg cursor-pointer transition-colors">
                        <option value="1">Albergue Yimpathí</option>
                        <option value="2">Centro de Día Meni</option>
                    </select>
                </div>
                
                <div class="relative h-80 w-full flex items-center justify-center border-2 border-dashed border-slate-700/50 rounded-xl overflow-hidden bg-slate-900/30" id="chartContainer">
                    <!-- Loading state -->
                    <div id="chartLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/80 z-10 backdrop-blur-sm">
                        <i data-lucide="loader-2" class="w-10 h-10 text-emerald-500 animate-spin mb-3"></i>
                        <p class="text-sm font-bold text-slate-300 uppercase tracking-widest">Calculando Algoritmo...</p>
                        <p class="text-[10px] text-slate-500 mt-2">Ajustando modelo de Suavizado Exponencial</p>
                    </div>
                    
                    <canvas id="demandChart" class="w-full h-full opacity-0 transition-opacity duration-1000"></canvas>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Animación sencilla de entrada para las tarjetas
        const cards = document.querySelectorAll('.dashboard-card');
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

        // ==========================================
        // CONSUMO DE API ML: PREDICTOR DE DEMANDA
        // ==========================================
        const jwtToken = "{{ session('api_token') }}";
        
        // CORRECCIÓN: Intentamos detectar si estamos en localhost o en una IP de red
        let baseUrl = "http://localhost:8000"; 
        if (window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
            baseUrl = `http://${window.location.hostname}:8000`;
        }
        
        let demandChart = null;

        async function fetchPrediccion(idAlbergue) {
            const loading = document.getElementById('chartLoading');
            const canvas = document.getElementById('demandChart');
            
            loading.style.display = 'flex';
            canvas.style.opacity = '0';

            try {
                const response = await fetch(`${baseUrl}/predicciones/albergue/${idAlbergue}`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${jwtToken}`,
                        'Content-Type': 'application/json'
                    }
                });

                if(!response.ok) throw new Error("Status code: " + response.status);
                
                const data = await response.json();
                console.log("Datos de Predicción Recibidos:", data); // Log de depuración
                renderChart(data.historico, data.prediccion);
                
                loading.style.display = 'none';
                canvas.style.opacity = '1';
            } catch (error) {
                console.error("M.L. Chart Error:", error);
                loading.innerHTML = `
                    <div class="text-center p-6">
                        <i data-lucide="alert-circle" class="w-12 h-12 text-rose-500 mx-auto mb-3 animate-pulse"></i>
                        <p class="text-sm font-bold text-rose-400 uppercase tracking-widest">Error de Conexión API</p>
                        <p class="text-[10px] text-slate-500 mt-2 max-w-xs mx-auto">
                            Asegúrate de que <span class="text-blue-400 font-mono">http://localhost:8000</span> sea accesible desde tu navegador.
                        </p>
                        <button onclick="location.reload()" class="mt-4 px-4 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] rounded-lg border border-slate-700 transition-all">
                            Reintentar Conexión
                        </button>
                    </div>`;
                lucide.createIcons();
               function renderChart(historico, prediccion) {
            const ctx = document.getElementById('demandChart').getContext('2d');
            
            if(demandChart) demandChart.destroy();

            // 1. Unir Fechas para un eje X continuo
            const labels = [...historico.fechas, ...prediccion.fechas];
            
            // 2. Padding de Nulos para el Histórico (valores reales + nulos al final)
            const datosHistorico = [
                ...historico.valores, 
                ...Array(prediccion.valores.length).fill(null)
            ];
            
            // 3. Padding de Nulos para la Predicción (nulos al inicio + valores proyectados)
            // Para que las líneas se conecten visualmente, el primer punto de la predicción 
            // debe ser el último punto del histórico.
            const ultimoValorHistorico = historico.valores[historico.valores.length - 1];
            const datosPrediccion = [
                ...Array(historico.valores.length - 1).fill(null),
                ultimoValorHistorico,
                ...prediccion.valores
            ];

            demandChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Tendencia Histórica',
                            data: datosHistorico,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.15)',
                            borderWidth: 2,
                            pointRadius: 2,
                            pointBackgroundColor: '#3b82f6',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Proyección Predictiva (30 Días)',
                            data: datosPrediccion,
                            borderColor: '#10b981',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 2,
                            pointBackgroundColor: '#10b981',
                            fill: false,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { 
                            display: true,
                            labels: { color: '#94a3b8', font: { size: 12, weight: 'bold' } } 
                        },
                        tooltip: { 
                            backgroundColor: 'rgba(15, 23, 42, 0.9)', 
                            titleColor: '#f8fafc', 
                            bodyColor: '#cbd5e1', 
                            borderColor: 'rgba(59, 130, 246, 0.3)', 
                            borderWidth: 1 
                        }
                    },
                    scales: {
                        x: { 
                            grid: { color: 'rgba(51, 65, 85, 0.1)', drawBorder: false }, 
                            ticks: { color: '#64748b', maxTicksLimit: 10, font: { size: 10 } } 
                        },
                        y: { 
                            grid: { color: 'rgba(51, 65, 85, 0.1)', drawBorder: false }, 
                            ticks: { color: '#64748b' }, 
                            beginAtZero: true 
                        }
                    }
                }
            });
        }           });
        }

        // Init inicial
        fetchPrediccion(document.getElementById('albergueSelect').value);

        // Al cambiar Albergue
        document.getElementById('albergueSelect').addEventListener('change', (e) => {
            fetchPrediccion(e.target.value);
        });
    });
</script>
@endpush