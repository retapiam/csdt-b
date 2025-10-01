<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NarracionIA;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NarracionIAController extends Controller
{
    /**
     * Listar todas las narraciones de IA
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = NarracionIA::with(['usuario']);

            // Filtros
            if ($request->has('usu_id')) {
                $query->where('usu_id', $request->usu_id);
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
                    $q->where('cod', 'like', "%{$search}%")
                      ->orWhere('tex', 'like', "%{$search}%")
                      ->orWhere('nar_gen', 'like', "%{$search}%");
                });
            }

            $narraciones = $query->orderBy('created_at', 'desc')->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Narraciones de IA obtenidas exitosamente',
                'data' => $narraciones->items(),
                'meta' => [
                    'pagination' => [
                        'current_page' => $narraciones->currentPage(),
                        'per_page' => $narraciones->perPage(),
                        'total' => $narraciones->total(),
                        'last_page' => $narraciones->lastPage(),
                        'from' => $narraciones->firstItem(),
                        'to' => $narraciones->lastItem()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener narraciones de IA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una narración específica
     */
    public function show($id): JsonResponse
    {
        try {
            $narracion = NarracionIA::with(['usuario'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Narración de IA obtenida exitosamente',
                'data' => $narracion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Narración de IA no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Crear nueva narración de IA
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'usu_id' => 'required|exists:usu,id',
                'cod' => 'required|string|max:50|unique:ai_nar,cod',
                'tip' => 'required|in:act,res,inf,com',
                'tex' => 'required|string',
                'nar_gen' => 'nullable|string',
                'con' => 'integer|min:0|max:100',
                'dat_cli' => 'nullable|json',
                'ubi' => 'nullable|json',
                'res_ai' => 'nullable|json',
                'est' => 'required|in:pen,pro,com,can'
            ]);

            $narracion = NarracionIA::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Narración de IA creada exitosamente',
                'data' => $narracion
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear narración de IA',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Actualizar narración de IA
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $narracion = NarracionIA::findOrFail($id);

            $request->validate([
                'cod' => 'required|string|max:50|unique:ai_nar,cod,' . $id,
                'tip' => 'required|in:act,res,inf,com',
                'tex' => 'required|string',
                'nar_gen' => 'nullable|string',
                'con' => 'integer|min:0|max:100',
                'dat_cli' => 'nullable|json',
                'ubi' => 'nullable|json',
                'res_ai' => 'nullable|json',
                'est' => 'required|in:pen,pro,com,can'
            ]);

            $narracion->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Narración de IA actualizada exitosamente',
                'data' => $narracion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar narración de IA',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Eliminar narración de IA
     */
    public function destroy($id): JsonResponse
    {
        try {
            $narracion = NarracionIA::findOrFail($id);
            $narracion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Narración de IA eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar narración de IA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar narración con IA
     */
    public function generar(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'usu_id' => 'required|exists:usu,id',
                'tip' => 'required|in:act,res,inf,com',
                'tex' => 'required|string',
                'dat_cli' => 'nullable|json',
                'ubi' => 'nullable|json'
            ]);

            // Generar código único
            $codigo = 'NAR_' . strtoupper($request->tip) . '_' . date('YmdHis') . '_' . rand(1000, 9999);

            // Simular generación de narración con IA
            $narracion = NarracionIA::create([
                'usu_id' => $request->usu_id,
                'cod' => $codigo,
                'tip' => $request->tip,
                'tex' => $request->tex,
                'nar_gen' => $this->generarNarracionIA($request->tex, $request->tip, $request->dat_cli, $request->ubi),
                'con' => rand(70, 95),
                'dat_cli' => $request->dat_cli,
                'ubi' => $request->ubi,
                'res_ai' => [
                    'modelo' => 'gpt-3.5-turbo',
                    'tokens_usados' => rand(100, 500),
                    'tiempo_procesamiento' => rand(2, 10),
                    'version' => '1.0.0'
                ],
                'est' => 'com'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Narración generada exitosamente',
                'data' => $narracion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar narración',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener narración por código
     */
    public function porCodigo($codigo): JsonResponse
    {
        try {
            $narracion = NarracionIA::with(['usuario'])
                ->where('cod', $codigo)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'message' => 'Narración obtenida exitosamente',
                'data' => $narracion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Narración no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Obtener estadísticas de narraciones
     */
    public function estadisticas(): JsonResponse
    {
        try {
            $estadisticas = [
                'total' => NarracionIA::count(),
                'por_tipo' => NarracionIA::selectRaw('tip, COUNT(*) as total')
                    ->groupBy('tip')
                    ->get(),
                'por_estado' => NarracionIA::selectRaw('est, COUNT(*) as total')
                    ->groupBy('est')
                    ->get(),
                'por_mes' => NarracionIA::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, COUNT(*) as total')
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->get(),
                'confianza_promedio' => NarracionIA::avg('con'),
                'recientes' => NarracionIA::with(['usuario'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas de narraciones obtenidas exitosamente',
                'data' => $estadisticas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas de narraciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar narración con IA (simulado)
     */
    private function generarNarracionIA($texto, $tipo, $datosCliente = null, $ubicacion = null): string
    {
        $plantillas = [
            'act' => "ACTA DE REUNIÓN\n\nFecha: " . date('d/m/Y') . "\nHora: " . date('H:i') . "\n\nAsunto: " . substr($texto, 0, 100) . "...\n\nDesarrollo:\n" . $texto . "\n\nConclusiones:\nSe acordó realizar las siguientes acciones basadas en el análisis de IA...",
            'res' => "RESUMEN EJECUTIVO\n\n" . substr($texto, 0, 200) . "...\n\nPuntos clave identificados por IA:\n• Aspecto relevante 1\n• Aspecto relevante 2\n• Aspecto relevante 3\n\nRecomendaciones:\nBasado en el análisis, se recomienda...",
            'inf' => "INFORME TÉCNICO\n\nIntroducción:\n" . $texto . "\n\nAnálisis realizado:\nLa inteligencia artificial ha procesado la información proporcionada y ha identificado patrones importantes...\n\nHallazgos:\n• Hallazgo 1\n• Hallazgo 2\n\nConclusiones:\nSe concluye que...",
            'com' => "COMUNICADO\n\n" . $texto . "\n\nMensaje generado por IA:\nBasado en el análisis de la información proporcionada, se comunica lo siguiente...\n\nFecha: " . date('d/m/Y') . "\nHora: " . date('H:i')
        ];

        return $plantillas[$tipo] ?? $texto;
    }
}
