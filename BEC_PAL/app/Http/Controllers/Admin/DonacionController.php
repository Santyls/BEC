<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DonacionController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim(env('BEC_API_URL', 'http://bec_api_app:5000'), '/');
    }

    public function index()
    {
        $response = Http::withToken(session('api_token'))->get("{$this->apiUrl}/donaciones/");

        if ($response->successful()) {
            return view('admin.donaciones.index', ['donaciones' => $response->json()]);
        }

        return view('admin.donaciones.index', ['donaciones' => [], 'error' => 'No se pudo conectar con la API para obtener las donaciones.']);
    }

    public function store(Request $request)
    {
        $unidad = match($request->unidad_medicion) {
            'piezas' => 1,
            'kg' => 2,
            'litros' => 3,
            'cajas' => 4,
            default => 1,
        };

        $condicion = match($request->condicion_id) {
            '1' => 'Nuevo',
            '2' => 'Bueno',
            '3' => 'Regular',
            default => 'Nuevo',
        };

        $payload = [
            'id_Categoria' => (int)$request->categoria_id,
            'Id_Condicion' => $condicion,
            'Cantidad' => (float)$request->cantidad,
            'Id_Unidad' => $unidad,
            'Marca' => $request->marca,
            'Id_Albergue' => (int)$request->albergue_destino_id
        ];

        // Solo agregar Id_Usuario si se seleccionó uno en el select
        if ($request->usuario_id) {
            $payload['Id_Usuario'] = (int)$request->usuario_id;
        }

        $response = Http::withToken(session('api_token'))->post("{$this->apiUrl}/donaciones/", $payload);

        if ($response->successful()) {
            return redirect()->route('admin.donaciones.index')->with('success', 'Donativo creado exitosamente.');
        }

        return back()->with('error', 'Error desde la API al registrar donación: ' . $response->body());
    }
}
