<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - BEC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { background-color: #0f172a; color: #f8fafc; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar-item-active { background: rgba(59, 130, 246, 0.1); color: #60a5fa; border-left: 4px solid #3b82f6; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">
    <!-- BARRA LATERAL -->
    <aside class="w-64 glass flex flex-col border-r border-slate-800">
        <div class="p-6">
            <h1 class="text-xl font-black tracking-tighter text-blue-500 uppercase">BEC<span class="text-white">_Admin</span></h1>
        </div>
        
        <nav class="flex-1 overflow-y-auto px-4 space-y-2 mt-2 pb-6">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active' : 'text-slate-400 hover:bg-slate-800' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i> Dashboard
            </a>
            
            <div class="pt-4 pb-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-3">Gestión Principal</span>
            </div>
            
            <a href="{{ route('admin.usuarios.index') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.usuarios.*') ? 'sidebar-item-active' : 'text-slate-400 hover:bg-slate-800' }}">
                <i data-lucide="users" class="w-5 h-5 mr-3"></i> Usuarios & Roles
            </a>
            <a href="{{ route('admin.albergues.index') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.albergues.*') ? 'sidebar-item-active' : 'text-slate-400 hover:bg-slate-800' }}">
                <i data-lucide="home" class="w-5 h-5 mr-3"></i> Albergues
            </a>
            <a href="{{ route('admin.campanas.index') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.campanas.*') ? 'sidebar-item-active' : 'text-slate-400 hover:bg-slate-800' }}">
                <i data-lucide="megaphone" class="w-5 h-5 mr-3"></i> Campañas
            </a>
            <a href="{{ route('admin.voluntariados.index') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.voluntariados.*') ? 'sidebar-item-active' : 'text-slate-400 hover:bg-slate-800' }}">
                <i data-lucide="calendar-heart" class="w-5 h-5 mr-3"></i> Voluntariados
            </a>
            <a href="{{ route('admin.donaciones.index') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.donaciones.*') ? 'sidebar-item-active' : 'text-slate-400 hover:bg-slate-800' }}">
                <i data-lucide="heart-handshake" class="w-5 h-5 mr-3"></i> Donaciones
            </a>

            <div class="pt-4 pb-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-3">Análisis</span>
            </div>

            <a href="{{ route('admin.reportes.index') }}" class="flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.reportes.*') ? 'sidebar-item-active' : 'text-slate-400 hover:bg-slate-800' }}">
                <i data-lucide="pie-chart" class="w-5 h-5 mr-3"></i> Reportes
            </a>
        </nav>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-1 flex flex-col h-screen">
        <header class="h-16 flex items-center px-8 border-b border-slate-800 justify-between glass z-10">
            <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">@yield('header_title', 'Inicio')</h2>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-200">Admin General</p>
                    <p class="text-[10px] text-emerald-400">● En línea</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center font-bold text-white shadow-lg">A</div>
            </div>
        </header>
        
        <div class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>