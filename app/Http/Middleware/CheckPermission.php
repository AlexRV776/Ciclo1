<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle($request, Closure $next, $permiso)
    {
        $usuario = Auth::user();

        // Sin usuario o sin rol → no permitido
        if (!$usuario || !$usuario->rol) {
            abort(403, 'No tienes permisos.');
        }

        // Buscar si el rol tiene ese permiso
        $tiene = $usuario->rol->permisos->contains('nombre', $permiso);

        if (!$tiene) {
            abort(403, 'No tienes permisos.');
        }

        return $next($request);
    }
}