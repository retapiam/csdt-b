<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IASpecializadaPostDoctorado;
use App\Services\IAPlanesGobiernoTerritorial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para servicios de IA especializada post-doctorado
 */
class IASpecializadaController extends Controller
{
    protected $iaEspecializada;
    protected $iaPlanes;

    public function __construct()
    {
        $this->iaEspecializada = app(IASpecializadaPostDoctorado::class);
        $this->iaPlanes = app(IAPlanesGobiernoTerritorial::class);
    }

    /**
     * Análisis de derecho minero
     */
    public function analizarDerechoMinero(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'datos_mineros' => 'required|string|max:10000',
            'tipo_mineria' => 'required|string|in:general,oro,carbon,esmeraldas,petroleo,gas',
            'jurisdiccion' => 'string|in:colombia,internacional,latinoamerica'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEspecializada->analizarDerechoMinero($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de derecho minero', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derecho minero',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de derecho ambiental
     */
    public function analizarDerechoAmbiental(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'datos_ambientales' => 'required|string|max:10000',
            'tipo_ambiental' => 'required|string|in:general,licencias,impacto,sostenibilidad',
            'jurisdiccion' => 'string|in:colombia,internacional,latinoamerica'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEspecializada->analizarDerechoAmbiental($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de derecho ambiental', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derecho ambiental',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de derecho étnico
     */
    public function analizarDerechoEtnico(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'datos_etnicos' => 'required|string|max:10000',
            'tipo_comunidad' => 'required|string|in:indigena,afrodescendiente,raizal,palenquero',
            'jurisdiccion' => 'string|in:colombia,internacional,latinoamerica'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEspecializada->analizarDerechoEtnico($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de derecho étnico', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derecho étnico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de derecho catastral
     */
    public function analizarDerechoCatastral(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'datos_catastrales' => 'required|string|max:10000',
            'tipo_predio' => 'required|string|in:general,urbano,rural,comercial,residencial',
            'jurisdiccion' => 'string|in:colombia,internacional,latinoamerica'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEspecializada->analizarDerechoCatastral($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de derecho catastral', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derecho catastral',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de derecho anti-corrupción
     */
    public function analizarDerechoAnticorrupcion(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'datos_anticorrupcion' => 'required|string|max:10000',
            'tipo_corrupcion' => 'required|string|in:general,cohecho,peculado,concusion,prevaricato',
            'jurisdiccion' => 'string|in:colombia,internacional,latinoamerica'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEspecializada->analizarDerechoAnticorrupcion($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de derecho anti-corrupción', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derecho anti-corrupción',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de derecho médico
     */
    public function analizarDerechoMedico(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'datos_medicos' => 'required|string|max:10000',
            'tipo_medico' => 'required|string|in:general,responsabilidad,consentimiento,bioetica',
            'jurisdiccion' => 'string|in:colombia,internacional,latinoamerica'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEspecializada->analizarDerechoMedico($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de derecho médico', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derecho médico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de derecho penal avanzado
     */
    public function analizarDerechoPenalAvanzado(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'datos_penales' => 'required|string|max:10000',
            'tipo_penal' => 'required|string|in:general,teoria_delito,procedimiento,criminologia',
            'jurisdiccion' => 'string|in:colombia,internacional,latinoamerica'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEspecializada->analizarDerechoPenalAvanzado($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de derecho penal avanzado', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derecho penal avanzado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de derecho disciplinario
     */
    public function analizarDerechoDisciplinario(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'datos_disciplinarios' => 'required|string|max:10000',
            'tipo_disciplinario' => 'required|string|in:general,funcion_publica,faltas,sanciones',
            'jurisdiccion' => 'string|in:colombia,internacional,latinoamerica'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEspecializada->analizarDerechoDisciplinario($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis'] ?? 'Análisis completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis de derecho disciplinario', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derecho disciplinario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis integral multi-especialidad
     */
    public function analisisIntegralMultiEspecialidad(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'datos_generales' => 'required|string|max:15000',
            'especialidades' => 'array',
            'especialidades.*' => 'string|in:derecho_minero,derecho_ambiental,derecho_etnico,derecho_catastral,derecho_anticorrupcion,derecho_medico,derecho_penal_avanzado,derecho_disciplinario',
            'jurisdiccion' => 'string|in:colombia,internacional,latinoamerica'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaEspecializada->analisisIntegralMultiEspecialidad($request->all());
            
            return response()->json([
                'success' => true,
                'analisisCompleto' => $resultado,
                'respuesta' => $resultado['analisis_consolidado'] ?? 'Análisis integral completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en análisis integral multi-especialidad', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis integral',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener especialidades disponibles
     */
    public function obtenerEspecialidadesDisponibles(): JsonResponse
    {
        try {
            $especialidades = $this->iaEspecializada->obtenerEspecialidadesDisponibles();
            
            return response()->json([
                'success' => true,
                'especialidades' => $especialidades
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo especialidades', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo especialidades disponibles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de análisis
     */
    public function obtenerEstadisticasAnalisis(): JsonResponse
    {
        try {
            $estadisticas = $this->iaEspecializada->obtenerEstadisticasAnalisis();
            
            return response()->json([
                'success' => true,
                'estadisticas' => $estadisticas
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo estadísticas de análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
