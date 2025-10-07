<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }

        // Mapear roles abreviados a completos
        $rolesMap = [
            'cli' => 'cliente',
            'ope' => 'operador',
            'adm' => 'administrador',
            'adm_gen' => 'superadmin'
        ];

        $rolesPermitidos = array_map(function($rol) use ($rolesMap) {
            return $rolesMap[$rol] ?? $rol;
        }, $roles);

        // Verificar si el rol del usuario está en los roles permitidos
        if (!in_array($user->rol, $rolesPermitidos) && !in_array($user->rol, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para acceder a este recurso',
                'rol_requerido' => $roles,
                'tu_rol' => $user->rol
            ], 403);
        }

        return $next($request);
    }
}

