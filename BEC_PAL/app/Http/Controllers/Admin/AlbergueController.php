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
        $this->apiUrl = rtrim(env('BEC_API_URL', 'http://bec_api_app:5000'), '/');
    }

    /** GET /admin/albergues — Vista principal o JSON para fetch JS */
    public function index(Request $request)
    {
        $res = Http::withToken(session('api_token'))->get("{$this->apiUrl}/albergues/");

        if ($request->boolean('json') || $request->wantsJson()) {
            return $res->successful()
                ? response()->json($res->json())
                : response()->json([], $res->status());
        }

        return view('admin.albergues.index');
    }

    /** POST /admin/albergues — Crear albergue (proxy) */
    public function store(Request $request)
    {
        $payload = [
            'Nombre_Albergue' => $request->Nombre_Albergue,
            'Capacidad_Max'   => (int) $request->Capacidad_Max,
            'Tel_Contacto'    => (int) $request->Tel_Contacto,
            'Id_Direccion'    => (int) $request->Id_Direccion,
        ];

        $res = Http::withToken(session('api_token'))
            ->post("{$this->apiUrl}/albergues/", $payload);

        return $res->successful()
            ? response()->json(['success' => true,  'message' => 'Albergue creado exitosamente.', 'data' => $res->json()])
            : response()->json(['success' => false, 'message' => $res->json()['detail'] ?? 'Error al crear el albergue.'], $res->status());
    }

    /** PATCH /admin/albergues/{id} — Editar albergue (proxy) */
    public function patch(Request $request, $id)
    {
        $payload = array_filter([
            'Nombre_Albergue' => $request->Nombre_Albergue,
            'Capacidad_Max'   => $request->Capacidad_Max  ? (int) $request->Capacidad_Max  : null,
            'Tel_Contacto'    => $request->Tel_Contacto   ? (int) $request->Tel_Contacto   : null,
            'Id_Direccion'    => $request->Id_Direccion   ? (int) $request->Id_Direccion   : null,
        ], fn($v) => !is_null($v));

        $res = Http::withToken(session('api_token'))
            ->patch("{$this->apiUrl}/albergues/{$id}", $payload);

        return $res->successful()
            ? response()->json(['success' => true,  'message' => 'Albergue actualizado.',  'data' => $res->json()])
            : response()->json(['success' => false, 'message' => $res->json()['detail'] ?? 'Error al actualizar el albergue.'], $res->status());
    }

    /** DELETE /admin/albergues/{id} — Eliminar albergue (proxy) */
    public function destroy($id)
    {
        $res = Http::withToken(session('api_token'))
            ->delete("{$this->apiUrl}/albergues/{$id}");

        return $res->successful()
            ? response()->json(['success' => true,  'message' => 'Albergue eliminado.'])
            : response()->json(['success' => false, 'message' => $res->json()['detail'] ?? 'Error al eliminar el albergue.'], $res->status());
    }
}
