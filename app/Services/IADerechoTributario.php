<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para Derecho Tributario
 * Nivel Post-Doctorado con acceso a bases de datos tributarias y jurisprudencia
 */
class IADerechoTributario
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelo;
    protected CircuitBreaker $circuitBreaker;

    // APIs públicas tributarias de Colombia
    protected $apisPublicas = [
        'dian' => 'https://www.dian.gov.co/',
        'corte_constitucional' => 'https://www.corteconstitucional.gov.co/relatoria/',
        'corte_suprema' => 'https://www.cortesuprema.gov.co/',
        'consejo_estado' => 'https://www.consejodeestado.gov.co/',
        'minhacienda' => 'https://www.minhacienda.gov.co/',
        'banco_república' => 'https://www.banrep.gov.co/',
        'superintendencia_sociedades' => 'https://www.supersociedades.gov.co/',
        'superintendencia_financiera' => 'https://www.superfinanciera.gov.co/',
        'contraloria' => 'https://www.contraloria.gov.co/',
        'procuraduria' => 'https://www.procuraduria.gov.co/'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->modelo = config('services.openai.model', 'gpt-4');
        $this->circuitBreaker = new CircuitBreaker('openai', 5, 60, 3);
    }

    /**
     * Análisis de Impuestos Directos
     */
    public function analizarImpuestosDirectos(array $datos): array
    {
        $cacheKey = 'ia_impuestos_directos_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaTributaria('impuestos_directos');
            $prompt = $this->construirPromptImpuestosDirectos($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisTributario('impuestos_directos', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Impuestos Indirectos
     */
    public function analizarImpuestosIndirectos(array $datos): array
    {
        $cacheKey = 'ia_impuestos_indirectos_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaTributaria('impuestos_indirectos');
            $prompt = $this->construirPromptImpuestosIndirectos($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisTributario('impuestos_indirectos', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Procedimiento Tributario
     */
    public function analizarProcedimientoTributario(array $datos): array
    {
        $cacheKey = 'ia_procedimiento_tributario_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaTributaria('procedimiento_tributario');
            $prompt = $this->construirPromptProcedimientoTributario($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisTributario('procedimiento_tributario', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Contencioso Tributario
     */
    public function analizarContenciosoTributario(array $datos): array
    {
        $cacheKey = 'ia_contencioso_tributario_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaTributaria('contencioso_tributario');
            $prompt = $this->construirPromptContenciosoTributario($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisTributario('contencioso_tributario', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Planeación Tributaria
     */
    public function analizarPlaneacionTributaria(array $datos): array
    {
        $cacheKey = 'ia_planeacion_tributaria_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaTributaria('planeacion_tributaria');
            $prompt = $this->construirPromptPlaneacionTributaria($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisTributario('planeacion_tributaria', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de derecho tributario
     */
    public function analisisIntegralDerechoTributario(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'impuestos_directos',
            'impuestos_indirectos',
            'procedimiento_tributario',
            'contencioso_tributario',
            'planeacion_tributaria'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisTributario($resultados, $datos);
    }

    /**
     * Obtener jurisprudencia tributaria de fuentes públicas
     */
    protected function obtenerJurisprudenciaTributaria(string $tipo): array
    {
        $cacheKey = "jurisprudencia_tributaria_{$tipo}";
        
        return Cache::remember($cacheKey, 3600, function () use ($tipo) {
            $jurisprudencia = [];
            
            try {
                // Consultar DIAN
                $response = Http::timeout(30)->get($this->apisPublicas['dian']);
                if ($response->successful()) {
                    $jurisprudencia['dian'] = $this->procesarRespuestaDIAN($response->body(), $tipo);
                }
                
                // Consultar Corte Constitucional
                $response = Http::timeout(30)->get($this->apisPublicas['corte_constitucional']);
                if ($response->successful()) {
                    $jurisprudencia['corte_constitucional'] = $this->procesarRespuestaCorteConstitucional($response->body(), $tipo);
                }
                
                // Consultar Corte Suprema
                $response = Http::timeout(30)->get($this->apisPublicas['corte_suprema']);
                if ($response->successful()) {
                    $jurisprudencia['corte_suprema'] = $this->procesarRespuestaCorteSuprema($response->body(), $tipo);
                }
                
                // Consultar Consejo de Estado
                $response = Http::timeout(30)->get($this->apisPublicas['consejo_estado']);
                if ($response->successful()) {
                    $jurisprudencia['consejo_estado'] = $this->procesarRespuestaConsejoEstado($response->body(), $tipo);
                }
                
            } catch (\Exception $e) {
                Log::error('Error obteniendo jurisprudencia tributaria', [
                    'tipo' => $tipo,
                    'error' => $e->getMessage()
                ]);
            }
            
            return $jurisprudencia;
        });
    }

    /**
     * Procesar respuesta de DIAN
     */
    protected function procesarRespuestaDIAN(string $html, string $tipo): array
    {
        $patrones = [
            'circulares' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Circular[^<]*)<\/a>/i',
            'conceptos' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Concepto[^<]*)<\/a>/i',
            'resoluciones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Resolución[^<]*)<\/a>/i',
            'decretos' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Decreto[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'DIAN'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de Corte Constitucional
     */
    protected function procesarRespuestaCorteConstitucional(string $html, string $tipo): array
    {
        $patrones = [
            'sentencias' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Sentencia[^<]*)<\/a>/i',
            'autos' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Auto[^<]*)<\/a>/i',
            'comunicados' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Comunicado[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'Corte Constitucional'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de Corte Suprema
     */
    protected function procesarRespuestaCorteSuprema(string $html, string $tipo): array
    {
        $patrones = [
            'sentencias' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Sentencia[^<]*)<\/a>/i',
            'jurisprudencia' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Jurisprudencia[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'Corte Suprema'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de Consejo de Estado
     */
    protected function procesarRespuestaConsejoEstado(string $html, string $tipo): array
    {
        $patrones = [
            'sentencias' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Sentencia[^<]*)<\/a>/i',
            'consejos' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Consejo[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'Consejo de Estado'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Ejecutar análisis tributario
     */
    protected function ejecutarAnalisisTributario(string $tipoAnalisis, string $prompt, array $datos): array
    {
        $response = Http::timeout(120)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $this->modelo,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Eres un experto en derecho tributario de nivel post-doctorado. " .
                                   "Especializado en impuestos directos e indirectos, procedimiento tributario, " .
                                   "contencioso tributario y planeación tributaria. Tienes acceso a jurisprudencia " .
                                   "actualizada de la DIAN, Corte Constitucional, Corte Suprema y Consejo de Estado. " .
                                   "Proporciona análisis exhaustivos con fundamentación académica sólida, " .
                                   "referencias específicas a jurisprudencia tributaria y explicaciones detalladas " .
                                   "del procedimiento legal tributario."
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 6000,
                'temperature' => 0.1,
            ]);

        if ($response->successful()) {
            $resultado = $response->json();
            $analisis = $resultado['choices'][0]['message']['content'];

            $this->guardarAnalisisTributario($tipoAnalisis, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'tipo_analisis' => $tipoAnalisis,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $this->modelo,
                'jurisprudencia_consultada' => $this->obtenerJurisprudenciaTributaria($tipoAnalisis),
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis tributario: ' . $response->body());
    }

    /**
     * Construir prompt para impuestos directos
     */
    protected function construirPromptImpuestosDirectos(array $datos, array $jurisprudencia): string
    {
        $tipoImpuesto = $datos['tipo_impuesto'] ?? 'general';
        $datosImpuesto = $datos['datos_impuesto'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Impuesto Directo';

        $jurisprudenciaText = $this->formatearJurisprudenciaTributaria($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - IMPUESTOS DIRECTOS

TIPO DE IMPUESTO: {$tipoImpuesto}
CASO: {$caso}

DATOS DEL IMPUESTO:
{$datosImpuesto}

JURISPRUDENCIA TRIBUTARIA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE IMPUESTOS DIRECTOS:
   - Estatuto Tributario Nacional (Ley 1607 de 2012)
   - Código de Procedimiento Tributario (Decreto 624 de 1989)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema
   - Jurisprudencia del Consejo de Estado

2. IMPUESTO SOBRE LA RENTA:
   - Concepto y naturaleza
   - Sujetos pasivos
   - Base gravable
   - Tarifas
   - Deducciones
   - Exenciones

3. IMPUESTO DE INDUSTRIA Y COMERCIO:
   - Concepto y naturaleza
   - Sujetos pasivos
   - Base gravable
   - Tarifas
   - Deducciones
   - Exenciones

4. IMPUESTO PREDIAL:
   - Concepto y naturaleza
   - Sujetos pasivos
   - Base gravable
   - Tarifas
   - Deducciones
   - Exenciones

5. IMPUESTO DE VEHÍCULOS:
   - Concepto y naturaleza
   - Sujetos pasivos
   - Base gravable
   - Tarifas
   - Deducciones
   - Exenciones

6. IMPUESTO DE TIMBRE:
   - Concepto y naturaleza
   - Sujetos pasivos
   - Base gravable
   - Tarifas
   - Deducciones
   - Exenciones

7. IMPUESTO DE RIQUEZA:
   - Concepto y naturaleza
   - Sujetos pasivos
   - Base gravable
   - Tarifas
   - Deducciones
   - Exenciones

8. JURISPRUDENCIA TRIBUTARIA:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Sentencias del Consejo de Estado
   - Conceptos de la DIAN
   - Circulares de la DIAN

9. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

10. RECOMENDACIONES ESTRATÉGICAS:
    - Optimización tributaria
    - Cumplimiento normativo
    - Planeación tributaria
    - Gestión de riesgos
    - Capacitación especializada
    - Asesoría profesional

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia tributaria y explicaciones detalladas del procedimiento legal tributario.";
    }

    /**
     * Formatear jurisprudencia tributaria para el prompt
     */
    protected function formatearJurisprudenciaTributaria(array $jurisprudencia): string
    {
        $texto = "JURISPRUDENCIA TRIBUTARIA CONSULTADA:\n\n";
        
        foreach ($jurisprudencia as $fuente => $categorias) {
            $texto .= "=== {$fuente} ===\n";
            foreach ($categorias as $categoria => $documentos) {
                $texto .= "{$categoria}:\n";
                foreach ($documentos as $doc) {
                    $texto .= "- {$doc['titulo']} ({$doc['fuente']})\n";
                    $texto .= "  URL: {$doc['url']}\n";
                }
                $texto .= "\n";
            }
        }
        
        return $texto;
    }

    /**
     * Construir prompt para impuestos indirectos
     */
    protected function construirPromptImpuestosIndirectos(array $datos, array $jurisprudencia): string
    {
        $tipoImpuesto = $datos['tipo_impuesto'] ?? 'general';
        $datosImpuesto = $datos['datos_impuesto'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Impuesto Indirecto';

        $jurisprudenciaText = $this->formatearJurisprudenciaTributaria($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - IMPUESTOS INDIRECTOS

TIPO DE IMPUESTO: {$tipoImpuesto}
CASO: {$caso}

DATOS DEL IMPUESTO:
{$datosImpuesto}

JURISPRUDENCIA TRIBUTARIA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE IMPUESTOS INDIRECTOS:
   - Estatuto Tributario Nacional (Ley 1607 de 2012)
   - Código de Procedimiento Tributario (Decreto 624 de 1989)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema
   - Jurisprudencia del Consejo de Estado

2. IMPUESTO AL VALOR AGREGADO (IVA):
   - Concepto y naturaleza
   - Sujetos pasivos
   - Base gravable
   - Tarifas
   - Deducciones
   - Exenciones

3. IMPUESTO AL CONSUMO:
   - Concepto y naturaleza
   - Sujetos pasivos
   - Base gravable
   - Tarifas
   - Deducciones
   - Exenciones

4. IMPUESTO A LAS VENTAS:
   - Concepto y naturaleza
   - Sujetos pasivos
   - Base gravable
   - Tarifas
   - Deducciones
   - Exenciones

5. IMPUESTO A LA GASOLINA:
   - Concepto y naturaleza
   - Sujetos pasivos
   - Base gravable
   - Tarifas
   - Deducciones
   - Exenciones

6. IMPUESTO AL TABACO:
   - Concepto y naturaleza
   - Sujetos pasivos
   - Base gravable
   - Tarifas
   - Deducciones
   - Exenciones

7. IMPUESTO AL ALCOHOL:
   - Concepto y naturaleza
   - Sujetos pasivos
   - Base gravable
   - Tarifas
   - Deducciones
   - Exenciones

8. JURISPRUDENCIA TRIBUTARIA:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Sentencias del Consejo de Estado
   - Conceptos de la DIAN
   - Circulares de la DIAN

9. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

10. RECOMENDACIONES ESTRATÉGICAS:
    - Optimización tributaria
    - Cumplimiento normativo
    - Planeación tributaria
    - Gestión de riesgos
    - Capacitación especializada
    - Asesoría profesional

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia tributaria y explicaciones detalladas del procedimiento legal tributario.";
    }

    /**
     * Construir prompt para procedimiento tributario
     */
    protected function construirPromptProcedimientoTributario(array $datos, array $jurisprudencia): string
    {
        $tipoProcedimiento = $datos['tipo_procedimiento'] ?? 'general';
        $datosProcedimiento = $datos['datos_procedimiento'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Procedimiento Tributario';

        $jurisprudenciaText = $this->formatearJurisprudenciaTributaria($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - PROCEDIMIENTO TRIBUTARIO

TIPO DE PROCEDIMIENTO: {$tipoProcedimiento}
CASO: {$caso}

DATOS DEL PROCEDIMIENTO:
{$datosProcedimiento}

JURISPRUDENCIA TRIBUTARIA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL PROCEDIMIENTO TRIBUTARIO:
   - Código de Procedimiento Tributario (Decreto 624 de 1989)
   - Estatuto Tributario Nacional (Ley 1607 de 2012)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema
   - Jurisprudencia del Consejo de Estado

2. PRINCIPIOS DEL PROCEDIMIENTO TRIBUTARIO:
   - Principio de legalidad
   - Principio de igualdad
   - Principio de contradicción
   - Principio de publicidad
   - Principio de economía procesal
   - Principio de celeridad

3. SUJETOS DEL PROCEDIMIENTO:
   - Administración tributaria
   - Contribuyentes
   - Responsables
   - Terceros
   - Representantes
   - Apoderados

4. ACTOS ADMINISTRATIVOS TRIBUTARIOS:
   - Concepto y naturaleza
   - Clasificación
   - Requisitos
   - Efectos
   - Nulidad
   - Revocación

5. PROCEDIMIENTO DE LIQUIDACIÓN:
   - Concepto y naturaleza
   - Iniciación
   - Trámite
   - Resolución
   - Recursos
   - Ejecución

6. PROCEDIMIENTO DE RECAUDO:
   - Concepto y naturaleza
   - Formas de pago
   - Plazos
   - Intereses
   - Sanciones
   - Medidas cautelares

7. PROCEDIMIENTO DE FISCALIZACIÓN:
   - Concepto y naturaleza
   - Iniciación
   - Trámite
   - Resolución
   - Recursos
   - Ejecución

8. PROCEDIMIENTO DE DEVOLUCIÓN:
   - Concepto y naturaleza
   - Requisitos
   - Trámite
   - Resolución
   - Recursos
   - Ejecución

9. JURISPRUDENCIA TRIBUTARIA:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Sentencias del Consejo de Estado
   - Conceptos de la DIAN
   - Circulares de la DIAN

10. RECOMENDACIONES ESTRATÉGICAS:
    - Cumplimiento procedimental
    - Defensa de derechos
    - Gestión de recursos
    - Capacitación especializada
    - Asesoría profesional
    - Representación legal

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia tributaria y explicaciones detalladas del procedimiento legal tributario.";
    }

    /**
     * Construir prompt para contencioso tributario
     */
    protected function construirPromptContenciosoTributario(array $datos, array $jurisprudencia): string
    {
        $tipoContencioso = $datos['tipo_contencioso'] ?? 'general';
        $datosContencioso = $datos['datos_contencioso'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Contencioso Tributario';

        $jurisprudenciaText = $this->formatearJurisprudenciaTributaria($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - CONTENCIOSO TRIBUTARIO

TIPO DE CONTENCIOSO: {$tipoContencioso}
CASO: {$caso}

DATOS DEL CONTENCIOSO:
{$datosContencioso}

JURISPRUDENCIA TRIBUTARIA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL CONTENCIOSO TRIBUTARIO:
   - Código de Procedimiento Tributario (Decreto 624 de 1989)
   - Estatuto Tributario Nacional (Ley 1607 de 2012)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema
   - Jurisprudencia del Consejo de Estado

2. PRINCIPIOS DEL CONTENCIOSO TRIBUTARIO:
   - Principio de legalidad
   - Principio de igualdad
   - Principio de contradicción
   - Principio de publicidad
   - Principio de economía procesal
   - Principio de celeridad

3. SUJETOS DEL CONTENCIOSO:
   - Administración tributaria
   - Contribuyentes
   - Responsables
   - Terceros
   - Representantes
   - Apoderados

4. PROCEDIMIENTO ADMINISTRATIVO:
   - Concepto y naturaleza
   - Iniciación
   - Trámite
   - Resolución
   - Recursos
   - Ejecución

5. PROCEDIMIENTO JUDICIAL:
   - Concepto y naturaleza
   - Competencia
   - Iniciación
   - Trámite
   - Sentencia
   - Recursos

6. RECURSOS ADMINISTRATIVOS:
   - Recurso de reposición
   - Recurso de apelación
   - Recurso de queja
   - Recurso de súplica
   - Recurso de casación
   - Recurso de revisión

7. RECURSOS JUDICIALES:
   - Acción de nulidad
   - Acción de restablecimiento del derecho
   - Acción de tutela
   - Acción de cumplimiento
   - Acción popular
   - Acción de grupo

8. JURISPRUDENCIA TRIBUTARIA:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Sentencias del Consejo de Estado
   - Conceptos de la DIAN
   - Circulares de la DIAN

9. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

10. RECOMENDACIONES ESTRATÉGICAS:
    - Estrategia de defensa
    - Gestión de recursos
    - Representación legal
    - Capacitación especializada
    - Asesoría profesional
    - Negociación y conciliación

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia tributaria y explicaciones detalladas del procedimiento legal tributario.";
    }

    /**
     * Construir prompt para planeación tributaria
     */
    protected function construirPromptPlaneacionTributaria(array $datos, array $jurisprudencia): string
    {
        $tipoPlaneacion = $datos['tipo_planeacion'] ?? 'general';
        $datosPlaneacion = $datos['datos_planeacion'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Planeación Tributaria';

        $jurisprudenciaText = $this->formatearJurisprudenciaTributaria($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - PLANEACIÓN TRIBUTARIA

TIPO DE PLANEACIÓN: {$tipoPlaneacion}
CASO: {$caso}

DATOS DE LA PLANEACIÓN:
{$datosPlaneacion}

JURISPRUDENCIA TRIBUTARIA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE LA PLANEACIÓN TRIBUTARIA:
   - Estatuto Tributario Nacional (Ley 1607 de 2012)
   - Código de Procedimiento Tributario (Decreto 624 de 1989)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema
   - Jurisprudencia del Consejo de Estado

2. PRINCIPIOS DE LA PLANEACIÓN TRIBUTARIA:
   - Principio de legalidad
   - Principio de transparencia
   - Principio de sustancia económica
   - Principio de realidad económica
   - Principio de buena fe
   - Principio de proporcionalidad

3. TÉCNICAS DE PLANEACIÓN TRIBUTARIA:
   - Optimización de estructura
   - Aprovechamiento de incentivos
   - Gestión de deducciones
   - Manejo de exenciones
   - Coordinación de impuestos
   - Timing de operaciones

4. ESTRUCTURAS TRIBUTARIAS:
   - Personas naturales
   - Personas jurídicas
   - Sociedades
   - Consorcios
   - Uniones temporales
   - Empresas unipersonales

5. INCENTIVOS TRIBUTARIOS:
   - Zonas francas
   - Regímenes especiales
   - Deducciones especiales
   - Exenciones
   - Descuentos
   - Créditos tributarios

6. GESTIÓN DE RIESGOS:
   - Identificación de riesgos
   - Evaluación de riesgos
   - Medidas de mitigación
   - Planes de contingencia
   - Monitoreo continuo
   - Actualización de riesgos

7. CUMPLIMIENTO NORMATIVO:
   - Obligaciones formales
   - Obligaciones sustanciales
   - Plazos y términos
   - Sanciones y multas
   - Procedimientos
   - Recursos

8. JURISPRUDENCIA TRIBUTARIA:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Sentencias del Consejo de Estado
   - Conceptos de la DIAN
   - Circulares de la DIAN

9. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

10. RECOMENDACIONES ESTRATÉGICAS:
    - Optimización tributaria
    - Cumplimiento normativo
    - Gestión de riesgos
    - Capacitación especializada
    - Asesoría profesional
    - Monitoreo continuo

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia tributaria y explicaciones detalladas del procedimiento legal tributario.";
    }

    /**
     * Consolidar análisis tributario
     */
    protected function consolidarAnalisisTributario(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_derecho_tributario',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'jurisprudencia_consultada' => [],
            'referencias_legales' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - DERECHO TRIBUTARIO\n\n";
        foreach ($resultados as $tipoAnalisis => $resultado) {
            if ($resultado['exito']) {
                $analisisConsolidado .= "=== {$tipoAnalisis} ===\n";
                $analisisConsolidado .= $resultado['analisis'] . "\n\n";
                
                // Consolidar jurisprudencia
                if (isset($resultado['jurisprudencia_consultada'])) {
                    $consolidado['jurisprudencia_consultada'] = array_merge(
                        $consolidado['jurisprudencia_consultada'],
                        $resultado['jurisprudencia_consultada']
                    );
                }
            }
        }

        $consolidado['analisis_consolidado'] = $analisisConsolidado;

        // Generar recomendaciones integrales
        $consolidado['recomendaciones_integrales'] = [
            'Optimizar la estructura tributaria',
            'Cumplir con las obligaciones tributarias',
            'Gestionar los riesgos tributarios',
            'Aprovechar los incentivos tributarios',
            'Desarrollar la planeación tributaria',
            'Capacitar en derecho tributario especializado'
        ];

        // Generar referencias legales
        $consolidado['referencias_legales'] = [
            'Estatuto Tributario Nacional (Ley 1607 de 2012)',
            'Código de Procedimiento Tributario (Decreto 624 de 1989)',
            'Jurisprudencia de la Corte Constitucional',
            'Jurisprudencia de la Corte Suprema',
            'Jurisprudencia del Consejo de Estado',
            'Conceptos de la DIAN',
            'Circulares de la DIAN',
            'Resoluciones de la DIAN'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis tributario
     */
    protected function guardarAnalisisTributario(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
    {
        try {
            AnalisisIA::create([
                'especialidad' => $tipoAnalisis,
                'nivel' => 'post_doctorado',
                'datos_entrada' => json_encode($datos),
                'analisis_completo' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo_ia' => $this->modelo,
                'estado' => 'completado',
                'fecha_analisis' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Error guardando análisis tributario', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de derecho tributario
     */
    public function obtenerEstadisticasDerechoTributario(): array
    {
        return [
            'total_analisis_tributario' => AnalisisIA::whereIn('especialidad', [
                'impuestos_directos',
                'impuestos_indirectos',
                'procedimiento_tributario',
                'contencioso_tributario',
                'planeacion_tributaria'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'impuestos_directos',
                'impuestos_indirectos',
                'procedimiento_tributario',
                'contencioso_tributario',
                'planeacion_tributaria'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'impuestos_directos',
                'impuestos_indirectos',
                'procedimiento_tributario',
                'contencioso_tributario',
                'planeacion_tributaria'
            ])->sum('tokens_usados'),
            'apis_publicas_consultadas' => array_keys($this->apisPublicas)
        ];
    }
}

