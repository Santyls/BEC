<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BecApiException;
use App\Http\Controllers\Controller;
use App\Services\BecApiClient;
use App\Support\Paginador;
use Illuminate\Http\Request;

class CampanaController extends Controller
{
    public function __construct(private BecApiClient $api) {}

    public function index(Request $request)
    {
        $campanas = $this->api->get('/campanas/');
        $estados = collect($this->api->get('/catalogos/estados-campanas'))->keyBy('id');

        $busqueda = trim((string) $request->query('q', ''));
        if ($busqueda !== '') {
            $texto = mb_strtolower($busqueda);
            $campanas = array_values(array_filter($campanas, function ($c) use ($texto) {
                $nombre = mb_strtolower($c['nombre']);
                $descripcion = mb_strtolower($c['descripcion_objetivos']);
                return str_contains($nombre, $texto) || str_contains($descripcion, $texto);
            }));
        }

        $campanas = Paginador::paginar($campanas);

        return view('admin.campanas.index', compact('campanas', 'estados', 'busqueda'));
    }

    public function create()
    {
        return view('admin.campanas.create');
    }

    public function store(Request $request)
    {
        // estado_id NO se pide aquí a propósito: toda campaña nace "Programada"
        // (lo fuerza la API) — no tendría sentido crear una ya Activa/Finalizada.
        $datos = $request->validate([
            'nombre' => 'required|string|min:3|max:150',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'descripcion_objetivos' => 'required|string',
        ]);

        try {
            $this->api->post('/campanas/', $datos);
        } catch (BecApiException $e) {
            return back()->withInput()->with('error', $e->mensajeUsuario());
        }

        return redirect()->route('admin.campanas.index')->with('exito', 'Campaña creada correctamente.');
    }

    public function edit(int $id)
    {
        $campana = $this->api->get("/campanas/{$id}");
        $estados = $this->api->get('/catalogos/estados-campanas');
        return view('admin.campanas.edit', compact('campana', 'estados'));
    }

    public function update(Request $request, int $id)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|min:3|max:150',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'estado_id' => 'required|integer',
            'descripcion_objetivos' => 'required|string',
        ]);

        try {
            $this->api->put("/campanas/{$id}", $datos);
        } catch (BecApiException $e) {
            return back()->withInput()->with('error', $e->mensajeUsuario());
        }

        return redirect()->route('admin.campanas.index')->with('exito', 'Campaña actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        try {
            $this->api->delete("/campanas/{$id}");
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', 'Campaña marcada como finalizada.');
    }
}
