<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para Consejo Municipal
 * Nivel Post-Doctorado con acceso a bases de datos municipales y jurisprudencia
 */
class IAConsejoMunicipal
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelo;
    protected CircuitBreaker $circuitBreaker;

    // APIs públicas municipales de Colombia
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
     * Análisis de Gobierno Municipal
     */
    public function analizarGobiernoMunicipal(array $datos): array
    {
        $cacheKey = 'ia_gobierno_municipal_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaMunicipal('gobierno_municipal');
            $prompt = $this->construirPromptGobiernoMunicipal($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisMunicipal('gobierno_municipal', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Planeación Municipal
     */
    public function analizarPlaneacionMunicipal(array $datos): array
    {
        $cacheKey = 'ia_planeacion_municipal_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaMunicipal('planeacion_municipal');
            $prompt = $this->construirPromptPlaneacionMunicipal($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisMunicipal('planeacion_municipal', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Presupuesto Municipal
     */
    public function analizarPresupuestoMunicipal(array $datos): array
    {
        $cacheKey = 'ia_presupuesto_municipal_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaMunicipal('presupuesto_municipal');
            $prompt = $this->construirPromptPresupuestoMunicipal($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisMunicipal('presupuesto_municipal', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Servicios Públicos
     */
    public function analizarServiciosPublicos(array $datos): array
    {
        $cacheKey = 'ia_servicios_publicos_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaMunicipal('servicios_publicos');
            $prompt = $this->construirPromptServiciosPublicos($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisMunicipal('servicios_publicos', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Participación Ciudadana Municipal
     */
    public function analizarParticipacionCiudadanaMunicipal(array $datos): array
    {
        $cacheKey = 'ia_participacion_ciudadana_municipal_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaMunicipal('participacion_ciudadana_municipal');
            $prompt = $this->construirPromptParticipacionCiudadanaMunicipal($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisMunicipal('participacion_ciudadana_municipal', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de consejo municipal
     */
    public function analisisIntegralConsejoMunicipal(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'gobierno_municipal',
            'planeacion_municipal',
            'presupuesto_municipal',
            'servicios_publicos',
            'participacion_ciudadana_municipal'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisMunicipal($resultados, $datos);
    }

    /**
     * Obtener jurisprudencia municipal de fuentes públicas
     */
    protected function obtenerJurisprudenciaMunicipal(string $tipo): array
    {
        $cacheKey = "jurisprudencia_municipal_{$tipo}";
        
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
                Log::error('Error obteniendo jurisprudencia municipal', [
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
     * Ejecutar análisis municipal
     */
    protected function ejecutarAnalisisMunicipal(string $tipoAnalisis, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en derecho municipal de nivel post-doctorado. " .
                                   "Especializado en gobierno municipal, planeación municipal, presupuesto municipal, " .
                                   "servicios públicos y participación ciudadana municipal. Tienes acceso a jurisprudencia " .
                                   "actualizada del Ministerio del Interior, Corte Constitucional, DANE y DNP. " .
                                   "Proporciona análisis exhaustivos con fundamentación académica sólida, " .
                                   "referencias específicas a jurisprudencia municipal y explicaciones detalladas " .
                                   "del procedimiento legal municipal."
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

            $this->guardarAnalisisMunicipal($tipoAnalisis, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'tipo_analisis' => $tipoAnalisis,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $this->modelo,
                'jurisprudencia_consultada' => $this->obtenerJurisprudenciaMunicipal($tipoAnalisis),
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis municipal: ' . $response->body());
    }

    /**
     * Construir prompt para gobierno municipal
     */
    protected function construirPromptGobiernoMunicipal(array $datos, array $jurisprudencia): string
    {
        $tipoGobierno = $datos['tipo_gobierno'] ?? 'general';
        $datosGobierno = $datos['datos_gobierno'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Gobierno Municipal';

        $jurisprudenciaText = $this->formatearJurisprudenciaMunicipal($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - GOBIERNO MUNICIPAL

TIPO DE GOBIERNO: {$tipoGobierno}
CASO: {$caso}

DATOS DEL GOBIERNO:
{$datosGobierno}

JURISPRUDENCIA MUNICIPAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL GOBIERNO MUNICIPAL:
   - Constitución Política (Art. 311-318)
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

3. ESTRUCTURA DEL GOBIERNO MUNICIPAL:
   - Alcaldía
   - Concejo Municipal
   - Secretarías
   - Departamentos
   - Oficinas
   - Entidades descentralizadas

4. FUNCIONES DEL ALCALDE:
   - Función ejecutiva
   - Función administrativa
   - Función de coordinación
   - Función de representación
   - Función de control
   - Función de planeación

5. FUNCIONES DEL CONCEJO:
   - Función legislativa
   - Función de control político
   - Función de planeación
   - Función de participación
   - Función de fiscalización
   - Función de representación

6. COMPETENCIAS MUNICIPALES:
   - Competencias propias
   - Competencias compartidas
   - Competencias delegadas
   - Competencias concurrentes
   - Competencias residuales
   - Competencias especiales

7. JURISPRUDENCIA MUNICIPAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Conceptos del Ministerio del Interior
   - Circulares municipales
   - Resoluciones municipales

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento municipal
    - Transparencia gubernamental
    - Participación ciudadana
    - Capacitación especializada
    - Asesoría profesional
    - Control democrático

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina municipal
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia municipal y explicaciones detalladas del procedimiento legal municipal.";
    }

    /**
     * Formatear jurisprudencia municipal para el prompt
     */
    protected function formatearJurisprudenciaMunicipal(array $jurisprudencia): string
    {
        $texto = "JURISPRUDENCIA MUNICIPAL CONSULTADA:\n\n";
        
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
     * Construir prompt para planeación municipal
     */
    protected function construirPromptPlaneacionMunicipal(array $datos, array $jurisprudencia): string
    {
        $tipoPlaneacion = $datos['tipo_planeacion'] ?? 'general';
        $datosPlaneacion = $datos['datos_planeacion'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Planeación Municipal';

        $jurisprudenciaText = $this->formatearJurisprudenciaMunicipal($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - PLANEACIÓN MUNICIPAL

TIPO DE PLANEACIÓN: {$tipoPlaneacion}
CASO: {$caso}

DATOS DE LA PLANEACIÓN:
{$datosPlaneacion}

JURISPRUDENCIA MUNICIPAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE PLANEACIÓN MUNICIPAL:
   - Constitución Política (Art. 311-318)
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
   - Plan de Desarrollo Municipal
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
   - Transferencias departamentales
   - Crédito público
   - Cooperación internacional
   - Asociaciones público-privadas

7. JURISPRUDENCIA MUNICIPAL:
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

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia municipal y explicaciones detalladas del procedimiento legal municipal.";
    }

    /**
     * Construir prompt para presupuesto municipal
     */
    protected function construirPromptPresupuestoMunicipal(array $datos, array $jurisprudencia): string
    {
        $tipoPresupuesto = $datos['tipo_presupuesto'] ?? 'general';
        $datosPresupuesto = $datos['datos_presupuesto'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Presupuesto Municipal';

        $jurisprudenciaText = $this->formatearJurisprudenciaMunicipal($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - PRESUPUESTO MUNICIPAL

TIPO DE PRESUPUESTO: {$tipoPresupuesto}
CASO: {$caso}

DATOS DEL PRESUPUESTO:
{$datosPresupuesto}

JURISPRUDENCIA MUNICIPAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL PRESUPUESTO MUNICIPAL:
   - Constitución Política (Art. 311-318)
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
   - Transferencias departamentales
   - Crédito público
   - Cooperación internacional
   - Asociaciones público-privadas

6. CONTROL PRESUPUESTAL:
   - Control interno
   - Control externo
   - Control social
   - Control ciudadano
   - Control político
   - Control judicial

7. JURISPRUDENCIA MUNICIPAL:
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

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia municipal y explicaciones detalladas del procedimiento legal municipal.";
    }

    /**
     * Construir prompt para servicios públicos
     */
    protected function construirPromptServiciosPublicos(array $datos, array $jurisprudencia): string
    {
        $tipoServicio = $datos['tipo_servicio'] ?? 'general';
        $datosServicio = $datos['datos_servicio'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Servicios Públicos';

        $jurisprudenciaText = $this->formatearJurisprudenciaMunicipal($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - SERVICIOS PÚBLICOS

TIPO DE SERVICIO: {$tipoServicio}
CASO: {$caso}

DATOS DEL SERVICIO:
{$datosServicio}

JURISPRUDENCIA MUNICIPAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE SERVICIOS PÚBLICOS:
   - Constitución Política (Art. 311-318)
   - Ley 142 de 1994 (Ley de Servicios Públicos Domiciliarios)
   - Ley 143 de 1994 (Ley de Electricidad)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de universalidad
   - Principio de continuidad
   - Principio de calidad
   - Principio de eficiencia
   - Principio de transparencia
   - Principio de participación

3. CLASIFICACIÓN DE SERVICIOS:
   - Servicios públicos domiciliarios
   - Servicios públicos no domiciliarios
   - Servicios públicos esenciales
   - Servicios públicos complementarios
   - Servicios públicos especiales
   - Servicios públicos de interés general

4. PRESTACIÓN DE SERVICIOS:
   - Prestación directa
   - Prestación indirecta
   - Concesión
   - Asociación
   - Contrato de gestión
   - Contrato de obra

5. REGULACIÓN Y CONTROL:
   - Comisión de Regulación
   - Superintendencias
   - Contralorías
   - Veedurías
   - Usuarios
   - Organizaciones sociales

6. TARIFAS Y SUBSIDIOS:
   - Estructura tarifaria
   - Subsidios cruzados
   - Subsidios directos
   - Fondo de Solidaridad
   - Fondo de Compensación
   - Fondo de Estabilización

7. JURISPRUDENCIA MUNICIPAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Conceptos de las Superintendencias
   - Circulares de servicios
   - Resoluciones de servicios

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de servicios
    - Transparencia en servicios
    - Participación en servicios
    - Capacitación especializada
    - Asesoría profesional
    - Control de servicios

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina de servicios
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia municipal y explicaciones detalladas del procedimiento legal municipal.";
    }

    /**
     * Construir prompt para participación ciudadana municipal
     */
    protected function construirPromptParticipacionCiudadanaMunicipal(array $datos, array $jurisprudencia): string
    {
        $tipoParticipacion = $datos['tipo_participacion'] ?? 'general';
        $datosParticipacion = $datos['datos_participacion'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Participación Ciudadana Municipal';

        $jurisprudenciaText = $this->formatearJurisprudenciaMunicipal($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - PARTICIPACIÓN CIUDADANA MUNICIPAL

TIPO DE PARTICIPACIÓN: {$tipoParticipacion}
CASO: {$caso}

DATOS DE PARTICIPACIÓN:
{$datosParticipacion}

JURISPRUDENCIA MUNICIPAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE PARTICIPACIÓN CIUDADANA MUNICIPAL:
   - Constitución Política (Art. 40, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113)
   - Ley 134 de 1994 (Ley de Mecanismos de Participación Ciudadana)
   - Ley 1757 de 2015 (Ley de Transparencia y Acceso a la Información)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de participación
   - Principio de transparencia
   - Principio de acceso a la información
   - Principio de rendición de cuentas
   - Principio de control social
   - Principio de deliberación

3. MECANISMOS DE PARTICIPACIÓN:
   - Voto
   - Plebiscito
   - Referendo
   - Consulta popular
   - Cabildo abierto
   - Iniciativa popular

4. DERECHOS DE PARTICIPACIÓN:
   - Derecho a la información
   - Derecho a la consulta
   - Derecho a la participación
   - Derecho a la deliberación
   - Derecho a la decisión
   - Derecho al control

5. PROCEDIMIENTOS DE PARTICIPACIÓN:
   - Iniciativa ciudadana
   - Trámite administrativo
   - Votación
   - Escrutinio
   - Proclamación
   - Recursos

6. CONTROL SOCIAL:
   - Veedurías ciudadanas
   - Rendición de cuentas
   - Audiencias públicas
   - Comités de seguimiento
   - Observatorios ciudadanos
   - Redes de control

7. JURISPRUDENCIA MUNICIPAL:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Resoluciones administrativas
   - Circulares de participación
   - Conceptos de participación

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento democrático
    - Transparencia participativa
    - Participación efectiva
    - Capacitación especializada
    - Asesoría profesional
    - Control ciudadano

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina participativa
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia municipal y explicaciones detalladas del procedimiento legal municipal.";
    }

    /**
     * Consolidar análisis municipal
     */
    protected function consolidarAnalisisMunicipal(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_consejo_municipal',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'jurisprudencia_consultada' => [],
            'referencias_legales' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - CONSEJO MUNICIPAL\n\n";
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
            'Fortalecer el gobierno municipal',
            'Desarrollar la planeación municipal',
            'Mejorar el presupuesto municipal',
            'Optimizar los servicios públicos',
            'Promover la participación ciudadana',
            'Capacitar en derecho municipal especializado'
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
            'Circulares municipales'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis municipal
     */
    protected function guardarAnalisisMunicipal(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis municipal', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de consejo municipal
     */
    public function obtenerEstadisticasConsejoMunicipal(): array
    {
        return [
            'total_analisis_municipal' => AnalisisIA::whereIn('especialidad', [
                'gobierno_municipal',
                'planeacion_municipal',
                'presupuesto_municipal',
                'servicios_publicos',
                'participacion_ciudadana_municipal'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'gobierno_municipal',
                'planeacion_municipal',
                'presupuesto_municipal',
                'servicios_publicos',
                'participacion_ciudadana_municipal'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'gobierno_municipal',
                'planeacion_municipal',
                'presupuesto_municipal',
                'servicios_publicos',
                'participacion_ciudadana_municipal'
            ])->sum('tokens_usados'),
            'apis_publicas_consultadas' => array_keys($this->apisPublicas)
        ];
    }
}

