<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IAInvestigacionForense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Controlador API para IA de Investigación Forense
 * Nivel Post-Doctorado especializado en análisis forense, evidencia digital y criminología
 */
class IAInvestigacionForenseController extends Controller
{
    protected IAInvestigacionForense $iaInvestigacionForense;

    public function __construct(IAInvestigacionForense $iaInvestigacionForense)
    {
        $this->iaInvestigacionForense = $iaInvestigacionForense;
    }

    /**
     * Análisis Forense Digital
     */
    public function analizarForenseDigital(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_forense' => 'required|string|max:255',
                'datos_forense' => 'required|string',
                'caso' => 'required|string|max:255'
            ]);

            $resultado = $this->iaInvestigacionForense->analizarForenseDigital($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis forense digital completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis forense digital: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Evidencia Digital
     */
    public function analizarEvidenciaDigital(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_evidencia' => 'required|string|max:255',
                'datos_evidencia' => 'required|string',
                'caso' => 'required|string|max:255'
            ]);

            $resultado = $this->iaInvestigacionForense->analizarEvidenciaDigital($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de evidencia digital completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de evidencia digital: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Cibercrimen
     */
    public function analizarCibercrimen(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_cibercrimen' => 'required|string|max:255',
                'datos_cibercrimen' => 'required|string',
                'caso' => 'required|string|max:255'
            ]);

            $resultado = $this->iaInvestigacionForense->analizarCibercrimen($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de cibercrimen completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de cibercrimen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Perfilación Criminal
     */
    public function analizarPerfilacionCriminal(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_perfilacion' => 'required|string|max:255',
                'datos_perfilacion' => 'required|string',
                'caso' => 'required|string|max:255'
            ]);

            $resultado = $this->iaInvestigacionForense->analizarPerfilacionCriminal($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de perfilación criminal completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de perfilación criminal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Criminología
     */
    public function analizarCriminologia(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_criminologia' => 'required|string|max:255',
                'datos_criminologia' => 'required|string',
                'caso' => 'required|string|max:255'
            ]);

            $resultado = $this->iaInvestigacionForense->analizarCriminologia($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de criminología completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de criminología: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Victimología
     */
    public function analizarVictimologia(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_victimologia' => 'required|string|max:255',
                'datos_victimologia' => 'required|string',
                'caso' => 'required|string|max:255'
            ]);

            $resultado = $this->iaInvestigacionForense->analizarVictimologia($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de victimología completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de victimología: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Psicología Forense
     */
    public function analizarPsicologiaForense(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_psicologia' => 'required|string|max:255',
                'datos_psicologia' => 'required|string',
                'caso' => 'required|string|max:255'
            ]);

            $resultado = $this->iaInvestigacionForense->analizarPsicologiaForense($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de psicología forense completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de psicología forense: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis integral de investigación forense
     */
    public function analisisIntegralInvestigacionForense(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipos_analisis' => 'array',
                'tipos_analisis.*' => 'string|in:forense_digital,evidencia_digital,cibercrimen,perfilacion_criminal,criminologia,victimologia,psicologia_forense',
                'datos_generales' => 'required|string'
            ]);

            $resultado = $this->iaInvestigacionForense->analisisIntegralInvestigacionForense($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis integral de investigación forense completado exitosamente',
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
     * Obtener estadísticas de investigación forense
     */
    public function obtenerEstadisticasInvestigacionForense(): JsonResponse
    {
        try {
            $estadisticas = $this->iaInvestigacionForense->obtenerEstadisticasInvestigacionForense();

            return response()->json([
                'exito' => true,
                'mensaje' => 'Estadísticas de investigación forense obtenidas exitosamente',
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
