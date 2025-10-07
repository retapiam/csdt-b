<?php

namespace App\Services;

use App\Models\AIConsulta;
use App\Models\AIAnalisisJuridico;
use App\Models\AIAnalisisEtnico;
use App\Models\AIAnalisisVeeduria;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class IAService
{
    protected $openAIKey;
    protected $anthropicKey;
    protected $huggingFaceKey;
    protected $cacheEnabled;
    protected $cacheDuration;

    public function __construct()
    {
        $this->openAIKey = config('services.openai.key');
        $this->anthropicKey = config('services.anthropic.key');
        $this->huggingFaceKey = config('services.huggingface.key');
        $this->cacheEnabled = config('ai.cache.enabled', true);
        $this->cacheDuration = config('ai.cache.duration', 3600);
    }

    /**
     * Análisis jurídico con múltiples proveedores
     */
    public function analizarJuridico(array $datos, int $userId): array
    {
        $startTime = microtime(true);

        try {
            // Generar hash para cache
            $cacheKey = $this->generarCacheKey('juridico', $datos);
            
            if ($this->cacheEnabled && Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Obtener configuración según tipo de caso
            $configuracion = $this->obtenerConfiguracionAnalisis($datos['tipo_caso'] ?? 'general');

            // Ejecutar análisis con múltiples proveedores
            $resultados = [];
            foreach ($configuracion['proveedores'] as $proveedor) {
                $resultado = $this->analizarConProveedor($proveedor, $datos);
                if ($resultado) {
                    $resultados[] = $resultado;
                }
            }

            // Unificar resultados
            $analisisUnificado = $this->unificarResultados($resultados);

            // Guardar en base de datos
            $analisisDB = AIAnalisisJuridico::create([
                'user_id' => $userId,
                'tipo_caso' => $datos['tipo_caso'] ?? 'general',
                'categoria_juridica' => $datos['categoria_juridica'] ?? 'Derecho General',
                'texto_analizado' => $datos['texto'] ?? $datos['narracion'] ?? '',
                'clasificaciones' => $analisisUnificado['clasificaciones'] ?? [],
                'resumen' => $analisisUnificado['resumen'] ?? '',
                'fundamentos_legales' => $analisisUnificado['fundamentos_legales'] ?? [],
                'recomendaciones' => $analisisUnificado['recomendaciones'] ?? [],
                'evaluacion_riesgos' => $analisisUnificado['evaluacion_riesgos'] ?? [],
                'confianza_promedio' => $analisisUnificado['confianza'] ?? 0,
                'proveedores_utilizados' => array_column($resultados, 'proveedor'),
                'nivel_analisis' => $datos['nivel_analisis'] ?? 'avanzado',
            ]);

            $tiempoTotal = (microtime(true) - $startTime) * 1000;

            $respuesta = [
                'id' => $analisisDB->id,
                'exito' => true,
                'analisis' => $analisisUnificado,
                'tiempo_procesamiento' => $tiempoTotal,
                'proveedores' => array_column($resultados, 'proveedor'),
            ];

            // Guardar en cache
            if ($this->cacheEnabled) {
                Cache::put($cacheKey, $respuesta, $this->cacheDuration);
            }

            return $respuesta;

        } catch (Exception $e) {
            Log::error('Error en análisis jurídico:', [
                'error' => $e->getMessage(),
                'datos' => $datos
            ]);

            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'analisis' => null
            ];
        }
    }

    /**
     * Análisis de casos étnicos
     */
    public function analizarCasoEtnico(array $datos, int $userId): array
    {
        try {
            $cacheKey = $this->generarCacheKey('etnico', $datos);
            
            if ($this->cacheEnabled && Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Análisis con IA territorial especializada
            $prompt = $this->generarPromptEtnico($datos);
            $resultado = $this->consultarOpenAI($prompt);

            // Procesar respuesta
            $analisis = $this->procesarRespuestaEtnica($resultado, $datos);

            // Guardar en BD
            $analisisDB = AIAnalisisEtnico::create([
                'user_id' => $userId,
                'grupo_etnico' => $datos['grupo_etnico'],
                'comunidad' => $datos['comunidad'],
                'ubicacion' => $datos['ubicacion'],
                'narracion' => $datos['narracion'],
                'tipo_etnico_detectado' => $analisis['tipo_detectado'],
                'confianza_tipo' => $analisis['confianza'],
                'derechos_afectados' => $analisis['derechos_afectados'],
                'requiere_consulta_previa' => $analisis['requiere_consulta_previa'],
                'nivel_urgencia' => $analisis['nivel_urgencia'],
                'impacto_territorial' => $analisis['impacto_territorial'],
                'impacto_cultural' => $analisis['impacto_cultural'],
                'impacto_autonomia' => $analisis['impacto_autonomia'],
                'recomendaciones' => $analisis['recomendaciones'],
                'procedimientos_sugeridos' => $analisis['procedimientos'],
                'normativas_aplicables' => $analisis['normativas'],
            ]);

            $respuesta = [
                'id' => $analisisDB->id,
                'exito' => true,
                'analisis' => $analisis
            ];

            if ($this->cacheEnabled) {
                Cache::put($cacheKey, $respuesta, $this->cacheDuration);
            }

            return $respuesta;

        } catch (Exception $e) {
            Log::error('Error en análisis étnico:', [
                'error' => $e->getMessage(),
                'datos' => $datos
            ]);

            return [
                'exito' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Análisis de veeduría ciudadana
     */
    public function analizarVeeduriaCiudadana(array $datos, int $userId): array
    {
        try {
            $cacheKey = $this->generarCacheKey('veeduria', $datos);
            
            if ($this->cacheEnabled && Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $prompt = $this->generarPromptVeeduria($datos);
            $resultado = $this->consultarOpenAI($prompt);
            $analisis = $this->procesarRespuestaVeeduria($resultado, $datos);

            $analisisDB = AIAnalisisVeeduria::create([
                'user_id' => $userId,
                'entidad' => $datos['entidad'],
                'proyecto' => $datos['proyecto'],
                'tipo_veeduria' => $datos['tipo_veeduria'],
                'narracion' => $datos['narracion'],
                'analisis_transparencia' => $analisis['transparencia'],
                'analisis_contratacion' => $analisis['contratacion'],
                'analisis_participacion' => $analisis['participacion'],
                'nivel_transparencia' => $analisis['nivel_transparencia'],
                'nivel_riesgo' => $analisis['nivel_riesgo'],
                'hallazgos' => $analisis['hallazgos'],
                'recomendaciones' => $analisis['recomendaciones'],
                'alertas' => $analisis['alertas'],
            ]);

            $respuesta = [
                'id' => $analisisDB->id,
                'exito' => true,
                'analisis' => $analisis
            ];

            if ($this->cacheEnabled) {
                Cache::put($cacheKey, $respuesta, $this->cacheDuration);
            }

            return $respuesta;

        } catch (Exception $e) {
            Log::error('Error en análisis de veeduría:', [
                'error' => $e->getMessage(),
                'datos' => $datos
            ]);

            return [
                'exito' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Consultar OpenAI
     */
    protected function consultarOpenAI(string $prompt, array $opciones = []): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->openAIKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $opciones['model'] ?? 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un experto abogado constitucionalista colombiano especializado en análisis jurídico.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $opciones['temperature'] ?? 0.1,
            'max_tokens' => $opciones['max_tokens'] ?? 4096,
        ]);

        if (!$response->successful()) {
            throw new Exception('Error al consultar OpenAI: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'proveedor' => 'openai',
            'modelo' => $opciones['model'] ?? 'gpt-4',
            'respuesta' => $data['choices'][0]['message']['content'] ?? '',
            'tokens' => $data['usage']['total_tokens'] ?? 0,
            'confianza' => 0.85
        ];
    }

    /**
     * Analizar con un proveedor específico
     */
    protected function analizarConProveedor(string $proveedor, array $datos): ?array
    {
        try {
            $prompt = $this->generarPromptJuridico($datos);

            switch ($proveedor) {
                case 'openai':
                    return $this->consultarOpenAI($prompt);
                
                case 'anthropic':
                    return $this->consultarAnthropic($prompt);
                
                default:
                    return $this->consultarOpenAI($prompt); // Fallback
            }
        } catch (Exception $e) {
            Log::error("Error con proveedor {$proveedor}:", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Consultar Anthropic Claude
     */
    protected function consultarAnthropic(string $prompt): array
    {
        // Implementación simplificada - ajustar según API real
        $response = Http::withHeaders([
            'x-api-key' => $this->anthropicKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-3-opus-20240229',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 4096,
        ]);

        if (!$response->successful()) {
            throw new Exception('Error al consultar Anthropic: ' . $response->body());
        }

        $data = $response->json();

        return [
            'proveedor' => 'anthropic',
            'modelo' => 'claude-3-opus',
            'respuesta' => $data['content'][0]['text'] ?? '',
            'tokens' => $data['usage']['total_tokens'] ?? 0,
            'confianza' => 0.88
        ];
    }

    /**
     * Generar prompt para análisis jurídico
     */
    protected function generarPromptJuridico(array $datos): string
    {
        return sprintf(
            "Analiza el siguiente caso jurídico en Colombia:\n\n" .
            "Tipo de caso: %s\n" .
            "Categoría: %s\n" .
            "Descripción: %s\n\n" .
            "Proporciona un análisis detallado que incluya:\n" .
            "1. Clasificación del tipo de caso\n" .
            "2. Fundamentos legales aplicables\n" .
            "3. Jurisprudencia relevante\n" .
            "4. Recomendaciones específicas\n" .
            "5. Evaluación de riesgos\n\n" .
            "Responde en formato JSON estructurado.",
            $datos['tipo_caso'] ?? 'general',
            $datos['categoria_juridica'] ?? 'Derecho General',
            $datos['texto'] ?? $datos['narracion'] ?? ''
        );
    }

    /**
     * Generar prompt para análisis étnico
     */
    protected function generarPromptEtnico(array $datos): string
    {
        return sprintf(
            "Analiza el siguiente caso relacionado con derechos étnicos en Colombia:\n\n" .
            "Grupo étnico: %s\n" .
            "Comunidad: %s\n" .
            "Ubicación: %s\n" .
            "Situación: %s\n\n" .
            "Evalúa:\n" .
            "1. Derechos fundamentales afectados\n" .
            "2. Necesidad de consulta previa\n" .
            "3. Impacto territorial, cultural y en la autonomía\n" .
            "4. Recomendaciones específicas\n" .
            "5. Procedimientos sugeridos\n\n" .
            "Responde en formato JSON.",
            $datos['grupo_etnico'],
            $datos['comunidad'],
            $datos['ubicacion'],
            $datos['narracion']
        );
    }

    /**
     * Generar prompt para veeduría
     */
    protected function generarPromptVeeduria(array $datos): string
    {
        return sprintf(
            "Analiza la siguiente situación de veeduría ciudadana:\n\n" .
            "Entidad: %s\n" .
            "Proyecto: %s\n" .
            "Tipo: %s\n" .
            "Descripción: %s\n\n" .
            "Evalúa:\n" .
            "1. Nivel de transparencia\n" .
            "2. Aspectos de contratación\n" .
            "3. Participación ciudadana\n" .
            "4. Riesgos identificados\n" .
            "5. Recomendaciones y alertas\n\n" .
            "Responde en formato JSON.",
            $datos['entidad'],
            $datos['proyecto'],
            $datos['tipo_veeduria'],
            $datos['narracion']
        );
    }

    /**
     * Unificar resultados de múltiples proveedores
     */
    protected function unificarResultados(array $resultados): array
    {
        if (empty($resultados)) {
            throw new Exception('No hay resultados para unificar');
        }

        // Combinar las respuestas de los proveedores
        $clasificaciones = [];
        $fundamentos = [];
        $confianzas = [];

        foreach ($resultados as $resultado) {
            $respuesta = json_decode($resultado['respuesta'], true);
            if ($respuesta) {
                $clasificaciones[] = $respuesta['clasificacion'] ?? [];
                $fundamentos[] = $respuesta['fundamentos'] ?? [];
                $confianzas[] = $resultado['confianza'];
            }
        }

        return [
            'clasificaciones' => $clasificaciones[0] ?? [],
            'resumen' => $resultados[0]['respuesta'] ?? '',
            'fundamentos_legales' => $fundamentos[0] ?? [],
            'recomendaciones' => [],
            'evaluacion_riesgos' => [],
            'confianza' => count($confianzas) > 0 ? array_sum($confianzas) / count($confianzas) : 0,
        ];
    }

    /**
     * Procesar respuesta étnica
     */
    protected function procesarRespuestaEtnica(array $resultado, array $datos): array
    {
        $respuesta = json_decode($resultado['respuesta'], true) ?? [];

        return [
            'tipo_detectado' => $datos['grupo_etnico'],
            'confianza' => $resultado['confianza'],
            'derechos_afectados' => $respuesta['derechos_afectados'] ?? [],
            'requiere_consulta_previa' => true,
            'nivel_urgencia' => 'alto',
            'impacto_territorial' => $respuesta['impacto_territorial'] ?? [],
            'impacto_cultural' => $respuesta['impacto_cultural'] ?? [],
            'impacto_autonomia' => $respuesta['impacto_autonomia'] ?? [],
            'recomendaciones' => $respuesta['recomendaciones'] ?? [],
            'procedimientos' => $respuesta['procedimientos'] ?? [],
            'normativas' => $respuesta['normativas'] ?? [],
        ];
    }

    /**
     * Procesar respuesta de veeduría
     */
    protected function procesarRespuestaVeeduria(array $resultado, array $datos): array
    {
        $respuesta = json_decode($resultado['respuesta'], true) ?? [];

        return [
            'transparencia' => $respuesta['transparencia'] ?? [],
            'contratacion' => $respuesta['contratacion'] ?? [],
            'participacion' => $respuesta['participacion'] ?? [],
            'nivel_transparencia' => 'medio',
            'nivel_riesgo' => 'medio',
            'hallazgos' => $respuesta['hallazgos'] ?? [],
            'recomendaciones' => $respuesta['recomendaciones'] ?? [],
            'alertas' => $respuesta['alertas'] ?? [],
        ];
    }

    /**
     * Obtener configuración de análisis según tipo
     */
    protected function obtenerConfiguracionAnalisis(string $tipoCaso): array
    {
        $configuraciones = [
            'constitucional' => [
                'proveedores' => ['openai', 'anthropic'],
                'especializacion' => 'derecho_constitucional'
            ],
            'etnico' => [
                'proveedores' => ['openai'],
                'especializacion' => 'derecho_etnico'
            ],
            'veeduria' => [
                'proveedores' => ['openai'],
                'especializacion' => 'control_social'
            ],
        ];

        return $configuraciones[$tipoCaso] ?? ['proveedores' => ['openai'], 'especializacion' => 'general'];
    }

    /**
     * Generar clave de cache
     */
    protected function generarCacheKey(string $tipo, array $datos): string
    {
        return 'ia_' . $tipo . '_' . md5(json_encode($datos));
    }
}

