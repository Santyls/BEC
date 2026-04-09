<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BEC Admin</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { background-color: #0f172a; color: #f8fafc; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-900 relative overflow-hidden">
    <!-- Fondos decorativos -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-emerald-600/10 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-md p-8 glass rounded-2xl shadow-xl z-10 relative">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black tracking-tighter text-blue-500 uppercase">BEC<span class="text-white">_Admin</span></h1>
            <p class="text-slate-400 mt-2 text-sm">Ingresa al panel de control central</p>
        </div>

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/50 text-red-500 px-4 py-3 rounded-xl mb-6 text-sm">
                {{ $errors->first() }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/50 text-red-500 px-4 py-3 rounded-xl mb-6 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Correo Electrónico</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="mail" class="w-5 h-5 text-slate-500"></i>
                    </div>
                    <!-- Prellenado con la credencial del admin -->
                    <input type="email" name="correo" value="admin@bec.com" required
                        class="w-full pl-10 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-slate-500 outline-none transition-all">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Contraseña</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="w-5 h-5 text-slate-500"></i>
                    </div>
                    <!-- Prellenado con la credencial del admin -->
                    <input type="password" name="password" value="admin123" required
                        class="w-full pl-10 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-slate-500 outline-none transition-all">
                </div>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-blue-500/25 flex justify-center items-center">
                <span>Ingresar al Sistema</span>
                <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
            </button>
        </form>
        
        <div class="mt-8 pt-6 border-t border-slate-800 text-center">
            <p class="text-xs text-slate-500">
                Sistema BEC &copy; 2026. Todos los derechos reservados.
            </p>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
