<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Controlador completo para gestión de usuarios
 * Implementa todas las operaciones CRUD estándar según CONTROL.md
 */
class UsuarioControllerCompleto extends Controller
{
    /**
     * Listar usuarios con filtros, búsqueda y paginación
     * GET /api/v1/usuarios
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Usuario::with(['roles']);

            // Filtros
            if ($request->has('rol') && $request->rol !== '') {
                $query->where('rol', $request->rol);
            }

            if ($request->has('est') && $request->est !== '') {
                $query->where('est', $request->est);
            }

            if ($request->has('ciu') && $request->ciu !== '') {
                $query->where('ciu', 'like', '%' . $request->ciu . '%');
            }

            if ($request->has('dep') && $request->dep !== '') {
                $query->where('dep', 'like', '%' . $request->dep . '%');
            }

            if ($request->has('gen') && $request->gen !== '') {
                $query->where('gen', $request->gen);
            }

            if ($request->has('cor_ver') && $request->cor_ver !== '') {
                $query->where('cor_ver', $request->cor_ver === 'true');
            }

            // Búsqueda global
            if ($request->has('buscar') && $request->buscar !== '') {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('nom', 'like', "%{$buscar}%")
                      ->orWhere('ape', 'like', "%{$buscar}%")
                      ->orWhere('cor', 'like', "%{$buscar}%")
                      ->orWhere('doc', 'like', "%{$buscar}%")
                      ->orWhere('tel', 'like', "%{$buscar}%")
                      ->orWhere('ciu', 'like', "%{$buscar}%")
                      ->orWhere('dep', 'like', "%{$buscar}%");
                });
            }

            // Filtro por fecha de registro
            if ($request->has('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }

            if ($request->has('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }

            // Ordenamiento
            $orden = $request->get('orden', 'created_at');
            $direccion = $request->get('direccion', 'desc');
            
            // Validar campos de ordenamiento
            $camposPermitidos = ['nom', 'ape', 'cor', 'rol', 'est', 'ciu', 'dep', 'created_at', 'ult_acc'];
            if (in_array($orden, $camposPermitidos)) {
                $query->orderBy($orden, $direccion);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Paginación
            $porPagina = min($request->get('por_pagina', 15), 100); // Máximo 100 por página
            $usuarios = $query->paginate($porPagina);

            // Formatear respuesta
            $datos = [
                'usuarios' => $usuarios->items(),
                'pagination' => [
                    'current_page' => $usuarios->currentPage(),
                    'per_page' => $usuarios->perPage(),
                    'total' => $usuarios->total(),
                    'last_page' => $usuarios->lastPage(),
                    'from' => $usuarios->firstItem(),
                    'to' => $usuarios->lastItem(),
                    'has_more_pages' => $usuarios->hasMorePages()
                ],
                'filters_applied' => $request->only(['rol', 'est', 'ciu', 'dep', 'gen', 'cor_ver', 'buscar', 'fecha_desde', 'fecha_hasta'])
            ];

            return response()->json([
                'success' => true,
                'message' => 'Usuarios obtenidos exitosamente',
                'data' => $datos
            ]);

        } catch (\Exception $e) {
            Log::error('Error al listar usuarios: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Crear nuevo usuario
     * POST /api/v1/usuarios
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validación
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:100',
                'ape' => 'required|string|max:100',
                'cor' => 'required|email|max:150|unique:usu,cor',
                'con' => 'required|string|min:8|confirmed',
                'con_confirmation' => 'required_with:con',
                'tel' => 'nullable|string|max:20',
                'doc' => 'nullable|string|max:20|unique:usu,doc',
                'tip_doc' => 'nullable|in:cc,ce,ti,pp,nit',
                'fec_nac' => 'nullable|date|before:today',
                'dir' => 'nullable|string|max:200',
                'ciu' => 'nullable|string|max:100',
                'dep' => 'nullable|string|max:100',
                'gen' => 'nullable|in:m,f,o,n',
                'rol' => 'required|in:cli,ope,adm,adm_gen',
                'est' => 'nullable|in:act,ina,sus,pen',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:rol,id'
            ], [
                'nom.required' => 'El nombre es obligatorio',
                'nom.max' => 'El nombre no puede exceder 100 caracteres',
                'ape.required' => 'Los apellidos son obligatorios',
                'ape.max' => 'Los apellidos no pueden exceder 100 caracteres',
                'cor.required' => 'El correo electrónico es obligatorio',
                'cor.email' => 'El correo electrónico debe tener un formato válido',
                'cor.unique' => 'Este correo electrónico ya está registrado',
                'con.required' => 'La contraseña es obligatoria',
                'con.min' => 'La contraseña debe tener al menos 8 caracteres',
                'con.confirmed' => 'Las contraseñas no coinciden',
                'doc.unique' => 'Este documento ya está registrado',
                'tip_doc.in' => 'El tipo de documento no es válido',
                'gen.in' => 'El género seleccionado no es válido',
                'rol.required' => 'El rol es obligatorio',
                'rol.in' => 'El rol seleccionado no es válido',
                'est.in' => 'El estado seleccionado no es válido',
                'roles.array' => 'Los roles deben ser un arreglo',
                'roles.*.exists' => 'Uno o más roles seleccionados no existen'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Crear usuario
            $usuario = Usuario::create([
                'nom' => $request->nom,
                'ape' => $request->ape,
                'cor' => strtolower(trim($request->cor)),
                'con' => Hash::make($request->con),
                'tel' => $request->tel,
                'doc' => $request->doc,
                'tip_doc' => $request->tip_doc,
                'fec_nac' => $request->fec_nac,
                'dir' => $request->dir,
                'ciu' => $request->ciu,
                'dep' => $request->dep,
                'gen' => $request->gen,
                'rol' => $request->rol,
                'est' => $request->est ?? 'pen',
                'not' => $request->not
            ]);

            // Asignar roles si se proporcionan
            if ($request->has('roles') && is_array($request->roles)) {
                $usuario->roles()->attach($request->roles, [
                    'act' => true,
                    'asig_por' => auth()->id() ?? $usuario->id,
                    'asig_en' => now(),
                    'not' => 'Asignación inicial de roles'
                ]);
            }

            // Log de creación
            Log::logCreacion('usu', $usuario->id, $usuario->toArray());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'data' => $usuario->load('roles')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear usuario: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Mostrar usuario específico
     * GET /api/v1/usuarios/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $usuario = Usuario::with([
                'roles',
                'veedurias' => function($query) {
                    $query->latest()->limit(10);
                },
                'donaciones' => function($query) {
                    $query->latest()->limit(10);
                },
                'tareasAsignadas' => function($query) {
                    $query->latest()->limit(10);
                },
                'tareasCreadas' => function($query) {
                    $query->latest()->limit(10);
                }
            ])->findOrFail($id);

            // Estadísticas del usuario
            $estadisticas = [
                'total_veedurias' => $usuario->veedurias()->count(),
                'veedurias_activas' => $usuario->veedurias()->whereIn('est', ['pen', 'pro', 'rad'])->count(),
                'total_donaciones' => $usuario->donaciones()->count(),
                'monto_total_donado' => $usuario->donaciones()->where('est', 'con')->sum('mon'),
                'tareas_asignadas' => $usuario->tareasAsignadas()->count(),
                'tareas_completadas' => $usuario->tareasAsignadas()->where('est', 'com')->count(),
                'tareas_pendientes' => $usuario->tareasAsignadas()->whereIn('est', ['pen', 'pro'])->count(),
                'dias_registrado' => $usuario->created_at->diffInDays(now())
            ];

            $datos = [
                'usuario' => $usuario,
                'estadisticas' => $estadisticas
            ];

            return response()->json([
                'success' => true,
                'message' => 'Usuario obtenido exitosamente',
                'data' => $datos
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al obtener usuario: ' . $e->getMessage(), [
                'usuario_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuario',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Actualizar usuario
     * PUT/PATCH /api/v1/usuarios/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $usuario = Usuario::findOrFail($id);

            // Validación
            $validator = Validator::make($request->all(), [
                'nom' => 'sometimes|string|max:100',
                'ape' => 'sometimes|string|max:100',
                'cor' => 'sometimes|email|max:150|unique:usu,cor,' . $id,
                'con' => 'sometimes|string|min:8|confirmed',
                'con_confirmation' => 'required_with:con',
                'tel' => 'nullable|string|max:20',
                'doc' => 'nullable|string|max:20|unique:usu,doc,' . $id,
                'tip_doc' => 'nullable|in:cc,ce,ti,pp,nit',
                'fec_nac' => 'nullable|date|before:today',
                'dir' => 'nullable|string|max:200',
                'ciu' => 'nullable|string|max:100',
                'dep' => 'nullable|string|max:100',
                'gen' => 'nullable|in:m,f,o,n',
                'rol' => 'sometimes|in:cli,ope,adm,adm_gen',
                'est' => 'sometimes|in:act,ina,sus,pen',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:rol,id'
            ], Usuario::mensajes());

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $datosAnteriores = $usuario->toArray();

            // Preparar datos para actualización
            $datos = $request->only([
                'nom', 'ape', 'cor', 'tel', 'doc', 'tip_doc', 
                'fec_nac', 'dir', 'ciu', 'dep', 'gen', 'rol', 'est', 'not'
            ]);

            // Hash de contraseña si se proporciona
            if ($request->has('con')) {
                $datos['con'] = Hash::make($request->con);
            }

            // Actualizar usuario
            $usuario->update($datos);

            // Actualizar roles si se proporcionan
            if ($request->has('roles')) {
                $usuario->roles()->sync($request->roles, [
                    'act' => true,
                    'asig_por' => auth()->id() ?? $usuario->id,
                    'asig_en' => now(),
                    'not' => 'Actualización de roles'
                ]);
            }

            // Log de actualización
            Log::logActualizacion('usu', $usuario->id, $datosAnteriores, $usuario->toArray());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'data' => $usuario->load('roles')
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al actualizar usuario: ' . $e->getMessage(), [
                'usuario_id' => $id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Eliminar usuario (soft delete)
     * DELETE /api/v1/usuarios/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $usuario = Usuario::findOrFail($id);

            // Verificar si el usuario tiene datos relacionados
            $tieneVeedurias = $usuario->veedurias()->count() > 0;
            $tieneDonaciones = $usuario->donaciones()->count() > 0;
            $tieneTareas = $usuario->tareasAsignadas()->count() > 0 || $usuario->tareasCreadas()->count() > 0;

            if ($tieneVeedurias || $tieneDonaciones || $tieneTareas) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el usuario porque tiene datos relacionados',
                    'data' => [
                        'tiene_veedurias' => $tieneVeedurias,
                        'tiene_donaciones' => $tieneDonaciones,
                        'tiene_tareas' => $tieneTareas
                    ]
                ], 422);
            }

            $datosAnteriores = $usuario->toArray();
            $usuario->delete();

            // Log de eliminación
            Log::logEliminacion('usu', $usuario->id, $datosAnteriores);

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al eliminar usuario: ' . $e->getMessage(), [
                'usuario_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Buscar usuarios
     * GET /api/v1/usuarios/buscar
     */
    public function buscar(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'q' => 'required|string|min:2|max:100',
                'limite' => 'nullable|integer|min:1|max:50',
                'rol' => 'nullable|in:cli,ope,adm,adm_gen',
                'est' => 'nullable|in:act,ina,sus,pen'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parámetros de búsqueda incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $termino = $request->get('q');
            $limite = $request->get('limite', 10);

            $query = Usuario::where(function($q) use ($termino) {
                $q->where('nom', 'like', "%{$termino}%")
                  ->orWhere('ape', 'like', "%{$termino}%")
                  ->orWhere('cor', 'like', "%{$termino}%")
                  ->orWhere('doc', 'like', "%{$termino}%")
                  ->orWhere('ciu', 'like', "%{$termino}%")
                  ->orWhere('dep', 'like', "%{$termino}%");
            });

            // Aplicar filtros adicionales
            if ($request->has('rol')) {
                $query->where('rol', $request->rol);
            }

            if ($request->has('est')) {
                $query->where('est', $request->est);
            }

            $usuarios = $query->where('est', 'act')
                ->select(['id', 'nom', 'ape', 'cor', 'rol', 'est', 'ciu', 'dep'])
                ->limit($limite)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Búsqueda completada exitosamente',
                'data' => $usuarios,
                'meta' => [
                    'termino' => $termino,
                    'total_resultados' => $usuarios->count(),
                    'limite_aplicado' => $limite
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en búsqueda de usuarios: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Validar datos de usuario
     * POST /api/v1/usuarios/validar
     */
    public function validar(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'cor' => 'required_without:doc|email|unique:usu,cor',
                'doc' => 'required_without:cor|string|unique:usu,doc'
            ], [
                'cor.required_without' => 'El correo o documento es obligatorio',
                'cor.email' => 'El correo electrónico debe tener un formato válido',
                'cor.unique' => 'Este correo electrónico ya está registrado',
                'doc.required_without' => 'El correo o documento es obligatorio',
                'doc.unique' => 'Este documento ya está registrado'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos no válidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Datos válidos'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en validación de usuario: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error en validación',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Restaurar usuario eliminado
     * POST /api/v1/usuarios/{id}/restaurar
     */
    public function restaurar($id): JsonResponse
    {
        try {
            $usuario = Usuario::withTrashed()->findOrFail($id);

            if (!$usuario->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no está eliminado'
                ], 422);
            }

            $usuario->restore();

            // Log de restauración
            Log::logRestauracion('usu', $usuario->id);

            return response()->json([
                'success' => true,
                'message' => 'Usuario restaurado exitosamente',
                'data' => $usuario
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al restaurar usuario: ' . $e->getMessage(), [
                'usuario_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar usuario',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Activar usuario
     * POST /api/v1/usuarios/{id}/activar
     */
    public function activar($id): JsonResponse
    {
        try {
            $usuario = Usuario::findOrFail($id);

            if ($usuario->est === 'act') {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya está activo'
                ], 422);
            }

            $datosAnteriores = $usuario->toArray();
            $usuario->update(['est' => 'act']);

            // Log de activación
            Log::logActualizacion('usu', $usuario->id, $datosAnteriores, $usuario->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Usuario activado exitosamente',
                'data' => $usuario
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al activar usuario: ' . $e->getMessage(), [
                'usuario_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al activar usuario',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Desactivar usuario
     * POST /api/v1/usuarios/{id}/desactivar
     */
    public function desactivar($id): JsonResponse
    {
        try {
            $usuario = Usuario::findOrFail($id);

            if ($usuario->est === 'ina') {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya está inactivo'
                ], 422);
            }

            $datosAnteriores = $usuario->toArray();
            $usuario->update(['est' => 'ina']);

            // Log de desactivación
            Log::logActualizacion('usu', $usuario->id, $datosAnteriores, $usuario->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Usuario desactivado exitosamente',
                'data' => $usuario
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al desactivar usuario: ' . $e->getMessage(), [
                'usuario_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar usuario',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Verificar correo del usuario
     * POST /api/v1/usuarios/{id}/verificar-correo
     */
    public function verificarCorreo($id): JsonResponse
    {
        try {
            $usuario = Usuario::findOrFail($id);

            if ($usuario->cor_ver) {
                return response()->json([
                    'success' => false,
                    'message' => 'El correo ya está verificado'
                ], 422);
            }

            $datosAnteriores = $usuario->toArray();
            $usuario->verificarCorreo();

            // Log de verificación
            Log::logActualizacion('usu', $usuario->id, $datosAnteriores, $usuario->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Correo verificado exitosamente',
                'data' => $usuario
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al verificar correo: ' . $e->getMessage(), [
                'usuario_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar correo',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de usuarios
     * GET /api/v1/usuarios/estadisticas
     */
    public function estadisticas(): JsonResponse
    {
        try {
            $estadisticas = [
                'total' => Usuario::count(),
                'activos' => Usuario::where('est', 'act')->count(),
                'inactivos' => Usuario::where('est', 'ina')->count(),
                'pendientes' => Usuario::where('est', 'pen')->count(),
                'suspendidos' => Usuario::where('est', 'sus')->count(),
                'clientes' => Usuario::where('rol', 'cli')->count(),
                'operadores' => Usuario::where('rol', 'ope')->count(),
                'administradores' => Usuario::where('rol', 'adm')->count(),
                'administradores_generales' => Usuario::where('rol', 'adm_gen')->count(),
                'verificados' => Usuario::where('cor_ver', true)->count(),
                'no_verificados' => Usuario::where('cor_ver', false)->count(),
                'por_genero' => Usuario::selectRaw('gen, COUNT(*) as total')
                    ->whereNotNull('gen')
                    ->groupBy('gen')
                    ->get(),
                'por_ciudad' => Usuario::selectRaw('ciu, COUNT(*) as total')
                    ->whereNotNull('ciu')
                    ->groupBy('ciu')
                    ->orderByDesc('total')
                    ->limit(10)
                    ->get(),
                'por_departamento' => Usuario::selectRaw('dep, COUNT(*) as total')
                    ->whereNotNull('dep')
                    ->groupBy('dep')
                    ->orderByDesc('total')
                    ->get(),
                'registros_mes_actual' => Usuario::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'registros_ultimo_mes' => Usuario::whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->count(),
                'promedio_registros_por_dia' => Usuario::whereDate('created_at', '>=', now()->subDays(30))
                    ->count() / 30
            ];

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas exitosamente',
                'data' => $estadisticas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Obtener usuarios activos con filtros
     * GET /api/v1/usuarios/activos
     */
    public function usuariosActivos(Request $request): JsonResponse
    {
        try {
            $query = Usuario::with(['roles'])
                ->where('est', 'act');

            // Filtros adicionales
            if ($request->has('rol')) {
                $query->where('rol', $request->rol);
            }

            if ($request->has('ciu')) {
                $query->where('ciu', 'like', '%' . $request->ciu . '%');
            }

            if ($request->has('dep')) {
                $query->where('dep', 'like', '%' . $request->dep . '%');
            }

            if ($request->has('buscar')) {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('nom', 'like', "%{$buscar}%")
                      ->orWhere('ape', 'like', "%{$buscar}%")
                      ->orWhere('cor', 'like', "%{$buscar}%")
                      ->orWhere('doc', 'like', "%{$buscar}%");
                });
            }

            // Ordenamiento
            $orden = $request->get('orden', 'created_at');
            $direccion = $request->get('direccion', 'desc');
            $query->orderBy($orden, $direccion);

            // Paginación
            $porPagina = min($request->get('por_pagina', 15), 100);
            $usuarios = $query->paginate($porPagina);

            return response()->json([
                'success' => true,
                'message' => 'Usuarios activos obtenidos exitosamente',
                'data' => [
                    'usuarios' => $usuarios->items(),
                    'pagination' => [
                        'current_page' => $usuarios->currentPage(),
                        'per_page' => $usuarios->perPage(),
                        'total' => $usuarios->total(),
                        'last_page' => $usuarios->lastPage(),
                        'from' => $usuarios->firstItem(),
                        'to' => $usuarios->lastItem()
                    ],
                    'estadisticas' => [
                        'total_activos' => $usuarios->total(),
                        'por_rol' => Usuario::where('est', 'act')
                            ->selectRaw('rol, COUNT(*) as total')
                            ->groupBy('rol')
                            ->get()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener usuarios activos: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios activos',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Exportar usuarios a CSV
     * GET /api/v1/usuarios/exportar
     */
    public function exportar(Request $request): JsonResponse
    {
        try {
            $query = Usuario::with(['roles']);

            // Aplicar filtros si se proporcionan
            if ($request->has('rol')) {
                $query->where('rol', $request->rol);
            }

            if ($request->has('est')) {
                $query->where('est', $request->est);
            }

            if ($request->has('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }

            if ($request->has('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }

            $usuarios = $query->get();

            // Generar CSV
            $filename = 'usuarios_' . now()->format('Y-m-d_H-i-s') . '.csv';
            $path = 'exports/' . $filename;

            $csvData = [];
            $csvData[] = [
                'ID', 'Nombre', 'Apellidos', 'Correo', 'Teléfono', 'Documento', 
                'Tipo Documento', 'Ciudad', 'Departamento', 'Rol', 'Estado',
                'Correo Verificado', 'Fecha Registro', 'Último Acceso'
            ];

            foreach ($usuarios as $usuario) {
                $csvData[] = [
                    $usuario->id,
                    $usuario->nom,
                    $usuario->ape,
                    $usuario->cor,
                    $usuario->tel,
                    $usuario->doc,
                    $usuario->tip_doc,
                    $usuario->ciu,
                    $usuario->dep,
                    $usuario->rol,
                    $usuario->est,
                    $usuario->cor_ver ? 'Sí' : 'No',
                    $usuario->created_at->format('Y-m-d H:i:s'),
                    $usuario->ult_acc ? $usuario->ult_acc->format('Y-m-d H:i:s') : 'Nunca'
                ];
            }

            Storage::put($path, $this->arrayToCsv($csvData));

            return response()->json([
                'success' => true,
                'message' => 'Archivo exportado exitosamente',
                'data' => [
                    'filename' => $filename,
                    'download_url' => Storage::url($path),
                    'total_registros' => $usuarios->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al exportar usuarios: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar usuarios',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Convertir array a CSV
     */
    private function arrayToCsv(array $data): string
    {
        $output = fopen('php://temp', 'r+');
        
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}
