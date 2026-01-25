<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    /**
     * Muestra el aviso de "Por favor verifica tu correo".
     */
    public function notice()
    {
        // Si ya verificó, lo mandamos al home
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }
        
        return view('auth.verify-email');
    }

    /**
     * Procesa el link que el usuario clica en su correo.
     * Laravel hace la magia de seguridad (firmas, hash) automáticamente con EmailVerificationRequest.
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill(); // Marca el email como verificado en la BD

        return redirect()->route('home')->with('success', '¡Gracias! Tu correo ha sido verificado exitosamente.');
    }

    /**
     * Reenvía el correo de verificación si se perdió.
     */
    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Se ha enviado un nuevo enlace de verificación a tu correo.');
    }
}