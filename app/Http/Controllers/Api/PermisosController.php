<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PermisoUsuario;
use App\Models\HistorialPermiso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermisosController extends Controller
{
    /**
     * Listar permisos de un usuario
     */
    public function listarPermisosUsuario(Request $request, $userId)
    {
        try {
            $usuario = User::findOrFail($userId);

            // Verificar permisos del usuario autenticado
            if (!$this->puedeGestionarPermisos($request->user(), $usuario)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para ver los permisos de este usuario'
                ], 403);
            }

            $permisos = PermisoUsuario::where('user_id', $userId)
                ->with(['otorgador', 'modificador'])
                ->get()
                ->map(function ($permiso) {
                    return [
                        'id' => $permiso->id,
                        'tipo_permiso' => $permiso->tipo_permiso,
                        'estado' => $permiso->estado,
                        'esta_activo' => $permiso->estaActivo(),
                        'es_temporal' => $permiso->es_temporal,
                        'fecha_inicio' => $permiso->fecha_inicio,
                        'fecha_fin' => $permiso->fecha_fin,
                        'dias_restantes' => $permiso->diasRestantes(),
                        'ha_expirado' => $permiso->haExpirado(),
                        'restricciones' => $permiso->restricciones,
                        'motivo_veto' => $permiso->motivo_veto,
                        'otorgado_por' => $permiso->otorgador ? $permiso->otorgador->name : null,
                        'modificado_por' => $permiso->modificador ? $permiso->modificador->name : null,
                        'notas' => $permiso->notas,
                        'created_at' => $permiso->created_at,
                        'updated_at' => $permiso->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'usuario' => [
                        'id' => $usuario->id,
                        'name' => $usuario->name,
                        'email' => $usuario->email,
                        'rol' => $usuario->rol,
                    ],
                    'permisos' => $permisos,
                    'permisos_activos' => $permisos->where('esta_activo', true)->count(),
                    'permisos_vetados' => $permisos->where('estado', 'vetado')->count(),
                    'permisos_temporales' => $permisos->where('es_temporal', true)->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener permisos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Otorgar permiso a un usuario
     */
    public function otorgarPermiso(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'tipo_permiso' => 'required|string',
            'es_temporal' => 'boolean',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'restricciones' => 'nullable|array',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $usuario = User::findOrFail($request->user_id);

            // Verificar permisos del usuario autenticado
            if (!$this->puedeGestionarPermisos($request->user(), $usuario)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para otorgar permisos a este usuario'
                ], 403);
            }

            // Verificar si ya existe el permiso
            $permisoExistente = PermisoUsuario::where('user_id', $request->user_id)
                ->where('tipo_permiso', $request->tipo_permiso)
                ->first();

            if ($permisoExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya tiene este permiso. Usa la opción de actualizar.'
                ], 409);
            }

            $permiso = PermisoUsuario::create([
                'user_id' => $request->user_id,
                'tipo_permiso' => $request->tipo_permiso,
                'estado' => $request->es_temporal ? 'temporal' : 'activo',
                'es_temporal' => $request->es_temporal ?? false,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'restricciones' => $request->restricciones,
                'otorgado_por' => $request->user()->id,
                'modificado_por' => $request->user()->id,
                'notas' => $request->notas,
            ]);

            // Registrar en historial
            HistorialPermiso::create([
                'permiso_id' => $permiso->id,
                'user_id' => $request->user_id,
                'modificado_por' => $request->user()->id,
                'accion' => 'crear',
                'estado_anterior' => null,
                'estado_nuevo' => $permiso->estado,
                'datos_nuevos' => $permiso->toArray(),
                'motivo' => 'Permiso otorgado inicialmente',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permiso otorgado exitosamente',
                'data' => $permiso
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al otorgar permiso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar permiso
     */
    public function actualizarPermiso(Request $request, $permisoId)
    {
        $validator = Validator::make($request->all(), [
            'estado' => 'nullable|in:activo,inactivo,vetado,temporal',
            'es_temporal' => 'boolean',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'restricciones' => 'nullable|array',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $permiso = PermisoUsuario::findOrFail($permisoId);

            // Verificar permisos del usuario autenticado
            if (!$this->puedeGestionarPermisos($request->user(), $permiso->usuario)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para modificar este permiso'
                ], 403);
            }

            $datosAnteriores = $permiso->toArray();
            $estadoAnterior = $permiso->estado;

            $permiso->update([
                'estado' => $request->estado ?? $permiso->estado,
                'es_temporal' => $request->es_temporal ?? $permiso->es_temporal,
                'fecha_inicio' => $request->fecha_inicio ?? $permiso->fecha_inicio,
                'fecha_fin' => $request->fecha_fin ?? $permiso->fecha_fin,
                'restricciones' => $request->restricciones ?? $permiso->restricciones,
                'modificado_por' => $request->user()->id,
                'notas' => $request->notas ?? $permiso->notas,
            ]);

            // Registrar en historial
            HistorialPermiso::create([
                'permiso_id' => $permiso->id,
                'user_id' => $permiso->user_id,
                'modificado_por' => $request->user()->id,
                'accion' => 'actualizar',
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $permiso->estado,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => $permiso->toArray(),
                'motivo' => $request->motivo ?? 'Permiso actualizado',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permiso actualizado exitosamente',
                'data' => $permiso
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar permiso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vetar permiso
     */
    public function vetarPermiso(Request $request, $permisoId)
    {
        $validator = Validator::make($request->all(), [
            'motivo' => 'required|string|min:10',
        ], [
            'motivo.required' => 'Debes especificar un motivo para vetar el permiso',
            'motivo.min' => 'El motivo debe tener al menos 10 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $permiso = PermisoUsuario::findOrFail($permisoId);

            // Verificar permisos del usuario autenticado
            if (!$this->puedeGestionarPermisos($request->user(), $permiso->usuario)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para vetar este permiso'
                ], 403);
            }

            $permiso->vetar($request->motivo, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Permiso vetado exitosamente',
                'data' => $permiso
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al vetar permiso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activar permiso vetado
     */
    public function activarPermiso(Request $request, $permisoId)
    {
        try {
            $permiso = PermisoUsuario::findOrFail($permisoId);

            // Verificar permisos del usuario autenticado
            if (!$this->puedeGestionarPermisos($request->user(), $permiso->usuario)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para activar este permiso'
                ], 403);
            }

            $permiso->activar($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Permiso activado exitosamente',
                'data' => $permiso
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al activar permiso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar permiso
     */
    public function eliminarPermiso(Request $request, $permisoId)
    {
        try {
            $permiso = PermisoUsuario::findOrFail($permisoId);

            // Verificar permisos del usuario autenticado
            if (!$this->puedeGestionarPermisos($request->user(), $permiso->usuario)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para eliminar este permiso'
                ], 403);
            }

            // Registrar en historial antes de eliminar
            HistorialPermiso::create([
                'permiso_id' => $permiso->id,
                'user_id' => $permiso->user_id,
                'modificado_por' => $request->user()->id,
                'accion' => 'eliminar',
                'estado_anterior' => $permiso->estado,
                'estado_nuevo' => 'eliminado',
                'datos_anteriores' => $permiso->toArray(),
                'motivo' => $request->motivo ?? 'Permiso eliminado',
            ]);

            $permiso->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permiso eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar permiso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si un usuario tiene un permiso específico activo
     */
    public function verificarPermiso(Request $request, $userId, $tipoPermiso)
    {
        try {
            $permiso = PermisoUsuario::where('user_id', $userId)
                ->where('tipo_permiso', $tipoPermiso)
                ->first();

            $tienePermiso = $permiso && $permiso->estaActivo();

            return response()->json([
                'success' => true,
                'data' => [
                    'tiene_permiso' => $tienePermiso,
                    'permiso' => $permiso,
                    'esta_activo' => $permiso ? $permiso->estaActivo() : false,
                    'esta_vetado' => $permiso && $permiso->estado === 'vetado',
                    'ha_expirado' => $permiso ? $permiso->haExpirado() : false,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar permiso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Historial de cambios de un permiso
     */
    public function historialPermiso($permisoId)
    {
        try {
            $permiso = PermisoUsuario::findOrFail($permisoId);

            $historial = HistorialPermiso::where('permiso_id', $permisoId)
                ->with('modificador')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'permiso' => $permiso,
                    'historial' => $historial
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si un usuario puede gestionar permisos de otro usuario
     */
    private function puedeGestionarPermisos(User $gestor, User $objetivo): bool
    {
        // Administrador General puede gestionar a todos
        if ($gestor->rol === 'adm_gen') {
            return true;
        }

        // Administrador puede gestionar a operadores y clientes
        if ($gestor->rol === 'adm' && in_array($objetivo->rol, ['ope', 'cli'])) {
            return true;
        }

        // Un usuario puede ver sus propios permisos
        if ($gestor->id === $objetivo->id) {
            return true;
        }

        return false;
    }

    /**
     * Obtener permisos por defecto de un rol
     */
    public function permisosRol($rol)
    {
        try {
            $permisos = DB::table('permisos_roles')
                ->where('rol', $rol)
                ->where('activo', true)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'rol' => $rol,
                    'permisos' => $permisos
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener permisos del rol',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

