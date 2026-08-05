<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\BecApiException;
use App\Http\Controllers\Controller;
use App\Services\BecApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    private const ROL_ADMIN = 1;

    public function __construct(private BecApiClient $api) {}

    public function mostrarFormulario()
    {
        if (Session::has('bec_access_token')) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function iniciarSesion(Request $request)
    {
        $datos = $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $tokens = $this->api->postForm('/auth/login?plataforma=web', [
                'username' => $datos['correo'],
                'password' => $datos['password'],
            ]);
        } catch (BecApiException $e) {
            throw ValidationException::withMessages([
                'correo' => $e->status === 401
                    ? 'Correo o contraseña incorrectos.'
                    : $e->mensajeUsuario(),
            ]);
        }

        Session::put('bec_access_token', $tokens['access_token']);
        Session::put('bec_refresh_token', $tokens['refresh_token']);

        $usuario = $this->api->get('/usuarios/me');

        if (($usuario['rol_id'] ?? null) !== self::ROL_ADMIN) {
            Session::forget(['bec_access_token', 'bec_refresh_token']);
            throw ValidationException::withMessages([
                'correo' => 'Este portal es exclusivo para administradores.',
            ]);
        }

        Session::put('bec_user', $usuario);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function cerrarSesion(Request $request)
    {
        try {
            $refreshToken = Session::get('bec_refresh_token');
            if ($refreshToken) {
                $this->api->post('/auth/logout', ['refresh_token' => $refreshToken]);
            }
        } catch (BecApiException) {
            // Si ya no era válido, no importa — igual vamos a cerrar la sesión local.
        }

        Session::forget(['bec_access_token', 'bec_refresh_token', 'bec_user']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
