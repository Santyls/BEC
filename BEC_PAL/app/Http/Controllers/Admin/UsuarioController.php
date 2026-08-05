<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BecApiException;
use App\Http\Controllers\Controller;
use App\Services\BecApiClient;
use App\Support\Paginador;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct(private BecApiClient $api) {}

    public function index(Request $request)
    {
        $usuarios = $this->api->get('/usuarios/', ['limit' => 200]);
        $roles = collect($this->api->get('/catalogos/roles'))->keyBy('id');

        $busqueda = trim((string) $request->query('q', ''));
        if ($busqueda !== '') {
            $texto = mb_strtolower($busqueda);
            $usuarios = array_values(array_filter($usuarios, function ($u) use ($texto, $roles) {
                $nombreCompleto = mb_strtolower(trim($u['nombre'].' '.$u['apellido_paterno'].' '.$u['apellido_materno']));
                $correo = mb_strtolower($u['correo'] ?? '');
                $rol = mb_strtolower($roles[$u['rol_id']]['nombre'] ?? '');
                return str_contains($nombreCompleto, $texto) || str_contains($correo, $texto) || str_contains($rol, $texto);
            }));
        }

        $usuarios = Paginador::paginar($usuarios);

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'busqueda'));
    }

    public function create()
    {
        $roles = $this->api->get('/catalogos/roles');
        $generos = $this->api->get('/catalogos/generos');
        return view('admin.usuarios.create', compact('roles', 'generos'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'telefono' => ['required', 'string', 'regex:/^\d{10}$/'],
            'correo' => 'nullable|email|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero_id' => 'nullable|integer',
            'rol_id' => 'required|integer',
        ], [
            'telefono.regex' => 'El teléfono debe tener 10 dígitos, sin espacios ni guiones.',
        ]);

        try {
            $this->api->post('/usuarios/', $datos);
        } catch (BecApiException $e) {
            return back()->withInput()->with('error', $e->mensajeUsuario());
        }

        return redirect()->route('admin.usuarios.index')->with('exito', 'Usuario creado correctamente.');
    }

    public function edit(int $id)
    {
        $usuario = $this->api->get("/usuarios/{$id}");
        $roles = $this->api->get('/catalogos/roles');
        $generos = $this->api->get('/catalogos/generos');
        return view('admin.usuarios.edit', compact('usuario', 'roles', 'generos'));
    }

    public function update(Request $request, int $id)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'telefono' => ['nullable', 'string', 'regex:/^\d{10}$/'],
            'correo' => 'nullable|email|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero_id' => 'nullable|integer',
            'rol_id' => 'required|integer',
            'motivo_veto' => 'nullable|string|max:500',
        ], [
            'telefono.regex' => 'El teléfono debe tener 10 dígitos, sin espacios ni guiones.',
        ]);

        // Checkbox: si no viene en el POST es que quedó desmarcado. Se envía explícito
        // (true/false) para que la API sí pueda revertir un veto existente.
        $datos['vetado'] = $request->boolean('vetado');
        if (!$datos['vetado']) {
            $datos['motivo_veto'] = null;
        }

        // Si el admin dejó el campo de contraseña vacío, no se envía (no se toca la actual).
        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        try {
            $this->api->put("/usuarios/{$id}", $datos);
        } catch (BecApiException $e) {
            return back()->withInput()->with('error', $e->mensajeUsuario());
        }

        return redirect()->route('admin.usuarios.index')->with('exito', 'Usuario actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        try {
            $this->api->delete("/usuarios/{$id}");
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', 'Usuario desactivado correctamente. Podrás reactivarlo durante 30 días.');
    }

    public function reactivar(int $id)
    {
        try {
            $this->api->post("/usuarios/{$id}/reactivar");
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', 'Usuario reactivado correctamente.');
    }

    public function eliminarPermanente(int $id)
    {
        try {
            $this->api->delete("/usuarios/{$id}/permanente");
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', 'Usuario eliminado permanentemente.');
    }

    public function vetar(Request $request, int $id)
    {
        $datos = $request->validate([
            'vetado' => 'required|boolean',
            'motivo_veto' => 'nullable|string|max:500',
        ]);

        try {
            $this->api->put("/usuarios/{$id}", $datos);
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', $datos['vetado'] ? 'Usuario vetado correctamente.' : 'Veto retirado correctamente.');
    }
}
