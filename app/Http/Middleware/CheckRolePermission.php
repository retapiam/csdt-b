<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRolePermission
{
    /**
     * Verificar permisos según el rol del usuario
     */
    public function handle(Request $request, Closure $next, string $action, string $module)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }

        // Verificar si el usuario tiene el permiso necesario
        if (!$this->hasPermission($user, $action, $module)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción',
                'required_permission' => "{$action}_{$module}",
                'your_role' => $user->rol
            ], 403);
        }

        return $next($request);
    }

    /**
     * Verificar permisos por rol y acción
     */
    private function hasPermission($user, string $action, string $module): bool
    {
        $rol = $user->rol;

        // Definir permisos por rol
        $permissions = [
            // Super Administrador - Acceso total
            'superadmin' => [
                'actividades' => ['ver', 'crear', 'editar', 'eliminar', 'asignar'],
                'tareas' => ['ver', 'crear', 'editar', 'eliminar', 'asignar'],
                'proyectos' => ['ver', 'crear', 'editar', 'eliminar'],
                'usuarios' => ['ver', 'crear', 'editar', 'eliminar'],
                'permisos' => ['ver', 'crear', 'editar', 'eliminar'],
            ],

            // Administrador - Gestión completa del proyecto
            'administrador' => [
                'actividades' => ['ver', 'crear', 'editar', 'asignar'],
                'tareas' => ['ver', 'crear', 'editar', 'asignar'],
                'proyectos' => ['ver', 'crear', 'editar'],
                'usuarios' => ['ver', 'editar'],
                'permisos' => ['ver'],
            ],

            // Operador - Puede crear actividades y tareas, ver y editar las asignadas
            'operador' => [
                'actividades' => ['ver', 'crear', 'editar'], // Puede crear y editar
                'tareas' => ['ver', 'crear', 'editar'], // Puede crear y editar
                'proyectos' => ['ver'],
                'usuarios' => ['ver'],
            ],

            // Cliente - Puede crear actividades y ver tareas asignadas
            'cliente' => [
                'actividades' => ['ver', 'crear'], // Cliente PUEDE crear actividades
                'tareas' => ['ver'], // Solo puede ver sus tareas
                'proyectos' => ['ver'],
            ],
        ];

        // Verificar si el rol tiene permisos para el módulo
        if (!isset($permissions[$rol][$module])) {
            return false;
        }

        // Verificar si el rol tiene el permiso específico
        return in_array($action, $permissions[$rol][$module]);
    }

    /**
     * Verificar si un usuario puede gestionar un recurso específico
     */
    public static function canManageResource($user, $resource, string $action): bool
    {
        // Super admin puede todo
        if ($user->rol === 'superadmin') {
            return true;
        }

        // Administrador puede gestionar recursos del proyecto
        if ($user->rol === 'administrador') {
            // Si el recurso tiene proyecto_id, verificar que sea del mismo proyecto
            // o que sea el creador del recurso
            if (isset($resource->proyecto_id) || isset($resource->creado_por)) {
                return true; // Por ahora permitimos todo a admin
            }
        }

        // Operador puede editar solo lo que creó
        if ($user->rol === 'operador') {
            if ($action === 'editar' || $action === 'eliminar') {
                // Solo puede editar/eliminar lo que creó
                return $resource->creado_por === $user->id || $resource->creada_por === $user->id;
            }
            return $action === 'ver' || $action === 'crear';
        }

        // Cliente solo puede ver y crear actividades, no puede editar
        if ($user->rol === 'cliente') {
            if ($action === 'crear' && get_class($resource) === 'App\Models\Actividad') {
                return true; // Cliente PUEDE crear actividades
            }
            return $action === 'ver' && (
                $resource->creado_por === $user->id || 
                $resource->creada_por === $user->id ||
                $resource->asignado_a === $user->id
            );
        }

        return false;
    }
}

