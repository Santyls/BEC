<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <script>
        // Mismo theme-init que layouts/admin.blade.php: si el usuario ya activo el
        // modo oscuro desde el panel, respetarlo tambien aqui (por defecto es claro).
        (function () {
            try {
                if (localStorage.getItem('bec-theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - BEC Admin</title>
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
    </style>
</head>
<body class="h-screen flex items-center justify-center">
    <div class="w-full max-w-sm glass rounded-2xl p-8 shadow-xl">
        <div class="text-center mb-6">
            <h1 class="text-xl font-black tracking-tighter text-blue-500 uppercase">BEC<span class="text-slate-900 dark:text-white">_Admin</span></h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Portal de administración</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-400 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Correo</label>
                <input type="email" name="correo" value="{{ old('correo') }}" required autofocus
                    class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Contraseña</label>
                <input type="password" name="password" required
                    class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <button type="submit"
                class="w-full bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl py-2.5 font-semibold text-white shadow-lg shadow-blue-500/20 hover:opacity-90 transition-opacity">
                Iniciar sesión
            </button>
        </form>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
