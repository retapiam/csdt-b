<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IAIPMA;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Controlador API para IA de IPMA (International Project Management Association)
 */
class IAIPMAController extends Controller
{
    protected IAIPMA $iaIPMA;

    public function __construct(IAIPMA $iaIPMA)
    {
        $this->iaIPMA = $iaIPMA;
    }

    /**
     * Análisis de competencias IPMA
     */
    public function analizarCompetenciasIPMA(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tipo_competencia' => 'required|string|in:competencias_tecnicas,competencias_conductuales,competencias_contextuales,competencias_liderazgo',
            'datos_competencia' => 'required|string|max:10000',
            'caso' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'exito' => false,
                'error' => 'Datos de entrada inválidos',
                'detalles' => $validator->errors()
            ], 400);
        }

        try {
            $datos = $request->all();
            $resultado = $this->iaIPMA->analizarCompetenciasIPMA($datos);

            return response()->json([
                'exito' => true,
                'datos' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('Error en análisis competencias IPMA', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'exito' => false,
                'error' => 'Error interno del servidor',
                'mensaje' => 'No se pudo completar el análisis de competencias IPMA'
            ], 500);
        }
    }

    /**
     * Análisis de código de ética IPMA
     */
    public function analizarCodigoEticaIPMA(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tipo_etica' => 'required|string|in:codigo_etica,conducta_profesional,valores,principios',
            'datos_etica' => 'required|string|max:10000',
            'caso' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'exito' => false,
                'error' => 'Datos de entrada inválidos',
                'detalles' => $validator->errors()
            ], 400);
        }

        try {
            $datos = $request->all();
            $resultado = $this->iaIPMA->analizarCodigoEticaIPMA($datos);

            return response()->json([
                'exito' => true,
                'datos' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('Error en análisis código de ética IPMA', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'exito' => false,
                'error' => 'Error interno del servidor',
                'mensaje' => 'No se pudo completar el análisis de código de ética IPMA'
            ], 500);
        }
    }

    /**
     * Análisis integral de IPMA
     */
    public function analisisIntegralIPMA(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'datos' => 'required|array',
            'tipos_analisis' => 'nullable|array',
            'caso' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'exito' => false,
                'error' => 'Datos de entrada inválidos',
                'detalles' => $validator->errors()
            ], 400);
        }

        try {
            $datos = $request->all();
            $resultado = $this->iaIPMA->analisisIntegralIPMA($datos);

            return response()->json([
                'exito' => true,
                'datos' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('Error en análisis integral IPMA', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'exito' => false,
                'error' => 'Error interno del servidor',
                'mensaje' => 'No se pudo completar el análisis integral IPMA'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de IPMA
     */
    public function estadisticasIPMA(): JsonResponse
    {
        try {
            $estadisticas = $this->iaIPMA->obtenerEstadisticasIPMA();

            return response()->json([
                'exito' => true,
                'datos' => $estadisticas
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas IPMA', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'exito' => false,
                'error' => 'Error interno del servidor',
                'mensaje' => 'No se pudieron obtener las estadísticas IPMA'
            ], 500);
        }
    }
}
