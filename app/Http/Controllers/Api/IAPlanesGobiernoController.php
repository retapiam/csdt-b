<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IAPlanesGobiernoTerritorial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para análisis de planes de gobierno y desarrollo territorial
 */
class IAPlanesGobiernoController extends Controller
{
    protected $iaPlanes;

    public function __construct()
    {
        $this->iaPlanes = app(IAPlanesGobiernoTerritorial::class);
    }

    /**
     * Análisis de Plan de Desarrollo Municipal
     */
    public function analizarPlanDesarrolloMunicipal(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'municipio' => 'required|string|max:255',
            'periodo' => 'required|string|max:50',
            'contenido_plan' => 'required|string|max:20000',
            'poblacion' => 'string|max:100',
            'categoria' => 'string|in:primera,segunda,tercera,cuarta,quinta,sexta,especial'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaPlanes->analizarPlanDesarrolloMunicipal($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de plan de desarrollo municipal', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de plan de desarrollo municipal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Plan de Desarrollo Departamental
     */
    public function analizarPlanDesarrolloDepartamental(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'departamento' => 'required|string|max:255',
            'periodo' => 'required|string|max:50',
            'contenido_plan' => 'required|string|max:20000',
            'municipios' => 'string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaPlanes->analizarPlanDesarrolloDepartamental($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de plan de desarrollo departamental', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de plan de desarrollo departamental',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Plan de Ordenamiento Territorial
     */
    public function analizarPlanOrdenamientoTerritorial(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'municipio' => 'required|string|max:255',
            'periodo' => 'required|string|max:50',
            'contenido_pot' => 'required|string|max:20000',
            'categoria' => 'string|in:primera,segunda,tercera,cuarta,quinta,sexta,especial'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaPlanes->analizarPlanOrdenamientoTerritorial($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de plan de ordenamiento territorial', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de plan de ordenamiento territorial',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Plan de Gobierno Étnico
     */
    public function analizarPlanGobiernoEtnico(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comunidad' => 'required|string|max:255',
            'tipo_comunidad' => 'required|string|in:indigena,afrodescendiente,raizal,palenquero',
            'periodo' => 'required|string|max:50',
            'contenido_plan' => 'required|string|max:20000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaPlanes->analizarPlanGobiernoEtnico($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de plan de gobierno étnico', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de plan de gobierno étnico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Plan de Vida Comunitario
     */
    public function analizarPlanVidaComunitario(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comunidad' => 'required|string|max:255',
            'tipo_comunidad' => 'required|string|in:indigena,afrodescendiente,raizal,palenquero',
            'periodo' => 'required|string|max:50',
            'contenido_plan' => 'required|string|max:20000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaPlanes->analizarPlanVidaComunitario($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de plan de vida comunitario', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de plan de vida comunitario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Plan de Etnodesarrollo
     */
    public function analizarPlanEtnodesarrollo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comunidad' => 'required|string|max:255',
            'tipo_comunidad' => 'required|string|in:indigena,afrodescendiente,raizal,palenquero',
            'periodo' => 'required|string|max:50',
            'contenido_plan' => 'required|string|max:20000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaPlanes->analizarPlanEtnodesarrollo($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de plan de etnodesarrollo', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de plan de etnodesarrollo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Plan Anti-Corrupción
     */
    public function analizarPlanAnticorrupcion(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'entidad' => 'required|string|max:255',
            'nivel' => 'required|string|in:municipal,departamental,nacional',
            'periodo' => 'required|string|max:50',
            'contenido_plan' => 'required|string|max:20000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaPlanes->analizarPlanAnticorrupcion($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de plan anti-corrupción', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de plan anti-corrupción',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Plan de Ética y Transparencia
     */
    public function analizarPlanEticaTransparencia(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'entidad' => 'required|string|max:255',
            'nivel' => 'required|string|in:municipal,departamental,nacional',
            'periodo' => 'required|string|max:50',
            'contenido_plan' => 'required|string|max:20000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaPlanes->analizarPlanEticaTransparencia($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de plan de ética y transparencia', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de plan de ética y transparencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis integral de planes de gobierno
     */
    public function analisisIntegralPlanesGobierno(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'datos_generales' => 'required|string|max:25000',
            'tipos_plan' => 'array',
            'tipos_plan.*' => 'string|in:desarrollo_municipal,desarrollo_departamental,ordenamiento_territorial,gobierno_etnico,vida_comunitario,etnodesarrollo,anticorrupcion,etica_transparencia',
            'nivel_gobierno' => 'string|in:municipal,departamental,nacional'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaPlanes->analisisIntegralPlanesGobierno($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis_consolidado'] ?? 'Análisis integral completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis integral de planes de gobierno', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis integral de planes de gobierno',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de planes
     */
    public function obtenerEstadisticasPlanes(): JsonResponse
    {
        try {
            $estadisticas = $this->iaPlanes->obtenerEstadisticasPlanes();
            
            return response()->json([
                'success' => true,
                'estadisticas' => $estadisticas
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de planes', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo estadísticas de planes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
