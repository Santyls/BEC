<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VoluntariadoController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        // En docker, el contenedor backend se llama bec_api_app y expone el 5000
        $this->apiUrl = rtrim(env('BEC_API_URL', 'http://bec_api_app:5000'), '/');
    }

    public function index()
    {
        $response = Http::withToken(session('api_token'))->get("{$this->apiUrl}/voluntariados/");

        if ($response->successful()) {
            return view('admin.voluntariados.index', ['voluntariados' => $response->json()]);
        }

        return view('admin.voluntariados.index', ['voluntariados' => [], 'error' => 'No se pudo conectar con la API para obtener los voluntariados.']);
    }

    public function store(Request $request)
    {
        $payload = [
            'Nombre_Voluntariado' => $request->nombre,
            'Requisitos' => $request->descripcion ?? 'N/A',
            'Id_Albergue' => (int)$request->albergue_id,
            'Fecha_Voluntariado' => $request->fecha,
            'Hora_Inicio' => $request->hora_inicio,
            'Hora_Fin' => $request->hora_fin,
            'Cupo_Maximo' => (int)$request->cupo,
        ];

        $response = Http::withToken(session('api_token'))->post("{$this->apiUrl}/voluntariados/", $payload);

        if ($response->successful()) {
            return redirect()->route('admin.voluntariados.index')->with('success', 'Voluntariado creado exitosamente.');
        }

        return back()->with('error', 'Error desde la API al crear el voluntariado: ' . $response->body());
    }
}
