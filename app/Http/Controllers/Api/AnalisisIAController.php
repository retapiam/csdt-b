<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalisisIA;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnalisisIAController extends Controller
{
    /**
     * Listar todos los análisis de IA
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = AnalisisIA::with(['usuario', 'veeduria']);

            // Filtros
            if ($request->has('usu_id')) {
                $query->where('usu_id', $request->usu_id);
            }

            if ($request->has('vee_id')) {
                $query->where('vee_id', $request->vee_id);
            }

            if ($request->has('tip')) {
                $query->where('tip', $request->tip);
            }

            if ($request->has('est')) {
                $query->where('est', $request->est);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('tex', 'like', "%{$search}%")
                      ->orWhere('tip', 'like', "%{$search}%");
                });
            }

            $analisis = $query->orderBy('created_at', 'desc')->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Análisis de IA obtenidos exitosamente',
                'data' => $analisis->items(),
                'meta' => [
                    'pagination' => [
                        'current_page' => $analisis->currentPage(),
                        'per_page' => $analisis->perPage(),
                        'total' => $analisis->total(),
                        'last_page' => $analisis->lastPage(),
                        'from' => $analisis->firstItem(),
                        'to' => $analisis->lastItem()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener análisis de IA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un análisis específico
     */
    public function show($id): JsonResponse
    {
        try {
            $analisis = AnalisisIA::with(['usuario', 'veeduria'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Análisis de IA obtenido exitosamente',
                'data' => $analisis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Análisis de IA no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Crear nuevo análisis de IA
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'usu_id' => 'required|exists:usu,id',
                'vee_id' => 'nullable|exists:vee,id',
                'tip' => 'required|string|max:50',
                'tex' => 'required|string',
                'con' => 'nullable|numeric|between:0,100',
                'est' => 'required|in:pen,pro,com,err',
                'met' => 'nullable|json'
            ]);

            $analisis = AnalisisIA::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Análisis de IA creado exitosamente',
                'data' => $analisis
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear análisis de IA',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Actualizar análisis de IA
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $analisis = AnalisisIA::findOrFail($id);

            $request->validate([
                'tip' => 'required|string|max:50',
                'tex' => 'required|string',
                'res' => 'nullable|json',
                'con' => 'nullable|numeric|between:0,100',
                'est' => 'required|in:pen,pro,com,err',
                'met' => 'nullable|json'
            ]);

            $analisis->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Análisis de IA actualizado exitosamente',
                'data' => $analisis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar análisis de IA',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Eliminar análisis de IA
     */
    public function destroy($id): JsonResponse
    {
        try {
            $analisis = AnalisisIA::findOrFail($id);
            $analisis->delete();

            return response()->json([
                'success' => true,
                'message' => 'Análisis de IA eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar análisis de IA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de análisis de IA
     */
    public function estadisticas(): JsonResponse
    {
        try {
            $estadisticas = [
                'total' => AnalisisIA::count(),
                'por_tipo' => AnalisisIA::selectRaw('tip, COUNT(*) as total')
                    ->groupBy('tip')
                    ->get(),
                'por_estado' => AnalisisIA::selectRaw('est, COUNT(*) as total')
                    ->groupBy('est')
                    ->get(),
                'por_mes' => AnalisisIA::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, COUNT(*) as total')
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->get(),
                'confianza_promedio' => AnalisisIA::avg('con'),
                'recientes' => AnalisisIA::with(['usuario', 'veeduria'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas de análisis de IA obtenidas exitosamente',
                'data' => $estadisticas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas de análisis de IA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analizar texto con IA
     */
    public function analizar(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'usu_id' => 'required|exists:usu,id',
                'vee_id' => 'nullable|exists:vee,id',
                'tip' => 'required|string|max:50',
                'tex' => 'required|string'
            ]);

            // Simular análisis de IA (aquí se integraría con el servicio real de IA)
            $analisis = AnalisisIA::create([
                'usu_id' => $request->usu_id,
                'vee_id' => $request->vee_id,
                'tip' => $request->tip,
                'tex' => $request->tex,
                'res' => [
                    'sentimiento' => 'positivo',
                    'confianza' => 0.85,
                    'categorias' => ['infraestructura', 'servicios'],
                    'recomendaciones' => [
                        'Revisar el estado de la infraestructura mencionada',
                        'Coordinar con las entidades responsables'
                    ]
                ],
                'con' => 85,
                'est' => 'com'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Análisis completado exitosamente',
                'data' => $analisis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al analizar texto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
