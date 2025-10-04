<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IAVeeduriaAnticorrupcion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Controlador API para IA de Veeduría y Anti-Corrupción
 * Nivel Post-Doctorado especializado en control social y transparencia
 */
class IAVeeduriaAnticorrupcionController extends Controller
{
    protected IAVeeduriaAnticorrupcion $iaVeeduriaAnticorrupcion;

    public function __construct(IAVeeduriaAnticorrupcion $iaVeeduriaAnticorrupcion)
    {
        $this->iaVeeduriaAnticorrupcion = $iaVeeduriaAnticorrupcion;
    }

    /**
     * Análisis de Veeduría Ciudadana
     */
    public function analizarVeeduriaCiudadana(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_veeduria' => 'required|string|max:255',
                'datos_veeduria' => 'required|string',
                'entidad' => 'required|string|max:255',
                'municipio' => 'required|string|max:255'
            ]);

            $resultado = $this->iaVeeduriaAnticorrupcion->analizarVeeduriaCiudadana($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de veeduría ciudadana completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de veeduría ciudadana: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Control Social
     */
    public function analizarControlSocial(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_control' => 'required|string|max:255',
                'datos_control' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaVeeduriaAnticorrupcion->analizarControlSocial($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de control social completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de control social: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Transparencia Pública
     */
    public function analizarTransparenciaPublica(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_transparencia' => 'required|string|max:255',
                'datos_transparencia' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaVeeduriaAnticorrupcion->analizarTransparenciaPublica($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de transparencia pública completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de transparencia pública: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Rendición de Cuentas
     */
    public function analizarRendicionCuentas(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_rendicion' => 'required|string|max:255',
                'datos_rendicion' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaVeeduriaAnticorrupcion->analizarRendicionCuentas($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de rendición de cuentas completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de rendición de cuentas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Participación Ciudadana
     */
    public function analizarParticipacionCiudadana(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_participacion' => 'required|string|max:255',
                'datos_participacion' => 'required|string',
                'municipio' => 'required|string|max:255'
            ]);

            $resultado = $this->iaVeeduriaAnticorrupcion->analizarParticipacionCiudadana($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de participación ciudadana completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de participación ciudadana: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Contratación Pública
     */
    public function analizarContratacionPublica(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_contratacion' => 'required|string|max:255',
                'datos_contratacion' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaVeeduriaAnticorrupcion->analizarContratacionPublica($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de contratación pública completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de contratación pública: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Gestión de Riesgos
     */
    public function analizarGestionRiesgos(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_riesgo' => 'required|string|max:255',
                'datos_riesgo' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaVeeduriaAnticorrupcion->analizarGestionRiesgos($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de gestión de riesgos completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de gestión de riesgos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis integral de veeduría y anti-corrupción
     */
    public function analisisIntegralVeeduriaAnticorrupcion(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipos_analisis' => 'array',
                'tipos_analisis.*' => 'string|in:veeduria_ciudadana,control_social,transparencia_publica,rendicion_cuentas,participacion_ciudadana,contratacion_publica,gestion_riesgos',
                'datos_generales' => 'required|string'
            ]);

            $resultado = $this->iaVeeduriaAnticorrupcion->analisisIntegralVeeduriaAnticorrupcion($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis integral de veeduría y anti-corrupción completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis integral: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de veeduría
     */
    public function obtenerEstadisticasVeeduria(): JsonResponse
    {
        try {
            $estadisticas = $this->iaVeeduriaAnticorrupcion->obtenerEstadisticasVeeduria();

            return response()->json([
                'exito' => true,
                'mensaje' => 'Estadísticas de veeduría obtenidas exitosamente',
                'datos' => $estadisticas
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error obteniendo estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}
