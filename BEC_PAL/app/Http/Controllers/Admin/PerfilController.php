<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BecApiException;
use App\Http\Controllers\Controller;
use App\Services\BecApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PerfilController extends Controller
{
    public function __construct(private BecApiClient $api) {}

    public function index()
    {
        $usuario = $this->api->get('/usuarios/me');
        $roles = collect($this->api->get('/catalogos/roles'))->keyBy('id');
        return view('admin.perfil.index', compact('usuario', 'roles'));
    }

    public function actualizarTelefono(Request $request)
    {
        $datos = $request->validate([
            'telefono' => ['required', 'string', 'regex:/^\d{10}$/'],
        ], [
            'telefono.regex' => 'El teléfono debe tener 10 dígitos, sin espacios ni guiones.',
        ]);

        try {
            $usuario = $this->api->put('/usuarios/me', $datos);
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        Session::put('bec_user', $usuario);

        return back()->with('exito', 'Teléfono actualizado correctamente.');
    }

    public function actualizarPassword(Request $request)
    {
        $datos = $request->validate([
            'password_actual' => 'required|string',
            'password_nueva' => 'required|string|min:6|confirmed',
        ]);

        try {
            $this->api->put('/usuarios/me/password', [
                'password_actual' => $datos['password_actual'],
                'password_nueva' => $datos['password_nueva'],
            ]);
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', 'Contraseña actualizada correctamente.');
    }

    public function actualizarFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,webp|max:5120',
        ]);

        try {
            $usuario = $this->api->postFile('/usuarios/me/foto', 'archivo', $request->file('foto'));
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        Session::put('bec_user', $usuario);

        return back()->with('exito', 'Foto de perfil actualizada correctamente.');
    }
}
