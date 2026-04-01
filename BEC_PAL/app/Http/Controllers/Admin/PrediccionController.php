<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PrediccionController extends Controller
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim(env('BEC_API_URL', 'http://bec_api_app:5000'), '/');
    }

    /**
     * GET /admin/dashboard
     * Carga el dashboard principal, inyectando albergues reales para el gráfico.
     */
    public function dashboard()
    {
        $res = Http::withToken(session('api_token'))->get("{$this->apiUrl}/albergues/");
        $albergues = $res->successful() ? $res->json() : [];
        return view('admin.dashboard', compact('albergues'));
    }

    /**
     * GET /admin/predicciones
     * Carga la vista principal, inyectando la lista de albergues.
     */
    public function index()
    {
        $res = Http::withToken(session('api_token'))->get("{$this->apiUrl}/albergues/");

        $albergues = $res->successful() ? $res->json() : [];

        return view('admin.predicciones.index', compact('albergues'));
    }

    /**
     * GET /admin/predicciones/datos?albergue_id={id}
     * Endpoint AJAX: hace proxy al endpoint de ML de BEC_API y retorna el JSON al frontend.
     */
    public function getDatosPrediccion(Request $request)
    {
        $albergueId = $request->query('albergue_id');

        if (!$albergueId) {
            return response()->json([
                'error' => 'Debes seleccionar un albergue para ver sus predicciones.'
            ], 422);
        }

        $res = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/predicciones/albergue/{$albergueId}");

        if ($res->successful()) {
            return response()->json($res->json());
        }

        $detail = $res->json()['detail'] ?? 'Error al obtener datos del modelo predictivo.';
        return response()->json(['error' => $detail], $res->status());
    }
}
