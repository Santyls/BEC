<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    
    public function showLogin() { return view('admin.login'); }
    public function login() { return redirect()->route('admin.dashboard')->with('success', '¡Bienvenido!'); }
    public function dashboard() {
        $stats = ['usuarios' => 42, 'programas' => 12, 'donaciones' => 35, 'voluntariados' => 8];
        $usuarios = [ (object)['id'=>101,'alias'=>'SuperVol','email'=>'a@bec.org','fecha'=>'2025-09-15','verificado'=>true,'donaciones'=>5,'voluntariados'=>2] ];
        return view('admin.dashboard', compact('stats', 'usuarios'));
    }

    //Vista de Administración de Donaciones
     
    public function donations()
    {
        // Mock Data: Donaciones detalladas
        $donaciones = [
            (object)[
                'id' => 845,
                'alias' => 'RestauranteLaCarreta',
                'usuario_id' => 105,
                'categoria' => 'Alimentos',
                'descripcion' => '20kg de Arroz y Frijol en saco',
                'marca' => 'Verde Valle',
                'cantidad' => 20,
                'unidad' => 'kg',
                'fecha' => '2025-09-12'
            ],
            (object)[
                'id' => 846,
                'alias' => 'AnaG',
                'usuario_id' => 104,
                'categoria' => 'Ropa',
                'descripcion' => 'Chamarras de invierno talla M',
                'marca' => 'Zara',
                'cantidad' => 15,
                'unidad' => 'piezas',
                'fecha' => '2025-09-15'
            ],
            (object)[
                'id' => 847,
                'alias' => 'SuperVoluntario',
                'usuario_id' => 101,
                'categoria' => 'Higiene',
                'descripcion' => 'Kits de aseo personal',
                'marca' => 'Colgate',
                'cantidad' => 50,
                'unidad' => 'kits',
                'fecha' => '2025-10-03'
            ],
        ];

        return view('admin.donations', compact('donaciones'));
    }

    
    public function volunteering() {
        $programas = []; $voluntarios = [];
        return view('admin.volunteering', compact('programas', 'voluntarios'));
    }
}