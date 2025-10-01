<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IAService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class IAController extends Controller
{
    protected $iaService;

    public function __construct(IAService $iaService)
    {
        $this->iaService = $iaService;
    }

    /**
     * Análisis de Naturaleza y Justicia Ordinaria
     */
    public function analizarNaturalezaJusticia(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hechos' => 'required|string|min:50',
            'tipo_caso' => 'nullable|string|in:penal,civil,administrativo,laboral'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarNaturalezaJusticia(
                $request->hechos,
                $request->tipo_caso ?? 'penal'
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de naturaleza y justicia completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derechos Étnicos
     */
    public function analizarDerechosEtnicos(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hechos' => 'required|string|min:50',
            'pueblo_indigena' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarDerechosEtnicos(
                $request->hechos,
                $request->pueblo_indigena
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de derechos étnicos completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derecho Constitucional
     */
    public function analizarDerechoConstitucional(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hechos' => 'required|string|min:50',
            'articulos' => 'nullable|array',
            'articulos.*' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarDerechoConstitucional(
                $request->hechos,
                $request->articulos ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de derecho constitucional completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derecho Administrativo
     */
    public function analizarDerechoAdministrativo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hechos' => 'required|string|min:50',
            'procedimiento' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarDerechoAdministrativo(
                $request->hechos,
                $request->procedimiento
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de derecho administrativo completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derecho Penal
     */
    public function analizarDerechoPenal(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hechos' => 'required|string|min:50',
            'tipo_delito' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarDerechoPenal(
                $request->hechos,
                $request->tipo_delito
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de derecho penal completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derecho Civil
     */
    public function analizarDerechoCivil(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hechos' => 'required|string|min:50',
            'materia' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarDerechoCivil(
                $request->hechos,
                $request->materia
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de derecho civil completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derecho Laboral
     */
    public function analizarDerechoLaboral(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hechos' => 'required|string|min:50',
            'tipo_relacion' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarDerechoLaboral(
                $request->hechos,
                $request->tipo_relacion
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de derecho laboral completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Medicina Natural
     */
    public function analizarMedicinaNatural(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sintomas' => 'required|string|min:20',
            'plantas' => 'nullable|array',
            'plantas.*' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarMedicinaNatural(
                $request->sintomas,
                $request->plantas ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de medicina natural completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derechos Ambientales
     */
    public function analizarDerechosAmbientales(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hechos' => 'required|string|min:50',
            'ecosistema' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarDerechosAmbientales(
                $request->hechos,
                $request->ecosistema
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de derechos ambientales completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derechos Mineros
     */
    public function analizarDerechosMineros(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hechos' => 'required|string|min:50',
            'tipo_mineria' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarDerechosMineros(
                $request->hechos,
                $request->tipo_mineria
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de derechos mineros completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Peritaje Catastral
     */
    public function analizarPeritajeCatastral(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'datos' => 'required|string|min:50',
            'tipo_peritaje' => 'nullable|string|in:avaluo,deslinde,subdivision'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarPeritajeCatastral(
                $request->datos,
                $request->tipo_peritaje ?? 'avaluo'
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de peritaje catastral completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Metodología Ágil
     */
    public function analizarMetodologiaAgil(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'proyecto' => 'required|string|min:50',
            'metodologia' => 'nullable|string|in:scrum,kanban,lean,agile'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarMetodologiaAgil(
                $request->proyecto,
                $request->metodologia ?? 'scrum'
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de metodología ágil completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Reseña Histórica
     */
    public function analizarResenaHistorica(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'eventos' => 'required|string|min:50',
            'periodo' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaService->analizarResenaHistorica(
                $request->eventos,
                $request->periodo
            );

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Análisis de reseña histórica completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis general multi-área
     */
    public function analisisGeneral(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hechos' => 'required|string|min:50',
            'areas' => 'required|array|min:1',
            'areas.*' => 'string|in:naturaleza_justicia,derechos_etnicos,derecho_constitucional,derecho_administrativo,derecho_penal,derecho_civil,derecho_laboral,medicina_natural,derechos_ambientales,derechos_mineros,peritaje_catastral,metodologia_agil,resena_historica'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultados = [];
            $hechos = $request->hechos;
            $areas = $request->areas;

            foreach ($areas as $area) {
                switch ($area) {
                    case 'naturaleza_justicia':
                        $resultados[$area] = $this->iaService->analizarNaturalezaJusticia($hechos);
                        break;
                    case 'derechos_etnicos':
                        $resultados[$area] = $this->iaService->analizarDerechosEtnicos($hechos);
                        break;
                    case 'derecho_constitucional':
                        $resultados[$area] = $this->iaService->analizarDerechoConstitucional($hechos);
                        break;
                    case 'derecho_administrativo':
                        $resultados[$area] = $this->iaService->analizarDerechoAdministrativo($hechos);
                        break;
                    case 'derecho_penal':
                        $resultados[$area] = $this->iaService->analizarDerechoPenal($hechos);
                        break;
                    case 'derecho_civil':
                        $resultados[$area] = $this->iaService->analizarDerechoCivil($hechos);
                        break;
                    case 'derecho_laboral':
                        $resultados[$area] = $this->iaService->analizarDerechoLaboral($hechos);
                        break;
                    case 'medicina_natural':
                        $resultados[$area] = $this->iaService->analizarMedicinaNatural($hechos);
                        break;
                    case 'derechos_ambientales':
                        $resultados[$area] = $this->iaService->analizarDerechosAmbientales($hechos);
                        break;
                    case 'derechos_mineros':
                        $resultados[$area] = $this->iaService->analizarDerechosMineros($hechos);
                        break;
                    case 'peritaje_catastral':
                        $resultados[$area] = $this->iaService->analizarPeritajeCatastral($hechos);
                        break;
                    case 'metodologia_agil':
                        $resultados[$area] = $this->iaService->analizarMetodologiaAgil($hechos);
                        break;
                    case 'resena_historica':
                        $resultados[$area] = $this->iaService->analizarResenaHistorica($hechos);
                        break;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $resultados,
                'message' => 'Análisis multi-área completado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el análisis multi-área',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // NUEVAS ESPECIALIDADES POST-DOCTORADO

    public function analizarDerechosCatastrales(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarDerechosCatastrales', 'datosCatastrales');
    }

    public function analizarDerechosMinerosAvanzado(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarDerechosMinerosAvanzado', 'datosMineros');
    }

    public function analizarDictamenAmbiental(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarDictamenAmbiental', 'datosAmbientales');
    }

    public function analizarDerechoInformatico(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarDerechoInformatico', 'casoInformatico');
    }

    public function analizarGeoportales(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarGeoportales', 'datosGeoespaciales');
    }

    public function analizarGeorreferenciacion(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarGeorreferenciacion', 'datosPredio');
    }

    public function analizarParticipacionCiudadana(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarParticipacionCiudadana', 'datosParticipacion');
    }

    public function analizarAccionPopular(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarAccionPopular', 'casoAccionPopular');
    }

    public function analizarReformaConstitucional(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarReformaConstitucional', 'propuestaReforma');
    }

    public function analizarPolitologia(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarPolitologia', 'fenomenoPolitico');
    }

    public function analizarForenseInformatico(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarForenseInformatico', 'evidenciaDigital');
    }

    public function analizarCrucesDatos(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarCrucesDatos', 'datosCruce');
    }

    public function analizarUbicacionPredios(Request $request)
    {
        return $this->handleAnalysis($request, 'analizarUbicacionPredios', 'datosColindantes');
    }
}
