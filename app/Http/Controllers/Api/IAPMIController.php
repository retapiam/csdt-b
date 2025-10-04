<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IAPMI;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Controlador API para IA de PMI (Project Management Institute)
 */
class IAPMIController extends Controller
{
    protected IAPMI $iaPMI;

    public function __construct(IAPMI $iaPMI)
    {
        $this->iaPMI = $iaPMI;
    }

    /**
     * Análisis de código de ética PMI
     */
    public function analizarCodigoEticaPMI(Request $request): JsonResponse
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
            $resultado = $this->iaPMI->analizarCodigoEticaPMI($datos);

            return response()->json([
                'exito' => true,
                'datos' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('Error en análisis código de ética PMI', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'exito' => false,
                'error' => 'Error interno del servidor',
                'mensaje' => 'No se pudo completar el análisis de código de ética PMI'
            ], 500);
        }
    }

    /**
     * Análisis de estándares PMI
     */
    public function analizarEstandaresPMI(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tipo_estandar' => 'required|string|in:estandares_gestion,estandares_proceso,estandares_calidad,estandares_sostenibilidad',
            'datos_estandar' => 'required|string|max:10000',
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
            $resultado = $this->iaPMI->analizarEstandaresPMI($datos);

            return response()->json([
                'exito' => true,
                'datos' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('Error en análisis estándares PMI', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'exito' => false,
                'error' => 'Error interno del servidor',
                'mensaje' => 'No se pudo completar el análisis de estándares PMI'
            ], 500);
        }
    }

    /**
     * Análisis integral de PMI
     */
    public function analisisIntegralPMI(Request $request): JsonResponse
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
            $resultado = $this->iaPMI->analisisIntegralPMI($datos);

            return response()->json([
                'exito' => true,
                'datos' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('Error en análisis integral PMI', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'exito' => false,
                'error' => 'Error interno del servidor',
                'mensaje' => 'No se pudo completar el análisis integral PMI'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de PMI
     */
    public function estadisticasPMI(): JsonResponse
    {
        try {
            $estadisticas = $this->iaPMI->obtenerEstadisticasPMI();

            return response()->json([
                'exito' => true,
                'datos' => $estadisticas
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas PMI', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'exito' => false,
                'error' => 'Error interno del servidor',
                'mensaje' => 'No se pudieron obtener las estadísticas PMI'
            ], 500);
        }
    }
}
