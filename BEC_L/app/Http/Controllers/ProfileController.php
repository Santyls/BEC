<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Muestra el formulario de editar perfil.
     */
    public function edit()
    {
        $user = Auth::user();
        
        // Cargar catálogos para los <select>
        $generos = DB::table('generos')->get();
        $paises = DB::table('paises')->get();
        $nacionalidades = DB::table('nacionalidades')->get();

        return view('profile.edit', compact('user', 'generos', 'paises', 'nacionalidades'));
    }

    /**
     * Actualiza la información del usuario.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validación
        $request->validate([
            'nombre' => 'required|string|max:100',
            'alias' => 'required|string|max:50|unique:usuarios,alias,' . $user->id,
            'edad' => 'required|integer|min:18|max:120',
            'genero_id' => 'required|exists:generos,id',
            'nacionalidad_id' => 'required|exists:nacionalidades,id',
            'pais_id' => 'required|exists:paises,id',
            'telefono' => 'nullable|string|max:20',
        ], [
            'edad.min' => 'Debes ser mayor de 18 años para registrarte.',
            'alias.unique' => 'Ese alias ya está en uso.'
        ]);

        // Actualizar Usuario
        // Usamos el método update del modelo Eloquent
        $user->update([
            'nombre' => $request->nombre,
            'alias' => $request->alias,
            'edad' => $request->edad,
            'genero_id' => $request->genero_id,
            'nacionalidad_id' => $request->nacionalidad_id,
            'pais_id' => $request->pais_id,
            'telefono' => $request->telefono,
        ]);

        return redirect()->route('home')->with('success', '¡Perfil actualizado correctamente!');
    }
}