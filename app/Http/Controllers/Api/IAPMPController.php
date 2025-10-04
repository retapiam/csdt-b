<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IAPMP;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Controlador API para IA de PMP (Project Management Professional)
 */
class IAPMPController extends Controller
{
    protected IAPMP $iaPMP;

    public function __construct(IAPMP $iaPMP)
    {
        $this->iaPMP = $iaPMP;
    }

    /**
     * Análisis de PMP
     */
    public function analizarPMP(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tipo_analisis' => 'required|string|in:guia_pmp,codigo_etica_pmp,mejores_practicas_pmp,metodologias_pmp,gestion_riesgos_pmp,gestion_calidad_pmp,gestion_recursos_pmp,gestion_stakeholders_pmp',
            'datos' => 'required|array',
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
            $resultado = $this->iaPMP->analizarPMP($datos);

            return response()->json([
                'exito' => true,
                'datos' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('Error en análisis PMP', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'exito' => false,
                'error' => 'Error interno del servidor',
                'mensaje' => 'No se pudo completar el análisis PMP'
            ], 500);
        }
    }

    /**
     * Análisis integral de PMP
     */
    public function analisisIntegralPMP(Request $request): JsonResponse
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
            $resultado = $this->iaPMP->analisisIntegralPMP($datos);

            return response()->json([
                'exito' => true,
                'datos' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('Error en análisis integral PMP', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'exito' => false,
                'error' => 'Error interno del servidor',
                'mensaje' => 'No se pudo completar el análisis integral PMP'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de PMP
     */
    public function estadisticasPMP(): JsonResponse
    {
        try {
            $estadisticas = $this->iaPMP->obtenerEstadisticasPMP();

            return response()->json([
                'exito' => true,
                'datos' => $estadisticas
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas PMP', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'exito' => false,
                'error' => 'Error interno del servidor',
                'mensaje' => 'No se pudieron obtener las estadísticas PMP'
            ], 500);
        }
    }
}
