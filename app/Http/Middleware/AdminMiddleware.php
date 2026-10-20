<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403,
            'No tienes permiso de administrador.');
        return $next($request);
    }

}
