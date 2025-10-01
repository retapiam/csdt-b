<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IAEtnicaEspecializada;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class IAEtnicaController extends Controller
{
    protected $iaEtnicaService;

    public function __construct(IAEtnicaEspecializada $iaEtnicaService)
    {
        $this->iaEtnicaService = $iaEtnicaService;
    }

    /**
     * Análisis de consulta étnica
     */
    public function analizarConsultaEtnica(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'consulta' => 'required|string|max:5000',
            'tipo_pueblo' => 'nullable|string|in:indigena,afrodescendiente',
            'area_especializada' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEtnicaService->analizarConsultaEtnica(
                $request->consulta,
                $request->tipo_pueblo ?? 'indigena',
                $request->area_especializada ?? 'derechos_etnicos'
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de consulta étnica completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de consulta étnica: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de marco jurídico étnico
     */
    public function analizarMarcoJuridico(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'consulta' => 'required|string|max:5000',
            'area_juridica' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEtnicaService->analizarMarcoJuridico(
                $request->consulta,
                $request->area_juridica ?? 'derechos_etnicos'
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de marco jurídico completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de marco jurídico: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de impacto territorial
     */
    public function analizarImpactoTerritorial(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'consulta' => 'required|string|max:5000',
            'ubicacion' => 'nullable|string|max:200'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEtnicaService->analizarImpactoTerritorial(
                $request->consulta,
                $request->ubicacion
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de impacto territorial completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de impacto territorial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de jurisdicción especial indígena
     */
    public function analizarJurisdiccionEspecialIndigena(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'caso' => 'required|string|max:5000',
            'pueblo_indigena' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEtnicaService->analizarJurisdiccionEspecialIndigena(
                $request->caso,
                $request->pueblo_indigena
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de jurisdicción especial indígena completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de jurisdicción especial indígena: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de patrimonio cultural étnico
     */
    public function analizarPatrimonioCulturalEtnico(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patrimonio' => 'required|string|max:5000',
            'tipo_patrimonio' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEtnicaService->analizarPatrimonioCulturalEtnico(
                $request->patrimonio,
                $request->tipo_patrimonio ?? 'saberes_tradicionales'
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de patrimonio cultural étnico completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de patrimonio cultural étnico: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de educación propia étnica
     */
    public function analizarEducacionPropiaEtnica(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_educativo' => 'required|string|max:5000',
            'pueblo_indigena' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEtnicaService->analizarEducacionPropiaEtnica(
                $request->plan_educativo,
                $request->pueblo_indigena
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de educación propia étnica completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de educación propia étnica: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de territorios ancestrales
     */
    public function analizarTerritoriosAncestrales(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'territorio' => 'required|string|max:5000',
            'tipo_territorio' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEtnicaService->analizarTerritoriosAncestrales(
                $request->territorio,
                $request->tipo_territorio ?? 'resguardo'
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de territorios ancestrales completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de territorios ancestrales: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de IA étnica
     */
    public function estadisticasIAEtnica(): JsonResponse
    {
        try {
            $estadisticas = [
                'total_consultas' => 0,
                'consultas_por_tipo' => [
                    'indigena' => 0,
                    'afrodescendiente' => 0
                ],
                'areas_especializadas' => [
                    'derechos_etnicos' => 0,
                    'jurisdiccion_especial' => 0,
                    'patrimonio_cultural' => 0,
                    'educacion_propia' => 0,
                    'territorios_ancestrales' => 0
                ],
                'ultima_actualizacion' => now()->toISOString()
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
                'message' => 'Estadísticas de IA étnica obtenidas'
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de IA étnica: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}