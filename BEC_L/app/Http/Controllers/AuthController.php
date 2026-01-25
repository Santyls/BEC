<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered; // <--- IMPORTANTE PARA EL EMAIL

class AuthController extends Controller
{
    // --- VISTAS ---
    
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    // --- LÓGICA ---

    /**
     * Procesa el registro de usuario.
     */
    public function processRegister(Request $request)
    {
        // 1. Validar
        $request->validate([
            'alias' => ['required', 'string', 'max:50', 'unique:usuarios'],
            'email' => ['required', 'string', 'email', 'max:120', 'unique:usuarios'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ]);

        // 2. Crear Usuario
        $user = User::create([
            'alias' => $request->alias,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => 2, // Usuario General
        ]);

        // 3.DISPARAR EVENTO DE VERIFICACIÓN DE CORREO
        // Esto envía el email a Mailtrap
        event(new Registered($user));

        // 4. Iniciar sesión y redirigir
        Auth::login($user);

        return redirect()->route('home')->with('success', '¡Cuenta creada! Revisa tu correo para verificarla.');
    }

    /**
     * Procesa el inicio de sesión.
     * (Esta es la función que faltaba y causaba el error)
     */
    public function processLogin(Request $request)
    {
        // 1. Validar credenciales
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Intentar loguear
        // 'remember' activa la cookie de "recordar sesión" si el input existiera
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            
            // Regenerar sesión por seguridad (evita Session Fixation)
            $request->session()->regenerate();

            // Redirigir a la intención original o al Home
            return redirect()->intended(route('home'))->with('success', '¡Bienvenido de nuevo!');
        }

        // 3. Si falla: devolver con error
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Has cerrado sesión correctamente.');
    }
}