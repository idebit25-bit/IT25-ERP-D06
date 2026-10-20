<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateA11
{
    public function handle(Request $request, Closure $next)
    {
        $expected = (string) config('integration.a11_token');
        if (strlen($expected) < 32 || !hash_equals($expected, (string) $request->bearerToken())) {
            return response()->json(['message' => 'Acceso no autorizado.'], 401);
        }

        return $next($request)->header('Cache-Control', 'no-store, private');
    }
}
