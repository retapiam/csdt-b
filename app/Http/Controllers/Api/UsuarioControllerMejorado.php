<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UsuarioMejorado;
use App\Models\LogMejorado;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Controlador mejorado para gestión de usuarios
 * Implementa operaciones CRUD completas con validaciones avanzadas
 */
class UsuarioControllerMejorado extends Controller
{
    // ========================================
    // OPERACIONES CRUD BÁSICAS
    // ========================================

    /**
     * Listar usuarios con filtros, búsqueda y paginación
     * GET /api/v1/usuarios
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = UsuarioMejorado::with(['veedurias', 'donaciones', 'tareas']);

            // Aplicar filtros
            $query = $this->aplicarFiltros($query, $request);

            // Aplicar búsqueda
            if ($request->has('buscar') && $request->buscar !== '') {
                $query->buscar($request->buscar);
            }

            // Aplicar ordenamiento
            $orden = $request->get('orden', 'created_at');
            $direccion = $request->get('direccion', 'desc');
            $query->orderBy($orden, $direccion);

            // Aplicar paginación
            $porPagina = $request->get('por_pagina', 15);
            $usuarios = $query->paginate($porPagina);

            // Registrar log
            $this->registrarLog('listar_usuarios', 'usu', null, null, null, $request);

            return response()->json([
                'success' => true,
                'data' => $usuarios,
                'message' => 'Usuarios obtenidos exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios: ' . $e->getMessage()
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
            $validator = $this->validarDatos($request, 'create');

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $datos = $request->all();
            $datos['con'] = Hash::make($datos['con']);
            $datos['cod'] = $this->generarCodigo();

            $usuario = UsuarioMejorado::create($datos);

            // Registrar log
            $this->registrarLog('crear_usuario', 'usu', $usuario->id, null, $usuario->toArray(), $request);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $usuario->load(['veedurias', 'donaciones', 'tareas']),
                'message' => 'Usuario creado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar usuario específico
     * GET /api/v1/usuarios/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $usuario = UsuarioMejorado::with(['veedurias', 'donaciones', 'tareas', 'archivos'])
                ->findOrFail($id);

            // Registrar log
            $this->registrarLog('ver_usuario', 'usu', $id, null, null, $request);

            return response()->json([
                'success' => true,
                'data' => $usuario,
                'message' => 'Usuario obtenido exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }
    }

    /**
     * Actualizar usuario
     * PUT /api/v1/usuarios/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $usuario = UsuarioMejorado::findOrFail($id);
            $datosAnteriores = $usuario->toArray();

            $validator = $this->validarDatos($request, 'update', $id);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $datos = $request->all();
            
            // Si se está actualizando la contraseña, encriptarla
            if (isset($datos['con'])) {
                $datos['con'] = Hash::make($datos['con']);
            }

            $usuario->update($datos);

            // Registrar log
            $this->registrarLog('actualizar_usuario', 'usu', $id, $datosAnteriores, $usuario->toArray(), $request);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $usuario->load(['veedurias', 'donaciones', 'tareas']),
                'message' => 'Usuario actualizado exitosamente'
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
     * Eliminar usuario (soft delete)
     * DELETE /api/v1/usuarios/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $usuario = UsuarioMejorado::findOrFail($id);
            $datosAnteriores = $usuario->toArray();

            DB::beginTransaction();

            $usuario->delete();

            // Registrar log
            $this->registrarLog('eliminar_usuario', 'usu', $id, $datosAnteriores, null, $request);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // OPERACIONES ADICIONALES
    // ========================================

    /**
     * Restaurar usuario eliminado
     * POST /api/v1/usuarios/{id}/restaurar
     */
    public function restaurar(Request $request, $id): JsonResponse
    {
        try {
            $usuario = UsuarioMejorado::withTrashed()->findOrFail($id);

            DB::beginTransaction();

            $usuario->restore();

            // Registrar log
            $this->registrarLog('restaurar_usuario', 'usu', $id, null, $usuario->toArray(), $request);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $usuario,
                'message' => 'Usuario restaurado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del usuario
     * PUT /api/v1/usuarios/{id}/cambiar-estado
     */
    public function cambiarEstado(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'est' => 'required|in:act,ina,pen,sus'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estado inválido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $usuario = UsuarioMejorado::findOrFail($id);
            $estadoAnterior = $usuario->est;

            DB::beginTransaction();

            $usuario->cambiarEstado($request->est);

            // Registrar log
            $this->registrarLog('cambiar_estado_usuario', 'usu', $id, 
                ['estado_anterior' => $estadoAnterior], 
                ['estado_nuevo' => $request->est], 
                $request
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $usuario,
                'message' => 'Estado del usuario actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar correo electrónico
     * POST /api/v1/usuarios/{id}/verificar-correo
     */
    public function verificarCorreo(Request $request, $id): JsonResponse
    {
        try {
            $usuario = UsuarioMejorado::findOrFail($id);

            DB::beginTransaction();

            $usuario->verificarCorreo();

            // Registrar log
            $this->registrarLog('verificar_correo_usuario', 'usu', $id, 
                ['correo_verificado' => false], 
                ['correo_verificado' => true], 
                $request
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $usuario,
                'message' => 'Correo electrónico verificado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar correo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar contraseña
     * PUT /api/v1/usuarios/{id}/cambiar-contrasena
     */
    public function cambiarContrasena(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'con_actual' => 'required|string',
                'con_nueva' => 'required|string|min:8|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $usuario = UsuarioMejorado::findOrFail($id);

            // Verificar contraseña actual
            if (!Hash::check($request->con_actual, $usuario->con)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta'
                ], 400);
            }

            DB::beginTransaction();

            $usuario->cambiarContrasena($request->con_nueva);

            // Registrar log
            $this->registrarLog('cambiar_contrasena_usuario', 'usu', $id, null, null, $request);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar contraseña: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // OPERACIONES DE BÚSQUEDA Y FILTROS
    // ========================================

    /**
     * Buscar usuarios
     * GET /api/v1/usuarios/buscar
     */
    public function buscar(Request $request): JsonResponse
    {
        try {
            $query = UsuarioMejorado::query();

            // Aplicar filtros
            $query = $this->aplicarFiltros($query, $request);

            // Aplicar búsqueda
            if ($request->has('termino') && $request->termino !== '') {
                $query->buscar($request->termino);
            }

            // Aplicar ordenamiento
            $orden = $request->get('orden', 'created_at');
            $direccion = $request->get('direccion', 'desc');
            $query->orderBy($orden, $direccion);

            // Aplicar paginación
            $porPagina = $request->get('por_pagina', 15);
            $usuarios = $query->paginate($porPagina);

            return response()->json([
                'success' => true,
                'data' => $usuarios,
                'message' => 'Búsqueda completada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar datos del usuario
     * POST /api/v1/usuarios/validar
     */
    public function validar(Request $request): JsonResponse
    {
        try {
            $validator = $this->validarDatos($request, 'validate');

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Datos válidos'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // OPERACIONES DE ESTADÍSTICAS
    // ========================================

    /**
     * Obtener estadísticas de usuarios
     * GET /api/v1/usuarios/estadisticas
     */
    public function estadisticas(Request $request): JsonResponse
    {
        try {
            $estadisticas = [
                'total' => UsuarioMejorado::count(),
                'activos' => UsuarioMejorado::activos()->count(),
                'inactivos' => UsuarioMejorado::inactivos()->count(),
                'pendientes' => UsuarioMejorado::pendientes()->count(),
                'suspendidos' => UsuarioMejorado::suspendidos()->count(),
                'por_rol' => UsuarioMejorado::selectRaw('rol, COUNT(*) as total')
                    ->groupBy('rol')
                    ->get(),
                'por_ciudad' => UsuarioMejorado::selectRaw('ciu, COUNT(*) as total')
                    ->whereNotNull('ciu')
                    ->groupBy('ciu')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get(),
                'por_departamento' => UsuarioMejorado::selectRaw('dep, COUNT(*) as total')
                    ->whereNotNull('dep')
                    ->groupBy('dep')
                    ->orderBy('total', 'desc')
                    ->get(),
                'correos_verificados' => UsuarioMejorado::where('cor_ver', true)->count(),
                'correos_no_verificados' => UsuarioMejorado::where('cor_ver', false)->count(),
                'registros_mes_actual' => UsuarioMejorado::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'registros_ultimo_mes' => UsuarioMejorado::whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
                'message' => 'Estadísticas obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de un usuario específico
     * GET /api/v1/usuarios/{id}/estadisticas
     */
    public function estadisticasUsuario(Request $request, $id): JsonResponse
    {
        try {
            $usuario = UsuarioMejorado::findOrFail($id);
            $estadisticas = $usuario->estadisticas();

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
                'message' => 'Estadísticas del usuario obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas del usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // OPERACIONES DE EXPORTACIÓN
    // ========================================

    /**
     * Exportar usuarios
     * GET /api/v1/usuarios/exportar
     */
    public function exportar(Request $request): JsonResponse
    {
        try {
            $query = UsuarioMejorado::query();

            // Aplicar filtros
            $query = $this->aplicarFiltros($query, $request);

            $usuarios = $query->get();
            $datosExport = $usuarios->map(function($usuario) {
                return $usuario->toArrayExport();
            });

            return response()->json([
                'success' => true,
                'data' => $datosExport,
                'total' => $datosExport->count(),
                'message' => 'Datos exportados exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar datos: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // MÉTODOS PRIVADOS
    // ========================================

    /**
     * Aplicar filtros a la consulta
     */
    private function aplicarFiltros($query, Request $request)
    {
        if ($request->has('rol') && $request->rol !== '') {
            $query->porRol($request->rol);
        }

        if ($request->has('est') && $request->est !== '') {
            $query->where('est', $request->est);
        }

        if ($request->has('ciu') && $request->ciu !== '') {
            $query->porCiudad($request->ciu);
        }

        if ($request->has('dep') && $request->dep !== '') {
            $query->porDepartamento($request->dep);
        }

        if ($request->has('gen') && $request->gen !== '') {
            $query->where('gen', $request->gen);
        }

        if ($request->has('cor_ver') && $request->cor_ver !== '') {
            $query->where('cor_ver', $request->cor_ver === 'true');
        }

        if ($request->has('fec_ini')) {
            $query->whereDate('created_at', '>=', $request->fec_ini);
        }

        if ($request->has('fec_fin')) {
            $query->whereDate('created_at', '<=', $request->fec_fin);
        }

        return $query;
    }

    /**
     * Validar datos del usuario
     */
    private function validarDatos(Request $request, string $accion, $id = null)
    {
        $reglas = [
            'nom' => 'required|string|max:100',
            'ape' => 'required|string|max:100',
            'cor' => 'required|email|max:150|unique:usu,cor' . ($id ? ",{$id}" : ''),
            'tel' => 'nullable|string|max:20',
            'doc' => 'required|string|max:20|unique:usu,doc' . ($id ? ",{$id}" : ''),
            'tip_doc' => 'required|in:cc,ce,ti,pp,nit',
            'fec_nac' => 'nullable|date|before:today',
            'dir' => 'nullable|string|max:200',
            'ciu' => 'nullable|string|max:100',
            'dep' => 'nullable|string|max:100',
            'gen' => 'nullable|in:m,f,o,n',
            'rol' => 'required|in:cli,ope,adm,adm_gen',
            'est' => 'nullable|in:act,ina,pen,sus',
        ];

        if ($accion === 'create') {
            $reglas['con'] = 'required|string|min:8|confirmed';
        } elseif ($accion === 'update') {
            $reglas['con'] = 'nullable|string|min:8|confirmed';
        }

        return Validator::make($request->all(), $reglas);
    }

    /**
     * Generar código único para el usuario
     */
    private function generarCodigo()
    {
        do {
            $codigo = 'USU' . strtoupper(Str::random(8));
        } while (UsuarioMejorado::where('cod', $codigo)->exists());

        return $codigo;
    }

    /**
     * Registrar log de actividad
     */
    private function registrarLog(string $accion, string $tabla, $registroId, $datosAnteriores, $datosNuevos, Request $request)
    {
        try {
            LogMejorado::create([
                'usu_id' => $request->user()?->id,
                'acc' => $accion,
                'tab' => $tabla,
                'reg_id' => $registroId,
                'des' => "Acción: {$accion} en {$tabla}",
                'dat_ant' => $datosAnteriores,
                'dat_nue' => $datosNuevos,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'niv' => 'info'
            ]);
        } catch (\Exception $e) {
            // Log de error silencioso para no interrumpir el flujo principal
            \Log::error('Error al registrar log: ' . $e->getMessage());
        }
    }
}
