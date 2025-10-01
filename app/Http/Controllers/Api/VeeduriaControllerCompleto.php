<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Veeduria;
use App\Models\Usuario;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Controlador completo para gestión de veedurías
 * Implementa todas las operaciones CRUD estándar según CONTROL.md
 */
class VeeduriaControllerCompleto extends Controller
{
    /**
     * Listar veedurías con filtros, búsqueda y paginación
     * GET /api/v1/veedurias
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Veeduria::with(['usuario', 'operador', 'tareas']);

            // Filtros
            if ($request->has('est') && $request->est !== '') {
                $query->where('est', $request->est);
            }

            if ($request->has('tip') && $request->tip !== '') {
                $query->where('tip', $request->tip);
            }

            if ($request->has('pri') && $request->pri !== '') {
                $query->where('pri', $request->pri);
            }

            if ($request->has('cat') && $request->cat !== '') {
                $query->where('cat', $request->cat);
            }

            if ($request->has('usu_id') && $request->usu_id !== '') {
                $query->where('usu_id', $request->usu_id);
            }

            if ($request->has('ope_id') && $request->ope_id !== '') {
                $query->where('ope_id', $request->ope_id);
            }

            if ($request->has('ciu') && $request->ciu !== '') {
                $query->where('ciu', 'like', '%' . $request->ciu . '%');
            }

            if ($request->has('dep') && $request->dep !== '') {
                $query->where('dep', 'like', '%' . $request->dep . '%');
            }

            // Filtro por presupuesto
            if ($request->has('pre_min')) {
                $query->where('pre', '>=', $request->pre_min);
            }

            if ($request->has('pre_max')) {
                $query->where('pre', '<=', $request->pre_max);
            }

            // Filtro por fecha de registro
            if ($request->has('fecha_desde')) {
                $query->whereDate('fec_reg', '>=', $request->fecha_desde);
            }

            if ($request->has('fecha_hasta')) {
                $query->whereDate('fec_reg', '<=', $request->fecha_hasta);
            }

            // Búsqueda global
            if ($request->has('buscar') && $request->buscar !== '') {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('tit', 'like', '%' . $buscar . '%')
                      ->orWhere('des', 'like', '%' . $buscar . '%')
                      ->orWhere('num_rad', 'like', '%' . $buscar . '%')
                      ->orWhere('ubi', 'like', '%' . $buscar . '%');
                });
            }

            // Ordenamiento
            $orden = $request->get('orden', 'fec_reg');
            $direccion = $request->get('direccion', 'desc');
            
            // Validar campos de ordenamiento
            $camposPermitidos = ['tit', 'est', 'tip', 'pri', 'cat', 'fec_reg', 'fec_rad', 'fec_cer', 'pre'];
            if (in_array($orden, $camposPermitidos)) {
                $query->orderBy($orden, $direccion);
            } else {
                $query->orderBy('fec_reg', 'desc');
            }

            // Paginación
            $porPagina = min($request->get('por_pagina', 15), 100);
            $veedurias = $query->paginate($porPagina);

            // Formatear respuesta
            $datos = [
                'veedurias' => $veedurias->items(),
                'pagination' => [
                    'current_page' => $veedurias->currentPage(),
                    'per_page' => $veedurias->perPage(),
                    'total' => $veedurias->total(),
                    'last_page' => $veedurias->lastPage(),
                    'from' => $veedurias->firstItem(),
                    'to' => $veedurias->lastItem(),
                    'has_more_pages' => $veedurias->hasMorePages()
                ],
                'filters_applied' => $request->only(['est', 'tip', 'pri', 'cat', 'usu_id', 'ope_id', 'ciu', 'dep', 'pre_min', 'pre_max', 'fecha_desde', 'fecha_hasta', 'buscar'])
            ];

            return response()->json([
                'success' => true,
                'message' => 'Veedurías obtenidas exitosamente',
                'data' => $datos
            ]);

        } catch (\Exception $e) {
            Log::error('Error al listar veedurías: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener veedurías',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Crear nueva veeduría
     * POST /api/v1/veedurias
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validación
            $validator = Validator::make($request->all(), [
                'usu_id' => 'required|exists:usu,id',
                'ope_id' => 'nullable|exists:usu,id',
                'tit' => 'required|string|max:200',
                'des' => 'required|string',
                'tip' => 'required|in:pet,que,rec,sug,fel,den',
                'est' => 'nullable|in:pen,pro,rad,cer,can',
                'pri' => 'nullable|in:baj,med,alt,urg',
                'cat' => 'nullable|in:inf,ser,seg,edu,sal,tra,amb,otr',
                'ubi' => 'nullable|string|max:200',
                'pre' => 'nullable|numeric|min:0',
                'not_ope' => 'nullable|string',
                'rec_ia' => 'nullable|array',
                'arc' => 'nullable|array'
            ], Veeduria::mensajes());

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Crear veeduría
            $veeduria = Veeduria::create([
                'usu_id' => $request->usu_id,
                'ope_id' => $request->ope_id,
                'tit' => $request->tit,
                'des' => $request->des,
                'tip' => $request->tip,
                'est' => $request->est ?? 'pen',
                'pri' => $request->pri ?? 'med',
                'cat' => $request->cat,
                'ubi' => $request->ubi,
                'pre' => $request->pre,
                'not_ope' => $request->not_ope,
                'rec_ia' => $request->rec_ia,
                'arc' => $request->arc
            ]);

            // Log de creación
            Log::logCreacion('vee', $veeduria->id, $veeduria->toArray());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Veeduría creada exitosamente',
                'data' => $veeduria->load(['usuario', 'operador'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear veeduría: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear veeduría',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Mostrar veeduría específica
     * GET /api/v1/veedurias/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $veeduria = Veeduria::with([
                'usuario',
                'operador',
                'tareas' => function($query) {
                    $query->latest()->limit(10);
                },
                'archivos' => function($query) {
                    $query->latest()->limit(10);
                },
                'analisisIA' => function($query) {
                    $query->latest()->limit(5);
                }
            ])->findOrFail($id);

            // Estadísticas de la veeduría
            $estadisticas = [
                'total_tareas' => $veeduria->tareas()->count(),
                'tareas_completadas' => $veeduria->tareas()->where('est', 'com')->count(),
                'tareas_pendientes' => $veeduria->tareas()->whereIn('est', ['pen', 'pro'])->count(),
                'tareas_vencidas' => $veeduria->tareas()->where('fec_ven', '<', now())->whereIn('est', ['pen', 'pro'])->count(),
                'total_archivos' => $veeduria->archivos()->count(),
                'dias_transcurridos' => $veeduria->fec_reg->diffInDays(now()),
                'es_urgente' => $veeduria->es_urgente,
                'estado_descripcion' => $veeduria->estado_descripcion,
                'tipo_descripcion' => $veeduria->tipo_descripcion,
                'prioridad_descripcion' => $veeduria->prioridad_descripcion,
                'categoria_descripcion' => $veeduria->categoria_descripcion
            ];

            $datos = [
                'veeduria' => $veeduria,
                'estadisticas' => $estadisticas
            ];

            return response()->json([
                'success' => true,
                'message' => 'Veeduría obtenida exitosamente',
                'data' => $datos
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veeduría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al obtener veeduría: ' . $e->getMessage(), [
                'veeduria_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener veeduría',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Actualizar veeduría
     * PUT/PATCH /api/v1/veedurias/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $veeduria = Veeduria::findOrFail($id);

            // Validación
            $validator = Validator::make($request->all(), Veeduria::reglas($id), Veeduria::mensajes());

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $datosAnteriores = $veeduria->toArray();

            // Preparar datos para actualización
            $datos = $request->only([
                'usu_id', 'ope_id', 'tit', 'des', 'tip', 'est', 
                'pri', 'cat', 'ubi', 'pre', 'not_ope', 'rec_ia', 'arc'
            ]);

            // Actualizar veeduría
            $veeduria->update($datos);

            // Log de actualización
            Log::logActualizacion('vee', $veeduria->id, $datosAnteriores, $veeduria->toArray());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Veeduría actualizada exitosamente',
                'data' => $veeduria->load(['usuario', 'operador'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veeduría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al actualizar veeduría: ' . $e->getMessage(), [
                'veeduria_id' => $id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar veeduría',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Eliminar veeduría (soft delete)
     * DELETE /api/v1/veedurias/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $veeduria = Veeduria::findOrFail($id);

            // Verificar si la veeduría tiene tareas relacionadas
            $tieneTareas = $veeduria->tareas()->count() > 0;
            $tieneArchivos = $veeduria->archivos()->count() > 0;

            if ($tieneTareas || $tieneArchivos) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la veeduría porque tiene datos relacionados',
                    'data' => [
                        'tiene_tareas' => $tieneTareas,
                        'tiene_archivos' => $tieneArchivos
                    ]
                ], 422);
            }

            $datosAnteriores = $veeduria->toArray();
            $veeduria->delete();

            // Log de eliminación
            Log::logEliminacion('vee', $veeduria->id, $datosAnteriores);

            return response()->json([
                'success' => true,
                'message' => 'Veeduría eliminada exitosamente'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veeduría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al eliminar veeduría: ' . $e->getMessage(), [
                'veeduria_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar veeduría',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Restaurar veeduría eliminada
     * POST /api/v1/veedurias/{id}/restaurar
     */
    public function restaurar($id): JsonResponse
    {
        try {
            $veeduria = Veeduria::withTrashed()->findOrFail($id);

            if (!$veeduria->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La veeduría no está eliminada'
                ], 422);
            }

