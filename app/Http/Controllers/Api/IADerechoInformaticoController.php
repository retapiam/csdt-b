<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IADerechoInformatico;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Controlador API para IA de Derecho Informático
 * Nivel Post-Doctorado especializado en ciberseguridad, protección de datos y delitos informáticos
 */
class IADerechoInformaticoController extends Controller
{
    protected IADerechoInformatico $iaDerechoInformatico;

    public function __construct(IADerechoInformatico $iaDerechoInformatico)
    {
        $this->iaDerechoInformatico = $iaDerechoInformatico;
    }

    /**
     * Análisis de Ciberseguridad
     */
    public function analizarCiberseguridad(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_ciberseguridad' => 'required|string|max:255',
                'datos_ciberseguridad' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaDerechoInformatico->analizarCiberseguridad($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de ciberseguridad completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de ciberseguridad: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Protección de Datos
     */
    public function analizarProteccionDatos(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_proteccion' => 'required|string|max:255',
                'datos_proteccion' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaDerechoInformatico->analizarProteccionDatos($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de protección de datos completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de protección de datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Delitos Informáticos
     */
    public function analizarDelitosInformaticos(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_delito' => 'required|string|max:255',
                'datos_delito' => 'required|string',
                'entidad' => 'required|string|max:255'
            ]);

            $resultado = $this->iaDerechoInformatico->analizarDelitosInformaticos($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de delitos informáticos completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de delitos informáticos: ' . $e->getMessage()
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

            $resultado = $this->iaDerechoInformatico->analizarEvidenciaDigital($datos);

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
     * Análisis de Comercio Electrónico
     */
    public function analizarComercioElectronico(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_comercio' => 'required|string|max:255',
                'datos_comercio' => 'required|string',
                'empresa' => 'required|string|max:255'
            ]);

            $resultado = $this->iaDerechoInformatico->analizarComercioElectronico($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de comercio electrónico completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de comercio electrónico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Inteligencia Artificial y Derecho
     */
    public function analizarIAyDerecho(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipo_ia' => 'required|string|max:255',
                'datos_ia' => 'required|string',
                'aplicacion' => 'required|string|max:255'
            ]);

            $resultado = $this->iaDerechoInformatico->analizarIAyDerecho($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis de IA y derecho completado exitosamente',
                'datos' => $resultado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en análisis de IA y derecho: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis integral de derecho informático
     */
    public function analisisIntegralDerechoInformatico(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'tipos_analisis' => 'array',
                'tipos_analisis.*' => 'string|in:ciberseguridad,proteccion_datos,delitos_informaticos,evidencia_digital,comercio_electronico,ia_derecho',
                'datos_generales' => 'required|string'
            ]);

            $resultado = $this->iaDerechoInformatico->analisisIntegralDerechoInformatico($datos);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Análisis integral de derecho informático completado exitosamente',
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
     * Obtener estadísticas de derecho informático
     */
    public function obtenerEstadisticasDerechoInformatico(): JsonResponse
    {
        try {
            $estadisticas = $this->iaDerechoInformatico->obtenerEstadisticasDerechoInformatico();

            return response()->json([
                'exito' => true,
                'mensaje' => 'Estadísticas de derecho informático obtenidas exitosamente',
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
