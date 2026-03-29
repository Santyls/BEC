<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AlbergueController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('BEC_API_URL', 'http://bec_api_app:5000');
    }

    public function index()
    {
        // Consumir la API usando el token almacenado en sesión
        $response = Http::withToken(session('api_token'))->get("{$this->apiUrl}/albergues/");

        if ($response->successful()) {
            $albergues = $response->json();
            return view('admin.albergues.index', compact('albergues'));
        }

        return view('admin.albergues.index', ['albergues' => [], 'error' => 'No se pudo conectar con la API para obtener los albergues.']);
    }

    public function store(Request $request)
    {
        $response = Http::withToken(session('api_token'))->post("{$this->apiUrl}/albergues/", [
            'Nombre_Albergue' => $request->nombre,
            'Capacidad_Max' => $request->capacidad ? (int)$request->capacidad : null,
            'Tel_Contacto' => (int)$request->telefono,
            // Asumimos que colonia_id mapea a un Id_Direccion existente de momento para propósitos prácticos de la UI de pruebas.
            'Id_Direccion' => (int)$request->colonia_id, 
        ]);

        if ($response->successful()) {
            return redirect()->route('admin.albergues.index')->with('success', 'Albergue creado exitosamente.');
        }

        return back()->with('error', 'Error desde la API al crear: ' . $response->body());
    }
}
