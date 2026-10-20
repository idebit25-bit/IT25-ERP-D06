<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Redirige a /admin después del restablecimiento de contraseña.
     */
    protected function redirectTo()
    {
        return route('login'); // Asegura que redirige a /admin
    }

    /**
     * Restablece la contraseña del usuario sin redirigir manualmente.
     */
    protected function resetPassword($user, $password)
{
    $user->forceFill([
        'password' => Hash::make($password),
        'remember_token' => Str::random(60),
    ])->save();

    event(new PasswordReset($user));

    // No logueamos al usuario, de modo que no se establezca la sesión.
    // $this->guard()->login($user);  <-- comentado o removido.
}

    /**
     * Asegura que después de resetear, se redirija a /admin.
     */
    protected function sendResetResponse(Request $request, $response)
   {
       return redirect()->route('login')->with('status', trans($response));
   }

    /**
     * Muestra la vista de restablecimiento de contraseña.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Maneja la solicitud de restablecimiento de contraseña.
     */
    public function reset(Request $request)
    {
        // Validación de los datos
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required|min:8',
        ]);

        // Intenta restablecer la contraseña
        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // Manejo de errores
        if ($response !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => trans($response)]);
        }

        return $this->sendResetResponse($request, $response);
    }
}