            $veeduria->restore();

            // Log de restauración
            Log::logRestauracion('vee', $veeduria->id);

            return response()->json([
                'success' => true,
                'message' => 'Veeduría restaurada exitosamente',
                'data' => $veeduria
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veeduría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al restaurar veeduría: ' . $e->getMessage(), [
                'veeduria_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar veeduría',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Radicar veeduría
     * POST /api/v1/veedurias/{id}/radicar
     */
    public function radicar($id): JsonResponse
    {
        try {
            $veeduria = Veeduria::findOrFail($id);

            if ($veeduria->est === 'rad') {
                return response()->json([
                    'success' => false,
                    'message' => 'La veeduría ya está radicada'
                ], 422);
            }

            if ($veeduria->est === 'cer') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede radicar una veeduría cerrada'
                ], 422);
            }

            if ($veeduria->est === 'can') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede radicar una veeduría cancelada'
                ], 422);
            }

            $datosAnteriores = $veeduria->toArray();
            $veeduria->radicar();

            // Log de radicación
            Log::logActualizacion('vee', $veeduria->id, $datosAnteriores, $veeduria->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Veeduría radicada exitosamente',
                'data' => $veeduria
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veeduría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al radicar veeduría: ' . $e->getMessage(), [
                'veeduria_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al radicar veeduría',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Cerrar veeduría
     * POST /api/v1/veedurias/{id}/cerrar
     */
    public function cerrar(Request $request, $id): JsonResponse
    {
        try {
            $veeduria = Veeduria::findOrFail($id);

            if ($veeduria->est === 'cer') {
                return response()->json([
                    'success' => false,
                    'message' => 'La veeduría ya está cerrada'
                ], 422);
            }

            if ($veeduria->est === 'can') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cerrar una veeduría cancelada'
                ], 422);
            }

            $notas = $request->get('notas');

            $datosAnteriores = $veeduria->toArray();
            $veeduria->cerrar($notas);

            // Log de cierre
            Log::logActualizacion('vee', $veeduria->id, $datosAnteriores, $veeduria->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Veeduría cerrada exitosamente',
                'data' => $veeduria
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veeduría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al cerrar veeduría: ' . $e->getMessage(), [
                'veeduria_id' => $id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar veeduría',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Cancelar veeduría
     * POST /api/v1/veedurias/{id}/cancelar
     */
    public function cancelar(Request $request, $id): JsonResponse
    {
        try {
            $veeduria = Veeduria::findOrFail($id);

            if ($veeduria->est === 'can') {
                return response()->json([
                    'success' => false,
                    'message' => 'La veeduría ya está cancelada'
                ], 422);
            }

            if ($veeduria->est === 'cer') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cancelar una veeduría cerrada'
                ], 422);
            }

            $motivo = $request->get('motivo');

            $datosAnteriores = $veeduria->toArray();
            $veeduria->cancelar($motivo);

            // Log de cancelación
            Log::logActualizacion('vee', $veeduria->id, $datosAnteriores, $veeduria->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Veeduría cancelada exitosamente',
                'data' => $veeduria
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veeduría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al cancelar veeduría: ' . $e->getMessage(), [
                'veeduria_id' => $id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar veeduría',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Asignar operador a veeduría
     * POST /api/v1/veedurias/{id}/asignar-operador
     */
    public function asignarOperador(Request $request, $id): JsonResponse
    {
        try {
            $veeduria = Veeduria::findOrFail($id);

            // Validación
            $validator = Validator::make($request->all(), [
                'ope_id' => 'required|exists:usu,id',
                'notas' => 'nullable|string'
            ], [
                'ope_id.required' => 'El operador es obligatorio',
                'ope_id.exists' => 'El operador seleccionado no existe'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar que el usuario sea operador
            $operador = Usuario::findOrFail($request->ope_id);
            if (!in_array($operador->rol, ['ope', 'adm', 'adm_gen'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario seleccionado no es un operador'
                ], 422);
            }

            $datosAnteriores = $veeduria->toArray();
            $veeduria->asignarOperador($request->ope_id, $request->notas);

            // Log de asignación
            Log::logActualizacion('vee', $veeduria->id, $datosAnteriores, $veeduria->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Operador asignado exitosamente',
                'data' => $veeduria->load(['usuario', 'operador'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veeduría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al asignar operador: ' . $e->getMessage(), [
                'veeduria_id' => $id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar operador',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Buscar veedurías
     * GET /api/v1/veedurias/buscar
     */
    public function buscar(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'termino' => 'required|string|min:2|max:100',
                'limite' => 'nullable|integer|min:1|max:50',
                'est' => 'nullable|in:pen,pro,rad,cer,can',
                'tip' => 'nullable|in:pet,que,rec,sug,fel,den'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parámetros de búsqueda incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $termino = $request->get('termino');
            $limite = $request->get('limite', 10);

            $query = Veeduria::with(['usuario', 'operador'])
                ->where(function($q) use ($termino) {
                    $q->where('tit', 'like', '%' . $termino . '%')
                      ->orWhere('des', 'like', '%' . $termino . '%')
                      ->orWhere('num_rad', 'like', '%' . $termino . '%')
                      ->orWhere('ubi', 'like', '%' . $termino . '%');
                });

            // Aplicar filtros adicionales
            if ($request->has('est')) {
                $query->where('est', $request->est);
            }

            if ($request->has('tip')) {
                $query->where('tip', $request->tip);
            }

            $veedurias = $query->select(['id', 'tit', 'tip', 'est', 'pri', 'fec_reg', 'usu_id', 'ope_id'])
                ->limit($limite)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Búsqueda completada exitosamente',
                'data' => $veedurias,
                'meta' => [
                    'termino' => $termino,
                    'total_resultados' => $veedurias->count(),
                    'limite_aplicado' => $limite
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en búsqueda de veedurías: ' . $e->getMessage(), [
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
     * Obtener estadísticas de veedurías
     * GET /api/v1/veedurias/estadisticas
     */
    public function estadisticas(Request $request): JsonResponse
    {
        try {
            $query = Veeduria::query();

            // Aplicar filtros de fecha si se proporcionan
            if ($request->has('fecha_desde')) {
                $query->whereDate('fec_reg', '>=', $request->fecha_desde);
            }

            if ($request->has('fecha_hasta')) {
                $query->whereDate('fec_reg', '<=', $request->fecha_hasta);
            }

            $estadisticas = [
                'total' => $query->count(),
                'por_estado' => $query->selectRaw('est, COUNT(*) as total')
                    ->groupBy('est')
                    ->get(),
                'por_tipo' => $query->selectRaw('tip, COUNT(*) as total')
                    ->groupBy('tip')
                    ->get(),
                'por_prioridad' => $query->selectRaw('pri, COUNT(*) as total')
                    ->groupBy('pri')
                    ->get(),
                'por_categoria' => $query->selectRaw('cat, COUNT(*) as total')
                    ->whereNotNull('cat')
                    ->groupBy('cat')
                    ->get(),
                'pendientes' => $query->where('est', 'pen')->count(),
                'en_proceso' => $query->where('est', 'pro')->count(),
                'radicadas' => $query->where('est', 'rad')->count(),
                'cerradas' => $query->where('est', 'cer')->count(),
                'canceladas' => $query->where('est', 'can')->count(),
                'con_presupuesto' => $query->whereNotNull('pre')->where('pre', '>', 0)->count(),
                'sin_presupuesto' => $query->where(function($q) {
                    $q->whereNull('pre')->orWhere('pre', 0);
                })->count(),
                'presupuesto_total' => $query->sum('pre'),
                'presupuesto_promedio' => $query->whereNotNull('pre')->where('pre', '>', 0)->avg('pre'),
                'urgentes' => $query->where('pri', 'urg')->count(),
                'por_mes' => $query->selectRaw('DATE_FORMAT(fec_reg, "%Y-%m") as mes, COUNT(*) as total')
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->limit(12)
                    ->get(),
                'tiempo_promedio_resolucion' => $query->whereNotNull('fec_cer')
                    ->selectRaw('AVG(DATEDIFF(fec_cer, fec_reg)) as dias_promedio')
                    ->value('dias_promedio')
            ];

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas exitosamente',
                'data' => $estadisticas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas: ' . $e->getMessage(), [
                'request' => $request->all(),
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
     * Obtener estadísticas específicas de una veeduría
     * GET /api/v1/veedurias/{id}/estadisticas
     */
    public function estadisticasVeeduria($id): JsonResponse
    {
        try {
            $veeduria = Veeduria::findOrFail($id);

            $estadisticas = [
                'total_tareas' => $veeduria->tareas()->count(),
                'tareas_completadas' => $veeduria->tareas()->where('est', 'com')->count(),
                'tareas_pendientes' => $veeduria->tareas()->whereIn('est', ['pen', 'pro'])->count(),
                'tareas_vencidas' => $veeduria->tareas()->where('fec_ven', '<', now())->whereIn('est', ['pen', 'pro'])->count(),
                'total_archivos' => $veeduria->archivos()->count(),
                'dias_transcurridos' => $veeduria->fec_reg->diffInDays(now()),
                'es_urgente' => $veeduria->es_urgente,
                'estado_descripcion' => $veeduria->estado_descripcion,
                'tipo_descripcion' => $veeduria->tipo_descripcion,
                'prioridad_descripcion' => $veeduria->prioridad_descripcion,
                'categoria_descripcion' => $veeduria->categoria_descripcion,
                'tiempo_resolucion' => $veeduria->fec_cer ? $veeduria->fec_reg->diffInDays($veeduria->fec_cer) : null,
                'analisis_ia_count' => $veeduria->analisisIA()->count(),
                'recomendaciones_ia_count' => $veeduria->rec_ia ? count($veeduria->rec_ia) : 0
            ];

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas de veeduría obtenidas exitosamente',
                'data' => $estadisticas
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veeduría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de veeduría: ' . $e->getMessage(), [
                'veeduria_id' => $id,
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
     * Agregar recomendación de IA
     * POST /api/v1/veedurias/{id}/recomendacion-ia
     */
    public function agregarRecomendacionIA(Request $request, $id): JsonResponse
    {
        try {
            $veeduria = Veeduria::findOrFail($id);

            // Validación
            $validator = Validator::make($request->all(), [
                'recomendacion' => 'required|string|max:1000',
                'tipo' => 'nullable|string|max:50'
            ], [
                'recomendacion.required' => 'La recomendación es obligatoria',
                'recomendacion.max' => 'La recomendación no puede exceder 1000 caracteres'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $datosAnteriores = $veeduria->toArray();
            $veeduria->agregarRecomendacionIA($request->recomendacion);

            // Log de actualización
            Log::logActualizacion('vee', $veeduria->id, $datosAnteriores, $veeduria->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Recomendación de IA agregada exitosamente',
                'data' => $veeduria
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Veeduría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al agregar recomendación IA: ' . $e->getMessage(), [
                'veeduria_id' => $id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar recomendación',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Exportar veedurías a CSV
     * GET /api/v1/veedurias/exportar
     */
    public function exportar(Request $request): JsonResponse
    {
        try {
            $query = Veeduria::with(['usuario', 'operador']);

            // Aplicar filtros si se proporcionan
            if ($request->has('est')) {
                $query->where('est', $request->est);
            }

            if ($request->has('tip')) {
                $query->where('tip', $request->tip);
            }

            if ($request->has('pri')) {
                $query->where('pri', $request->pri);
            }

            if ($request->has('fecha_desde')) {
                $query->whereDate('fec_reg', '>=', $request->fecha_desde);
            }

            if ($request->has('fecha_hasta')) {
                $query->whereDate('fec_reg', '<=', $request->fecha_hasta);
            }

            $veedurias = $query->get();

            // Generar CSV
            $filename = 'veedurias_' . now()->format('Y-m-d_H-i-s') . '.csv';
            $path = 'exports/' . $filename;

            $csvData = [];
            $csvData[] = [
                'ID', 'Título', 'Tipo', 'Estado', 'Prioridad', 'Categoría', 'Usuario', 
                'Operador', 'Fecha Registro', 'Fecha Radicación', 'Fecha Cierre', 
                'Número Radicación', 'Presupuesto', 'Ubicación'
            ];

            foreach ($veedurias as $veeduria) {
                $csvData[] = [
                    $veeduria->id,
                    $veeduria->tit,
                    $veeduria->tipo_descripcion,
                    $veeduria->estado_descripcion,
                    $veeduria->prioridad_descripcion,
                    $veeduria->categoria_descripcion,
                    $veeduria->usuario ? $veeduria->usuario->nombre_completo : '',
                    $veeduria->operador ? $veeduria->operador->nombre_completo : '',
                    $veeduria->fec_reg->format('Y-m-d H:i:s'),
                    $veeduria->fec_rad ? $veeduria->fec_rad->format('Y-m-d H:i:s') : '',
                    $veeduria->fec_cer ? $veeduria->fec_cer->format('Y-m-d H:i:s') : '',
                    $veeduria->num_rad,
                    $veeduria->pre,
                    $veeduria->ubi
                ];
            }

            Storage::put($path, $this->arrayToCsv($csvData));

            return response()->json([
                'success' => true,
                'message' => 'Archivo exportado exitosamente',
                'data' => [
                    'filename' => $filename,
                    'download_url' => Storage::url($path),
                    'total_registros' => $veedurias->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al exportar veedurías: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar veedurías',
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
