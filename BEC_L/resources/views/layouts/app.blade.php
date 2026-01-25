<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEC | @yield('title', 'Blue Earth Coders')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bec: {
                            dark: '#1E3A5F',     
                            primary: '#3B82F6',  
                            light: '#60A5FA',  
                            pale: '#DBEAFE',     
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Poppins', 'sans-serif'],
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; }
        
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1E3A5F; }
        ::-webkit-scrollbar-thumb { background: #3B82F6; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #60A5FA; }
    </style>
</head>
<body class="antialiased text-gray-800 bg-gray-100">

    @unless(request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('verification.notice') || request()->is('admin*'))
        
        <nav class="fixed w-full z-50 top-0 transition-all duration-300 p-4">
            <div class="max-w-7xl mx-auto bg-bec-dark/90 backdrop-blur-md border border-white/10 rounded-full px-6 py-3 flex justify-between items-center shadow-xl">
                
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="text-xl font-bold tracking-wide font-display text-white group-hover:text-bec-light transition">BEC</span>
                </a>

                <div class="hidden md:flex gap-8 text-sm font-medium text-blue-200">
                    <a href="{{ route('home') }}" class="hover:text-white transition relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-0.5 after:bg-bec-light after:transition-all hover:after:w-full">Inicio</a>
                    <a href="#" class="hover:text-white transition">Donaciones</a>
                    <a href="#" class="hover:text-white transition">Voluntariado</a>
                </div>

              
                <div class="relative">
                    @auth
                        
                        <button onclick="toggleDropdown()" class="flex items-center gap-3 focus:outline-none group">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-semibold text-white group-hover:text-bec-light transition">{{ Auth::user()->alias }}</p>
                                <p class="text-[10px] text-blue-300 uppercase tracking-wider">Usuario</p>
                            </div>
                          
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-bec-primary to-purple-600 flex items-center justify-center text-white font-bold shadow-lg border-2 border-white/10 group-hover:border-bec-light transition">
                                {{ substr(Auth::user()->alias, 0, 1) }}
                            </div>
                        </button>

                       
                        <div id="userDropdown" class="hidden absolute right-0 mt-4 w-56 bg-bec-dark/95 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl py-2 transform origin-top-right transition-all z-50">
                            
                            <div class="absolute -top-2 right-4 w-4 h-4 bg-bec-dark border-t border-l border-white/10 transform rotate-45"></div>

                            <div class="px-4 py-3 border-b border-white/10">
                                <p class="text-sm text-white font-bold">Mi Cuenta</p>
                                <p class="text-xs text-blue-300 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-blue-100 hover:bg-white/10 hover:text-white transition flex items-center gap-2">
                                <span>👤</span> Completar Perfil
                            </a>
                            <a href="{{ route('address.create') }}" class="block px-4 py-2 text-sm text-blue-100 hover:bg-white/10 hover:text-white transition flex items-center gap-2">
                                <span>🏠</span> Mis Direcciones
                            </a>
                            
                            <div class="border-t border-white/10 my-1"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-300 hover:bg-red-500/20 hover:text-red-200 transition flex items-center gap-2">
                                    <span>🚪</span> Cerrar Sesión
                                </button>
                            </form>
                        </div>

                    @else
                        <a href="{{ route('login') }}" class="bg-bec-primary hover:bg-blue-600 text-white px-6 py-2 rounded-full text-sm font-semibold transition shadow-lg hover:shadow-bec-primary/50 border border-white/10">
                            Iniciar Sesión
                        </a>
                    @endauth
                </div>
            </div>
        </nav>
    @endunless 

    @if(session('success'))
        <div class="fixed top-24 right-4 z-50 bg-green-500/90 backdrop-blur-md text-white px-6 py-3 rounded-xl shadow-2xl border border-white/20 animate-fade-in flex items-center gap-3">
            <span></span> {{ session('success') }}
        </div>
    @endif

    <div class="pt-0">
        @yield('content')
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        window.onclick = function(event) {
            if (!event.target.closest('.relative')) {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            }
        }
    </script>
</body>
</html>