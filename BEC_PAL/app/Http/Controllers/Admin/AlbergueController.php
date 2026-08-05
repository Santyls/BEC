<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BecApiException;
use App\Http\Controllers\Controller;
use App\Services\BecApiClient;
use App\Support\Paginador;
use Illuminate\Http\Request;

class AlbergueController extends Controller
{
    public function __construct(private BecApiClient $api) {}

    public function index(Request $request)
    {
        $albergues = $this->api->get('/albergues/', ['solo_activos' => false]);

        $busqueda = trim((string) $request->query('q', ''));
        if ($busqueda !== '') {
            $texto = mb_strtolower($busqueda);
            $albergues = array_values(array_filter($albergues, function ($a) use ($texto) {
                $nombre = mb_strtolower($a['nombre']);
                $colonia = mb_strtolower($a['direccion']['colonia'] ?? '');
                $municipio = mb_strtolower($a['direccion']['municipio'] ?? '');
                return str_contains($nombre, $texto) || str_contains($colonia, $texto) || str_contains($municipio, $texto);
            }));
        }

        $albergues = Paginador::paginar($albergues);

        return view('admin.albergues.index', compact('albergues', 'busqueda'));
    }

    public function create()
    {
        $estados = $this->api->get('/catalogos/estados');
        return view('admin.albergues.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $datos = $this->validarDatos($request);

        try {
            $this->api->post('/albergues/', $datos);
        } catch (BecApiException $e) {
            return back()->withInput()->with('error', $e->mensajeUsuario());
        }

        return redirect()->route('admin.albergues.index')->with('exito', 'Albergue registrado correctamente.');
    }

    public function edit(int $id)
    {
        $albergue = $this->api->get("/albergues/{$id}");
        $estados = $this->api->get('/catalogos/estados');
        return view('admin.albergues.edit', compact('albergue', 'estados'));
    }

    public function update(Request $request, int $id)
    {
        $datos = $this->validarDatos($request);

        try {
            $this->api->put("/albergues/{$id}", $datos);
        } catch (BecApiException $e) {
            return back()->withInput()->with('error', $e->mensajeUsuario());
        }

        return redirect()->route('admin.albergues.index')->with('exito', 'Albergue actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        try {
            $this->api->delete("/albergues/{$id}");
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', 'Albergue desactivado correctamente. Podrás reactivarlo durante 30 días.');
    }

    public function reactivar(int $id)
    {
        try {
            $this->api->post("/albergues/{$id}/reactivar");
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', 'Albergue reactivado correctamente.');
    }

    public function eliminarPermanente(int $id)
    {
        try {
            $this->api->delete("/albergues/{$id}/permanente");
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', 'Albergue eliminado permanentemente.');
    }

    private function validarDatos(Request $request): array
    {
        $datos = $request->validate([
            'nombre' => 'required|string|min:3|max:150',
            'capacidad_max' => 'required|integer|min:1',
            'telefono' => ['required', 'string', 'regex:/^\d{10}$/'],
            'estado_id' => 'required|integer',
            'municipio' => 'required|string|max:100',
            'colonia' => 'required|string|max:150',
            'calle' => 'required|string|max:150',
            'numero_exterior' => 'required|string|max:20',
            'numero_interior' => 'nullable|string|max:20',
            'codigo_postal' => ['required', 'string', 'regex:/^\d{5}$/'],
        ], [
            'telefono.regex' => 'El teléfono debe tener 10 dígitos, sin espacios ni guiones.',
            'codigo_postal.regex' => 'El código postal debe tener 5 dígitos.',
        ]);

        return [
            'nombre' => $datos['nombre'],
            'capacidad_max' => $datos['capacidad_max'],
            'telefono' => $datos['telefono'],
            'direccion' => [
                'estado_id' => $datos['estado_id'],
                'municipio' => $datos['municipio'],
                'colonia' => $datos['colonia'],
                'calle' => $datos['calle'],
                'numero_exterior' => $datos['numero_exterior'],
                'numero_interior' => $datos['numero_interior'] ?? null,
                'codigo_postal' => $datos['codigo_postal'],
            ],
        ];
    }
}
