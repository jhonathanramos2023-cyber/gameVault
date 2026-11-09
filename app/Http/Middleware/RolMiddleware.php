<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolMiddleware
{
    /**
     * Comprueba el rol solicitado antes de dejar pasar a la acción administrativa.
     *
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        abort_unless(
            $usuario && $usuario->activo && in_array($usuario->rol, $roles, true),
            403,
            'No tienes permiso para acceder a esta sección.'
        );

        return $next($request);
    }
}
