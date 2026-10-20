<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        // 1. Si la URL es "/pages/{slug}" (un segmento "pages" + 1 slug)
        //    o "/logout", permitimos sin exigir admin.
        if ($this->isUserOnlyRoute($request)) {
            // El usuario normal (role=user) puede pasar
            return $next($request);
        }

        // 2. De lo contrario, EXIGIMOS role=admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            // Redirigir si no es admin
            return redirect('/pages/alguna-pagina')
                ->with('error', 'No tienes permiso de administrador.');
        }

        // 3. Si es admin, continuar
        return $next($request);
    }

    private function isUserOnlyRoute($request)
    {
        // /logout (POST)
        if ($request->is('logout')) {
            return true;
        }

        // /pages/{slug} => segment(1)='pages', segment(2)=slug, segment(3) no existe
        if ($request->segment(1) === 'pages' 
            && $request->segment(2) !== null 
            && $request->segment(3) === null 
            && $request->isMethod('GET')
        ) {
            return true;
        }

        return false;
    }
}
