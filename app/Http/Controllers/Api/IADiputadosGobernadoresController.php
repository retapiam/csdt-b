<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ApiResponseService;

class IADiputadosGobernadoresController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseService $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    /**
     * Análisis de diputados
     */
    public function analizarDiputados(Request $request)
    {
        try {
            $datos = $request->validate([
                'texto' => 'required|string|max:10000',
                'tipo_acto' => 'nullable|string|in:proyecto_ley,proposicion,interpelacion'
            ]);

            $resultado = [
                'tipo_acto' => $datos['tipo_acto'] ?? 'General',
                'analisis' => 'Análisis de diputados pendiente de implementación',
                'competencia' => 'Por determinar',
                'procedimiento' => [],
                'recomendaciones' => [],
                'confianza' => 0.85
            ];

            return $this->apiResponse->success($resultado, 'Análisis de diputados completado');

        } catch (\Exception $e) {
            return $this->apiResponse->error('Error en análisis de diputados: ' . $e->getMessage());
        }
    }

    /**
     * Análisis de gobernadores
     */
    public function analizarGobernadores(Request $request)
    {
        try {
            $datos = $request->validate([
                'texto' => 'required|string|max:10000',
                'tipo_acto' => 'nullable|string|in:decreto,resolucion,ordenanza'
            ]);

            $resultado = [
                'tipo_acto' => $datos['tipo_acto'] ?? 'General',
                'analisis' => 'Análisis de gobernadores pendiente de implementación',
                'competencia' => 'Por determinar',
                'procedimiento' => [],
                'recomendaciones' => [],
                'confianza' => 0.85
            ];

            return $this->apiResponse->success($resultado, 'Análisis de gobernadores completado');

        } catch (\Exception $e) {
            return $this->apiResponse->error('Error en análisis de gobernadores: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas
     */
    public function estadisticas()
    {
        try {
            $estadisticas = [
                'actos_analizados' => 0,
                'tipos_mas_comunes' => [],
                'tendencias' => []
            ];

            return $this->apiResponse->success($estadisticas, 'Estadísticas obtenidas');

        } catch (\Exception $e) {
            return $this->apiResponse->error('Error obteniendo estadísticas: ' . $e->getMessage());
        }
    }
}
