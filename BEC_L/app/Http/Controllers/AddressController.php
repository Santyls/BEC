<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Address;

class AddressController extends Controller
{
    public function create()
    {
        $paises = DB::table('paises')->get();
        return view('profile.address', compact('paises'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pais_id' => 'required|exists:paises,id',
            'estado' => 'required|string|max:100',
            'municipio' => 'required|string|max:100',
            'colonia' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:10',
            'calle' => 'required|string|max:150',
            'num_exterior' => 'required|string|max:20',
            'num_interior' => 'nullable|string|max:20',
        ]);

        Address::updateOrCreate(
            ['usuario_id' => Auth::id()],
            $request->all()
        );

        return redirect()->route('home')->with('success', '¡Dirección guardada correctamente!');
    }
}