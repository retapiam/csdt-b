<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnalisisIADerechosEspecializados;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AnalisisIADerechosEspecializadosController extends Controller
{
    /**
     * Obtener todos los análisis
     */
    public function index(Request $request)
    {
        $query = AnalisisIADerechosEspecializados::with('usuario');

        // Filtros
        if ($request->has('area_derecho')) {
            $query->where('area_derecho', $request->area_derecho);
        }

        if ($request->has('tipo_analisis')) {
            $query->where('tipo_analisis', $request->tipo_analisis);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin]);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $analisis = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $analisis,
            'message' => 'Análisis obtenidos exitosamente'
        ]);
    }

    /**
     * Obtener un análisis específico
     */
    public function show($id)
    {
        $analisis = AnalisisIADerechosEspecializados::with('usuario')->find($id);

        if (!$analisis) {
            return response()->json([
                'success' => false,
                'message' => 'Análisis no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $analisis,
            'message' => 'Análisis obtenido exitosamente'
        ]);
    }

    /**
     * Crear nuevo análisis
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_derecho' => 'required|string|max:100',
            'tipo_analisis' => 'required|string|max:100',
            'datos_entrada' => 'required|array',
            'resultado_ia' => 'required|array',
            'metadata' => 'nullable|array',
            'tokens_usados' => 'nullable|integer|min:0',
            'modelo_ia' => 'nullable|string|max:50',
            'tiempo_procesamiento' => 'nullable|numeric|min:0',
            'estado' => 'nullable|in:pendiente,procesando,completado,fallido',
            'observaciones' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $analisis = AnalisisIADerechosEspecializados::create([
                'usuario_id' => Auth::id(),
                'area_derecho' => $request->area_derecho,
                'tipo_analisis' => $request->tipo_analisis,
                'datos_entrada' => $request->datos_entrada,
                'resultado_ia' => $request->resultado_ia,
                'metadata' => $request->metadata,
                'tokens_usados' => $request->tokens_usados ?? 0,
                'modelo_ia' => $request->modelo_ia ?? 'gpt-4',
                'tiempo_procesamiento' => $request->tiempo_procesamiento ?? 0,
                'estado' => $request->estado ?? 'completado',
                'observaciones' => $request->observaciones
            ]);

            return response()->json([
                'success' => true,
                'data' => $analisis,
                'message' => 'Análisis creado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar análisis
     */
    public function update(Request $request, $id)
    {
        $analisis = AnalisisIADerechosEspecializados::find($id);

        if (!$analisis) {
            return response()->json([
                'success' => false,
                'message' => 'Análisis no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'area_derecho' => 'sometimes|string|max:100',
            'tipo_analisis' => 'sometimes|string|max:100',
            'datos_entrada' => 'sometimes|array',
            'resultado_ia' => 'sometimes|array',
            'metadata' => 'nullable|array',
            'tokens_usados' => 'nullable|integer|min:0',
            'modelo_ia' => 'nullable|string|max:50',
            'tiempo_procesamiento' => 'nullable|numeric|min:0',
            'estado' => 'nullable|in:pendiente,procesando,completado,fallido',
            'observaciones' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $analisis->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $analisis,
                'message' => 'Análisis actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar análisis
     */
    public function destroy($id)
    {
        $analisis = AnalisisIADerechosEspecializados::find($id);

        if (!$analisis) {
            return response()->json([
                'success' => false,
                'message' => 'Análisis no encontrado'
            ], 404);
        }

        try {
            $analisis->delete();

            return response()->json([
                'success' => true,
                'message' => 'Análisis eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restaurar análisis eliminado
     */
    public function restore($id)
    {
        $analisis = AnalisisIADerechosEspecializados::withTrashed()->find($id);

        if (!$analisis) {
            return response()->json([
                'success' => false,
                'message' => 'Análisis no encontrado'
            ], 404);
        }

        try {
            $analisis->restore();

            return response()->json([
                'success' => true,
                'data' => $analisis,
                'message' => 'Análisis restaurado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de análisis
     */
    public function estadisticas()
    {
        try {
            $estadisticas = AnalisisIADerechosEspecializados::obtenerEstadisticas();

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
                'message' => 'Estadísticas obtenidas exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener análisis más frecuentes
     */
    public function analisisFrecuentes(Request $request)
    {
        $limite = $request->get('limite', 10);

        try {
            $analisisFrecuentes = AnalisisIADerechosEspecializados::obtenerAnalisisFrecuentes($limite);

            return response()->json([
                'success' => true,
                'data' => $analisisFrecuentes,
                'message' => 'Análisis frecuentes obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener análisis frecuentes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener análisis por rango de fechas
     */
    public function analisisPorFechas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $analisis = AnalisisIADerechosEspecializados::obtenerAnalisisPorFechas(
                $request->fecha_inicio,
                $request->fecha_fin
            );

            return response()->json([
                'success' => true,
                'data' => $analisis,
                'message' => 'Análisis por fechas obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener análisis por fechas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener análisis por usuario y área
     */
    public function analisisPorUsuarioArea(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_id' => 'required|exists:usuarios,id',
            'area' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $analisis = AnalisisIADerechosEspecializados::obtenerAnalisisPorUsuarioArea(
                $request->usuario_id,
                $request->area
            );

            return response()->json([
                'success' => true,
                'data' => $analisis,
                'message' => 'Análisis por usuario y área obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener análisis por usuario y área',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar análisis como completado
     */
    public function marcarCompletado($id)
    {
        $analisis = AnalisisIADerechosEspecializados::find($id);

        if (!$analisis) {
            return response()->json([
                'success' => false,
                'message' => 'Análisis no encontrado'
            ], 404);
        }

        try {
            $analisis->marcarCompletado();

            return response()->json([
                'success' => true,
                'data' => $analisis,
                'message' => 'Análisis marcado como completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar análisis como completado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar análisis como fallido
     */
    public function marcarFallido(Request $request, $id)
    {
        $analisis = AnalisisIADerechosEspecializados::find($id);

        if (!$analisis) {
            return response()->json([
                'success' => false,
                'message' => 'Análisis no encontrado'
            ], 404);
        }

        try {
            $analisis->marcarFallido($request->observaciones);

            return response()->json([
                'success' => true,
                'data' => $analisis,
                'message' => 'Análisis marcado como fallido'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar análisis como fallido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resumen de análisis
     */
    public function resumen($id)
    {
        $analisis = AnalisisIADerechosEspecializados::find($id);

        if (!$analisis) {
            return response()->json([
                'success' => false,
                'message' => 'Análisis no encontrado'
            ], 404);
        }

        try {
            $resumen = $analisis->obtenerResumen();

            return response()->json([
                'success' => true,
                'data' => $resumen,
                'message' => 'Resumen de análisis obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen de análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
