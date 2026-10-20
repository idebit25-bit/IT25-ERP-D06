<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Redireccionar a esta ruta después del login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Constructor del controlador.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout'); // Permite solo a invitados acceder al login
    }

    /**
     * Mostrar el formulario de login.
     */
    public function showLoginForm()
    {
        return view('auth.login'); // Asegúrate de que esta vista exista en resources/views/auth/login.blade.php
    }

    /**
     * Manejar la autenticación del usuario.
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return redirect()->intended($this->redirectTo);
        }

        return back()->withErrors(['email' => 'Las credenciales no coinciden.'])->withInput($request->only('email', 'remember'));
    }

    /**
     * Cerrar sesión del usuario.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/login');
    }
}
