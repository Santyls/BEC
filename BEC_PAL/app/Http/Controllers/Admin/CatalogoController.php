<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Proxy hacia los endpoints de catálogos de BEC_API:
 * estados, municipios, colonias y direcciones.
 * Evita problemas de CORS al pasar el JWT desde la sesión de Laravel.
 */
class CatalogoController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim(env('BEC_API_URL', 'http://bec_api_app:5000'), '/');
    }

    /** GET /admin/catalogos/estados */
    public function estados()
    {
        $res = Http::withToken(session('api_token'))->get("{$this->apiUrl}/estados");
        return $res->successful()
            ? response()->json($res->json())
            : response()->json([], $res->status());
    }

    /** GET /admin/catalogos/estados/{id}/municipios */
    public function municipios($idEstado)
    {
        $res = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/estados/{$idEstado}/municipios");
        return $res->successful()
            ? response()->json($res->json())
            : response()->json([], $res->status());
    }

    /** GET /admin/catalogos/municipios/{id}/colonias */
    public function colonias($idMunicipio)
    {
        $res = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/municipios/{$idMunicipio}/colonias");
        return $res->successful()
            ? response()->json($res->json())
            : response()->json([], $res->status());
    }

    /** POST /admin/catalogos/direcciones */
    public function storeDireccion(Request $request)
    {
        $res = Http::withToken(session('api_token'))
            ->post("{$this->apiUrl}/direcciones", [
                'Id_Colonia'  => (int) $request->Id_Colonia,
                'Calle'       => $request->Calle,
                'No_exterior' => $request->No_exterior,
            ]);

        return $res->successful()
            ? response()->json(['success' => true, 'data' => $res->json()])
            : response()->json([
                'success' => false,
                'message' => $res->json()['detail'] ?? 'Error al crear la dirección.',
              ], $res->status());
    }

    /** GET /admin/catalogos/albergues */
    public function listarAlbergues()
    {
        $res = Http::withToken(session('api_token'))->get("{$this->apiUrl}/albergues");
        return $res->successful()
            ? response()->json($res->json())
            : response()->json([], $res->status());
    }
}
