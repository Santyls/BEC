<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <script>
        // Theme init: aplica el tema guardado antes de pintar la página para evitar
        // el parpadeo de tema incorrecto. Por defecto siempre es claro: solo un
        // toggle explícito previo (persistido en localStorage) activa el oscuro.
        (function () {
            try {
                if (localStorage.getItem('bec-theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - BEC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { background-color: #F1F5F9; color: #0F172A; }
        .dark body { background-color: #0f172a; color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(10px); border: 1px solid rgba(15, 23, 42, 0.08); }
        .dark .glass { background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar-item-active { background: rgba(59, 130, 246, 0.1); color: #1d4ed8; border-left: 4px solid #3b82f6; }
        .dark .sidebar-item-active { color: #60a5fa; }
    </style>
    @stack('styles')
</head>
<body class="flex h-screen overflow-hidden max-w-full">
    <!-- Fondo oscuro que aparece detrás del menú en móvil (al tocarlo, se cierra) -->
    <div id="sidebar-backdrop" class="hidden fixed inset-0 bg-slate-900/50 z-30 lg:hidden"></div>

    <!-- BARRA LATERAL
         En móvil está oculta (hidden) y se muestra encima del contenido con el
         botón de hamburguesa; a partir de lg (lg:flex lg:static) vuelve a ser la
         columna fija de siempre. Se oculta con `hidden` y no moviéndola con
         translate/left porque esas utilidades no se aplicaban de forma confiable
         sobre este elemento (.glass usa backdrop-filter). -->
    <aside id="sidebar" class="w-64 glass flex flex-col border-r border-slate-200 dark:border-slate-800 fixed inset-y-0 left-0 z-40 hidden lg:flex lg:static">
        <div class="p-6">
            <h1 class="text-xl font-black tracking-tighter text-blue-500 uppercase">BEC<span class="text-slate-900 dark:text-white">_Admin</span></h1>
        </div>

        <nav class="flex-1 overflow-y-auto px-4 space-y-2 mt-2 pb-6">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i> Dashboard
            </a>

            <div class="pt-4 pb-1">
                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest px-3">Gestión Principal</span>
            </div>

            <a href="{{ route('admin.usuarios.index') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.usuarios.*') ? 'sidebar-item-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="users" class="w-5 h-5 mr-3"></i> Usuarios & Roles
            </a>
            <a href="{{ route('admin.albergues.index') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.albergues.*') ? 'sidebar-item-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="home" class="w-5 h-5 mr-3"></i> Albergues
            </a>
            <a href="{{ route('admin.campanas.index') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.campanas.*') ? 'sidebar-item-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="megaphone" class="w-5 h-5 mr-3"></i> Campañas
            </a>
            <a href="{{ route('admin.voluntariados.index') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.voluntariados.*') ? 'sidebar-item-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="calendar-heart" class="w-5 h-5 mr-3"></i> Voluntariados
            </a>
            <a href="{{ route('admin.donaciones.index') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.donaciones.*') ? 'sidebar-item-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="heart-handshake" class="w-5 h-5 mr-3"></i> Donaciones
            </a>

            <div class="pt-4 pb-1">
                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest px-3">Análisis</span>
            </div>

            <a href="{{ route('admin.reportes.index') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.reportes.*') ? 'sidebar-item-active' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="pie-chart" class="w-5 h-5 mr-3"></i> Reportes
            </a>
        </nav>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-1 flex flex-col h-screen">
        <header class="h-16 flex items-center px-4 sm:px-8 border-b border-slate-200 dark:border-slate-800 justify-between glass z-10">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" id="sidebar-toggle" title="Menú" class="lg:hidden w-9 h-9 -ml-1 rounded-lg flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex-shrink-0">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400 truncate">@yield('header_title', 'Inicio')</h2>
            </div>
            <div class="flex items-center space-x-3 flex-shrink-0">
                <button type="button" id="theme-toggle" class="w-9 h-9 rounded-full flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Cambiar tema">
                    <i data-lucide="moon" class="w-5 h-5 block dark:hidden"></i>
                    <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
                </button>
                <a href="{{ route('admin.perfil.index') }}" class="flex items-center space-x-3 hover:opacity-80 transition-opacity" title="Mi Perfil">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ session('bec_user')['nombre'] ?? 'Admin' }}</p>
                        <p class="text-[10px] text-emerald-700 dark:text-emerald-400">● En línea</p>
                    </div>
                    @if (session('bec_user')['foto_url'] ?? null)
                        <img src="{{ rtrim(config('services.bec_api.public_url'), '/') . session('bec_user')['foto_url'] }}" alt="Foto de perfil" class="w-9 h-9 rounded-full object-cover shadow-lg">
                    @else
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center font-bold text-white shadow-lg">
                            {{ strtoupper(substr(session('bec_user')['nombre'] ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}" data-confirm="¿Cerrar tu sesión?">
                    @csrf
                    <button type="submit" class="text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Cerrar sesión">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                    </button>
                </form>
            </div>
        </header>

        @if (session('exito'))
            <div class="mx-4 sm:mx-8 mt-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-sm">
                {{ session('exito') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mx-4 sm:mx-8 mt-4 px-4 py-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-400 text-sm">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mx-4 sm:mx-8 mt-4 px-4 py-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-400 text-sm">
                <p class="font-semibold mb-1">Revisa los siguientes datos:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $mensaje)
                        <li>{{ $mensaje }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            @yield('content')
        </div>
    </main>

    <!-- MODAL DE CONFIRMACIÓN (reemplaza al confirm() nativo del navegador) -->
    <div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div id="confirm-modal-backdrop" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div id="confirm-modal-card" class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-sm mx-4 p-6 scale-95 opacity-0 transition-all duration-150">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Confirmar acción</h3>
            <p id="confirm-modal-message" class="text-sm text-slate-600 dark:text-slate-300 mb-6"></p>
            <div class="flex justify-end gap-3">
                <button type="button" id="confirm-modal-cancel" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Cancelar</button>
                <button type="button" id="confirm-modal-accept" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-rose-600 hover:bg-rose-500 transition-colors shadow-lg">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
    <script src="{{ asset('js/confirm-dialog.js') }}"></script>
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/password-toggle.js') }}"></script>
    @stack('scripts')
</body>
</html>
