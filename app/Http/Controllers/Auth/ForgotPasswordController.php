<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    // Mostrar el formulario para solicitar el enlace de restablecimiento de contraseña
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password'); // Vista para solicitar el correo
    }

    // Enviar el enlace de restablecimiento de contraseña
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']); // Validar el correo electrónico

        $email = trim($request->email);

        // Enviar el enlace de restablecimiento de contraseña
        $status = Password::sendResetLink(['email' => $email]);

        // Si el enlace es enviado, se muestra un mensaje de éxito, si no, de error
        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    // Mostrar el formulario para restablecer la contraseña con el token
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]); // Vista para restablecer la contraseña
    }

    // Restablecer la contraseña
    public function reset(Request $request)
    {
        // Registrar en los logs la solicitud de restablecimiento
        Log::info('Solicitud de restablecimiento recibida para: ' . $request->email);

        // Validar los campos
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Intentar restablecer la contraseña
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Actualizar la contraseña
                Log::info('Actualizando contraseña para: ' . $user->email);

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                Log::info('Contraseña actualizada en la base de datos');

                // Disparar evento de restablecimiento de contraseña
                event(new PasswordReset($user));

                // Autenticar al usuario después del restablecimiento
                Auth::login($user);
            }
        );

        // Verificar si la contraseña se restableció con éxito
        if ($status === Password::PASSWORD_RESET) {
            Log::info('Contraseña restablecida con éxito');
            return redirect()->route('login')->with('status', __($status));
        } else {
            // En caso de error, devolver con el mensaje de error
            Log::error('Error al restablecer la contraseña: ' . __($status));
            return back()->withErrors(['email' => [__($status)]]);
        }
    }
}
