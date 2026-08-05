<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BecApiException;
use App\Http\Controllers\Controller;
use App\Services\BecApiClient;
use App\Support\Paginador;
use Illuminate\Http\Request;

class DonacionController extends Controller
{
    public function __construct(private BecApiClient $api) {}

    public function index(Request $request)
    {
        $filtros = [];
        if ($request->filled('categoria_id')) {
            $filtros['categoria_id'] = $request->query('categoria_id');
        }

        $donaciones = $this->api->get('/donaciones/', $filtros);
        $categorias = collect($this->api->get('/catalogos/categorias'))->keyBy('id');
        $condiciones = collect($this->api->get('/catalogos/condiciones'))->keyBy('id');
        $unidades = collect($this->api->get('/catalogos/unidades'))->keyBy('id');

        $busqueda = trim((string) $request->query('q', ''));
        if ($busqueda !== '') {
            $texto = mb_strtolower($busqueda);
            $donaciones = array_values(array_filter($donaciones, function ($d) use ($texto, $categorias) {
                $categoria = mb_strtolower($categorias[$d['categoria_id']]['nombre'] ?? '');
                $marca = mb_strtolower($d['marca'] ?? '');
                $albergue = mb_strtolower($d['albergue']['nombre'] ?? '');
                return str_contains($categoria, $texto) || str_contains($marca, $texto) || str_contains($albergue, $texto);
            }));
        }

        $donaciones = Paginador::paginar($donaciones);

        return view('admin.donaciones.index', compact('donaciones', 'categorias', 'condiciones', 'unidades', 'busqueda'));
    }

    public function create()
    {
        $usuarios = $this->api->get('/usuarios/', ['limit' => 200]);
        $categorias = $this->api->get('/catalogos/categorias');
        $condiciones = $this->api->get('/catalogos/condiciones');
        $unidades = $this->api->get('/catalogos/unidades');
        $albergues = $this->api->get('/albergues/');
        return view('admin.donaciones.create', compact('usuarios', 'categorias', 'condiciones', 'unidades', 'albergues'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'usuario_id' => 'nullable|integer',
            'categoria_id' => 'required|integer',
            'condicion_id' => 'required|integer',
            'cantidad' => 'required|numeric|min:0.01',
            'unidad_id' => 'required|integer',
            'marca' => 'nullable|string|max:100',
            'albergue_id' => 'required|integer',
        ]);
        $datos['usuario_id'] = empty($datos['usuario_id']) ? null : $datos['usuario_id'];

        try {
            $this->api->post('/donaciones/', $datos);
        } catch (BecApiException $e) {
            return back()->withInput()->with('error', $e->mensajeUsuario());
        }

        return redirect()->route('admin.donaciones.index')->with('exito', 'Donación registrada correctamente.');
    }

    public function edit(int $id)
    {
        try {
            $donacion = $this->api->get("/donaciones/{$id}");
        } catch (BecApiException $e) {
            return redirect()->route('admin.donaciones.index')->with('error', $e->mensajeUsuario());
        }

        $usuarios = $this->api->get('/usuarios/', ['limit' => 200]);
        $categorias = $this->api->get('/catalogos/categorias');
        $condiciones = $this->api->get('/catalogos/condiciones');
        $unidades = $this->api->get('/catalogos/unidades');
        $albergues = $this->api->get('/albergues/');

        return view('admin.donaciones.edit', compact('donacion', 'usuarios', 'categorias', 'condiciones', 'unidades', 'albergues'));
    }

    public function update(Request $request, int $id)
    {
        $datos = $request->validate([
            'usuario_id' => 'nullable|integer',
            'categoria_id' => 'required|integer',
            'condicion_id' => 'required|integer',
            'cantidad' => 'required|numeric|min:0.01',
            'unidad_id' => 'required|integer',
            'marca' => 'nullable|string|max:100',
            'albergue_id' => 'required|integer',
        ]);
        $datos['usuario_id'] = empty($datos['usuario_id']) ? null : $datos['usuario_id'];

        try {
            $this->api->put("/donaciones/{$id}", $datos);
        } catch (BecApiException $e) {
            return back()->withInput()->with('error', $e->mensajeUsuario());
        }

        return redirect()->route('admin.donaciones.index')->with('exito', 'Donación actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        try {
            $this->api->delete("/donaciones/{$id}");
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return redirect()->route('admin.donaciones.index')->with('exito', 'Donación eliminada correctamente.');
    }
}
