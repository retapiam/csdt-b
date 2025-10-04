<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para PMP (Project Management Professional)
 * Nivel Post-Doctorado con enfoque ético y gestión de proyectos
 */
class IAPMP
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelo;
    protected CircuitBreaker $circuitBreaker;

    // APIs públicas de gestión de proyectos y ética
    protected $apisPublicas = [
        'pmi' => 'https://www.pmi.org/',
        'ipma' => 'https://www.ipma.world/',
        'pmp_guide' => 'https://www.pmi.org/pmbok-guide-standards',
        'ethics_pmi' => 'https://www.pmi.org/about/ethics',
        'standards_pmi' => 'https://www.pmi.org/standards',
        'knowledge_pmi' => 'https://www.pmi.org/learning',
        'certification_pmi' => 'https://www.pmi.org/certifications',
        'research_pmi' => 'https://www.pmi.org/learning/thought-leadership',
        'global_standards' => 'https://www.iso.org/',
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
     * Análisis de Código de Ética PMI
     */
    public function analizarCodigoEticaPMI(array $datos): array
    {
        $cacheKey = 'ia_codigo_etica_pmi_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPMP('codigo_etica_pmi');
            $prompt = $this->construirPromptCodigoEticaPMI($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPMP('codigo_etica_pmi', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Estándares PMBOK
     */
    public function analizarEstandaresPMBOK(array $datos): array
    {
        $cacheKey = 'ia_estandares_pmbok_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPMP('estandares_pmbok');
            $prompt = $this->construirPromptEstandaresPMBOK($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPMP('estandares_pmbok', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Gestión de Proyectos Éticos
     */
    public function analizarGestionProyectosEticos(array $datos): array
    {
        $cacheKey = 'ia_gestion_proyectos_eticos_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPMP('gestion_proyectos_eticos');
            $prompt = $this->construirPromptGestionProyectosEticos($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPMP('gestion_proyectos_eticos', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Formulación de Proyectos
     */
    public function analizarFormulacionProyectos(array $datos): array
    {
        $cacheKey = 'ia_formulacion_proyectos_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPMP('formulacion_proyectos');
            $prompt = $this->construirPromptFormulacionProyectos($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPMP('formulacion_proyectos', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Manuales de Conducta
     */
    public function analizarManualesConducta(array $datos): array
    {
        $cacheKey = 'ia_manuales_conducta_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPMP('manuales_conducta');
            $prompt = $this->construirPromptManualesConducta($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPMP('manuales_conducta', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de PMP
     */
    public function analisisIntegralPMP(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'codigo_etica_pmi',
            'estandares_pmbok',
            'gestion_proyectos_eticos',
            'formulacion_proyectos',
            'manuales_conducta'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisPMP($resultados, $datos);
    }

    /**
     * Obtener jurisprudencia PMP de fuentes públicas
     */
    protected function obtenerJurisprudenciaPMP(string $tipo): array
    {
        $cacheKey = "jurisprudencia_pmp_{$tipo}";
        
        return Cache::remember($cacheKey, 3600, function () use ($tipo) {
            $jurisprudencia = [];
            
            try {
                // Consultar PMI
                $response = Http::timeout(30)->get($this->apisPublicas['pmi']);
                if ($response->successful()) {
                    $jurisprudencia['pmi'] = $this->procesarRespuestaPMI($response->body(), $tipo);
                }
                
                // Consultar IPMA
                $response = Http::timeout(30)->get($this->apisPublicas['ipma']);
                if ($response->successful()) {
                    $jurisprudencia['ipma'] = $this->procesarRespuestaIPMA($response->body(), $tipo);
                }
                
                // Consultar estándares PMI
                $response = Http::timeout(30)->get($this->apisPublicas['standards_pmi']);
                if ($response->successful()) {
                    $jurisprudencia['standards_pmi'] = $this->procesarRespuestaStandardsPMI($response->body(), $tipo);
                }
                
                // Consultar ética PMI
                $response = Http::timeout(30)->get($this->apisPublicas['ethics_pmi']);
                if ($response->successful()) {
                    $jurisprudencia['ethics_pmi'] = $this->procesarRespuestaEthicsPMI($response->body(), $tipo);
                }
                
            } catch (\Exception $e) {
                Log::error('Error obteniendo jurisprudencia PMP', [
                    'tipo' => $tipo,
                    'error' => $e->getMessage()
                ]);
            }
            
            return $jurisprudencia;
        });
    }

    /**
     * Procesar respuesta de PMI
     */
    protected function procesarRespuestaPMI(string $html, string $tipo): array
    {
        $patrones = [
            'estandares' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Standard[^<]*)<\/a>/i',
            'guias' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Guide[^<]*)<\/a>/i',
            'certificaciones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Certification[^<]*)<\/a>/i',
            'etica' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Ethics[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'Project Management Institute (PMI)'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de IPMA
     */
    protected function procesarRespuestaIPMA(string $html, string $tipo): array
    {
        $patrones = [
            'estandares' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Standard[^<]*)<\/a>/i',
            'competencia' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Competence[^<]*)<\/a>/i',
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
     * Procesar respuesta de estándares PMI
     */
    protected function procesarRespuestaStandardsPMI(string $html, string $tipo): array
    {
        $patrones = [
            'pmbok' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*PMBOK[^<]*)<\/a>/i',
            'agile' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Agile[^<]*)<\/a>/i',
            'practices' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Practice[^<]*)<\/a>/i',
            'methodology' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Methodology[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'PMI Standards'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de ética PMI
     */
    protected function procesarRespuestaEthicsPMI(string $html, string $tipo): array
    {
        $patrones = [
            'codigo_etica' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Code[^<]*)<\/a>/i',
            'conducta' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Conduct[^<]*)<\/a>/i',
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
                        'fuente' => 'PMI Ethics'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Ejecutar análisis PMP
     */
    protected function ejecutarAnalisisPMP(string $tipoAnalisis, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en gestión de proyectos PMP de nivel post-doctorado. " .
                                   "Especializado en códigos de ética PMI, estándares PMBOK, gestión ética de proyectos, " .
                                   "formulación de proyectos y manuales de conducta organizacional. Tienes acceso a " .
                                   "estándares actualizados de PMI, IPMA y organizaciones internacionales de gestión " .
                                   "de proyectos. Proporciona análisis exhaustivos con fundamentación académica sólida, " .
                                   "referencias específicas a estándares y códigos de ética, y explicaciones detalladas " .
                                   "del procedimiento de gestión de proyectos éticos."
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

            $this->guardarAnalisisPMP($tipoAnalisis, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'tipo_analisis' => $tipoAnalisis,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $this->modelo,
                'jurisprudencia_consultada' => $this->obtenerJurisprudenciaPMP($tipoAnalisis),
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis PMP: ' . $response->body());
    }

    /**
     * Construir prompt para código de ética PMI
     */
    protected function construirPromptCodigoEticaPMI(array $datos, array $jurisprudencia): string
    {
        $tipoEtica = $datos['tipo_etica'] ?? 'general';
        $datosEtica = $datos['datos_etica'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Código de Ética PMI';

        $jurisprudenciaText = $this->formatearJurisprudenciaPMP($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - CÓDIGO DE ÉTICA PMI

TIPO DE ÉTICA: {$tipoEtica}
CASO: {$caso}

DATOS DE ÉTICA:
{$datosEtica}

JURISPRUDENCIA PMP CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL CÓDIGO DE ÉTICA PMI:
   - Código de Ética y Conducta Profesional PMI
   - Estándares de Ética en Gestión de Proyectos
   - Principios Éticos Fundamentales
   - Valores Profesionales
   - Responsabilidades Profesionales

2. PRINCIPIOS ÉTICOS FUNDAMENTALES:
   - Responsabilidad
   - Respeto
   - Equidad
   - Honestidad
   - Integridad
   - Transparencia

3. VALORES PROFESIONALES:
   - Compromiso con la excelencia
   - Respeto por la diversidad
   - Integridad personal
   - Responsabilidad social
   - Liderazgo ético
   - Innovación responsable

4. RESPONSABILIDADES PROFESIONALES:
   - Hacia la profesión
   - Hacia los clientes
   - Hacia los equipos
   - Hacia la sociedad
   - Hacia el medio ambiente
   - Hacia las generaciones futuras

5. CÓDIGO DE CONDUCTA:
   - Estándares de comportamiento
   - Prohibiciones éticas
   - Conflictos de interés
   - Confidencialidad
   - Uso de información
   - Relaciones profesionales

6. GESTIÓN DE CONFLICTOS ÉTICOS:
   - Identificación de conflictos
   - Evaluación de alternativas
   - Toma de decisiones éticas
   - Resolución de conflictos
   - Medidas correctivas
   - Prevención de conflictos

7. APLICACIÓN EN GESTIÓN DE PROYECTOS:
   - Planificación ética
   - Ejecución responsable
   - Monitoreo de cumplimiento
   - Evaluación de resultados
   - Mejora continua
   - Rendición de cuentas

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
    - Código de Ética PMI
    - Estándares internacionales
    - Casos de estudio
    - Investigaciones académicas
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a códigos de ética y explicaciones detalladas del procedimiento de gestión ética de proyectos.";
    }

    /**
     * Formatear jurisprudencia PMP para el prompt
     */
    protected function formatearJurisprudenciaPMP(array $jurisprudencia): string
    {
        $texto = "JURISPRUDENCIA PMP CONSULTADA:\n\n";
        
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
     * Construir prompt para estándares PMBOK
     */
    protected function construirPromptEstandaresPMBOK(array $datos, array $jurisprudencia): string
    {
        $tipoEstandar = $datos['tipo_estandar'] ?? 'general';
        $datosEstandar = $datos['datos_estandar'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Estándares PMBOK';

        $jurisprudenciaText = $this->formatearJurisprudenciaPMP($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - ESTÁNDARES PMBOK

TIPO DE ESTÁNDAR: {$tipoEstandar}
CASO: {$caso}

DATOS DEL ESTÁNDAR:
{$datosEstandar}

JURISPRUDENCIA PMP CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE ESTÁNDARES PMBOK:
   - Guía del PMBOK (Project Management Body of Knowledge)
   - Estándares de Gestión de Proyectos
   - Mejores Prácticas Internacionales
   - Metodologías Ágiles
   - Estándares de Calidad

2. ÁREAS DE CONOCIMIENTO:
   - Gestión de la Integración
   - Gestión del Alcance
   - Gestión del Tiempo
   - Gestión del Costo
   - Gestión de la Calidad
   - Gestión de los Recursos Humanos
   - Gestión de las Comunicaciones
   - Gestión de los Riesgos
   - Gestión de las Adquisiciones
   - Gestión de los Interesados

3. GRUPOS DE PROCESOS:
   - Iniciación
   - Planificación
   - Ejecución
   - Monitoreo y Control
   - Cierre

4. PROCESOS DE GESTIÓN:
   - Procesos de Iniciación
   - Procesos de Planificación
   - Procesos de Ejecución
   - Procesos de Monitoreo y Control
   - Procesos de Cierre

5. HERRAMIENTAS Y TÉCNICAS:
   - Herramientas de Planificación
   - Herramientas de Ejecución
   - Herramientas de Control
   - Herramientas de Comunicación
   - Herramientas de Gestión de Riesgos

6. ENTREGABLES:
   - Documentos de Proyecto
   - Planes de Gestión
   - Informes de Progreso
   - Documentos de Cierre
   - Lecciones Aprendidas

7. MÉTRICAS Y KPIs:
   - Indicadores de Rendimiento
   - Métricas de Calidad
   - Métricas de Tiempo
   - Métricas de Costo
   - Métricas de Satisfacción

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Aplicación de estándares
   - Criterios de implementación
   - Interpretación profesional
   - Efectos de la aplicación

9. RECOMENDACIONES ESTRATÉGICAS:
    - Implementación de estándares
    - Capacitación profesional
    - Mejora continua
    - Adaptación organizacional
    - Certificación profesional
    - Liderazgo en gestión

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Guía del PMBOK
    - Estándares PMI
    - Metodologías ágiles
    - Mejores prácticas
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a estándares y explicaciones detalladas del procedimiento de gestión de proyectos.";
    }

    /**
     * Construir prompt para gestión de proyectos éticos
     */
    protected function construirPromptGestionProyectosEticos(array $datos, array $jurisprudencia): string
    {
        $tipoGestion = $datos['tipo_gestion'] ?? 'general';
        $datosGestion = $datos['datos_gestion'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Gestión de Proyectos Éticos';

        $jurisprudenciaText = $this->formatearJurisprudenciaPMP($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - GESTIÓN DE PROYECTOS ÉTICOS

TIPO DE GESTIÓN: {$tipoGestion}
CASO: {$caso}

DATOS DE LA GESTIÓN:
{$datosGestion}

JURISPRUDENCIA PMP CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE GESTIÓN ÉTICA:
   - Principios Éticos en Gestión de Proyectos
   - Códigos de Conducta Profesional
   - Estándares de Responsabilidad Social
   - Normas de Transparencia
   - Regulaciones de Cumplimiento

2. PRINCIPIOS ÉTICOS FUNDAMENTALES:
   - Integridad
   - Transparencia
   - Responsabilidad
   - Respeto
   - Equidad
   - Sostenibilidad

3. GESTIÓN ÉTICA DEL PROYECTO:
   - Planificación Ética
   - Ejecución Responsable
   - Monitoreo de Cumplimiento
   - Evaluación de Impacto
   - Rendición de Cuentas
   - Mejora Continua

4. STAKEHOLDERS Y ÉTICA:
   - Identificación de Interesados
   - Gestión de Expectativas
   - Comunicación Transparente
   - Resolución de Conflictos
   - Participación Inclusiva
   - Responsabilidad Social

5. RIESGOS ÉTICOS:
   - Identificación de Riesgos Éticos
   - Evaluación de Impacto
   - Medidas de Mitigación
   - Planes de Contingencia
   - Monitoreo Continuo
   - Respuesta a Incidentes

6. COMPLIANCE Y CUMPLIMIENTO:
   - Normativas Aplicables
   - Estándares de Calidad
   - Regulaciones Ambientales
   - Normas de Seguridad
   - Requisitos Legales
   - Auditorías Éticas

7. SOSTENIBILIDAD:
   - Impacto Ambiental
   - Responsabilidad Social
   - Desarrollo Sostenible
   - Economía Circular
   - Inclusión Social
   - Equidad Intergeneracional

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones éticas
   - Criterios de aplicación
   - Interpretación profesional
   - Efectos de las decisiones

9. RECOMENDACIONES ESTRATÉGICAS:
    - Implementación de gestión ética
    - Capacitación en ética
    - Monitoreo continuo
    - Mejora de procesos
    - Desarrollo profesional
    - Liderazgo ético

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Códigos de ética profesionales
    - Estándares de sostenibilidad
    - Guías de responsabilidad social
    - Mejores prácticas éticas
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a principios éticos y explicaciones detalladas del procedimiento de gestión ética de proyectos.";
    }

    /**
     * Construir prompt para formulación de proyectos
     */
    protected function construirPromptFormulacionProyectos(array $datos, array $jurisprudencia): string
    {
        $tipoFormulacion = $datos['tipo_formulacion'] ?? 'general';
        $datosFormulacion = $datos['datos_formulacion'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Formulación de Proyectos';

        $jurisprudenciaText = $this->formatearJurisprudenciaPMP($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - FORMULACIÓN DE PROYECTOS

TIPO DE FORMULACIÓN: {$tipoFormulacion}
CASO: {$caso}

DATOS DE LA FORMULACIÓN:
{$datosFormulacion}

JURISPRUDENCIA PMP CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE FORMULACIÓN:
   - Metodologías de Formulación
   - Estándares de Calidad
   - Guías de Mejores Prácticas
   - Normativas Sectoriales
   - Requisitos de Cumplimiento

2. FASES DE FORMULACIÓN:
   - Identificación del Problema
   - Análisis de Viabilidad
   - Diseño del Proyecto
   - Planificación Detallada
   - Validación y Aprobación
   - Inicio de Ejecución

3. COMPONENTES DEL PROYECTO:
   - Objetivos y Metas
   - Alcance y Límites
   - Cronograma y Recursos
   - Presupuesto y Financiación
   - Riesgos y Mitigaciones
   - Indicadores y Métricas

4. ANÁLISIS DE VIABILIDAD:
   - Viabilidad Técnica
   - Viabilidad Económica
   - Viabilidad Financiera
   - Viabilidad Ambiental
   - Viabilidad Social
   - Viabilidad Legal

5. GESTIÓN DE STAKEHOLDERS:
   - Identificación de Interesados
   - Análisis de Influencia
   - Estrategias de Participación
   - Comunicación y Consulta
   - Gestión de Expectativas
   - Resolución de Conflictos

6. PLANIFICACIÓN DETALLADA:
   - Estructura de Desglose del Trabajo
   - Cronograma de Actividades
   - Asignación de Recursos
   - Presupuesto Detallado
   - Plan de Gestión de Riesgos
   - Plan de Comunicaciones

7. INDICADORES Y MÉTRICAS:
   - Indicadores de Resultado
   - Indicadores de Impacto
   - Métricas de Rendimiento
   - Indicadores de Calidad
   - Métricas de Satisfacción
   - Indicadores de Sostenibilidad

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Aplicación de metodologías
   - Criterios de formulación
   - Interpretación profesional
   - Efectos de la formulación

9. RECOMENDACIONES ESTRATÉGICAS:
    - Mejora de la formulación
    - Capacitación especializada
    - Herramientas de apoyo
    - Procesos de validación
    - Desarrollo profesional
    - Liderazgo en formulación

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Metodologías de formulación
    - Guías de mejores prácticas
    - Estándares de calidad
    - Casos de estudio
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a metodologías y explicaciones detalladas del procedimiento de formulación de proyectos.";
    }

    /**
     * Construir prompt para manuales de conducta
     */
    protected function construirPromptManualesConducta(array $datos, array $jurisprudencia): string
    {
        $tipoManual = $datos['tipo_manual'] ?? 'general';
        $datosManual = $datos['datos_manual'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Manuales de Conducta';

        $jurisprudenciaText = $this->formatearJurisprudenciaPMP($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - MANUALES DE CONDUCTA

TIPO DE MANUAL: {$tipoManual}
CASO: {$caso}

DATOS DEL MANUAL:
{$datosManual}

JURISPRUDENCIA PMP CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE MANUALES DE CONDUCTA:
   - Códigos de Ética Organizacional
   - Políticas de Conducta
   - Estándares de Comportamiento
   - Normas de Integridad
   - Regulaciones de Cumplimiento

2. COMPONENTES DEL MANUAL:
   - Declaración de Valores
   - Principios Éticos
   - Estándares de Conducta
   - Políticas Específicas
   - Procedimientos de Cumplimiento
   - Mecanismos de Denuncia

3. PRINCIPIOS ÉTICOS FUNDAMENTALES:
   - Integridad
   - Honestidad
   - Transparencia
   - Respeto
   - Equidad
   - Responsabilidad

4. ÁREAS DE CONDUCTA:
   - Relaciones Laborales
   - Conflictos de Interés
   - Confidencialidad
   - Uso de Recursos
   - Comunicaciones
   - Relaciones Externas

5. POLÍTICAS ESPECÍFICAS:
   - Política de Diversidad
   - Política de Inclusión
   - Política de Sostenibilidad
   - Política de Seguridad
   - Política de Calidad
   - Política de Innovación

6. PROCEDIMIENTOS DE CUMPLIMIENTO:
   - Identificación de Violaciones
   - Investigación de Casos
   - Medidas Correctivas
   - Sanciones Disciplinarias
   - Apelaciones
   - Seguimiento y Monitoreo

7. MECANISMOS DE DENUNCIA:
   - Canales de Denuncia
   - Protección del Denunciante
   - Investigación Confidencial
   - Resolución de Casos
   - Seguimiento de Acciones
   - Mejora Continua

8. IMPLEMENTACIÓN:
   - Plan de Comunicación
   - Capacitación y Entrenamiento
   - Monitoreo de Cumplimiento
   - Evaluación de Efectividad
   - Actualización Periódica
   - Mejora Continua

9. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Aplicación de políticas
   - Criterios de interpretación
   - Resolución de conflictos
   - Efectos de la implementación

10. RECOMENDACIONES ESTRATÉGICAS:
    - Desarrollo de manuales
    - Implementación efectiva
    - Capacitación especializada
    - Monitoreo continuo
    - Mejora de procesos
    - Liderazgo ético

11. BIBLIOGRAFÍA Y REFERENCIAS:
    - Códigos de ética profesionales
    - Estándares de conducta
    - Mejores prácticas organizacionales
    - Guías de implementación
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a códigos de ética y explicaciones detalladas del procedimiento de desarrollo e implementación de manuales de conducta.";
    }

    /**
     * Consolidar análisis PMP
     */
    protected function consolidarAnalisisPMP(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_pmp',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'jurisprudencia_consultada' => [],
            'referencias_legales' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - PMP (PROJECT MANAGEMENT PROFESSIONAL)\n\n";
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
            'Implementar códigos de ética PMI',
            'Aplicar estándares PMBOK',
            'Desarrollar gestión ética de proyectos',
            'Mejorar formulación de proyectos',
            'Crear manuales de conducta organizacional',
            'Capacitar en gestión de proyectos éticos'
        ];

        // Generar referencias legales
        $consolidado['referencias_legales'] = [
            'Código de Ética y Conducta Profesional PMI',
            'Guía del PMBOK (Project Management Body of Knowledge)',
            'Estándares de Gestión de Proyectos PMI',
            'Metodologías Ágiles',
            'Estándares de Calidad ISO',
            'Mejores Prácticas Internacionales',
            'Códigos de Ética Profesional',
            'Estándares de Responsabilidad Social'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis PMP
     */
    protected function guardarAnalisisPMP(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis PMP', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de PMP
     */
    public function obtenerEstadisticasPMP(): array
    {
        return [
            'total_analisis_pmp' => AnalisisIA::whereIn('especialidad', [
                'codigo_etica_pmi',
                'estandares_pmbok',
                'gestion_proyectos_eticos',
                'formulacion_proyectos',
                'manuales_conducta'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'codigo_etica_pmi',
                'estandares_pmbok',
                'gestion_proyectos_eticos',
                'formulacion_proyectos',
                'manuales_conducta'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'codigo_etica_pmi',
                'estandares_pmbok',
                'gestion_proyectos_eticos',
                'formulacion_proyectos',
                'manuales_conducta'
            ])->sum('tokens_usados'),
            'apis_publicas_consultadas' => array_keys($this->apisPublicas)
        ];
    }
}
