<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UsuarioController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim(env('BEC_API_URL', 'http://bec_api_app:5000'), '/');
    }

    /**
     * Muestra la lista de usuarios.
     * - Sin parámetros  → devuelve la vista Blade.
     * - Con ?json=1     → actúa como proxy y devuelve los usuarios como JSON
     *                     para que el Fetch del frontend pueda consumirlos
     *                     sin problemas de CORS.
     */
    public function index(Request $request)
    {
        if ($request->query('json') === '1') {
            $response = Http::withToken(session('api_token'))
                ->get("{$this->apiUrl}/usuarios/");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([], $response->status());
        }

        return view('admin.usuarios.index', [
            'api_token' => session('api_token'),
            'api_url'   => $this->apiUrl,
        ]);
    }

    /**
     * Crea un nuevo usuario (POST).
     * Llamado via Fetch desde el modal de la vista.
     */
    public function store(Request $request)
    {
        $payload = [
            'Nombre'       => $request->Nombre,
            'Apellido_P'   => $request->Apellido_P,
            'Apellido_M'   => $request->Apellido_M,
            'Correo'       => $request->Correo,
            'Password'     => $request->Password,
            'Edad'         => (int) $request->Edad,
            'Tel'          => (int) $request->Tel,
            'Id_Rol'       => (int) $request->Id_Rol,
            'Id_Genero'    => (int) $request->Id_Genero,
            'Id_Direccion' => (int) $request->Id_Direccion,
        ];

        if ($request->Id_Albergue) {
            $payload['Id_Albergue'] = (int) $request->Id_Albergue;
        }

        $response = Http::withToken(session('api_token'))
            ->post("{$this->apiUrl}/usuarios/", $payload);

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Usuario creado exitosamente.', 'data' => $response->json()]);
        }

        return response()->json([
            'success' => false,
            'message' => $response->json()['detail'] ?? 'Error al crear el usuario.',
        ], $response->status());
    }

    /**
     * Actualiza un usuario existente (PUT).
     * Llamado via Fetch desde el modal de edición.
     */
    public function update(Request $request, $id)
    {
        $payload = [
            'Nombre'       => $request->Nombre,
            'Apellido_P'   => $request->Apellido_P,
            'Apellido_M'   => $request->Apellido_M,
            'Correo'       => $request->Correo,
            'Password'     => $request->Password,
            'Edad'         => (int) $request->Edad,
            'Tel'          => (int) $request->Tel,
            'Id_Rol'       => (int) $request->Id_Rol,
            'Id_Genero'    => (int) $request->Id_Genero,
            'Id_Direccion' => (int) $request->Id_Direccion,
        ];

        if ($request->Id_Albergue) {
            $payload['Id_Albergue'] = (int) $request->Id_Albergue;
        }

        $response = Http::withToken(session('api_token'))
            ->put("{$this->apiUrl}/usuarios/{$id}", $payload);

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente.']);
        }

        return response()->json([
            'success' => false,
            'message' => $response->json()['detail'] ?? 'Error al actualizar el usuario.',
        ], $response->status());
    }

    /**
     * Elimina un usuario (DELETE).
     * Llamado via Fetch desde la tabla.
     */
    public function destroy($id)
    {
        $response = Http::withToken(session('api_token'))
            ->delete("{$this->apiUrl}/usuarios/{$id}");

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
        }

        return response()->json([
            'success' => false,
            'message' => $response->json()['detail'] ?? 'Error al eliminar el usuario.',
        ], $response->status());
    }
}
