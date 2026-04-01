<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    //Muestra el formulario de login
    public function showLogin()
    {
        if (session('api_token')) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    //Procesa el login contra la BEC_API
    public function login(Request $request)
    {
        $request->validate([
            'correo'   => 'required|email',
            'password' => 'required|string',
        ]);

        $apiUrl = env('BEC_API_URL', 'http://bec_api_app:5000');

        //La API usa OAuth2PasswordRequestForm: envia como form-urlencoded
        $response = Http::asForm()->post("{$apiUrl}/login", [
            'username' => $request->correo,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $token = $response->json('access_token');

            //Guardar token y datos básicos del usuario en la sesión
            session([
                'api_token'  => $token,
                'user_email' => $request->correo,
            ]);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'correo' => 'Credenciales incorrectas. Verifica tu correo y contraseña.',
        ])->withInput($request->only('correo'));
    }

    //Cierra la sesión
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }
}
