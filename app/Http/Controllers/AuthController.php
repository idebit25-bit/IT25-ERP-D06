<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Page; // Importar el modelo Page para buscar la página con order=1

class AuthController extends Controller
{
    public function __construct()
    {
        // Solo los invitados pueden ver login; logout es para autenticados
        $this->middleware('guest')->except('logout');
    }

    public function showLogin()
    {
        // Carga la vista de login personalizada
        return view('auth.custom-login');
    }

    public function authenticate(Request $request)
    {
        // Validar credenciales de email y password
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Intentar autenticar
        if (Auth::attempt($credentials)) {
            // Regenerar la sesión para evitar fijación de sesión
            $request->session()->regenerate();

            // Verificar el rol del usuario autenticado
            $user = Auth::user();
            if ($user->role === 'admin') {
                // Si es admin, lo mandamos a /admin
                return redirect()->route('admin');
            } else {
                // MODIFICACIÓN: En lugar de iniciar la inducción, va primero al Lobby
                return redirect()->route('lobby');
            }
        }

        // Si falla la autenticación, volver atrás con error
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden.',
        ]);
    }

    public function logout(Request $request)
    {
        // Cerrar sesión
        Auth::logout();

        // Invalidar sesión y regenerar token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirigir a login
        return redirect()->route('login');
    }
}
