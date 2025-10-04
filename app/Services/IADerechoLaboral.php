<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para Derecho Laboral
 * Nivel Post-Doctorado con acceso a bases de datos laborales y jurisprudencia
 */
class IADerechoLaboral
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelo;
    protected CircuitBreaker $circuitBreaker;

    // APIs públicas laborales de Colombia
    protected $apisPublicas = [
        'min_trabajo' => 'https://www.mintrabajo.gov.co/',
        'corte_constitucional' => 'https://www.corteconstitucional.gov.co/relatoria/',
        'corte_suprema' => 'https://www.cortesuprema.gov.co/',
        'consejo_estado' => 'https://www.consejodeestado.gov.co/',
        'oit' => 'https://www.ilo.org/',
        'superintendencia_subsidio_familiar' => 'https://www.supersubsidio.gov.co/',
        'caja_compensacion' => 'https://www.ccf.org.co/',
        'pensiones' => 'https://www.colpensiones.gov.co/',
        'salud' => 'https://www.minsalud.gov.co/',
        'riesgos_laborales' => 'https://www.arlsura.com/'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->modelo = config('services.openai.model', 'gpt-4');
        $this->circuitBreaker = new CircuitBreaker('openai', 5, 60, 3);
    }

    /**
     * Análisis de Contrato de Trabajo
     */
    public function analizarContratoTrabajo(array $datos): array
    {
        $cacheKey = 'ia_contrato_trabajo_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaLaboral('contrato_trabajo');
            $prompt = $this->construirPromptContratoTrabajo($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisLaboral('contrato_trabajo', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Derechos Laborales
     */
    public function analizarDerechosLaborales(array $datos): array
    {
        $cacheKey = 'ia_derechos_laborales_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaLaboral('derechos_laborales');
            $prompt = $this->construirPromptDerechosLaborales($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisLaboral('derechos_laborales', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Seguridad Social
     */
    public function analizarSeguridadSocial(array $datos): array
    {
        $cacheKey = 'ia_seguridad_social_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaLaboral('seguridad_social');
            $prompt = $this->construirPromptSeguridadSocial($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisLaboral('seguridad_social', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Procedimiento Laboral
     */
    public function analizarProcedimientoLaboral(array $datos): array
    {
        $cacheKey = 'ia_procedimiento_laboral_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaLaboral('procedimiento_laboral');
            $prompt = $this->construirPromptProcedimientoLaboral($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisLaboral('procedimiento_laboral', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Contencioso Laboral
     */
    public function analizarContenciosoLaboral(array $datos): array
    {
        $cacheKey = 'ia_contencioso_laboral_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaLaboral('contencioso_laboral');
            $prompt = $this->construirPromptContenciosoLaboral($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisLaboral('contencioso_laboral', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de derecho laboral
     */
    public function analisisIntegralDerechoLaboral(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'contrato_trabajo',
            'derechos_laborales',
            'seguridad_social',
            'procedimiento_laboral',
            'contencioso_laboral'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisLaboral($resultados, $datos);
    }

    /**
     * Obtener jurisprudencia laboral de fuentes públicas
     */
    protected function obtenerJurisprudenciaLaboral(string $tipo): array
    {
        $cacheKey = "jurisprudencia_laboral_{$tipo}";
        
        return Cache::remember($cacheKey, 3600, function () use ($tipo) {
            $jurisprudencia = [];
            
            try {
                // Consultar Ministerio del Trabajo
                $response = Http::timeout(30)->get($this->apisPublicas['min_trabajo']);
                if ($response->successful()) {
                    $jurisprudencia['min_trabajo'] = $this->procesarRespuestaMinTrabajo($response->body(), $tipo);
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
                
                // Consultar OIT
                $response = Http::timeout(30)->get($this->apisPublicas['oit']);
                if ($response->successful()) {
                    $jurisprudencia['oit'] = $this->procesarRespuestaOIT($response->body(), $tipo);
                }
                
            } catch (\Exception $e) {
                Log::error('Error obteniendo jurisprudencia laboral', [
                    'tipo' => $tipo,
                    'error' => $e->getMessage()
                ]);
            }
            
            return $jurisprudencia;
        });
    }

    /**
     * Procesar respuesta del Ministerio del Trabajo
     */
    protected function procesarRespuestaMinTrabajo(string $html, string $tipo): array
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
                        'fuente' => 'Ministerio del Trabajo'
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
     * Procesar respuesta de OIT
     */
    protected function procesarRespuestaOIT(string $html, string $tipo): array
    {
        $patrones = [
            'convenios' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Convenio[^<]*)<\/a>/i',
            'recomendaciones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Recomendación[^<]*)<\/a>/i',
            'informes' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Informe[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'Organización Internacional del Trabajo'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Ejecutar análisis laboral
     */
    protected function ejecutarAnalisisLaboral(string $tipoAnalisis, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en derecho laboral de nivel post-doctorado. " .
                                   "Especializado en contrato de trabajo, derechos laborales, seguridad social, " .
                                   "procedimiento laboral y contencioso laboral. Tienes acceso a jurisprudencia " .
                                   "actualizada del Ministerio del Trabajo, Corte Constitucional, Corte Suprema " .
                                   "y OIT. Proporciona análisis exhaustivos con fundamentación académica sólida, " .
                                   "referencias específicas a jurisprudencia laboral y explicaciones detalladas " .
                                   "del procedimiento legal laboral."
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

            $this->guardarAnalisisLaboral($tipoAnalisis, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'tipo_analisis' => $tipoAnalisis,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $this->modelo,
                'jurisprudencia_consultada' => $this->obtenerJurisprudenciaLaboral($tipoAnalisis),
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis laboral: ' . $response->body());
    }

    /**
     * Construir prompt para contrato de trabajo
     */
    protected function construirPromptContratoTrabajo(array $datos, array $jurisprudencia): string
    {
        $tipoContrato = $datos['tipo_contrato'] ?? 'general';
        $datosContrato = $datos['datos_contrato'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Contrato de Trabajo';

        $jurisprudenciaText = $this->formatearJurisprudenciaLaboral($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - CONTRATO DE TRABAJO

TIPO DE CONTRATO: {$tipoContrato}
CASO: {$caso}

DATOS DEL CONTRATO:
{$datosContrato}

JURISPRUDENCIA LABORAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL CONTRATO DE TRABAJO:
   - Código Sustantivo del Trabajo (Ley 1 de 1950)
   - Constitución Política (Art. 25, 26, 53, 54, 55, 56)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema
   - Convenios de la OIT

2. CONCEPTO Y NATURALEZA:
   - Concepto de contrato de trabajo
   - Características del contrato
   - Elementos del contrato
   - Clasificación de contratos
   - Modalidades de contratación
   - Contratos especiales

3. SUJETOS DEL CONTRATO:
   - Trabajador
   - Empleador
   - Intermediario
   - Contratista
   - Subcontratista
   - Terceros

4. ELEMENTOS DEL CONTRATO:
   - Prestación personal del servicio
   - Subordinación
   - Remuneración
   - Prestación del servicio
   - Voluntad de las partes
   - Capacidad legal

5. CLASES DE CONTRATO:
   - Contrato a término fijo
   - Contrato a término indefinido
   - Contrato de obra o labor
   - Contrato ocasional
   - Contrato accidental
   - Contrato de aprendizaje

6. OBLIGACIONES DEL TRABAJADOR:
   - Prestar el servicio
   - Obedecer las órdenes
   - Guardar reserva
   - Cuidar los bienes
   - Observar las normas
   - Cumplir el horario

7. OBLIGACIONES DEL EMPLEADOR:
   - Pagar la remuneración
   - Proporcionar trabajo
   - Respetar los derechos
   - Garantizar la seguridad
   - Capacitar al trabajador
   - Respetar la dignidad

8. JURISPRUDENCIA LABORAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Conceptos del Ministerio del Trabajo
   - Convenios de la OIT
   - Circulares laborales

9. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

10. RECOMENDACIONES ESTRATÉGICAS:
    - Cumplimiento contractual
    - Protección de derechos
    - Gestión de recursos humanos
    - Capacitación especializada
    - Asesoría profesional
    - Negociación colectiva

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia laboral y explicaciones detalladas del procedimiento legal laboral.";
    }

    /**
     * Formatear jurisprudencia laboral para el prompt
     */
    protected function formatearJurisprudenciaLaboral(array $jurisprudencia): string
    {
        $texto = "JURISPRUDENCIA LABORAL CONSULTADA:\n\n";
        
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
     * Construir prompt para derechos laborales
     */
    protected function construirPromptDerechosLaborales(array $datos, array $jurisprudencia): string
    {
        $tipoDerecho = $datos['tipo_derecho'] ?? 'general';
        $datosDerecho = $datos['datos_derecho'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Derechos Laborales';

        $jurisprudenciaText = $this->formatearJurisprudenciaLaboral($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - DERECHOS LABORALES

TIPO DE DERECHO: {$tipoDerecho}
CASO: {$caso}

DATOS DEL DERECHO:
{$datosDerecho}

JURISPRUDENCIA LABORAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE DERECHOS LABORALES:
   - Constitución Política (Art. 25, 26, 53, 54, 55, 56)
   - Código Sustantivo del Trabajo (Ley 1 de 1950)
   - Convenios de la OIT
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de igualdad
   - Principio de no discriminación
   - Principio de dignidad humana
   - Principio de libertad de trabajo
   - Principio de estabilidad laboral
   - Principio de irrenunciabilidad

3. DERECHOS FUNDAMENTALES:
   - Derecho al trabajo
   - Derecho a la igualdad
   - Derecho a la no discriminación
   - Derecho a la libertad
   - Derecho a la dignidad
   - Derecho a la intimidad

4. DERECHOS ECONÓMICOS:
   - Derecho a la remuneración
   - Derecho a la prima de servicios
   - Derecho a las cesantías
   - Derecho a las vacaciones
   - Derecho a las prestaciones
   - Derecho a la indemnización

5. DERECHOS SOCIALES:
   - Derecho a la seguridad social
   - Derecho a la salud
   - Derecho a la pensión
   - Derecho a la capacitación
   - Derecho a la recreación
   - Derecho a la vivienda

6. DERECHOS COLECTIVOS:
   - Derecho de asociación
   - Derecho de sindicalización
   - Derecho de huelga
   - Derecho de negociación
   - Derecho de participación
   - Derecho de representación

7. DERECHOS DE PROTECCIÓN:
   - Derecho a la seguridad
   - Derecho a la salud ocupacional
   - Derecho a la protección especial
   - Derecho a la estabilidad
   - Derecho a la indemnización
   - Derecho a la reintegración

8. JURISPRUDENCIA LABORAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Conceptos del Ministerio del Trabajo
   - Convenios de la OIT
   - Circulares laborales

9. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

10. RECOMENDACIONES ESTRATÉGICAS:
    - Protección de derechos
    - Cumplimiento normativo
    - Gestión de recursos humanos
    - Capacitación especializada
    - Asesoría profesional
    - Defensa de derechos

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia laboral y explicaciones detalladas del procedimiento legal laboral.";
    }

    /**
     * Construir prompt para seguridad social
     */
    protected function construirPromptSeguridadSocial(array $datos, array $jurisprudencia): string
    {
        $tipoSeguridad = $datos['tipo_seguridad'] ?? 'general';
        $datosSeguridad = $datos['datos_seguridad'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Seguridad Social';

        $jurisprudenciaText = $this->formatearJurisprudenciaLaboral($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - SEGURIDAD SOCIAL

TIPO DE SEGURIDAD: {$tipoSeguridad}
CASO: {$caso}

DATOS DE LA SEGURIDAD:
{$datosSeguridad}

JURISPRUDENCIA LABORAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE SEGURIDAD SOCIAL:
   - Constitución Política (Art. 48, 49, 50, 51, 52)
   - Ley 100 de 1993 (Sistema de Seguridad Social)
   - Ley 1122 de 2007 (Reforma al Sistema de Seguridad Social)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de universalidad
   - Principio de solidaridad
   - Principio de integralidad
   - Principio de unidad
   - Principio de participación
   - Principio de eficiencia

3. SISTEMA GENERAL DE PENSIONES:
   - Régimen de prima media
   - Régimen de ahorro individual
   - Requisitos de pensión
   - Cálculo de pensión
   - Administración de pensiones
   - Supervisión y control

4. SISTEMA GENERAL DE RIESGOS LABORALES:
   - Administradora de riesgos laborales
   - Cobertura de riesgos
   - Prestaciones económicas
   - Prestaciones asistenciales
   - Prevención de riesgos
   - Investigación de accidentes

5. SISTEMA GENERAL DE SALUD:
   - Entidades promotoras de salud
   - Instituciones prestadoras de salud
   - Plan de beneficios
   - Copagos y cuotas moderadoras
   - Atención de urgencias
   - Medicamentos

6. SUBSIDIO FAMILIAR:
   - Cajas de compensación familiar
   - Beneficiarios del subsidio
   - Monto del subsidio
   - Pago del subsidio
   - Supervisión y control
   - Sanciones

7. JURISPRUDENCIA LABORAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Conceptos del Ministerio del Trabajo
   - Convenios de la OIT
   - Circulares laborales

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
    - Cumplimiento normativo
    - Protección de derechos
    - Gestión de riesgos
    - Capacitación especializada
    - Asesoría profesional
    - Supervisión y control

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina laboral
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia laboral y explicaciones detalladas del procedimiento legal laboral.";
    }

    /**
     * Construir prompt para procedimiento laboral
     */
    protected function construirPromptProcedimientoLaboral(array $datos, array $jurisprudencia): string
    {
        $tipoProcedimiento = $datos['tipo_procedimiento'] ?? 'general';
        $datosProcedimiento = $datos['datos_procedimiento'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Procedimiento Laboral';

        $jurisprudenciaText = $this->formatearJurisprudenciaLaboral($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - PROCEDIMIENTO LABORAL

TIPO DE PROCEDIMIENTO: {$tipoProcedimiento}
CASO: {$caso}

DATOS DEL PROCEDIMIENTO:
{$datosProcedimiento}

JURISPRUDENCIA LABORAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL PROCEDIMIENTO LABORAL:
   - Código Procesal del Trabajo (Ley 712 de 2001)
   - Código Sustantivo del Trabajo (Ley 1 de 1950)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema
   - Convenios de la OIT

2. PRINCIPIOS DEL PROCEDIMIENTO:
   - Principio de oralidad
   - Principio de inmediación
   - Principio de concentración
   - Principio de publicidad
   - Principio de economía procesal
   - Principio de celeridad

3. COMPETENCIA LABORAL:
   - Competencia por la materia
   - Competencia por el territorio
   - Competencia por la cuantía
   - Competencia por la conexidad
   - Competencia por la prevención
   - Competencia por la acumulación

4. SUJETOS DEL PROCEDIMIENTO:
   - Juez laboral
   - Trabajador
   - Empleador
   - Representantes
   - Apoderados
   - Terceros

5. ACTOS PROCESALES:
   - Demanda
   - Contestación
   - Pruebas
   - Alegatos
   - Sentencia
   - Recursos

6. PRUEBAS EN EL PROCEDIMIENTO:
   - Prueba testimonial
   - Prueba documental
   - Prueba pericial
   - Prueba de inspección
   - Prueba de confesión
   - Prueba de indicios

7. RECURSOS LABORALES:
   - Recurso de apelación
   - Recurso de casación
   - Recurso de súplica
   - Recurso de queja
   - Recurso de revisión
   - Recurso de reposición

8. JURISPRUDENCIA LABORAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Conceptos del Ministerio del Trabajo
   - Convenios de la OIT
   - Circulares laborales

9. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

10. RECOMENDACIONES ESTRATÉGICAS:
    - Cumplimiento procesal
    - Defensa de derechos
    - Gestión de recursos
    - Capacitación especializada
    - Asesoría profesional
    - Representación legal

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia laboral y explicaciones detalladas del procedimiento legal laboral.";
    }

    /**
     * Construir prompt para contencioso laboral
     */
    protected function construirPromptContenciosoLaboral(array $datos, array $jurisprudencia): string
    {
        $tipoContencioso = $datos['tipo_contencioso'] ?? 'general';
        $datosContencioso = $datos['datos_contencioso'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Contencioso Laboral';

        $jurisprudenciaText = $this->formatearJurisprudenciaLaboral($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - CONTENCIOSO LABORAL

TIPO DE CONTENCIOSO: {$tipoContencioso}
CASO: {$caso}

DATOS DEL CONTENCIOSO:
{$datosContencioso}

JURISPRUDENCIA LABORAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL CONTENCIOSO LABORAL:
   - Código Procesal del Trabajo (Ley 712 de 2001)
   - Código Sustantivo del Trabajo (Ley 1 de 1950)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema
   - Convenios de la OIT

2. PRINCIPIOS DEL CONTENCIOSO:
   - Principio de oralidad
   - Principio de inmediación
   - Principio de concentración
   - Principio de publicidad
   - Principio de economía procesal
   - Principio de celeridad

3. SUJETOS DEL CONTENCIOSO:
   - Juez laboral
   - Trabajador
   - Empleador
   - Representantes
   - Apoderados
   - Terceros

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

8. JURISPRUDENCIA LABORAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Conceptos del Ministerio del Trabajo
   - Convenios de la OIT
   - Circulares laborales

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

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia laboral y explicaciones detalladas del procedimiento legal laboral.";
    }

    /**
     * Consolidar análisis laboral
     */
    protected function consolidarAnalisisLaboral(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_derecho_laboral',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'jurisprudencia_consultada' => [],
            'referencias_legales' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - DERECHO LABORAL\n\n";
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
            'Cumplir con las obligaciones laborales',
            'Proteger los derechos de los trabajadores',
            'Gestionar la seguridad social',
            'Desarrollar el procedimiento laboral',
            'Fortalecer el contencioso laboral',
            'Capacitar en derecho laboral especializado'
        ];

        // Generar referencias legales
        $consolidado['referencias_legales'] = [
            'Código Sustantivo del Trabajo (Ley 1 de 1950)',
            'Código Procesal del Trabajo (Ley 712 de 2001)',
            'Ley 100 de 1993 (Sistema de Seguridad Social)',
            'Jurisprudencia de la Corte Constitucional',
            'Jurisprudencia de la Corte Suprema',
            'Conceptos del Ministerio del Trabajo',
            'Convenios de la OIT',
            'Circulares laborales'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis laboral
     */
    protected function guardarAnalisisLaboral(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis laboral', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de derecho laboral
     */
    public function obtenerEstadisticasDerechoLaboral(): array
    {
        return [
            'total_analisis_laboral' => AnalisisIA::whereIn('especialidad', [
                'contrato_trabajo',
                'derechos_laborales',
                'seguridad_social',
                'procedimiento_laboral',
                'contencioso_laboral'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'contrato_trabajo',
                'derechos_laborales',
                'seguridad_social',
                'procedimiento_laboral',
                'contencioso_laboral'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'contrato_trabajo',
                'derechos_laborales',
                'seguridad_social',
                'procedimiento_laboral',
                'contencioso_laboral'
            ])->sum('tokens_usados'),
            'apis_publicas_consultadas' => array_keys($this->apisPublicas)
        ];
    }
}

