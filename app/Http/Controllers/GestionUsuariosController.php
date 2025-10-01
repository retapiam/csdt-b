<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Pagina;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GestionUsuariosController extends Controller
{
    /**
     * Mostrar la vista de gestión de usuarios
     */
    public function index()
    {
        return view('admin.gestion-usuarios');
    }

    /**
     * Obtener todos los usuarios con sus roles
     */
    public function obtenerUsuarios(): JsonResponse
    {
        try {
            $usuarios = Usuario::with(['roles'])
                ->select('id', 'nom', 'ape', 'cor', 'rol', 'est', 'ult_acc', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $usuarios
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un usuario específico
     */
    public function obtenerUsuario($id): JsonResponse
    {
        try {
            $usuario = Usuario::with(['roles', 'paginasAccesibles'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $usuario
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }
    }

    /**
     * Crear un nuevo usuario
     */
    public function crearUsuario(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100',
            'ape' => 'required|string|max:100',
            'cor' => 'required|email|unique:usu,cor',
            'con' => 'required|string|min:8',
            'tel' => 'nullable|string|max:20',
            'doc' => 'required|string|max:20|unique:usu,doc',
            'tip_doc' => 'required|string|in:cc,ce,ti,pp,nit',
            'fec_nac' => 'nullable|date|before:today',
            'dir' => 'nullable|string|max:255',
            'ciu' => 'nullable|string|max:100',
            'dep' => 'nullable|string|max:100',
            'gen' => 'nullable|string|in:m,f,o,n',
            'rol' => 'required|string|in:cli,ope,adm,adm_gen',
            'est' => 'nullable|string|in:act,ina,sus,pen'
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

            $usuario = Usuario::create([
                'nom' => $request->nom,
                'ape' => $request->ape,
                'cor' => $request->cor,
                'con' => $request->con,
                'tel' => $request->tel,
                'doc' => $request->doc,
                'tip_doc' => $request->tip_doc,
                'fec_nac' => $request->fec_nac,
                'dir' => $request->dir,
                'ciu' => $request->ciu,
                'dep' => $request->dep,
                'gen' => $request->gen,
                'rol' => $request->rol,
                'est' => $request->est ?? 'act',
                'cor_ver' => true,
                'cor_ver_en' => now()
            ]);

            // Asignar rol correspondiente
            $rol = Rol::where('nom', $this->obtenerNombreRol($request->rol))->first();
            if ($rol) {
                $usuario->roles()->attach($rol->id, [
                    'act' => true,
                    'asig_por' => auth()->id(),
                    'asig_en' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'data' => $usuario->load('roles')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un usuario existente
     */
    public function actualizarUsuario(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:100',
            'ape' => 'sometimes|string|max:100',
            'cor' => 'sometimes|email|unique:usu,cor,' . $id,
            'tel' => 'nullable|string|max:20',
            'doc' => 'sometimes|string|max:20|unique:usu,doc,' . $id,
            'tip_doc' => 'sometimes|string|in:cc,ce,ti,pp,nit',
            'fec_nac' => 'nullable|date|before:today',
            'dir' => 'nullable|string|max:255',
            'ciu' => 'nullable|string|max:100',
            'dep' => 'nullable|string|max:100',
            'gen' => 'nullable|string|in:m,f,o,n',
            'rol' => 'sometimes|string|in:cli,ope,adm,adm_gen',
            'est' => 'sometimes|string|in:act,ina,sus,pen'
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

            $usuario = Usuario::findOrFail($id);
            $usuario->update($request->only([
                'nom', 'ape', 'cor', 'tel', 'doc', 'tip_doc', 'fec_nac',
                'dir', 'ciu', 'dep', 'gen', 'rol', 'est'
            ]));

            // Actualizar rol si cambió
            if ($request->has('rol')) {
                $rolAnterior = $usuario->roles()->first();
                if ($rolAnterior) {
                    $usuario->roles()->detach($rolAnterior->id);
                }

                $nuevoRol = Rol::where('nom', $this->obtenerNombreRol($request->rol))->first();
                if ($nuevoRol) {
                    $usuario->roles()->attach($nuevoRol->id, [
                        'act' => true,
                        'asig_por' => auth()->id(),
                        'asig_en' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente',
                'data' => $usuario->load('roles')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un usuario (soft delete)
     */
    public function eliminarUsuario($id): JsonResponse
    {
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restaurar un usuario eliminado
     */
    public function restaurarUsuario($id): JsonResponse
    {
        try {
            $usuario = Usuario::withTrashed()->findOrFail($id);
            $usuario->restore();

            return response()->json([
                'success' => true,
                'message' => 'Usuario restaurado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado de un usuario
     */
    public function cambiarEstado(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'estado' => 'required|string|in:act,ina,sus,pen'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Estado inválido',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->update(['est' => $request->estado]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar contraseña de un usuario
     */
    public function cambiarPassword(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->update(['con' => $request->password]);

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar contraseña: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener páginas accesibles para un usuario
     */
    public function obtenerPaginasUsuario($id): JsonResponse
    {
        try {
            $usuario = Usuario::findOrFail($id);
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
     * Obtener estadísticas de usuarios
     */
    public function obtenerEstadisticas(): JsonResponse
    {
        try {
            $estadisticas = [
                'total_usuarios' => Usuario::count(),
                'usuarios_activos' => Usuario::where('est', 'act')->count(),
                'usuarios_inactivos' => Usuario::where('est', 'ina')->count(),
                'usuarios_suspendidos' => Usuario::where('est', 'sus')->count(),
                'usuarios_pendientes' => Usuario::where('est', 'pen')->count(),
                'usuarios_por_rol' => Usuario::selectRaw('rol, COUNT(*) as total')
                    ->groupBy('rol')
                    ->get(),
                'usuarios_por_estado' => Usuario::selectRaw('est, COUNT(*) as total')
                    ->groupBy('est')
                    ->get(),
                'usuarios_recientes' => Usuario::where('created_at', '>=', now()->subDays(30))->count(),
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

    /**
     * Obtener nombre del rol por código
     */
    private function obtenerNombreRol($codigoRol)
    {
        $roles = [
            'cli' => 'Cliente',
            'ope' => 'Operador',
            'adm' => 'Administrador',
            'adm_gen' => 'Administrador General'
        ];

        return $roles[$codigoRol] ?? $codigoRol;
    }
}
