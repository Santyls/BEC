@extends('layouts.app')

@section('title', 'Admin | Donaciones')

@section('content')
<div class="relative min-h-screen w-full bg-bec-dark overflow-hidden font-sans text-white">

    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0f172a] via-[#1e3a5f] to-black"></div>
        <div class="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] bg-green-500/10 rounded-full blur-[100px] animate-blob"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-bec-primary/10 rounded-full blur-[100px] animate-blob animation-delay-2000"></div>
    </div>

    <nav class="fixed w-full z-50 top-0 transition-all duration-300 p-4">
        <div class="max-w-7xl mx-auto bg-white/5 backdrop-blur-xl border border-white/10 rounded-full px-6 py-3 flex flex-wrap justify-between items-center shadow-2xl">
            <div class="flex items-center gap-3">
                <div class="bg-bec-primary/80 p-1.5 rounded-lg shadow-lg">
                    <span class="text-lg font-bold text-white font-display tracking-wide">BEC</span>
                </div>
                <span class="text-xs font-bold text-blue-200 uppercase tracking-[0.2em] hidden sm:block">Admin Panel</span>
            </div>
            <div class="hidden lg:flex items-center gap-1 p-1 bg-black/20 rounded-full border border-white/5">
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-1.5 text-xs font-medium text-blue-200 hover:text-white hover:bg-white/5 rounded-full transition">Dashboard</a>
                <a href="#" class="px-4 py-1.5 text-xs font-medium text-blue-200 hover:text-white hover:bg-white/5 rounded-full transition">+ Usuario</a>
                <a href="{{ route('admin.donations') }}" class="px-4 py-1.5 text-xs font-medium text-white bg-white/10 rounded-full shadow-sm border border-white/10 transition">Donaciones</a>
                <a href="{{ route('admin.volunteering') }}" class="px-4 py-1.5 text-xs font-medium text-blue-200 hover:text-white hover:bg-white/5 rounded-full transition">Voluntariados</a>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-bec-light to-bec-primary p-[1px]"><div class="w-full h-full rounded-full bg-bec-dark flex items-center justify-center"><span class="text-xs font-bold">AD</span></div></div>
            </div>
        </div>
    </nav>

    <main class="relative z-10 flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-12">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white font-display mb-1">Gestión de Donaciones</h1>
            <p class="text-blue-200 text-sm font-light">Registra, edita y monitorea el inventario de ayudas.</p>
        </div>

 
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl mb-12 animate-fade-in relative overflow-hidden">

            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>

            <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-4">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-8 bg-green-400 rounded-full"></span>
                    <span id="form-title">Nueva Donación</span>
                </h2>
                <button type="button" onclick="resetForm()" class="text-xs text-blue-300 hover:text-white transition underline hidden" id="btn-cancel-edit">
                    Cancelar Edición
                </button>
            </div>

            <form action="#" method="POST" id="donationForm"> 
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    
                 
                    <div>
                        <label class="block text-xs font-bold text-blue-200 uppercase tracking-wider mb-2">Usuario (ID o Alias)</label>
                        <div class="relative">
                            <input type="text" id="alias" name="alias" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-green-400/50 transition" placeholder="Buscar usuario...">
                            <div class="absolute right-3 top-3 text-white/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                 
                    <div>
                        <label class="block text-xs font-bold text-blue-200 uppercase tracking-wider mb-2">Categoría</label>
                        <select id="categoria" name="categoria" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-green-400/50 transition [&>option]:text-black">
                            <option value="" disabled selected>Seleccionar...</option>
                            <option value="Alimentos">Alimentos</option>
                            <option value="Ropa">Ropa</option>
                            <option value="Higiene">Higiene</option>
                            <option value="Medicamentos">Medicamentos</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                  
                    <div>
                        <label class="block text-xs font-bold text-blue-200 uppercase tracking-wider mb-2">Marca</label>
                        <input type="text" id="marca" name="marca" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-green-400/50 transition" placeholder="Ej. Verde Valle">
                    </div>

                   
                    <div>
                        <label class="block text-xs font-bold text-blue-200 uppercase tracking-wider mb-2">Cantidad</label>
                        <input type="number" id="cantidad" name="cantidad" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-green-400/50 transition" placeholder="0">
                    </div>

                   
                    <div>
                        <label class="block text-xs font-bold text-blue-200 uppercase tracking-wider mb-2">Unidad de Medida</label>
                        <select id="unidad" name="unidad" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-green-400/50 transition [&>option]:text-black">
                            <option value="piezas">Piezas</option>
                            <option value="kg">Kilogramos (kg)</option>
                            <option value="litros">Litros (L)</option>
                            <option value="cajas">Cajas</option>
                            <option value="kits">Kits</option>
                        </select>
                    </div>

                
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-blue-200 uppercase tracking-wider mb-2">Descripción Detallada</label>
                        <textarea id="descripcion" name="descripcion" rows="2" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-green-400/50 transition resize-none" placeholder="Detalles del estado, caducidad o especificaciones..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" id="btn-submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-green-500/20 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span id="btn-text">Añadir Donación</span>
                    </button>
                </div>
            </form>
        </div>

       
        <div class="animate-slide-up animation-delay-500">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs uppercase text-blue-300 font-bold tracking-wider bg-black/20">
                                <th class="p-5 border-b border-white/10">ID</th>
                                <th class="p-5 border-b border-white/10">Donante</th>
                                <th class="p-5 border-b border-white/10">Categoría</th>
                                <th class="p-5 border-b border-white/10 w-1/3">Descripción</th>
                                <th class="p-5 border-b border-white/10">Marca</th>
                                <th class="p-5 border-b border-white/10 text-center">Cantidad</th>
                                <th class="p-5 border-b border-white/10 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm text-blue-100">
                            @foreach($donaciones as $donacion)
                            <tr class="hover:bg-white/5 transition duration-150 group" id="row-{{ $donacion->id }}">
                                <td class="p-5 font-mono text-white/50 text-xs">#{{ $donacion->id }}</td>
                                <td class="p-5 font-bold text-white">{{ $donacion->alias }}</td>
                                <td class="p-5">
                                    <span class="px-2 py-1 rounded-md bg-white/5 border border-white/10 text-xs font-medium">
                                        {{ $donacion->categoria }}
                                    </span>
                                </td>
                                <td class="p-5 text-white/80">{{ $donacion->descripcion }}</td>
                                <td class="p-5 text-blue-200">{{ $donacion->marca }}</td>
                                <td class="p-5 text-center font-bold text-white">
                                    {{ $donacion->cantidad }} <span class="text-xs font-normal text-blue-300">{{ $donacion->unidad }}</span>
                                </td>
                                <td class="p-5 text-right">
                                    <div class="flex justify-end gap-2">
                                     
                                        <button onclick="editDonation({{ json_encode($donacion) }})" class="p-2 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white border border-blue-500/20 transition" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                       
                                        <button class="p-2 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/20 transition" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div>

<script>
    function editDonation(data) {

        window.scrollTo({ top: 0, behavior: 'smooth' });

        document.getElementById('form-title').innerText = 'Editar Donación #' + data.id;
        document.getElementById('btn-text').innerText = 'Guardar Cambios';
        document.getElementById('btn-submit').classList.replace('bg-green-500', 'bg-blue-500');
        document.getElementById('btn-submit').classList.replace('hover:bg-green-600', 'hover:bg-blue-600');
        document.getElementById('btn-cancel-edit').classList.remove('hidden');

        document.getElementById('alias').value = data.alias;
        document.getElementById('categoria').value = data.categoria;
        document.getElementById('marca').value = data.marca;
        document.getElementById('cantidad').value = data.cantidad;
        document.getElementById('unidad').value = data.unidad;
        document.getElementById('descripcion').value = data.descripcion;

        const inputs = document.querySelectorAll('#donationForm input, #donationForm select, #donationForm textarea');
        inputs.forEach(input => {
            input.classList.add('ring-2', 'ring-blue-500/50');
            setTimeout(() => input.classList.remove('ring-2', 'ring-blue-500/50'), 1000);
        });
    }

    function resetForm() {
        document.getElementById('donationForm').reset();
        
        document.getElementById('form-title').innerText = 'Nueva Donación';
        document.getElementById('btn-text').innerText = 'Añadir Donación';
        document.getElementById('btn-submit').classList.replace('bg-blue-500', 'bg-green-500');
        document.getElementById('btn-submit').classList.replace('hover:bg-blue-600', 'hover:bg-green-600');
        document.getElementById('btn-cancel-edit').classList.add('hidden');
    }
</script>
@endsection