<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para IPMA (International Project Management Association)
 * Nivel Post-Doctorado con enfoque ético y competencias profesionales
 */
class IAIPMA
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelo;
    protected CircuitBreaker $circuitBreaker;

    // APIs públicas de IPMA y gestión de proyectos
    protected $apisPublicas = [
        'ipma' => 'https://www.ipma.world/',
        'ipma_competence' => 'https://www.ipma.world/competence/',
        'ipma_ethics' => 'https://www.ipma.world/ethics/',
        'ipma_certification' => 'https://www.ipma.world/certification/',
        'ipma_standards' => 'https://www.ipma.world/standards/',
        'ipma_research' => 'https://www.ipma.world/research/',
        'ipma_events' => 'https://www.ipma.world/events/',
        'ipma_publications' => 'https://www.ipma.world/publications/',
        'global_project_management' => 'https://www.pmi.org/',
        'project_management_ethics' => 'https://www.apm.org.uk/'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->modelo = config('services.openai.model', 'gpt-4');
        $this->circuitBreaker = new CircuitBreaker('openai', 5, 60, 3);
    }

    /**
     * Análisis de Competencias IPMA
     */
    public function analizarCompetenciasIPMA(array $datos): array
    {
        $cacheKey = 'ia_competencias_ipma_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaIPMA('competencias_ipma');
            $prompt = $this->construirPromptCompetenciasIPMA($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisIPMA('competencias_ipma', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Código de Ética IPMA
     */
    public function analizarCodigoEticaIPMA(array $datos): array
    {
        $cacheKey = 'ia_codigo_etica_ipma_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaIPMA('codigo_etica_ipma');
            $prompt = $this->construirPromptCodigoEticaIPMA($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisIPMA('codigo_etica_ipma', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Certificación IPMA
     */
    public function analizarCertificacionIPMA(array $datos): array
    {
        $cacheKey = 'ia_certificacion_ipma_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaIPMA('certificacion_ipma');
            $prompt = $this->construirPromptCertificacionIPMA($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisIPMA('certificacion_ipma', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Estándares IPMA
     */
    public function analizarEstandaresIPMA(array $datos): array
    {
        $cacheKey = 'ia_estandares_ipma_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaIPMA('estandares_ipma');
            $prompt = $this->construirPromptEstandaresIPMA($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisIPMA('estandares_ipma', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Liderazgo Ético en Proyectos
     */
    public function analizarLiderazgoEticoProyectos(array $datos): array
    {
        $cacheKey = 'ia_liderazgo_etico_proyectos_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaIPMA('liderazgo_etico_proyectos');
            $prompt = $this->construirPromptLiderazgoEticoProyectos($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisIPMA('liderazgo_etico_proyectos', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de IPMA
     */
    public function analisisIntegralIPMA(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'competencias_ipma',
            'codigo_etica_ipma',
            'certificacion_ipma',
            'estandares_ipma',
            'liderazgo_etico_proyectos'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisIPMA($resultados, $datos);
    }

    /**
     * Obtener jurisprudencia IPMA de fuentes públicas
     */
    protected function obtenerJurisprudenciaIPMA(string $tipo): array
    {
        $cacheKey = "jurisprudencia_ipma_{$tipo}";
        
        return Cache::remember($cacheKey, 3600, function () use ($tipo) {
            $jurisprudencia = [];
            
            try {
                // Consultar IPMA
                $response = Http::timeout(30)->get($this->apisPublicas['ipma']);
                if ($response->successful()) {
                    $jurisprudencia['ipma'] = $this->procesarRespuestaIPMA($response->body(), $tipo);
                }
                
                // Consultar competencias IPMA
                $response = Http::timeout(30)->get($this->apisPublicas['ipma_competence']);
                if ($response->successful()) {
                    $jurisprudencia['ipma_competence'] = $this->procesarRespuestaCompetenciasIPMA($response->body(), $tipo);
                }
                
                // Consultar ética IPMA
                $response = Http::timeout(30)->get($this->apisPublicas['ipma_ethics']);
                if ($response->successful()) {
                    $jurisprudencia['ipma_ethics'] = $this->procesarRespuestaEthicsIPMA($response->body(), $tipo);
                }
                
                // Consultar certificación IPMA
                $response = Http::timeout(30)->get($this->apisPublicas['ipma_certification']);
                if ($response->successful()) {
                    $jurisprudencia['ipma_certification'] = $this->procesarRespuestaCertificacionIPMA($response->body(), $tipo);
                }
                
            } catch (\Exception $e) {
                Log::error('Error obteniendo jurisprudencia IPMA', [
                    'tipo' => $tipo,
                    'error' => $e->getMessage()
                ]);
            }
            
            return $jurisprudencia;
        });
    }

    /**
     * Procesar respuesta de IPMA
     */
    protected function procesarRespuestaIPMA(string $html, string $tipo): array
    {
        $patrones = [
            'estandares' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Standard[^<]*)<\/a>/i',
            'competencias' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Competence[^<]*)<\/a>/i',
            'etica' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Ethics[^<]*)<\/a>/i',
            'certificacion' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Certification[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'International Project Management Association (IPMA)'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de competencias IPMA
     */
    protected function procesarRespuestaCompetenciasIPMA(string $html, string $tipo): array
    {
        $patrones = [
            'competencias_tecnicas' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Technical[^<]*)<\/a>/i',
            'competencias_conductuales' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Behavioural[^<]*)<\/a>/i',
            'competencias_contextuales' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Contextual[^<]*)<\/a>/i',
            'competencias_liderazgo' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Leadership[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'IPMA Competence'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de ética IPMA
     */
    protected function procesarRespuestaEthicsIPMA(string $html, string $tipo): array
    {
        $patrones = [
            'codigo_etica' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Code[^<]*)<\/a>/i',
            'conducta_profesional' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Conduct[^<]*)<\/a>/i',
            'valores' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Values[^<]*)<\/a>/i',
            'principios' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Principles[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'IPMA Ethics'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de certificación IPMA
     */
    protected function procesarRespuestaCertificacionIPMA(string $html, string $tipo): array
    {
        $patrones = [
            'certificaciones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Certification[^<]*)<\/a>/i',
            'niveles' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Level[^<]*)<\/a>/i',
            'requisitos' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Requirements[^<]*)<\/a>/i',
            'proceso' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Process[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'IPMA Certification'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Ejecutar análisis IPMA
     */
    protected function ejecutarAnalisisIPMA(string $tipoAnalisis, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en gestión de proyectos IPMA de nivel post-doctorado. " .
                                   "Especializado en competencias profesionales, códigos de ética IPMA, " .
                                   "certificaciones, estándares y liderazgo ético en proyectos. Tienes acceso a " .
                                   "estándares actualizados de IPMA y organizaciones internacionales de gestión " .
                                   "de proyectos. Proporciona análisis exhaustivos con fundamentación académica sólida, " .
                                   "referencias específicas a competencias y códigos de ética, y explicaciones detalladas " .
                                   "del procedimiento de gestión ética de proyectos con enfoque en competencias profesionales."
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

            $this->guardarAnalisisIPMA($tipoAnalisis, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'tipo_analisis' => $tipoAnalisis,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $this->modelo,
                'jurisprudencia_consultada' => $this->obtenerJurisprudenciaIPMA($tipoAnalisis),
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis IPMA: ' . $response->body());
    }

    /**
     * Construir prompt para competencias IPMA
     */
    protected function construirPromptCompetenciasIPMA(array $datos, array $jurisprudencia): string
    {
        $tipoCompetencia = $datos['tipo_competencia'] ?? 'general';
        $datosCompetencia = $datos['datos_competencia'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Competencias IPMA';

        $jurisprudenciaText = $this->formatearJurisprudenciaIPMA($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - COMPETENCIAS IPMA

TIPO DE COMPETENCIA: {$tipoCompetencia}
CASO: {$caso}

DATOS DE LA COMPETENCIA:
{$datosCompetencia}

JURISPRUDENCIA IPMA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE COMPETENCIAS IPMA:
   - Modelo de Competencias IPMA
   - Estándares de Competencia Profesional
   - Guías de Evaluación de Competencias
   - Marcos de Certificación
   - Mejores Prácticas Internacionales

2. COMPETENCIAS TÉCNICAS:
   - Gestión de Proyectos
   - Gestión de Programas
   - Gestión de Portafolios
   - Gestión de Riesgos
   - Gestión de Calidad
   - Gestión de Recursos

3. COMPETENCIAS CONDUCTUALES:
   - Liderazgo
   - Comunicación
   - Trabajo en Equipo
   - Resolución de Conflictos
   - Toma de Decisiones
   - Adaptabilidad

4. COMPETENCIAS CONTEXTUALES:
   - Gestión de Stakeholders
   - Gestión de Cambios
   - Gestión de Crisis
   - Gestión de Sostenibilidad
   - Gestión de Innovación
   - Gestión de Conocimiento

5. COMPETENCIAS DE LIDERAZGO:
   - Liderazgo Transformacional
   - Liderazgo Situacional
   - Liderazgo Ético
   - Liderazgo Inclusivo
   - Liderazgo Sostenible
   - Liderazgo Digital

6. EVALUACIÓN DE COMPETENCIAS:
   - Métodos de Evaluación
   - Herramientas de Evaluación
   - Criterios de Evaluación
   - Procesos de Certificación
   - Mantenimiento de Competencias
   - Desarrollo Profesional

7. DESARROLLO DE COMPETENCIAS:
   - Planificación del Desarrollo
   - Estrategias de Aprendizaje
   - Mentoring y Coaching
   - Experiencia Práctica
   - Formación Continua
   - Certificación Profesional

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Aplicación de competencias
   - Criterios de evaluación
   - Interpretación profesional
   - Efectos del desarrollo

9. RECOMENDACIONES ESTRATÉGICAS:
    - Desarrollo de competencias
    - Evaluación continua
    - Certificación profesional
    - Mejora de procesos
    - Desarrollo organizacional
    - Liderazgo en competencias

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Modelo de Competencias IPMA
    - Estándares de Certificación
    - Guías de Evaluación
    - Mejores Prácticas
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a competencias y explicaciones detalladas del procedimiento de desarrollo y evaluación de competencias profesionales.";
    }

    /**
     * Formatear jurisprudencia IPMA para el prompt
     */
    protected function formatearJurisprudenciaIPMA(array $jurisprudencia): string
    {
        $texto = "JURISPRUDENCIA IPMA CONSULTADA:\n\n";
        
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
     * Construir prompt para código de ética IPMA
     */
    protected function construirPromptCodigoEticaIPMA(array $datos, array $jurisprudencia): string
    {
        $tipoEtica = $datos['tipo_etica'] ?? 'general';
        $datosEtica = $datos['datos_etica'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Código de Ética IPMA';

        $jurisprudenciaText = $this->formatearJurisprudenciaIPMA($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - CÓDIGO DE ÉTICA IPMA

TIPO DE ÉTICA: {$tipoEtica}
CASO: {$caso}

DATOS DE ÉTICA:
{$datosEtica}

JURISPRUDENCIA IPMA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL CÓDIGO DE ÉTICA IPMA:
   - Código de Ética y Conducta Profesional IPMA
   - Estándares de Ética en Gestión de Proyectos
   - Principios Éticos Fundamentales
   - Valores Profesionales
   - Responsabilidades Profesionales

2. PRINCIPIOS ÉTICOS FUNDAMENTALES:
   - Integridad
   - Honestidad
   - Transparencia
   - Respeto
   - Equidad
   - Responsabilidad

3. VALORES PROFESIONALES:
   - Excelencia Profesional
   - Respeto por la Diversidad
   - Integridad Personal
   - Responsabilidad Social
   - Liderazgo Ético
   - Innovación Responsable

4. RESPONSABILIDADES PROFESIONALES:
   - Hacia la Profesión
   - Hacia los Clientes
   - Hacia los Equipos
   - Hacia la Sociedad
   - Hacia el Medio Ambiente
   - Hacia las Generaciones Futuras

5. CÓDIGO DE CONDUCTA:
   - Estándares de Comportamiento
   - Prohibiciones Éticas
   - Conflictos de Interés
   - Confidencialidad
   - Uso de Información
   - Relaciones Profesionales

6. GESTIÓN DE CONFLICTOS ÉTICOS:
   - Identificación de Conflictos
   - Evaluación de Alternativas
   - Toma de Decisiones Éticas
   - Resolución de Conflictos
   - Medidas Correctivas
   - Prevención de Conflictos

7. APLICACIÓN EN GESTIÓN DE PROYECTOS:
   - Planificación Ética
   - Ejecución Responsable
   - Monitoreo de Cumplimiento
   - Evaluación de Resultados
   - Mejora Continua
   - Rendición de Cuentas

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones éticas
   - Criterios de aplicación
   - Interpretación profesional
   - Efectos de las decisiones

9. RECOMENDACIONES ESTRATÉGICAS:
    - Implementación del código
    - Capacitación ética
    - Monitoreo continuo
    - Mejora de procesos
    - Desarrollo profesional
    - Liderazgo ético

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Código de Ética IPMA
    - Estándares internacionales
    - Casos de estudio
    - Investigaciones académicas
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a códigos de ética y explicaciones detalladas del procedimiento de gestión ética de proyectos.";
    }

    /**
     * Construir prompt para certificación IPMA
     */
    protected function construirPromptCertificacionIPMA(array $datos, array $jurisprudencia): string
    {
        $tipoCertificacion = $datos['tipo_certificacion'] ?? 'general';
        $datosCertificacion = $datos['datos_certificacion'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Certificación IPMA';

        $jurisprudenciaText = $this->formatearJurisprudenciaIPMA($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - CERTIFICACIÓN IPMA

TIPO DE CERTIFICACIÓN: {$tipoCertificacion}
CASO: {$caso}

DATOS DE LA CERTIFICACIÓN:
{$datosCertificacion}

JURISPRUDENCIA IPMA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE CERTIFICACIÓN IPMA:
   - Sistema de Certificación IPMA
   - Niveles de Certificación
   - Requisitos de Certificación
   - Procesos de Evaluación
   - Estándares de Competencia

2. NIVELES DE CERTIFICACIÓN:
   - IPMA Level A (Programme Director)
   - IPMA Level B (Senior Project Manager)
   - IPMA Level C (Project Manager)
   - IPMA Level D (Project Management Associate)

3. REQUISITOS DE CERTIFICACIÓN:
   - Experiencia Profesional
   - Competencias Técnicas
   - Competencias Conductuales
   - Competencias Contextuales
   - Formación Específica
   - Evaluación de Competencias

4. PROCESO DE CERTIFICACIÓN:
   - Aplicación
   - Evaluación de Documentos
   - Evaluación de Competencias
   - Entrevista Profesional
   - Decisión de Certificación
   - Mantenimiento de Certificación

5. EVALUACIÓN DE COMPETENCIAS:
   - Métodos de Evaluación
   - Herramientas de Evaluación
   - Criterios de Evaluación
   - Procesos de Evaluación
   - Resultados de Evaluación
   - Desarrollo de Competencias

6. MANTENIMIENTO DE CERTIFICACIÓN:
   - Requisitos de Mantenimiento
   - Desarrollo Profesional Continuo
   - Renovación de Certificación
   - Actualización de Competencias
   - Seguimiento de Desempeño
   - Mejora Continua

7. BENEFICIOS DE LA CERTIFICACIÓN:
   - Reconocimiento Profesional
   - Desarrollo de Competencias
   - Mejora de Desempeño
   - Oportunidades de Carrera
   - Red Profesional
   - Liderazgo en la Profesión

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Aplicación de competencias
   - Criterios de evaluación
   - Interpretación profesional
   - Efectos de la certificación

9. RECOMENDACIONES ESTRATÉGICAS:
    - Preparación para certificación
    - Desarrollo de competencias
    - Mantenimiento de certificación
    - Mejora continua
    - Desarrollo profesional
    - Liderazgo en certificación

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Sistema de Certificación IPMA
    - Estándares de Competencia
    - Guías de Evaluación
    - Mejores Prácticas
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a certificaciones y explicaciones detalladas del procedimiento de certificación profesional.";
    }

    /**
     * Construir prompt para estándares IPMA
     */
    protected function construirPromptEstandaresIPMA(array $datos, array $jurisprudencia): string
    {
        $tipoEstandar = $datos['tipo_estandar'] ?? 'general';
        $datosEstandar = $datos['datos_estandar'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Estándares IPMA';

        $jurisprudenciaText = $this->formatearJurisprudenciaIPMA($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - ESTÁNDARES IPMA

TIPO DE ESTÁNDAR: {$tipoEstandar}
CASO: {$caso}

DATOS DEL ESTÁNDAR:
{$datosEstandar}

JURISPRUDENCIA IPMA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE ESTÁNDARES IPMA:
   - Estándares de Gestión de Proyectos IPMA
   - Mejores Prácticas Internacionales
   - Guías de Implementación
   - Normativas de Calidad
   - Estándares de Competencia

2. ESTÁNDARES DE GESTIÓN:
   - Gestión de Proyectos
   - Gestión de Programas
   - Gestión de Portafolios
   - Gestión de Riesgos
   - Gestión de Calidad
   - Gestión de Recursos

3. ESTÁNDARES DE COMPETENCIA:
   - Competencias Técnicas
   - Competencias Conductuales
   - Competencias Contextuales
   - Competencias de Liderazgo
   - Competencias Éticas
   - Competencias de Sostenibilidad

4. ESTÁNDARES DE PROCESO:
   - Procesos de Iniciación
   - Procesos de Planificación
   - Procesos de Ejecución
   - Procesos de Monitoreo
   - Procesos de Cierre
   - Procesos de Mejora

5. ESTÁNDARES DE CALIDAD:
   - Criterios de Calidad
   - Métricas de Calidad
   - Evaluación de Calidad
   - Mejora de Calidad
   - Certificación de Calidad
   - Aseguramiento de Calidad

6. ESTÁNDARES DE SOSTENIBILIDAD:
   - Gestión Ambiental
   - Responsabilidad Social
   - Desarrollo Sostenible
   - Economía Circular
   - Inclusión Social
   - Equidad Intergeneracional

7. IMPLEMENTACIÓN DE ESTÁNDARES:
   - Planificación de Implementación
   - Capacitación y Entrenamiento
   - Monitoreo de Cumplimiento
   - Evaluación de Efectividad
   - Mejora Continua
   - Actualización de Estándares

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Aplicación de estándares
   - Criterios de implementación
   - Interpretación profesional
   - Efectos de la implementación

9. RECOMENDACIONES ESTRATÉGICAS:
    - Implementación de estándares
    - Capacitación especializada
    - Monitoreo continuo
    - Mejora de procesos
    - Desarrollo organizacional
    - Liderazgo en estándares

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Estándares IPMA
    - Mejores Prácticas Internacionales
    - Guías de Implementación
    - Casos de Estudio
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a estándares y explicaciones detalladas del procedimiento de implementación de estándares.";
    }

    /**
     * Construir prompt para liderazgo ético en proyectos
     */
    protected function construirPromptLiderazgoEticoProyectos(array $datos, array $jurisprudencia): string
    {
        $tipoLiderazgo = $datos['tipo_liderazgo'] ?? 'general';
        $datosLiderazgo = $datos['datos_liderazgo'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Liderazgo Ético en Proyectos';

        $jurisprudenciaText = $this->formatearJurisprudenciaIPMA($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - LIDERAZGO ÉTICO EN PROYECTOS

TIPO DE LIDERAZGO: {$tipoLiderazgo}
CASO: {$caso}

DATOS DEL LIDERAZGO:
{$datosLiderazgo}

JURISPRUDENCIA IPMA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL LIDERAZGO ÉTICO:
   - Principios de Liderazgo Ético
   - Códigos de Conducta Profesional
   - Estándares de Liderazgo
   - Valores Éticos
   - Responsabilidades de Liderazgo

2. PRINCIPIOS DE LIDERAZGO ÉTICO:
   - Integridad
   - Transparencia
   - Responsabilidad
   - Respeto
   - Equidad
   - Sostenibilidad

3. COMPETENCIAS DE LIDERAZGO ÉTICO:
   - Liderazgo Transformacional
   - Liderazgo Situacional
   - Liderazgo Inclusivo
   - Liderazgo Sostenible
   - Liderazgo Digital
   - Liderazgo Colaborativo

4. GESTIÓN ÉTICA DE EQUIPOS:
   - Construcción de Equipos
   - Desarrollo de Competencias
   - Gestión de Conflictos
   - Comunicación Ética
   - Motivación Ética
   - Evaluación Ética

5. TOMA DE DECISIONES ÉTICAS:
   - Identificación de Dilemas Éticos
   - Análisis de Alternativas
   - Evaluación de Impacto
   - Toma de Decisiones
   - Implementación de Decisiones
   - Seguimiento y Evaluación

6. GESTIÓN DE STAKEHOLDERS ÉTICA:
   - Identificación de Interesados
   - Análisis de Intereses
   - Comunicación Transparente
   - Participación Inclusiva
   - Resolución de Conflictos
   - Rendición de Cuentas

7. SOSTENIBILIDAD Y LIDERAZGO:
   - Impacto Ambiental
   - Responsabilidad Social
   - Desarrollo Sostenible
   - Economía Circular
   - Inclusión Social
   - Equidad Intergeneracional

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Aplicación de liderazgo ético
   - Criterios de evaluación
   - Interpretación profesional
   - Efectos del liderazgo

9. RECOMENDACIONES ESTRATÉGICAS:
    - Desarrollo de liderazgo ético
    - Capacitación especializada
    - Monitoreo continuo
    - Mejora de procesos
    - Desarrollo organizacional
    - Liderazgo transformacional

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Principios de Liderazgo Ético
    - Códigos de Conducta
    - Estándares de Liderazgo
    - Mejores Prácticas
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a liderazgo ético y explicaciones detalladas del procedimiento de liderazgo ético en proyectos.";
    }

    /**
     * Consolidar análisis IPMA
     */
    protected function consolidarAnalisisIPMA(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_ipma',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'jurisprudencia_consultada' => [],
            'referencias_legales' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - IPMA (INTERNATIONAL PROJECT MANAGEMENT ASSOCIATION)\n\n";
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
            'Desarrollar competencias IPMA',
            'Implementar códigos de ética IPMA',
            'Obtener certificación IPMA',
            'Aplicar estándares IPMA',
            'Desarrollar liderazgo ético',
            'Capacitar en gestión de proyectos éticos'
        ];

        // Generar referencias legales
        $consolidado['referencias_legales'] = [
            'Modelo de Competencias IPMA',
            'Código de Ética IPMA',
            'Sistema de Certificación IPMA',
            'Estándares de Gestión de Proyectos IPMA',
            'Guías de Liderazgo Ético',
            'Mejores Prácticas Internacionales',
            'Estándares de Sostenibilidad',
            'Códigos de Conducta Profesional'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis IPMA
     */
    protected function guardarAnalisisIPMA(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis IPMA', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de IPMA
     */
    public function obtenerEstadisticasIPMA(): array
    {
        return [
            'total_analisis_ipma' => AnalisisIA::whereIn('especialidad', [
                'competencias_ipma',
                'codigo_etica_ipma',
                'certificacion_ipma',
                'estandares_ipma',
                'liderazgo_etico_proyectos'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'competencias_ipma',
                'codigo_etica_ipma',
                'certificacion_ipma',
                'estandares_ipma',
                'liderazgo_etico_proyectos'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'competencias_ipma',
                'codigo_etica_ipma',
                'certificacion_ipma',
                'estandares_ipma',
                'liderazgo_etico_proyectos'
            ])->sum('tokens_usados'),
            'apis_publicas_consultadas' => array_keys($this->apisPublicas)
        ];
    }
}
