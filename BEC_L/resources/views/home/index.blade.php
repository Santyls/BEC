<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEC - UI Style Guide</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        bec: {
                            dark: '#0B132B',
                            base: '#1C2541',
                            primary: '#3A506B',
                            accent: '#5BC0BE',
                            light: '#E2F0F9',
                        },
                        status: {
                            success: '#10B981', // Verde
                            warning: '#F59E0B', // Amarillo/Naranja
                            danger: '#EF4444',  // Rojo
                            info: '#3B82F6',    // Azul
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Estilos base para el scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.3); }
    </style>
</head>
<body class="bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-slate-900 via-blue-950 to-bec-dark min-h-screen text-slate-200 font-sans antialiased p-6 md:p-12 selection:bg-cyan-500/30">

    <!-- Header del Sistema de Diseño -->
    <header class="mb-12 border-b border-white/10 pb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="bg-blue-600 text-white font-bold px-3 py-1 rounded-lg text-xl tracking-wider shadow-lg shadow-blue-500/30">BEC</div>
                <h1 class="text-3xl font-semibold text-white">Sistema de Diseño (UI Kit)</h1>
            </div>
            <p class="text-slate-400">Guía de estilos unificada para Administrador, Ciudadano y Recepcionista.</p>
        </div>
        <div class="hidden md:flex gap-4">
            <span class="px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-sm flex items-center gap-2"><i data-lucide="moon" class="w-4 h-4 text-cyan-400"></i> Tema Oscuro Activo</span>
        </div>
    </header>

    <div class="max-w-7xl mx-auto space-y-20">

        <!-- 1. COLORES -->
        <section class="space-y-6">
            <h2 class="text-2xl font-semibold text-white border-l-4 border-cyan-400 pl-3">1. Paleta de Colores</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 p-4 rounded-2xl flex flex-col gap-3 hover:bg-white/10 transition-colors">
                    <div class="h-16 w-full rounded-xl bg-bec-dark shadow-inner"></div>
                    <div>
                        <p class="text-sm font-semibold text-white">Dark Base</p>
                        <p class="text-xs text-slate-400">#0B132B</p>
                    </div>
                </div>
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 p-4 rounded-2xl flex flex-col gap-3 hover:bg-white/10 transition-colors">
                    <div class="h-16 w-full rounded-xl bg-bec-base shadow-inner border border-white/5"></div>
                    <div>
                        <p class="text-sm font-semibold text-white">Card Base</p>
                        <p class="text-xs text-slate-400">#1C2541</p>
                    </div>
                </div>
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 p-4 rounded-2xl flex flex-col gap-3 hover:bg-white/10 transition-colors">
                    <div class="h-16 w-full rounded-xl bg-cyan-500 shadow-inner"></div>
                    <div>
                        <p class="text-sm font-semibold text-white">Accent / Hover</p>
                        <p class="text-xs text-slate-400">#06B6D4</p>
                    </div>
                </div>
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 p-4 rounded-2xl flex flex-col gap-3 hover:bg-white/10 transition-colors">
                    <div class="h-16 w-full rounded-xl bg-status-success shadow-inner"></div>
                    <div>
                        <p class="text-sm font-semibold text-white">Éxito / Verificado</p>
                        <p class="text-xs text-slate-400">#10B981</p>
                    </div>
                </div>
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 p-4 rounded-2xl flex flex-col gap-3 hover:bg-white/10 transition-colors">
                    <div class="h-16 w-full rounded-xl bg-status-warning shadow-inner"></div>
                    <div>
                        <p class="text-sm font-semibold text-white">Pendiente</p>
                        <p class="text-xs text-slate-400">#F59E0B</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. TIPOGRAFÍA & JERARQUÍA -->
        <section class="space-y-6">
            <h2 class="text-2xl font-semibold text-white border-l-4 border-cyan-400 pl-3">2. Tipografía y Textos (Fuente: Poppins)</h2>
            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div>
                        <h1 class="text-4xl md:text-5xl font-bold text-white mb-2">Heading 1</h1>
                        <p class="text-xs text-slate-500 font-mono">text-4xl md:text-5xl font-bold text-white</p>
                    </div>
                    <div>
                        <h2 class="text-3xl font-semibold text-white mb-2">Heading 2 (Secciones)</h2>
                        <p class="text-xs text-slate-500 font-mono">text-3xl font-semibold text-white</p>
                    </div>
                    <div>
                        <h3 class="text-xl font-medium text-slate-200 mb-2">Heading 3 (Tarjetas)</h3>
                        <p class="text-xs text-slate-500 font-mono">text-xl font-medium text-slate-200</p>
                    </div>
                </div>
                <div class="space-y-6">
                    <div>
                        <p class="text-base text-slate-300 leading-relaxed">
                            <strong class="text-white">Párrafo Principal:</strong> Este es el texto de lectura regular. Utilizado para descripciones de albergues, detalles de donaciones y textos largos. Tiene un contraste suave para no cansar la vista, respetando el fondo azul profundo.
                        </p>
                        <p class="text-xs text-slate-500 font-mono mt-2">text-base text-slate-300</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">
                            <strong>Texto Secundario (Small):</strong> Ideal para fechas, descripciones en tablas, o metadatos en tarjetas de voluntariado.
                        </p>
                        <p class="text-xs text-slate-500 font-mono mt-2">text-sm text-slate-400</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. BOTONES E INTERACCIONES -->
        <section class="space-y-6">
            <h2 class="text-2xl font-semibold text-white border-l-4 border-cyan-400 pl-3">3. Botones (Basados en Imagen 3)</h2>
            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 flex flex-wrap gap-6 items-center">
                <div class="flex flex-col gap-2 items-center">
                    <button class="bg-cyan-600 hover:bg-cyan-500 text-white px-8 py-2.5 rounded-full font-medium transition-all shadow-[0_0_15px_rgba(6,182,212,0.3)] hover:shadow-[0_0_25px_rgba(6,182,212,0.5)] flex items-center gap-2">
                        <span>Donar Ahora</span>
                        <i data-lucide="heart" class="w-4 h-4"></i>
                    </button>
                    <span class="text-xs text-slate-500">Primario</span>
                </div>
                <div class="flex flex-col gap-2 items-center">
                    <button class="bg-transparent border border-white/30 hover:border-white text-white px-8 py-2.5 rounded-full font-medium transition-all hover:bg-white/5 flex items-center gap-2">
                        <span>Ver Albergues</span>
                    </button>
                    <span class="text-xs text-slate-500">Secundario (Outline)</span>
                </div>
                <div class="flex flex-col gap-2 items-center">
                    <button class="bg-white/10 hover:bg-white/20 border border-white/10 text-white p-3 rounded-full transition-all">
                        <i data-lucide="download" class="w-5 h-5"></i>
                    </button>
                    <span class="text-xs text-slate-500">Icono Acción</span>
                </div>
                <div class="flex flex-col gap-2 items-center">
                    <button class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-600 px-4 py-1.5 rounded-full transition-colors">
                        Editar
                    </button>
                    <span class="text-xs text-slate-500">Pequeño (Tablas)</span>
                </div>
            </div>
        </section>

        <!-- 4. CONTENEDORES, INPUTS Y DASHBOARD ELEMENTS -->
        <section class="space-y-6">
            <h2 class="text-2xl font-semibold text-white border-l-4 border-cyan-400 pl-3">4. Componentes y Formularios</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-6 space-y-5 lg:col-span-1">
                    <h3 class="text-white font-medium mb-4">Campos de Entrada</h3>
                    <div class="space-y-1">
                        <label class="text-sm text-slate-400 ml-2">Buscar usuario o albergue</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="search" class="w-4 h-4 text-slate-500"></i>
                            </div>
                            <input type="text" class="w-full bg-black/20 border border-white/10 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-white rounded-full pl-10 pr-4 py-2.5 text-sm transition-all outline-none placeholder-slate-500" placeholder="Buscar...">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm text-slate-400 ml-2">Seleccionar Rol</label>
                        <select class="w-full bg-black/20 border border-white/10 focus:border-cyan-500 text-slate-200 rounded-full px-4 py-2.5 text-sm transition-all outline-none appearance-none cursor-pointer">
                            <option value="1">Administrador</option>
                            <option value="2">Recepcionista</option>
                            <option value="3">Ciudadano</option>
                        </select>
                    </div>
                </div>
                <div class="lg:col-span-2 space-y-4">
                    <h3 class="text-white font-medium pl-2">Tarjetas de Métricas (Glassmorphism)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white/[0.03] backdrop-blur-xl border border-white/[0.08] p-5 rounded-3xl relative overflow-hidden group hover:bg-white/[0.05] transition-all">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150"></div>
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-blue-500/20 p-2.5 rounded-xl border border-blue-500/30">
                                    <i data-lucide="users" class="w-5 h-5 text-blue-400"></i>
                                </div>
                                <span class="bg-status-success/20 text-status-success text-xs font-medium px-2 py-1 rounded-full">+12%</span>
                            </div>
                            <h4 class="text-3xl font-bold text-white mb-1">42</h4>
                            <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Usuarios Registrados</p>
                        </div>
                        <div class="bg-white/[0.03] backdrop-blur-xl border border-white/[0.08] p-5 rounded-3xl relative overflow-hidden group hover:bg-white/[0.05] transition-all">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150"></div>
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-purple-500/20 p-2.5 rounded-xl border border-purple-500/30">
                                    <i data-lucide="package" class="w-5 h-5 text-purple-400"></i>
                                </div>
                                <span class="bg-white/5 text-slate-300 border border-white/10 text-xs font-medium px-2 py-1 rounded-full">Activos</span>
                            </div>
                            <h4 class="text-3xl font-bold text-white mb-1">12</h4>
                            <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Albergues</p>
                        </div>
                        <div class="bg-white/[0.03] backdrop-blur-xl border border-white/[0.08] p-5 rounded-3xl relative overflow-hidden group hover:bg-white/[0.05] transition-all">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition-transform group-hover:scale-150"></div>
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-green-500/20 p-2.5 rounded-xl border border-green-500/30">
                                    <i data-lucide="box" class="w-5 h-5 text-green-400"></i>
                                </div>
                                <span class="bg-status-success/20 text-status-success text-xs font-medium px-2 py-1 rounded-full">+5 hoy</span>
                            </div>
                            <h4 class="text-3xl font-bold text-white mb-1">35</h4>
                            <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Donaciones</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. TABLAS DE DATOS -->
        <section class="space-y-6">
            <h2 class="text-2xl font-semibold text-white border-l-4 border-cyan-400 pl-3">5. Tabla de Datos (Estilo Administrador)</h2>
            <div class="bg-white/[0.03] backdrop-blur-xl border border-white/[0.08] rounded-3xl overflow-hidden">
                <div class="p-6 border-b border-white/[0.05] flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Usuarios Registrados</h3>
                        <p class="text-sm text-slate-400">Actividad reciente en la plataforma</p>
                    </div>
                    <button class="text-sm bg-white/5 hover:bg-white/10 text-white border border-white/10 px-4 py-2 rounded-full transition-colors flex items-center gap-2">
                        <i data-lucide="filter" class="w-4 h-4"></i> Filtros
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs text-slate-400 uppercase bg-black/20 border-b border-white/[0.05]">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Usuario</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Email</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Estado</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">Donaciones</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.02]">
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold">S</div>
                                        <span class="font-medium text-white">SuperVoluntario</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">alex@bec.org</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-status-success/10 text-status-success border border-status-success/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-status-success"></span> Verificado
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-cyan-400">5</td>
                                <td class="px-6 py-4">
                                    <button class="text-slate-400 hover:text-white transition-colors"><i data-lucide="more-horizontal" class="w-5 h-5"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 6. VISTA DE EJEMPLO: HOME CIUDADANO (RESTUARADA CON FOOTER DENTRO) -->
        <section class="space-y-6 pb-20">
            <h2 class="text-2xl font-semibold text-white border-l-4 border-cyan-400 pl-3">6. Vista Ensamblada: Home / Inicio Ciudadano</h2>
            
            <!-- Mockup de Navegador para enmarcar la vista -->
            <div class="rounded-[2.5rem] border border-white/10 bg-[#0B132B]/50 overflow-hidden shadow-2xl relative">
                
                <!-- Barra superior del falso navegador -->
                <div class="bg-black/30 px-6 py-4 border-b border-white/10 flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                    <div class="ml-4 px-4 py-1 rounded-full bg-white/5 text-xs text-slate-400 font-mono border border-white/5 flex-1 max-w-sm text-center truncate">
                        bec.org.mx/inicio
                    </div>
                </div>

                <!-- CONTENIDO REAL DE LA VISTA HOME -->
                <div class="relative min-h-[600px]">
                    
                    <!-- Navegación -->
                    <nav class="px-8 py-5 flex items-center justify-between border-b border-white/[0.02]">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-600 text-white font-bold px-3 py-1 rounded-lg text-xl tracking-wider shadow-lg shadow-blue-500/30">BEC</div>
                            <span class="font-medium text-white tracking-wide hidden md:block">Blue Earth Coders</span>
                        </div>
                        
                        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                            <a href="#" class="text-white">Inicio</a>
                            <a href="#" class="hover:text-white transition-colors">Albergues</a>
                            <a href="#" class="hover:text-white transition-colors">Nosotros</a>
                        </div>

                        <div class="flex items-center gap-4">
                            <a href="#" class="text-sm font-medium text-slate-300 hover:text-white transition-colors hidden sm:block">Iniciar Sesión</a>
                            <button class="bg-white/10 hover:bg-white/20 text-white px-5 py-2 rounded-full text-sm font-medium transition-colors border border-white/10">
                                Registrarse
                            </button>
                        </div>
                    </nav>

                    <!-- Hero Section a DOS COLUMNAS (Restaurada) -->
                    <main class="px-6 md:px-12 pt-16 pb-12">
                        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
                            
                            <!-- Columna Izquierda: Textos -->
                            <div class="space-y-8 z-10 relative">
                                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-sm font-medium">
                                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                                    Uniendo fuerzas para un México mejor
                                </div>
                                
                                <h1 class="text-5xl md:text-6xl font-bold text-white leading-tight">
                                    Conectando ayuda con <br/>
                                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">quienes más lo necesitan</span>
                                </h1>
                                
                                <p class="text-lg text-slate-300 max-w-lg leading-relaxed">
                                    En México, el 5.3% de la población vive en pobreza extrema. En <strong>BEC</strong> facilitamos donaciones y voluntariado conectando a ciudadanos directamente con los albergues que los requieren.
                                </p>
                                
                                <div class="flex flex-col sm:flex-row items-center gap-4 pt-4">
                                    <button class="w-full sm:w-auto bg-cyan-600 hover:bg-cyan-500 text-white px-8 py-3.5 rounded-full font-medium transition-all shadow-[0_0_15px_rgba(6,182,212,0.3)] hover:shadow-[0_0_25px_rgba(6,182,212,0.5)] flex items-center justify-center gap-2">
                                        <span>Hacer una Donación</span>
                                        <i data-lucide="heart" class="w-5 h-5"></i>
                                    </button>
                                    <button class="w-full sm:w-auto bg-transparent border border-white/30 hover:border-white text-white px-8 py-3.5 rounded-full font-medium transition-all hover:bg-white/5 flex items-center justify-center gap-2">
                                        <span>Ser Voluntario</span>
                                        <i data-lucide="hand-metal" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Columna Derecha: Imagen y Elementos Flotantes (Restaurada) -->
                            <div class="relative hidden lg:block">
                                <!-- Fondo decorativo detrás de la imagen -->
                                <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/20 to-blue-500/20 rounded-[2rem] transform rotate-3"></div>
                                
                                <!-- Imagen real obtenida de Unsplash -->
                                <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Voluntarios ayudando" class="relative rounded-[2rem] object-cover h-[500px] w-full shadow-2xl border border-white/10">
                                
                                <!-- Tarjeta Flotante 1 -->
                                <div class="absolute -bottom-6 -left-6 bg-white/10 backdrop-blur-xl border border-white/20 p-4 rounded-2xl shadow-xl flex items-center gap-4 animate-bounce" style="animation-duration: 3s;">
                                    <div class="bg-cyan-500/20 p-3 rounded-xl text-cyan-400">
                                        <i data-lucide="home" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-white">+150</p>
                                        <p class="text-xs text-slate-300">Albergues</p>
                                    </div>
                                </div>

                                <!-- Tarjeta Flotante 2 -->
                                <div class="absolute -top-6 -right-6 bg-white/10 backdrop-blur-xl border border-white/20 p-4 rounded-2xl shadow-xl flex items-center gap-4 animate-bounce" style="animation-duration: 4s; animation-delay: 1s;">
                                    <div class="bg-blue-500/20 p-3 rounded-xl text-blue-400">
                                        <i data-lucide="heart" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-white">+50k</p>
                                        <p class="text-xs text-slate-300">Donaciones</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>

                    <!-- Tarjetas de Información / Causa -->
                    <div class="px-6 md:px-12 mt-12 mb-16 relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto">
                            
                            <!-- Card 1 -->
                            <div class="bg-white/[0.03] backdrop-blur-xl border border-white/[0.08] p-8 rounded-3xl hover:bg-white/[0.05] transition-colors group">
                                <div class="bg-blue-500/20 w-12 h-12 rounded-2xl flex items-center justify-center border border-blue-500/30 mb-6 group-hover:scale-110 transition-transform">
                                    <i data-lucide="map-pin" class="w-6 h-6 text-blue-400"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-white mb-3">Localiza Albergues</h3>
                                <p class="text-sm text-slate-400 leading-relaxed mb-6">
                                    Encuentra en nuestro mapa interactivo los centros de ayuda y albergues más cercanos a ti que necesitan asistencia urgente.
                                </p>
                                <a href="#" class="text-cyan-400 text-sm font-medium flex items-center gap-1 hover:gap-2 transition-all">
                                    Ver mapa <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </div>

                            <!-- Card 2 -->
                            <div class="bg-white/[0.03] backdrop-blur-xl border border-white/[0.08] p-8 rounded-3xl hover:bg-white/[0.05] transition-colors group">
                                <div class="bg-green-500/20 w-12 h-12 rounded-2xl flex items-center justify-center border border-green-500/30 mb-6 group-hover:scale-110 transition-transform">
                                    <i data-lucide="package-open" class="w-6 h-6 text-green-400"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-white mb-3">Gestión Transparente</h3>
                                <p class="text-sm text-slate-400 leading-relaxed mb-6">
                                    Controla el historial de tus donaciones y visualiza en qué categorías (alimentos, ropa, medicinas) tu ayuda genera mayor impacto.
                                </p>
                                <a href="#" class="text-cyan-400 text-sm font-medium flex items-center gap-1 hover:gap-2 transition-all">
                                    Conoce más <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </div>

                            <!-- Card 3 -->
                            <div class="bg-white/[0.03] backdrop-blur-xl border border-white/[0.08] p-8 rounded-3xl hover:bg-white/[0.05] transition-colors group">
                                <div class="bg-purple-500/20 w-12 h-12 rounded-2xl flex items-center justify-center border border-purple-500/30 mb-6 group-hover:scale-110 transition-transform">
                                    <i data-lucide="users" class="w-6 h-6 text-purple-400"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-white mb-3">Red de Voluntarios</h3>
                                <p class="text-sm text-slate-400 leading-relaxed mb-6">
                                    Apúntate a campañas presenciales. No solo cerramos brechas tecnológicas, sino que unificamos el esfuerzo humano.
                                </p>
                                <a href="#" class="text-cyan-400 text-sm font-medium flex items-center gap-1 hover:gap-2 transition-all">
                                    Unirse <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- FOOTER RESTAURADO DENTRO DE LA PANTALLA -->
                    <footer class="border-t border-white/10 bg-black/20 pt-12 pb-8 px-6 md:px-12">
                        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                            <div class="col-span-1 md:col-span-2">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="bg-blue-600 text-white font-bold px-3 py-1 rounded-lg text-xl tracking-wider shadow-lg shadow-blue-500/30">BEC</div>
                                    <span class="font-medium text-white tracking-wide">Blue Earth Coders</span>
                                </div>
                                <p class="text-slate-400 text-sm max-w-sm">Conectando la solidaridad ciudadana con las necesidades más urgentes de los albergues en México.</p>
                            </div>
                            <div>
                                <h4 class="text-white font-medium mb-4">Plataforma</h4>
                                <ul class="space-y-2 text-sm text-slate-400">
                                    <li><a href="#" class="hover:text-cyan-400 transition-colors">Inicio</a></li>
                                    <li><a href="#" class="hover:text-cyan-400 transition-colors">Albergues</a></li>
                                    <li><a href="#" class="hover:text-cyan-400 transition-colors">Donaciones</a></li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-white font-medium mb-4">Legal</h4>
                                <ul class="space-y-2 text-sm text-slate-400">
                                    <li><a href="#" class="hover:text-cyan-400 transition-colors">Privacidad</a></li>
                                    <li><a href="#" class="hover:text-cyan-400 transition-colors">Términos</a></li>
                                    <li><a href="#" class="hover:text-cyan-400 transition-colors">Contacto</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="max-w-7xl mx-auto border-t border-white/10 pt-8 text-center text-sm text-slate-500">
                            &copy; 2026 Blue Earth Coders. Todos los derechos reservados.
                        </div>
                    </footer>

                </div>
            </div>
            <!-- Fin Mockup de Navegador -->
            
        </section>

    </div>

    <!-- Inicializar Iconos -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>

