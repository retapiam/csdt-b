<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para PMI (Project Management Institute)
 * Nivel Post-Doctorado con enfoque ético y códigos de conducta profesional
 */
class IAPMI
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelo;
    protected CircuitBreaker $circuitBreaker;

    // APIs públicas de PMI y gestión de proyectos
    protected $apisPublicas = [
        'pmi' => 'https://www.pmi.org/',
        'pmi_ethics' => 'https://www.pmi.org/about/ethics',
        'pmi_code' => 'https://www.pmi.org/about/ethics/code-of-ethics',
        'pmi_standards' => 'https://www.pmi.org/standards',
        'pmi_certification' => 'https://www.pmi.org/certifications',
        'pmi_pmp' => 'https://www.pmi.org/certifications/project-management-pmp',
        'pmi_agile' => 'https://www.pmi.org/certifications/agile',
        'pmi_risk' => 'https://www.pmi.org/certifications/risk-management',
        'pmi_schedule' => 'https://www.pmi.org/certifications/schedule-management',
        'pmi_governance' => 'https://www.pmi.org/governance'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->modelo = config('services.openai.model', 'gpt-4');
        $this->circuitBreaker = new CircuitBreaker('openai', 5, 60, 3);
    }

    /**
     * Análisis de Código de Ética y Conducta Profesional PMI
     */
    public function analizarCodigoEticaPMI(array $datos): array
    {
        $cacheKey = 'ia_codigo_etica_pmi_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPMI('codigo_etica_pmi');
            $prompt = $this->construirPromptCodigoEticaPMI($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPMI('codigo_etica_pmi', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Estándares PMI
     */
    public function analizarEstandaresPMI(array $datos): array
    {
        $cacheKey = 'ia_estandares_pmi_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPMI('estandares_pmi');
            $prompt = $this->construirPromptEstandaresPMI($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPMI('estandares_pmi', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Certificaciones PMI
     */
    public function analizarCertificacionesPMI(array $datos): array
    {
        $cacheKey = 'ia_certificaciones_pmi_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPMI('certificaciones_pmi');
            $prompt = $this->construirPromptCertificacionesPMI($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPMI('certificaciones_pmi', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Gobernanza PMI
     */
    public function analizarGobernanzaPMI(array $datos): array
    {
        $cacheKey = 'ia_gobernanza_pmi_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPMI('gobernanza_pmi');
            $prompt = $this->construirPromptGobernanzaPMI($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPMI('gobernanza_pmi', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de PMI
     */
    public function analisisIntegralPMI(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'codigo_etica_pmi',
            'estandares_pmi',
            'certificaciones_pmi',
            'gobernanza_pmi'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisPMI($resultados, $datos);
    }

    /**
     * Obtener jurisprudencia PMI de fuentes públicas
     */
    protected function obtenerJurisprudenciaPMI(string $tipo): array
    {
        $cacheKey = "jurisprudencia_pmi_{$tipo}";
        
        return Cache::remember($cacheKey, 3600, function () use ($tipo) {
            $jurisprudencia = [];
            
            try {
                // Consultar PMI
                $response = Http::timeout(30)->get($this->apisPublicas['pmi']);
                if ($response->successful()) {
                    $jurisprudencia['pmi'] = $this->procesarRespuestaPMI($response->body(), $tipo);
                }
                
                // Consultar ética PMI
                $response = Http::timeout(30)->get($this->apisPublicas['pmi_ethics']);
                if ($response->successful()) {
                    $jurisprudencia['pmi_ethics'] = $this->procesarRespuestaEthicsPMI($response->body(), $tipo);
                }
                
                // Consultar código PMI
                $response = Http::timeout(30)->get($this->apisPublicas['pmi_code']);
                if ($response->successful()) {
                    $jurisprudencia['pmi_code'] = $this->procesarRespuestaCodePMI($response->body(), $tipo);
                }
                
                // Consultar estándares PMI
                $response = Http::timeout(30)->get($this->apisPublicas['pmi_standards']);
                if ($response->successful()) {
                    $jurisprudencia['pmi_standards'] = $this->procesarRespuestaStandardsPMI($response->body(), $tipo);
                }
                
            } catch (\Exception $e) {
                Log::error('Error obteniendo jurisprudencia PMI', [
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
            'certificaciones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Certification[^<]*)<\/a>/i',
            'etica' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Ethics[^<]*)<\/a>/i',
            'gobernanza' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Governance[^<]*)<\/a>/i'
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
     * Procesar respuesta de ética PMI
     */
    protected function procesarRespuestaEthicsPMI(string $html, string $tipo): array
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
                        'fuente' => 'PMI Ethics'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de código PMI
     */
    protected function procesarRespuestaCodePMI(string $html, string $tipo): array
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
                        'fuente' => 'PMI Code of Ethics'
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
            'estandares' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Standard[^<]*)<\/a>/i',
            'guias' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Guide[^<]*)<\/a>/i',
            'mejores_practicas' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Practice[^<]*)<\/a>/i',
            'metodologias' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Methodology[^<]*)<\/a>/i'
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
     * Ejecutar análisis PMI
     */
    protected function ejecutarAnalisisPMI(string $tipoAnalisis, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en gestión de proyectos PMI de nivel post-doctorado. " .
                                   "Especializado en códigos de ética PMI, estándares de gestión de proyectos, " .
                                   "certificaciones PMI y gobernanza. Tienes acceso a estándares actualizados de PMI " .
                                   "y organizaciones internacionales de gestión de proyectos. Proporciona análisis " .
                                   "exhaustivos con fundamentación académica sólida, referencias específicas a códigos " .
                                   "de ética y estándares, y explicaciones detalladas del procedimiento de gestión " .
                                   "ética de proyectos con enfoque en códigos de conducta profesional."
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

            $this->guardarAnalisisPMI($tipoAnalisis, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'tipo_analisis' => $tipoAnalisis,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $this->modelo,
                'jurisprudencia_consultada' => $this->obtenerJurisprudenciaPMI($tipoAnalisis),
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis PMI: ' . $response->body());
    }

    /**
     * Construir prompt para código de ética PMI
     */
    protected function construirPromptCodigoEticaPMI(array $datos, array $jurisprudencia): string
    {
        $tipoEtica = $datos['tipo_etica'] ?? 'general';
        $datosEtica = $datos['datos_etica'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Código de Ética PMI';

        $jurisprudenciaText = $this->formatearJurisprudenciaPMI($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - CÓDIGO DE ÉTICA Y CONDUCTA PROFESIONAL PMI

TIPO DE ÉTICA: {$tipoEtica}
CASO: {$caso}

DATOS DE ÉTICA:
{$datosEtica}

JURISPRUDENCIA PMI CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL CÓDIGO DE ÉTICA PMI:
   - Código de Ética y Conducta Profesional PMI
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
    - Código de Ética PMI
    - Estándares internacionales
    - Casos de estudio
    - Investigaciones académicas
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a códigos de ética y explicaciones detalladas del procedimiento de gestión ética de proyectos.";
    }

    /**
     * Formatear jurisprudencia PMI para el prompt
     */
    protected function formatearJurisprudenciaPMI(array $jurisprudencia): string
    {
        $texto = "JURISPRUDENCIA PMI CONSULTADA:\n\n";
        
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
     * Construir prompt para estándares PMI
     */
    protected function construirPromptEstandaresPMI(array $datos, array $jurisprudencia): string
    {
        $tipoEstandar = $datos['tipo_estandar'] ?? 'general';
        $datosEstandar = $datos['datos_estandar'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Estándares PMI';

        $jurisprudenciaText = $this->formatearJurisprudenciaPMI($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - ESTÁNDARES PMI

TIPO DE ESTÁNDAR: {$tipoEstandar}
CASO: {$caso}

DATOS DEL ESTÁNDAR:
{$datosEstandar}

JURISPRUDENCIA PMI CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE ESTÁNDARES PMI:
   - Estándares de Gestión de Proyectos PMI
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

3. ESTÁNDARES DE PROCESO:
   - Procesos de Iniciación
   - Procesos de Planificación
   - Procesos de Ejecución
   - Procesos de Monitoreo
   - Procesos de Cierre
   - Procesos de Mejora

4. ESTÁNDARES DE CALIDAD:
   - Criterios de Calidad
   - Métricas de Calidad
   - Evaluación de Calidad
   - Mejora de Calidad
   - Certificación de Calidad
   - Aseguramiento de Calidad

5. ESTÁNDARES DE SOSTENIBILIDAD:
   - Gestión Ambiental
   - Responsabilidad Social
   - Desarrollo Sostenible
   - Economía Circular
   - Inclusión Social
   - Equidad Intergeneracional

6. IMPLEMENTACIÓN DE ESTÁNDARES:
   - Planificación de Implementación
   - Capacitación y Entrenamiento
   - Monitoreo de Cumplimiento
   - Evaluación de Efectividad
   - Mejora Continua
   - Actualización de Estándares

7. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Aplicación de estándares
   - Criterios de implementación
   - Interpretación profesional
   - Efectos de la implementación

8. RECOMENDACIONES ESTRATÉGICAS:
    - Implementación de estándares
    - Capacitación especializada
    - Monitoreo continuo
    - Mejora de procesos
    - Desarrollo organizacional
    - Liderazgo en estándares

9. BIBLIOGRAFÍA Y REFERENCIAS:
    - Estándares PMI
    - Mejores Prácticas Internacionales
    - Guías de Implementación
    - Casos de Estudio
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a estándares y explicaciones detalladas del procedimiento de implementación de estándares.";
    }

    /**
     * Construir prompt para certificaciones PMI
     */
    protected function construirPromptCertificacionesPMI(array $datos, array $jurisprudencia): string
    {
        $tipoCertificacion = $datos['tipo_certificacion'] ?? 'general';
        $datosCertificacion = $datos['datos_certificacion'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Certificaciones PMI';

        $jurisprudenciaText = $this->formatearJurisprudenciaPMI($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - CERTIFICACIONES PMI

TIPO DE CERTIFICACIÓN: {$tipoCertificacion}
CASO: {$caso}

DATOS DE LA CERTIFICACIÓN:
{$datosCertificacion}

JURISPRUDENCIA PMI CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE CERTIFICACIONES PMI:
   - Sistema de Certificación PMI
   - Tipos de Certificación
   - Requisitos de Certificación
   - Procesos de Evaluación
   - Estándares de Competencia

2. TIPOS DE CERTIFICACIÓN:
   - PMP (Project Management Professional)
   - CAPM (Certified Associate in Project Management)
   - PgMP (Program Management Professional)
   - PfMP (Portfolio Management Professional)
   - PMI-ACP (PMI Agile Certified Practitioner)
   - PMI-RMP (PMI Risk Management Professional)
   - PMI-SP (PMI Scheduling Professional)

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
   - Examen de Certificación
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
    - Sistema de Certificación PMI
    - Estándares de Competencia
    - Guías de Evaluación
    - Mejores Prácticas
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a certificaciones y explicaciones detalladas del procedimiento de certificación profesional.";
    }

    /**
     * Construir prompt para gobernanza PMI
     */
    protected function construirPromptGobernanzaPMI(array $datos, array $jurisprudencia): string
    {
        $tipoGobernanza = $datos['tipo_gobernanza'] ?? 'general';
        $datosGobernanza = $datos['datos_gobernanza'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Gobernanza PMI';

        $jurisprudenciaText = $this->formatearJurisprudenciaPMI($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - GOBERNANZA PMI

TIPO DE GOBERNANZA: {$tipoGobernanza}
CASO: {$caso}

DATOS DE LA GOBERNANZA:
{$datosGobernanza}

JURISPRUDENCIA PMI CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE GOBERNANZA PMI:
   - Estructura de Gobernanza PMI
   - Políticas de Gobernanza
   - Procedimientos de Gobernanza
   - Estándares de Gobernanza
   - Mejores Prácticas de Gobernanza

2. ESTRUCTURA DE GOBERNANZA:
   - Junta Directiva
   - Comités de Gobernanza
   - Órganos de Control
   - Órganos de Supervisión
   - Órganos de Evaluación
   - Órganos de Mejora

3. POLÍTICAS DE GOBERNANZA:
   - Políticas de Transparencia
   - Políticas de Rendición de Cuentas
   - Políticas de Participación
   - Políticas de Inclusión
   - Políticas de Sostenibilidad
   - Políticas de Innovación

4. PROCEDIMIENTOS DE GOBERNANZA:
   - Procedimientos de Toma de Decisiones
   - Procedimientos de Evaluación
   - Procedimientos de Control
   - Procedimientos de Supervisión
   - Procedimientos de Mejora
   - Procedimientos de Transparencia

5. ESTÁNDARES DE GOBERNANZA:
   - Estándares de Transparencia
   - Estándares de Rendición de Cuentas
   - Estándares de Participación
   - Estándares de Inclusión
   - Estándares de Sostenibilidad
   - Estándares de Innovación

6. MEJORES PRÁCTICAS DE GOBERNANZA:
   - Gobernanza Transparente
   - Gobernanza Participativa
   - Gobernanza Inclusiva
   - Gobernanza Sostenible
   - Gobernanza Innovadora
   - Gobernanza Efectiva

7. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Aplicación de gobernanza
   - Criterios de implementación
   - Interpretación profesional
   - Efectos de la gobernanza

8. RECOMENDACIONES ESTRATÉGICAS:
    - Implementación de gobernanza
    - Capacitación especializada
    - Monitoreo continuo
    - Mejora de procesos
    - Desarrollo organizacional
    - Liderazgo en gobernanza

9. BIBLIOGRAFÍA Y REFERENCIAS:
    - Estándares de Gobernanza PMI
    - Mejores Prácticas de Gobernanza
    - Guías de Implementación
    - Casos de Estudio
    - Fuentes especializadas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a gobernanza y explicaciones detalladas del procedimiento de implementación de gobernanza.";
    }

    /**
     * Consolidar análisis PMI
     */
    protected function consolidarAnalisisPMI(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_pmi',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'jurisprudencia_consultada' => [],
            'referencias_legales' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - PMI (PROJECT MANAGEMENT INSTITUTE)\n\n";
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
            'Aplicar estándares PMI',
            'Obtener certificaciones PMI',
            'Implementar gobernanza PMI',
            'Desarrollar competencias PMI',
            'Capacitar en gestión de proyectos éticos'
        ];

        // Generar referencias legales
        $consolidado['referencias_legales'] = [
            'Código de Ética PMI',
            'Estándares de Gestión de Proyectos PMI',
            'Sistema de Certificación PMI',
            'Guías de Gobernanza PMI',
            'Mejores Prácticas PMI',
            'Estándares de Sostenibilidad',
            'Códigos de Conducta Profesional',
            'Estándares Internacionales'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis PMI
     */
    protected function guardarAnalisisPMI(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis PMI', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de PMI
     */
    public function obtenerEstadisticasPMI(): array
    {
        return [
            'total_analisis_pmi' => AnalisisIA::whereIn('especialidad', [
                'codigo_etica_pmi',
                'estandares_pmi',
                'certificaciones_pmi',
                'gobernanza_pmi'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'codigo_etica_pmi',
                'estandares_pmi',
                'certificaciones_pmi',
                'gobernanza_pmi'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'codigo_etica_pmi',
                'estandares_pmi',
                'certificaciones_pmi',
                'gobernanza_pmi'
            ])->sum('tokens_usados'),
            'apis_publicas_consultadas' => array_keys($this->apisPublicas)
        ];
    }
}
