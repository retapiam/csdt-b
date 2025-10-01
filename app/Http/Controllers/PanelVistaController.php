<?php

namespace App\Http\Controllers;

use App\Models\Pagina;
use App\Models\Rol;
use App\Models\PermisoMejorado;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PanelVistaController extends Controller
{
    /**
     * Mostrar el panel de vista principal
     */
    public function index()
    {
        $paginas = Pagina::with(['roles', 'permisos'])
            ->orderBy('carpeta')
            ->orderBy('orden')
            ->get()
            ->groupBy('carpeta');

        $roles = Rol::activos()->get();
        $permisos = PermisoMejorado::activos()->get();

        return view('admin.panel-vista', compact('paginas', 'roles', 'permisos'));
    }

    /**
     * Obtener todas las páginas con sus permisos y roles
     */
    public function obtenerPaginas(): JsonResponse
    {
        $paginas = Pagina::with(['roles', 'permisos'])
            ->orderBy('carpeta')
            ->orderBy('orden')
            ->get()
            ->groupBy('carpeta');

        return response()->json([
            'success' => true,
            'data' => $paginas
        ]);
    }

    /**
     * Actualizar permisos de una página
     */
    public function actualizarPermisos(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pagina_id' => 'required|exists:paginas,id',
            'permisos' => 'array',
            'permisos.*' => 'exists:perm,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $pagina = Pagina::findOrFail($request->pagina_id);
            $pagina->permisos()->sync($request->permisos ?? []);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permisos actualizados correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar permisos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar roles de una página
     */
    public function actualizarRoles(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pagina_id' => 'required|exists:paginas,id',
            'roles' => 'array',
            'roles.*.rol_id' => 'exists:rol,id',
            'roles.*.activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $pagina = Pagina::findOrFail($request->pagina_id);
            
            // Preparar datos para sync
            $rolesData = [];
            foreach ($request->roles as $rolData) {
                $rolesData[$rolData['rol_id']] = [
                    'activo' => $rolData['activo'] ?? true,
                    'asignado_por' => auth()->id(),
                    'asignado_en' => now()
                ];
            }

            $pagina->roles()->sync($rolesData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Roles actualizados correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar roles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar estado de una página
     */
    public function actualizarEstado(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pagina_id' => 'required|exists:paginas,id',
            'estado' => 'required|in:activa,inactiva,bloqueada'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $pagina = Pagina::findOrFail($request->pagina_id);
            $pagina->update(['estado' => $request->estado]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener páginas accesibles para un usuario específico
     */
    public function obtenerPaginasUsuario(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'usuario_id' => 'required|exists:usu,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $usuario = Usuario::findOrFail($request->usuario_id);
            $paginas = Pagina::obtenerPaginasAccesibles($usuario);

            return response()->json([
                'success' => true,
                'data' => $paginas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener páginas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nueva página
     */
    public function crearPagina(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), Pagina::reglas());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $pagina = Pagina::create($request->all());

            // Asignar permisos si se proporcionan
            if ($request->has('permisos')) {
                $pagina->permisos()->sync($request->permisos);
            }

            // Asignar roles si se proporcionan
            if ($request->has('roles')) {
                $rolesData = [];
                foreach ($request->roles as $rolId) {
                    $rolesData[$rolId] = [
                        'activo' => true,
                        'asignado_por' => auth()->id(),
                        'asignado_en' => now()
                    ];
                }
                $pagina->roles()->sync($rolesData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Página creada correctamente',
                'data' => $pagina->load(['roles', 'permisos'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear página: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar página existente
     */
    public function actualizarPagina(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), Pagina::reglas($id));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $pagina = Pagina::findOrFail($id);
            $pagina->update($request->all());

            // Actualizar permisos si se proporcionan
            if ($request->has('permisos')) {
                $pagina->permisos()->sync($request->permisos);
            }

            // Actualizar roles si se proporcionan
            if ($request->has('roles')) {
                $rolesData = [];
                foreach ($request->roles as $rolId) {
                    $rolesData[$rolId] = [
                        'activo' => true,
                        'asignado_por' => auth()->id(),
                        'asignado_en' => now()
                    ];
                }
                $pagina->roles()->sync($rolesData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Página actualizada correctamente',
                'data' => $pagina->load(['roles', 'permisos'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar página: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar página
     */
    public function eliminarPagina($id): JsonResponse
    {
        try {
            $pagina = Pagina::findOrFail($id);
            $pagina->delete();

            return response()->json([
                'success' => true,
                'message' => 'Página eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar página: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas del panel de vista
     */
    public function obtenerEstadisticas(): JsonResponse
    {
        try {
            $estadisticas = [
                'total_paginas' => Pagina::count(),
                'paginas_activas' => Pagina::activas()->count(),
                'paginas_publicas' => Pagina::publicas()->count(),
                'paginas_por_carpeta' => Pagina::select('carpeta', DB::raw('count(*) as total'))
                    ->groupBy('carpeta')
                    ->get(),
                'paginas_por_tipo' => Pagina::select('tipo', DB::raw('count(*) as total'))
                    ->groupBy('tipo')
                    ->get(),
                'total_roles' => Rol::activos()->count(),
                'total_permisos' => PermisoMejorado::activos()->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}
