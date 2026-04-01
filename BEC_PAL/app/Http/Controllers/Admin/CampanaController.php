<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CampanaController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('BEC_API_URL', 'http://bec_api_app:5000');
    }

    public function index()
    {
        // Consumir la API usando el token
        $response = Http::withToken(session('api_token'))->get("{$this->apiUrl}/campanas/");

        if ($response->successful()) {
            $campanas = $response->json();
            return view('admin.campanas.index', compact('campanas'));
        }

        return view('admin.campanas.index', ['campanas' => [], 'error' => 'No se pudo conectar con la API para obtener las campañas.']);
    }

    public function store(Request $request)
    {
        $response = Http::withToken(session('api_token'))->post("{$this->apiUrl}/campanas/", [
            'Nombre_Campana' => $request->nombre,
            'Fecha_Inicio' => $request->fecha_inicio,
            'Fecha_Fin' => $request->fecha_fin,
            'id_Estado_campana' => (int)$request->estado,
            'Descripcion_Objetivos' => $request->descripcion,
        ]);

        if ($response->successful()) {
            return redirect()->route('admin.campanas.index')->with('success', 'Campaña creada exitosamente.');
        }

        return back()->with('error', 'Error desde la API al crear: ' . $response->body());
    }
}
