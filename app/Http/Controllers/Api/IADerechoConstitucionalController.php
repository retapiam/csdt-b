<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IADerechoConstitucional;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class IADerechoConstitucionalController extends Controller
{
    protected IADerechoConstitucional $iaDerechoConstitucional;

    public function __construct(IADerechoConstitucional $iaDerechoConstitucional)
    {
        $this->iaDerechoConstitucional = $iaDerechoConstitucional;
    }

    /**
     * Análisis de derechos fundamentales
     */
    public function analizarDerechosFundamentales(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_derecho' => 'required|string',
                'datos_derecho' => 'required|string',
                'caso' => 'nullable|string'
            ]);

            $resultado = $this->iaDerechoConstitucional->analizarDerechosFundamentales($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de derechos fundamentales completado',
                'datos' => $resultado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de derechos fundamentales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de control constitucional
     */
    public function analizarControlConstitucional(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_control' => 'required|string',
                'datos_control' => 'required|string',
                'caso' => 'nullable|string'
            ]);

            $resultado = $this->iaDerechoConstitucional->analizarControlConstitucional($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de control constitucional completado',
                'datos' => $resultado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de control constitucional',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de acciones constitucionales
     */
    public function analizarAccionesConstitucionales(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_accion' => 'required|string',
                'datos_accion' => 'required|string',
                'caso' => 'nullable|string'
            ]);

            $resultado = $this->iaDerechoConstitucional->analizarAccionesConstitucionales($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de acciones constitucionales completado',
                'datos' => $resultado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de acciones constitucionales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de estructura del estado
     */
    public function analizarEstructuraEstado(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_estructura' => 'required|string',
                'datos_estructura' => 'required|string',
                'caso' => 'nullable|string'
            ]);

            $resultado = $this->iaDerechoConstitucional->analizarEstructuraEstado($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de estructura del estado completado',
                'datos' => $resultado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de estructura del estado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de reforma constitucional
     */
    public function analizarReformaConstitucional(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_reforma' => 'required|string',
                'datos_reforma' => 'required|string',
                'caso' => 'nullable|string'
            ]);

            $resultado = $this->iaDerechoConstitucional->analizarReformaConstitucional($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de reforma constitucional completado',
                'datos' => $resultado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de reforma constitucional',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis integral de derecho constitucional
     */
    public function analisisIntegral(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipos_analisis' => 'nullable|array',
                'datos_generales' => 'required|string',
                'caso' => 'nullable|string'
            ]);

            $resultado = $this->iaDerechoConstitucional->analisisIntegralDerechoConstitucional($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis integral de derecho constitucional completado',
                'datos' => $resultado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis integral de derecho constitucional',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de derecho constitucional
     */
    public function estadisticas(): JsonResponse
    {
        try {
            $estadisticas = $this->iaDerechoConstitucional->obtenerEstadisticasDerechoConstitucional();

            return response()->json([
                'exito' => true,
                'mensaje' => 'Estadísticas obtenidas correctamente',
                'datos' => $estadisticas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error obteniendo estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

