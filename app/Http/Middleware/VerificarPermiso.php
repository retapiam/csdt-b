<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PermisoUsuario;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermiso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }

        // Administrador General tiene todos los permisos
        if ($user->rol === 'adm_gen') {
            return $next($request);
        }

        // Verificar permiso específico
        $tienePermiso = PermisoUsuario::where('user_id', $user->id)
            ->where('tipo_permiso', $permiso)
            ->activos()
            ->exists();

        if (!$tienePermiso) {
            return response()->json([
                'success' => false,
                'message' => "No tienes el permiso: {$permiso}"
            ], 403);
        }

        return $next($request);
    }
}

