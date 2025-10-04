<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IAFiscalizacion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Controlador API para IA de Fiscalización
 * Nivel Post-Doctorado especializado en control fiscal, auditoría y supervisión
 */
class IAFiscalizacionController extends Controller
{
    protected IAFiscalizacion $iaFiscalizacion;

    public function __construct(IAFiscalizacion $iaFiscalizacion)
    {
        $this->iaFiscalizacion = $iaFiscalizacion;
    }

    /**
     * Análisis de Control Fiscal
     */
    public function analizarControlFiscal(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_control' => 'required|string|max:255',
                'datos_control' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaFiscalizacion->analizarControlFiscal($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de control fiscal completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de control fiscal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Auditoría Pública
     */
    public function analizarAuditoriaPublica(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_auditoria' => 'required|string|max:255',
                'datos_auditoria' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaFiscalizacion->analizarAuditoriaPublica($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de auditoría pública completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de auditoría pública: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Supervisión Financiera
     */
    public function analizarSupervisionFinanciera(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_supervision' => 'required|string|max:255',
                'datos_supervision' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaFiscalizacion->analizarSupervisionFinanciera($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de supervisión financiera completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de supervisión financiera: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Gestión de Riesgos Fiscal
     */
    public function analizarGestionRiesgosFiscal(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_riesgo' => 'required|string|max:255',
                'datos_riesgo' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaFiscalizacion->analizarGestionRiesgosFiscal($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de gestión de riesgos fiscal completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de gestión de riesgos fiscal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Cumplimiento Normativo
     */
    public function analizarCumplimientoNormativo(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_cumplimiento' => 'required|string|max:255',
                'datos_cumplimiento' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaFiscalizacion->analizarCumplimientoNormativo($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de cumplimiento normativo completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de cumplimiento normativo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Rendición de Cuentas Fiscal
     */
    public function analizarRendicionCuentasFiscal(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_rendicion' => 'required|string|max:255',
                'datos_rendicion' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaFiscalizacion->analizarRendicionCuentasFiscal($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de rendición de cuentas fiscal completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de rendición de cuentas fiscal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Control Interno
     */
    public function analizarControlInterno(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_control' => 'required|string|max:255',
                'datos_control' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaFiscalizacion->analizarControlInterno($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de control interno completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de control interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis integral de fiscalización
     */
    public function analisisIntegralFiscalizacion(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipos_analisis' => 'array',
                'tipos_analisis.*' => 'string|in:control_fiscal,auditoria_publica,supervision_financiera,gestion_riesgos_fiscal,cumplimiento_normativo,rendicion_cuentas_fiscal,control_interno',
                'datos_generales' => 'required|string'
            ]);

            $resultado = $this->iaFiscalizacion->analisisIntegralFiscalizacion($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis integral de fiscalización completado exitosamente',
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
     * Obtener estadísticas de fiscalización
     */
    public function obtenerEstadisticasFiscalizacion(): JsonResponse
    {
        try {
            $estadisticas = $this->iaFiscalizacion->obtenerEstadisticasFiscalizacion();

            return response()->json([
                'exito' => true,
                'mensaje' => 'Estadísticas de fiscalización obtenidas exitosamente',
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
