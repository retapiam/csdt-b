<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para Diputados y Gobernadores
 * Nivel Post-Doctorado con acceso a bases de datos departamentales y jurisprudencia
 */
class IADiputadosGobernadores
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelo;
    protected CircuitBreaker $circuitBreaker;

    // APIs públicas departamentales de Colombia
    protected $apisPublicas = [
        'min_interior' => 'https://www.mininterior.gov.co/',
        'corte_constitucional' => 'https://www.corteconstitucional.gov.co/relatoria/',
        'corte_suprema' => 'https://www.cortesuprema.gov.co/',
        'consejo_estado' => 'https://www.consejodeestado.gov.co/',
        'dane' => 'https://www.dane.gov.co/',
        'dnp' => 'https://www.dnp.gov.co/',
        'min_hacienda' => 'https://www.minhacienda.gov.co/',
        'contraloria' => 'https://www.contraloria.gov.co/',
        'procuraduria' => 'https://www.procuraduria.gov.co/',
        'defensoria' => 'https://www.defensoria.gov.co/'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->modelo = config('services.openai.model', 'gpt-4');
        $this->circuitBreaker = new CircuitBreaker('openai', 5, 60, 3);
    }

    /**
     * Análisis de Gobierno Departamental
     */
    public function analizarGobiernoDepartamental(array $datos): array
    {
        $cacheKey = 'ia_gobierno_departamental_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaDepartamental('gobierno_departamental');
            $prompt = $this->construirPromptGobiernoDepartamental($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisDepartamental('gobierno_departamental', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Asamblea Departamental
     */
    public function analizarAsambleaDepartamental(array $datos): array
    {
        $cacheKey = 'ia_asamblea_departamental_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaDepartamental('asamblea_departamental');
            $prompt = $this->construirPromptAsambleaDepartamental($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisDepartamental('asamblea_departamental', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Planeación Departamental
     */
    public function analizarPlaneacionDepartamental(array $datos): array
    {
        $cacheKey = 'ia_planeacion_departamental_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaDepartamental('planeacion_departamental');
            $prompt = $this->construirPromptPlaneacionDepartamental($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisDepartamental('planeacion_departamental', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Presupuesto Departamental
     */
    public function analizarPresupuestoDepartamental(array $datos): array
    {
        $cacheKey = 'ia_presupuesto_departamental_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaDepartamental('presupuesto_departamental');
            $prompt = $this->construirPromptPresupuestoDepartamental($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisDepartamental('presupuesto_departamental', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Competencias Departamentales
     */
    public function analizarCompetenciasDepartamentales(array $datos): array
    {
        $cacheKey = 'ia_competencias_departamentales_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaDepartamental('competencias_departamentales');
            $prompt = $this->construirPromptCompetenciasDepartamentales($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisDepartamental('competencias_departamentales', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de diputados y gobernadores
     */
    public function analisisIntegralDiputadosGobernadores(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'gobierno_departamental',
            'asamblea_departamental',
            'planeacion_departamental',
            'presupuesto_departamental',
            'competencias_departamentales'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisDepartamental($resultados, $datos);
    }

    /**
     * Obtener jurisprudencia departamental de fuentes públicas
     */
    protected function obtenerJurisprudenciaDepartamental(string $tipo): array
    {
        $cacheKey = "jurisprudencia_departamental_{$tipo}";
        
        return Cache::remember($cacheKey, 3600, function () use ($tipo) {
            $jurisprudencia = [];
            
            try {
                // Consultar Ministerio del Interior
                $response = Http::timeout(30)->get($this->apisPublicas['min_interior']);
                if ($response->successful()) {
                    $jurisprudencia['min_interior'] = $this->procesarRespuestaMinInterior($response->body(), $tipo);
                }
                
                // Consultar Corte Constitucional
                $response = Http::timeout(30)->get($this->apisPublicas['corte_constitucional']);
                if ($response->successful()) {
                    $jurisprudencia['corte_constitucional'] = $this->procesarRespuestaCorteConstitucional($response->body(), $tipo);
                }
                
                // Consultar DANE
                $response = Http::timeout(30)->get($this->apisPublicas['dane']);
                if ($response->successful()) {
                    $jurisprudencia['dane'] = $this->procesarRespuestaDANE($response->body(), $tipo);
                }
                
                // Consultar DNP
                $response = Http::timeout(30)->get($this->apisPublicas['dnp']);
                if ($response->successful()) {
                    $jurisprudencia['dnp'] = $this->procesarRespuestaDNP($response->body(), $tipo);
                }
                
            } catch (\Exception $e) {
                Log::error('Error obteniendo jurisprudencia departamental', [
                    'tipo' => $tipo,
                    'error' => $e->getMessage()
                ]);
            }
            
            return $jurisprudencia;
        });
    }

    /**
     * Procesar respuesta del Ministerio del Interior
     */
    protected function procesarRespuestaMinInterior(string $html, string $tipo): array
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
                        'fuente' => 'Ministerio del Interior'
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
     * Procesar respuesta de DANE
     */
    protected function procesarRespuestaDANE(string $html, string $tipo): array
    {
        $patrones = [
            'estadisticas' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Estadística[^<]*)<\/a>/i',
            'censos' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Censo[^<]*)<\/a>/i',
            'encuestas' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Encuesta[^<]*)<\/a>/i',
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
                        'fuente' => 'DANE'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de DNP
     */
    protected function procesarRespuestaDNP(string $html, string $tipo): array
    {
        $patrones = [
            'planes' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Plan[^<]*)<\/a>/i',
            'proyectos' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Proyecto[^<]*)<\/a>/i',
            'programas' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Programa[^<]*)<\/a>/i',
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
                        'fuente' => 'DNP'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Ejecutar análisis departamental
     */
    protected function ejecutarAnalisisDepartamental(string $tipoAnalisis, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en derecho departamental de nivel post-doctorado. " .
                                   "Especializado en gobierno departamental, asamblea departamental, planeación departamental, " .
                                   "presupuesto departamental y competencias departamentales. Tienes acceso a jurisprudencia " .
                                   "actualizada del Ministerio del Interior, Corte Constitucional, DANE y DNP. " .
                                   "Proporciona análisis exhaustivos con fundamentación académica sólida, " .
                                   "referencias específicas a jurisprudencia departamental y explicaciones detalladas " .
                                   "del procedimiento legal departamental."
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

            $this->guardarAnalisisDepartamental($tipoAnalisis, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'tipo_analisis' => $tipoAnalisis,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $this->modelo,
                'jurisprudencia_consultada' => $this->obtenerJurisprudenciaDepartamental($tipoAnalisis),
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis departamental: ' . $response->body());
    }

    /**
     * Construir prompt para gobierno departamental
     */
    protected function construirPromptGobiernoDepartamental(array $datos, array $jurisprudencia): string
    {
        $tipoGobierno = $datos['tipo_gobierno'] ?? 'general';
        $datosGobierno = $datos['datos_gobierno'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Gobierno Departamental';

        $jurisprudenciaText = $this->formatearJurisprudenciaDepartamental($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - GOBIERNO DEPARTAMENTAL

TIPO DE GOBIERNO: {$tipoGobierno}
CASO: {$caso}

DATOS DEL GOBIERNO:
{$datosGobierno}

JURISPRUDENCIA DEPARTAMENTAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL GOBIERNO DEPARTAMENTAL:
   - Constitución Política (Art. 299-310)
   - Ley 136 de 1994 (Ley Orgánica de las Entidades Territoriales)
   - Ley 1551 de 2012 (Ley de Ordenamiento Territorial)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de autonomía
   - Principio de descentralización
   - Principio de participación
   - Principio de transparencia
   - Principio de eficiencia
   - Principio de responsabilidad

3. ESTRUCTURA DEL GOBIERNO DEPARTAMENTAL:
   - Gobernación
   - Asamblea Departamental
   - Secretarías
   - Departamentos
   - Oficinas
   - Entidades descentralizadas

4. FUNCIONES DEL GOBERNADOR:
   - Función ejecutiva
   - Función administrativa
   - Función de coordinación
   - Función de representación
   - Función de control
   - Función de planeación

5. FUNCIONES DE LA ASAMBLEA:
   - Función legislativa
   - Función de control político
   - Función de planeación
   - Función de participación
   - Función de fiscalización
   - Función de representación

6. COMPETENCIAS DEPARTAMENTALES:
   - Competencias propias
   - Competencias compartidas
   - Competencias delegadas
   - Competencias concurrentes
   - Competencias residuales
   - Competencias especiales

7. JURISPRUDENCIA DEPARTAMENTAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Conceptos del Ministerio del Interior
   - Circulares departamentales
   - Resoluciones departamentales

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento departamental
    - Transparencia gubernamental
    - Participación ciudadana
    - Capacitación especializada
    - Asesoría profesional
    - Control democrático

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina departamental
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia departamental y explicaciones detalladas del procedimiento legal departamental.";
    }

    /**
     * Formatear jurisprudencia departamental para el prompt
     */
    protected function formatearJurisprudenciaDepartamental(array $jurisprudencia): string
    {
        $texto = "JURISPRUDENCIA DEPARTAMENTAL CONSULTADA:\n\n";
        
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
     * Construir prompt para asamblea departamental
     */
    protected function construirPromptAsambleaDepartamental(array $datos, array $jurisprudencia): string
    {
        $tipoAsamblea = $datos['tipo_asamblea'] ?? 'general';
        $datosAsamblea = $datos['datos_asamblea'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Asamblea Departamental';

        $jurisprudenciaText = $this->formatearJurisprudenciaDepartamental($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - ASAMBLEA DEPARTAMENTAL

TIPO DE ASAMBLEA: {$tipoAsamblea}
CASO: {$caso}

DATOS DE LA ASAMBLEA:
{$datosAsamblea}

JURISPRUDENCIA DEPARTAMENTAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE LA ASAMBLEA DEPARTAMENTAL:
   - Constitución Política (Art. 299-310)
   - Ley 136 de 1994 (Ley Orgánica de las Entidades Territoriales)
   - Reglamento de la Asamblea
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de representación
   - Principio de deliberación
   - Principio de publicidad
   - Principio de transparencia
   - Principio de responsabilidad
   - Principio de eficiencia

3. ESTRUCTURA DE LA ASAMBLEA:
   - Asamblea Departamental
   - Mesa Directiva
   - Comisiones
   - Secretaría General
   - Oficinas
   - Entidades de apoyo

4. FUNCIONES DE LA ASAMBLEA:
   - Función legislativa
   - Función de control político
   - Función de planeación
   - Función de participación
   - Función de fiscalización
   - Función de representación

5. PROCEDIMIENTO LEGISLATIVO:
   - Iniciativa legislativa
   - Trámite en comisiones
   - Discusión en plenaria
   - Sanción gubernamental
   - Promulgación
   - Publicación

6. CONTROL POLÍTICO:
   - Moción de censura
   - Interpelación
   - Citación
   - Comisión de investigación
   - Audiencias públicas
   - Informes

7. JURISPRUDENCIA DEPARTAMENTAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Resoluciones de la Asamblea
   - Reglamentos departamentales
   - Circulares departamentales

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento asambleario
    - Transparencia legislativa
    - Participación ciudadana
    - Capacitación especializada
    - Asesoría profesional
    - Control democrático

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina asamblearia
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia departamental y explicaciones detalladas del procedimiento legal departamental.";
    }

    /**
     * Construir prompt para planeación departamental
     */
    protected function construirPromptPlaneacionDepartamental(array $datos, array $jurisprudencia): string
    {
        $tipoPlaneacion = $datos['tipo_planeacion'] ?? 'general';
        $datosPlaneacion = $datos['datos_planeacion'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Planeación Departamental';

        $jurisprudenciaText = $this->formatearJurisprudenciaDepartamental($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - PLANEACIÓN DEPARTAMENTAL

TIPO DE PLANEACIÓN: {$tipoPlaneacion}
CASO: {$caso}

DATOS DE LA PLANEACIÓN:
{$datosPlaneacion}

JURISPRUDENCIA DEPARTAMENTAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE PLANEACIÓN DEPARTAMENTAL:
   - Constitución Política (Art. 299-310)
   - Ley 152 de 1994 (Ley Orgánica del Plan de Desarrollo)
   - Ley 388 de 1997 (Ley de Desarrollo Territorial)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de integralidad
   - Principio de participación
   - Principio de transparencia
   - Principio de eficiencia
   - Principio de sostenibilidad
   - Principio de equidad

3. INSTRUMENTOS DE PLANEACIÓN:
   - Plan de Desarrollo Departamental
   - Plan de Ordenamiento Territorial
   - Plan de Inversiones
   - Plan de Acción
   - Plan de Gestión
   - Plan de Contingencia

4. PROCESO DE PLANEACIÓN:
   - Diagnóstico
   - Formulación
   - Aprobación
   - Ejecución
   - Seguimiento
   - Evaluación

5. PARTICIPACIÓN CIUDADANA:
   - Consulta previa
   - Audiencias públicas
   - Mesas de trabajo
   - Comités de seguimiento
   - Veedurías ciudadanas
   - Rendición de cuentas

6. FINANCIACIÓN:
   - Recursos propios
   - Transferencias nacionales
   - Crédito público
   - Cooperación internacional
   - Asociaciones público-privadas
   - Fondos especiales

7. JURISPRUDENCIA DEPARTAMENTAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Conceptos del DNP
   - Circulares de planeación
   - Resoluciones de planeación

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de la planeación
    - Transparencia en la planeación
    - Participación efectiva
    - Capacitación especializada
    - Asesoría profesional
    - Control de la planeación

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina de planeación
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia departamental y explicaciones detalladas del procedimiento legal departamental.";
    }

    /**
     * Construir prompt para presupuesto departamental
     */
    protected function construirPromptPresupuestoDepartamental(array $datos, array $jurisprudencia): string
    {
        $tipoPresupuesto = $datos['tipo_presupuesto'] ?? 'general';
        $datosPresupuesto = $datos['datos_presupuesto'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Presupuesto Departamental';

        $jurisprudenciaText = $this->formatearJurisprudenciaDepartamental($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - PRESUPUESTO DEPARTAMENTAL

TIPO DE PRESUPUESTO: {$tipoPresupuesto}
CASO: {$caso}

DATOS DEL PRESUPUESTO:
{$datosPresupuesto}

JURISPRUDENCIA DEPARTAMENTAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL PRESUPUESTO DEPARTAMENTAL:
   - Constitución Política (Art. 299-310)
   - Ley 617 de 2000 (Ley de Competencias y Recursos)
   - Ley 819 de 2003 (Ley de Transparencia Fiscal)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de legalidad
   - Principio de transparencia
   - Principio de eficiencia
   - Principio de equidad
   - Principio de sostenibilidad
   - Principio de responsabilidad

3. ESTRUCTURA DEL PRESUPUESTO:
   - Ingresos
   - Gastos
   - Inversión
   - Funcionamiento
   - Deuda pública
   - Reservas

4. PROCESO PRESUPUESTAL:
   - Formulación
   - Aprobación
   - Ejecución
   - Seguimiento
   - Control
   - Evaluación

5. FUENTES DE FINANCIACIÓN:
   - Recursos propios
   - Transferencias nacionales
   - Crédito público
   - Cooperación internacional
   - Asociaciones público-privadas
   - Fondos especiales

6. CONTROL PRESUPUESTAL:
   - Control interno
   - Control externo
   - Control social
   - Control ciudadano
   - Control político
   - Control judicial

7. JURISPRUDENCIA DEPARTAMENTAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Conceptos del Ministerio de Hacienda
   - Circulares presupuestales
   - Resoluciones presupuestales

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento presupuestal
    - Transparencia presupuestal
    - Participación presupuestal
    - Capacitación especializada
    - Asesoría profesional
    - Control presupuestal

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina presupuestal
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia departamental y explicaciones detalladas del procedimiento legal departamental.";
    }

    /**
     * Construir prompt para competencias departamentales
     */
    protected function construirPromptCompetenciasDepartamentales(array $datos, array $jurisprudencia): string
    {
        $tipoCompetencia = $datos['tipo_competencia'] ?? 'general';
        $datosCompetencia = $datos['datos_competencia'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Competencias Departamentales';

        $jurisprudenciaText = $this->formatearJurisprudenciaDepartamental($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - COMPETENCIAS DEPARTAMENTALES

TIPO DE COMPETENCIA: {$tipoCompetencia}
CASO: {$caso}

DATOS DE LA COMPETENCIA:
{$datosCompetencia}

JURISPRUDENCIA DEPARTAMENTAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE COMPETENCIAS DEPARTAMENTALES:
   - Constitución Política (Art. 299-310)
   - Ley 136 de 1994 (Ley Orgánica de las Entidades Territoriales)
   - Ley 1551 de 2012 (Ley de Ordenamiento Territorial)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de autonomía
   - Principio de descentralización
   - Principio de subsidiariedad
   - Principio de concurrencia
   - Principio de coordinación
   - Principio de complementariedad

3. COMPETENCIAS PROPIAS:
   - Competencias exclusivas
   - Competencias residuales
   - Competencias especiales
   - Competencias de desarrollo
   - Competencias de coordinación
   - Competencias de representación

4. COMPETENCIAS COMPARTIDAS:
   - Competencias concurrentes
   - Competencias coordinadas
   - Competencias complementarias
   - Competencias de apoyo
   - Competencias de seguimiento
   - Competencias de evaluación

5. COMPETENCIAS DELEGADAS:
   - Competencias transferidas
   - Competencias asignadas
   - Competencias encomendadas
   - Competencias de ejecución
   - Competencias de gestión
   - Competencias de administración

6. LÍMITES Y RESTRICCIONES:
   - Límites constitucionales
   - Límites legales
   - Límites reglamentarios
   - Límites presupuestales
   - Límites de capacidad
   - Límites de competencia

7. JURISPRUDENCIA DEPARTAMENTAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Conceptos del Ministerio del Interior
   - Circulares departamentales
   - Resoluciones departamentales

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de competencias
    - Transparencia competencial
    - Participación competencial
    - Capacitación especializada
    - Asesoría profesional
    - Control competencial

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina competencial
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia departamental y explicaciones detalladas del procedimiento legal departamental.";
    }

    /**
     * Consolidar análisis departamental
     */
    protected function consolidarAnalisisDepartamental(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_diputados_gobernadores',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'jurisprudencia_consultada' => [],
            'referencias_legales' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - DIPUTADOS Y GOBERNADORES\n\n";
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
            'Fortalecer el gobierno departamental',
            'Desarrollar la asamblea departamental',
            'Mejorar la planeación departamental',
            'Optimizar el presupuesto departamental',
            'Fortalecer las competencias departamentales',
            'Capacitar en derecho departamental especializado'
        ];

        // Generar referencias legales
        $consolidado['referencias_legales'] = [
            'Constitución Política de Colombia',
            'Ley 136 de 1994 (Ley Orgánica de las Entidades Territoriales)',
            'Ley 152 de 1994 (Ley Orgánica del Plan de Desarrollo)',
            'Ley 388 de 1997 (Ley de Desarrollo Territorial)',
            'Jurisprudencia de la Corte Constitucional',
            'Jurisprudencia de la Corte Suprema',
            'Conceptos del Ministerio del Interior',
            'Circulares departamentales'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis departamental
     */
    protected function guardarAnalisisDepartamental(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis departamental', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de diputados y gobernadores
     */
    public function obtenerEstadisticasDiputadosGobernadores(): array
    {
        return [
            'total_analisis_departamental' => AnalisisIA::whereIn('especialidad', [
                'gobierno_departamental',
                'asamblea_departamental',
                'planeacion_departamental',
                'presupuesto_departamental',
                'competencias_departamentales'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'gobierno_departamental',
                'asamblea_departamental',
                'planeacion_departamental',
                'presupuesto_departamental',
                'competencias_departamentales'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'gobierno_departamental',
                'asamblea_departamental',
                'planeacion_departamental',
                'presupuesto_departamental',
                'competencias_departamentales'
            ])->sum('tokens_usados'),
            'apis_publicas_consultadas' => array_keys($this->apisPublicas)
        ];
    }
}

