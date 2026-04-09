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
        $this->apiUrl = rtrim(env('BEC_API_URL', 'http://bec_api_app:5000'), '/');
    }

    /** GET /admin/voluntariados — Vista o JSON para fetch JS */
    public function index(Request $request)
    {
        if ($request->boolean('json') || $request->wantsJson()) {
            $res = Http::withToken(session('api_token'))->get("{$this->apiUrl}/voluntariados/");
            return $res->successful()
                ? response()->json($res->json())
                : response()->json([], $res->status());
        }

        return view('admin.voluntariados.index');
    }

    /** POST /admin/voluntariados — Crear voluntariado */
    public function store(Request $request)
    {
        $payload = [
            'Nombre_Voluntariado'   => $request->Nombre_Voluntariado,
            'Fecha_prog'            => $request->Fecha_prog,
            'Hora_inicio'           => $request->Hora_inicio,
            'Hora_Fin'              => $request->Hora_Fin,
            'Estado_Voluntariado'   => $request->Estado_Voluntariado,
            'Descripcion_Requisitos'=> $request->Descripcion_Requisitos,
            'Id_albergue'           => $request->Id_albergue  !== null ? (int) $request->Id_albergue  : null,
            'id_campana'            => $request->id_campana   !== null ? (int) $request->id_campana   : null,
            'Cupo_Max'              => $request->Cupo_Max     !== null ? (int) $request->Cupo_Max     : null,
        ];

        $res = Http::withToken(session('api_token'))
            ->post("{$this->apiUrl}/voluntariados/", $payload);

        return $res->successful()
            ? response()->json(['success' => true,  'message' => 'Voluntariado creado exitosamente.', 'data' => $res->json()])
            : response()->json(['success' => false, 'message' => $res->json()['detail'] ?? 'Error al crear el voluntariado.'], $res->status());
    }

    /** PATCH /admin/voluntariados/{id} — Editar voluntariado */
    public function patch(Request $request, $id)
    {
        // Solo enviar los campos que vienen en el request (PATCH semántico)
        $payload = [];

        $campos = ['Nombre_Voluntariado', 'Fecha_prog', 'Hora_inicio', 'Hora_Fin',
                   'Estado_Voluntariado', 'Descripcion_Requisitos', 'Id_albergue', 'id_campana', 'Cupo_Max'];

        foreach ($campos as $campo) {
            if ($request->has($campo)) {
                $val = $request->input($campo);
                // Castear enteros donde corresponde
                if (in_array($campo, ['Id_albergue', 'id_campana', 'Cupo_Max'])) {
                    $payload[$campo] = $val !== null ? (int) $val : null;
                } else {
                    $payload[$campo] = $val;
                }
            }
        }

        $res = Http::withToken(session('api_token'))
            ->patch("{$this->apiUrl}/voluntariados/{$id}", $payload);

        return $res->successful()
            ? response()->json(['success' => true,  'message' => 'Voluntariado actualizado.',  'data' => $res->json()])
            : response()->json(['success' => false, 'message' => $res->json()['detail'] ?? 'Error al actualizar.'], $res->status());
    }

    /** DELETE /admin/voluntariados/{id} — Eliminar voluntariado */
    public function destroy($id)
    {
        $res = Http::withToken(session('api_token'))
            ->delete("{$this->apiUrl}/voluntariados/{$id}");

        return $res->successful()
            ? response()->json(['success' => true,  'message' => 'Voluntariado eliminado.'])
            : response()->json(['success' => false, 'message' => $res->json()['detail'] ?? 'Error al eliminar.'], $res->status());
    }
}
