<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para Derecho Internacional
 * Nivel Post-Doctorado con acceso a bases de datos internacionales y jurisprudencia
 */
class IADerechoInternacional
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelo;
    protected CircuitBreaker $circuitBreaker;

    // APIs públicas internacionales
    protected $apisPublicas = [
        'corte_icj' => 'https://www.icj-cij.org/',
        'corte_europea' => 'https://www.echr.coe.int/',
        'corte_interamericana' => 'https://www.corteidh.or.cr/',
        'onu' => 'https://www.un.org/',
        'oas' => 'https://www.oas.org/',
        'can' => 'https://www.comunidadandina.org/',
        'mercosur' => 'https://www.mercosur.int/',
        'aladi' => 'https://www.aladi.org/',
        'corte_penal_internacional' => 'https://www.icc-cpi.int/',
        'oit' => 'https://www.ilo.org/'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->modelo = config('services.openai.model', 'gpt-4');
        $this->circuitBreaker = new CircuitBreaker('openai', 5, 60, 3);
    }

    /**
     * Análisis de Derecho Internacional Público
     */
    public function analizarDerechoInternacionalPublico(array $datos): array
    {
        $cacheKey = 'ia_derecho_internacional_publico_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaInternacional('derecho_internacional_publico');
            $prompt = $this->construirPromptDerechoInternacionalPublico($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisInternacional('derecho_internacional_publico', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Derecho Internacional Privado
     */
    public function analizarDerechoInternacionalPrivado(array $datos): array
    {
        $cacheKey = 'ia_derecho_internacional_privado_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaInternacional('derecho_internacional_privado');
            $prompt = $this->construirPromptDerechoInternacionalPrivado($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisInternacional('derecho_internacional_privado', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Derecho Internacional Humanitario
     */
    public function analizarDerechoInternacionalHumanitario(array $datos): array
    {
        $cacheKey = 'ia_derecho_internacional_humanitario_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaInternacional('derecho_internacional_humanitario');
            $prompt = $this->construirPromptDerechoInternacionalHumanitario($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisInternacional('derecho_internacional_humanitario', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Derecho Internacional de los Derechos Humanos
     */
    public function analizarDerechoInternacionalDerechosHumanos(array $datos): array
    {
        $cacheKey = 'ia_derecho_internacional_derechos_humanos_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaInternacional('derecho_internacional_derechos_humanos');
            $prompt = $this->construirPromptDerechoInternacionalDerechosHumanos($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisInternacional('derecho_internacional_derechos_humanos', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Derecho Internacional Económico
     */
    public function analizarDerechoInternacionalEconomico(array $datos): array
    {
        $cacheKey = 'ia_derecho_internacional_economico_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaInternacional('derecho_internacional_economico');
            $prompt = $this->construirPromptDerechoInternacionalEconomico($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisInternacional('derecho_internacional_economico', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de derecho internacional
     */
    public function analisisIntegralDerechoInternacional(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'derecho_internacional_publico',
            'derecho_internacional_privado',
            'derecho_internacional_humanitario',
            'derecho_internacional_derechos_humanos',
            'derecho_internacional_economico'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisInternacional($resultados, $datos);
    }

    /**
     * Obtener jurisprudencia internacional de fuentes públicas
     */
    protected function obtenerJurisprudenciaInternacional(string $tipo): array
    {
        $cacheKey = "jurisprudencia_internacional_{$tipo}";
        
        return Cache::remember($cacheKey, 3600, function () use ($tipo) {
            $jurisprudencia = [];
            
            try {
                // Consultar Corte Internacional de Justicia
                $response = Http::timeout(30)->get($this->apisPublicas['corte_icj']);
                if ($response->successful()) {
                    $jurisprudencia['corte_icj'] = $this->procesarRespuestaCorteICJ($response->body(), $tipo);
                }
                
                // Consultar Corte Interamericana de Derechos Humanos
                $response = Http::timeout(30)->get($this->apisPublicas['corte_interamericana']);
                if ($response->successful()) {
                    $jurisprudencia['corte_interamericana'] = $this->procesarRespuestaCorteInteramericana($response->body(), $tipo);
                }
                
                // Consultar ONU
                $response = Http::timeout(30)->get($this->apisPublicas['onu']);
                if ($response->successful()) {
                    $jurisprudencia['onu'] = $this->procesarRespuestaONU($response->body(), $tipo);
                }
                
                // Consultar OEA
                $response = Http::timeout(30)->get($this->apisPublicas['oas']);
                if ($response->successful()) {
                    $jurisprudencia['oas'] = $this->procesarRespuestaOEA($response->body(), $tipo);
                }
                
            } catch (\Exception $e) {
                Log::error('Error obteniendo jurisprudencia internacional', [
                    'tipo' => $tipo,
                    'error' => $e->getMessage()
                ]);
            }
            
            return $jurisprudencia;
        });
    }

    /**
     * Procesar respuesta de Corte Internacional de Justicia
     */
    protected function procesarRespuestaCorteICJ(string $html, string $tipo): array
    {
        $patrones = [
            'casos' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Case[^<]*)<\/a>/i',
            'opiniones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Opinion[^<]*)<\/a>/i',
            'ordenes' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Order[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'Corte Internacional de Justicia'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de Corte Interamericana
     */
    protected function procesarRespuestaCorteInteramericana(string $html, string $tipo): array
    {
        $patrones = [
            'sentencias' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Sentencia[^<]*)<\/a>/i',
            'opiniones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Opinión[^<]*)<\/a>/i',
            'medidas' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Medidas[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'Corte Interamericana de Derechos Humanos'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de ONU
     */
    protected function procesarRespuestaONU(string $html, string $tipo): array
    {
        $patrones = [
            'resoluciones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Resolution[^<]*)<\/a>/i',
            'tratados' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Treaty[^<]*)<\/a>/i',
            'convenciones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Convention[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'Organización de las Naciones Unidas'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de OEA
     */
    protected function procesarRespuestaOEA(string $html, string $tipo): array
    {
        $patrones = [
            'resoluciones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Resolución[^<]*)<\/a>/i',
            'tratados' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Tratado[^<]*)<\/a>/i',
            'convenciones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Convención[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'Organización de los Estados Americanos'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Ejecutar análisis internacional
     */
    protected function ejecutarAnalisisInternacional(string $tipoAnalisis, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en derecho internacional de nivel post-doctorado. " .
                                   "Especializado en derecho internacional público, privado, humanitario, " .
                                   "derechos humanos y económico. Tienes acceso a jurisprudencia actualizada " .
                                   "de tribunales internacionales, organizaciones internacionales y " .
                                   "tratados vigentes. Proporciona análisis exhaustivos con fundamentación " .
                                   "académica sólida, referencias específicas a jurisprudencia internacional " .
                                   "y explicaciones detalladas del procedimiento legal internacional."
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

            $this->guardarAnalisisInternacional($tipoAnalisis, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'tipo_analisis' => $tipoAnalisis,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $this->modelo,
                'jurisprudencia_consultada' => $this->obtenerJurisprudenciaInternacional($tipoAnalisis),
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis internacional: ' . $response->body());
    }

    /**
     * Construir prompt para derecho internacional público
     */
    protected function construirPromptDerechoInternacionalPublico(array $datos, array $jurisprudencia): string
    {
        $tipoDerecho = $datos['tipo_derecho'] ?? 'general';
        $datosDerecho = $datos['datos_derecho'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Derecho Internacional Público';

        $jurisprudenciaText = $this->formatearJurisprudenciaInternacional($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - DERECHO INTERNACIONAL PÚBLICO

TIPO DE DERECHO: {$tipoDerecho}
CASO: {$caso}

DATOS DEL CASO:
{$datosDerecho}

JURISPRUDENCIA INTERNACIONAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL DERECHO INTERNACIONAL PÚBLICO:
   - Carta de las Naciones Unidas
   - Estatuto de la Corte Internacional de Justicia
   - Convención de Viena sobre el Derecho de los Tratados
   - Convención de Viena sobre Relaciones Diplomáticas
   - Convención de Viena sobre Relaciones Consulares
   - Jurisprudencia internacional actualizada

2. SUJETOS DEL DERECHO INTERNACIONAL:
   - Estados
   - Organizaciones internacionales
   - Individuos
   - Pueblos
   - Movimientos de liberación nacional
   - Empresas transnacionales

3. FUENTES DEL DERECHO INTERNACIONAL:
   - Tratados internacionales
   - Costumbre internacional
   - Principios generales del derecho
   - Decisiones judiciales
   - Doctrina de publicistas
   - Actos unilaterales

4. PRINCIPIOS FUNDAMENTALES:
   - Soberanía estatal
   - Igualdad soberana
   - No intervención
   - Autodeterminación de los pueblos
   - Prohibición del uso de la fuerza
   - Solución pacífica de controversias

5. RESPONSABILIDAD INTERNACIONAL:
   - Concepto de responsabilidad
   - Elementos de la responsabilidad
   - Circunstancias que excluyen la ilicitud
   - Consecuencias de la responsabilidad
   - Reparación
   - Contramedidas

6. SOLUCIÓN DE CONTROVERSIAS:
   - Negociación
   - Mediación
   - Conciliación
   - Arbitraje
   - Solución judicial
   - Solución por organizaciones internacionales

7. TRIBUNALES INTERNACIONALES:
   - Corte Internacional de Justicia
   - Corte Penal Internacional
   - Tribunales ad hoc
   - Tribunales regionales
   - Tribunales especializados
   - Tribunales híbridos

8. ORGANIZACIONES INTERNACIONALES:
   - Organización de las Naciones Unidas
   - Organización de los Estados Americanos
   - Unión Europea
   - Unión Africana
   - Liga de los Estados Árabes
   - Asociación de Naciones del Sudeste Asiático

9. JURISPRUDENCIA INTERNACIONAL:
   - Sentencias de la Corte Internacional de Justicia
   - Opiniones consultivas
   - Medidas provisionales
   - Precedentes internacionales
   - Líneas jurisprudenciales
   - Evolución jurisprudencial

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento del derecho internacional
    - Mejora de los mecanismos de solución
    - Desarrollo de la cooperación internacional
    - Capacitación especializada
    - Investigación académica
    - Cooperación regional

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia internacional y explicaciones detalladas del procedimiento legal internacional.";
    }

    /**
     * Formatear jurisprudencia internacional para el prompt
     */
    protected function formatearJurisprudenciaInternacional(array $jurisprudencia): string
    {
        $texto = "JURISPRUDENCIA INTERNACIONAL CONSULTADA:\n\n";
        
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
     * Construir prompt para derecho internacional privado
     */
    protected function construirPromptDerechoInternacionalPrivado(array $datos, array $jurisprudencia): string
    {
        $tipoDerecho = $datos['tipo_derecho'] ?? 'general';
        $datosDerecho = $datos['datos_derecho'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Derecho Internacional Privado';

        $jurisprudenciaText = $this->formatearJurisprudenciaInternacional($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - DERECHO INTERNACIONAL PRIVADO

TIPO DE DERECHO: {$tipoDerecho}
CASO: {$caso}

DATOS DEL CASO:
{$datosDerecho}

JURISPRUDENCIA INTERNACIONAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL DERECHO INTERNACIONAL PRIVADO:
   - Convención de La Haya sobre Derecho Internacional Privado
   - Convención de Bruselas sobre Competencia Judicial
   - Convención de Nueva York sobre Reconocimiento y Ejecución de Laudos Arbitrales
   - Convención de Viena sobre Compraventa Internacional de Mercaderías
   - Jurisprudencia internacional actualizada

2. CONFLICTOS DE LEYES:
   - Concepto de conflicto de leyes
   - Elementos de conexión
   - Criterios de conexión
   - Clasificación de las normas
   - Calificación
   - Reenvío

3. COMPETENCIA JUDICIAL:
   - Competencia directa
   - Competencia indirecta
   - Competencia exclusiva
   - Competencia concurrente
   - Competencia residual
   - Competencia de urgencia

4. RECONOCIMIENTO Y EJECUCIÓN:
   - Concepto de reconocimiento
   - Concepto de ejecución
   - Requisitos de reconocimiento
   - Requisitos de ejecución
   - Causas de denegación
   - Procedimiento

5. ARBITRAJE INTERNACIONAL:
   - Concepto de arbitraje internacional
   - Convención de Nueva York
   - Ley Modelo de la CNUDMI
   - Procedimiento arbitral
   - Laudo arbitral
   - Reconocimiento y ejecución

6. CONTRATOS INTERNACIONALES:
   - Concepto de contrato internacional
   - Ley aplicable
   - Autonomía de la voluntad
   - Convención de Viena
   - Incoterms
   - Cláusulas especiales

7. DERECHO DE FAMILIA INTERNACIONAL:
   - Matrimonio internacional
   - Divorcio internacional
   - Adopción internacional
   - Alimentos internacionales
   - Tutela internacional
   - Sucesiones internacionales

8. DERECHO MERCANTIL INTERNACIONAL:
   - Compraventa internacional
   - Transporte internacional
   - Seguros internacionales
   - Pagos internacionales
   - Propiedad intelectual
   - Competencia desleal

9. JURISPRUDENCIA INTERNACIONAL:
   - Sentencias de tribunales internacionales
   - Precedentes internacionales
   - Líneas jurisprudenciales
   - Criterios de interpretación
   - Evolución jurisprudencial

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento del derecho internacional privado
    - Mejora de los mecanismos de solución
    - Desarrollo de la cooperación internacional
    - Capacitación especializada
    - Investigación académica
    - Cooperación regional

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia internacional y explicaciones detalladas del procedimiento legal internacional.";
    }

    /**
     * Construir prompt para derecho internacional humanitario
     */
    protected function construirPromptDerechoInternacionalHumanitario(array $datos, array $jurisprudencia): string
    {
        $tipoDerecho = $datos['tipo_derecho'] ?? 'general';
        $datosDerecho = $datos['datos_derecho'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Derecho Internacional Humanitario';

        $jurisprudenciaText = $this->formatearJurisprudenciaInternacional($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - DERECHO INTERNACIONAL HUMANITARIO

TIPO DE DERECHO: {$tipoDerecho}
CASO: {$caso}

DATOS DEL CASO:
{$datosDerecho}

JURISPRUDENCIA INTERNACIONAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL DERECHO INTERNACIONAL HUMANITARIO:
   - Convenios de Ginebra de 1949
   - Protocolos Adicionales de 1977
   - Convención de La Haya de 1907
   - Convención sobre Armas Químicas
   - Convención sobre Armas Biológicas
   - Convención sobre Minas Antipersonal

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de humanidad
   - Principio de distinción
   - Principio de proporcionalidad
   - Principio de necesidad militar
   - Principio de precaución
   - Principio de no discriminación

3. PROTECCIÓN DE PERSONAS:
   - Combatientes
   - No combatientes
   - Civiles
   - Prisioneros de guerra
   - Heridos y enfermos
   - Personal sanitario

4. PROTECCIÓN DE BIENES:
   - Bienes civiles
   - Bienes culturales
   - Bienes ambientales
   - Infraestructura esencial
   - Bienes de uso civil
   - Bienes de uso mixto

5. MÉTODOS Y MEDIOS DE COMBATE:
   - Prohibiciones generales
   - Armas prohibidas
   - Métodos prohibidos
   - Medidas de precaución
   - Protección del medio ambiente
   - Protección de obras e instalaciones

6. RESPONSABILIDAD PENAL:
   - Crímenes de guerra
   - Crímenes contra la humanidad
   - Genocidio
   - Agresión
   - Responsabilidad individual
   - Responsabilidad de mando

7. TRIBUNALES PENALES:
   - Corte Penal Internacional
   - Tribunales ad hoc
   - Tribunales híbridos
   - Tribunales nacionales
   - Jurisdicción universal
   - Cooperación internacional

8. MECANISMOS DE CONTROL:
   - Comité Internacional de la Cruz Roja
   - Alto Comisionado de las Naciones Unidas para los Refugiados
   - Organizaciones no gubernamentales
   - Mecanismos de supervisión
   - Procedimientos de queja
   - Investigaciones

9. JURISPRUDENCIA INTERNACIONAL:
   - Sentencias de tribunales penales
   - Precedentes internacionales
   - Líneas jurisprudenciales
   - Criterios de interpretación
   - Evolución jurisprudencial

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento del derecho internacional humanitario
    - Mejora de los mecanismos de control
    - Desarrollo de la cooperación internacional
    - Capacitación especializada
    - Investigación académica
    - Cooperación regional

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia internacional y explicaciones detalladas del procedimiento legal internacional.";
    }

    /**
     * Construir prompt para derecho internacional de derechos humanos
     */
    protected function construirPromptDerechoInternacionalDerechosHumanos(array $datos, array $jurisprudencia): string
    {
        $tipoDerecho = $datos['tipo_derecho'] ?? 'general';
        $datosDerecho = $datos['datos_derecho'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Derecho Internacional de Derechos Humanos';

        $jurisprudenciaText = $this->formatearJurisprudenciaInternacional($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - DERECHO INTERNACIONAL DE DERECHOS HUMANOS

TIPO DE DERECHO: {$tipoDerecho}
CASO: {$caso}

DATOS DEL CASO:
{$datosDerecho}

JURISPRUDENCIA INTERNACIONAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL DERECHO INTERNACIONAL DE DERECHOS HUMANOS:
   - Declaración Universal de Derechos Humanos
   - Pacto Internacional de Derechos Civiles y Políticos
   - Pacto Internacional de Derechos Económicos, Sociales y Culturales
   - Convención Americana sobre Derechos Humanos
   - Convención Europea de Derechos Humanos
   - Convención Africana sobre Derechos Humanos

2. PRINCIPIOS FUNDAMENTALES:
   - Universalidad
   - Indivisibilidad
   - Interdependencia
   - Progresividad
   - No regresividad
   - Efectividad

3. DERECHOS CIVILES Y POLÍTICOS:
   - Derecho a la vida
   - Derecho a la libertad
   - Derecho a la seguridad personal
   - Derecho a la igualdad
   - Derecho a la no discriminación
   - Derecho a la participación política

4. DERECHOS ECONÓMICOS, SOCIALES Y CULTURALES:
   - Derecho al trabajo
   - Derecho a la educación
   - Derecho a la salud
   - Derecho a la vivienda
   - Derecho a la alimentación
   - Derecho a la cultura

5. DERECHOS DE SOLIDARIDAD:
   - Derecho al desarrollo
   - Derecho a la paz
   - Derecho al medio ambiente
   - Derecho a la información
   - Derecho a la comunicación
   - Derecho a la participación

6. MECANISMOS DE PROTECCIÓN:
   - Sistema universal de protección
   - Sistema regional de protección
   - Procedimientos especiales
   - Procedimientos de queja
   - Procedimientos de investigación
   - Procedimientos de seguimiento

7. TRIBUNALES DE DERECHOS HUMANOS:
   - Corte Interamericana de Derechos Humanos
   - Corte Europea de Derechos Humanos
   - Corte Africana de Derechos Humanos
   - Comité de Derechos Humanos
   - Comité de Derechos Económicos, Sociales y Culturales
   - Comité para la Eliminación de la Discriminación Racial

8. JURISPRUDENCIA INTERNACIONAL:
   - Sentencias de tribunales de derechos humanos
   - Precedentes internacionales
   - Líneas jurisprudenciales
   - Criterios de interpretación
   - Evolución jurisprudencial

9. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de la protección de derechos humanos
    - Mejora de los mecanismos de protección
    - Desarrollo de la cooperación internacional
    - Capacitación especializada
    - Investigación académica
    - Cooperación regional

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia internacional y explicaciones detalladas del procedimiento legal internacional.";
    }

    /**
     * Construir prompt para derecho internacional económico
     */
    protected function construirPromptDerechoInternacionalEconomico(array $datos, array $jurisprudencia): string
    {
        $tipoDerecho = $datos['tipo_derecho'] ?? 'general';
        $datosDerecho = $datos['datos_derecho'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Derecho Internacional Económico';

        $jurisprudenciaText = $this->formatearJurisprudenciaInternacional($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - DERECHO INTERNACIONAL ECONÓMICO

TIPO DE DERECHO: {$tipoDerecho}
CASO: {$caso}

DATOS DEL CASO:
{$datosDerecho}

JURISPRUDENCIA INTERNACIONAL CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL DERECHO INTERNACIONAL ECONÓMICO:
   - Acuerdo General sobre Aranceles Aduaneros y Comercio (GATT)
   - Acuerdo sobre la Organización Mundial del Comercio (OMC)
   - Convención de Viena sobre Compraventa Internacional de Mercaderías
   - Convención de Nueva York sobre Reconocimiento y Ejecución de Laudos Arbitrales
   - Convención de La Haya sobre Derecho Internacional Privado
   - Jurisprudencia internacional actualizada

2. PRINCIPIOS FUNDAMENTALES:
   - No discriminación
   - Trato nacional
   - Nación más favorecida
   - Transparencia
   - Reciprocidad
   - Equilibrio de intereses

3. COMERCIO INTERNACIONAL:
   - Liberalización del comercio
   - Reducción de aranceles
   - Eliminación de barreras no arancelarias
   - Medidas de salvaguardia
   - Medidas antidumping
   - Medidas compensatorias

4. INVERSIONES INTERNACIONALES:
   - Protección de inversiones
   - Tratamiento justo y equitativo
   - Protección y seguridad plenas
   - No expropiación
   - Libre transferencia
   - Solución de controversias

5. PROPIEDAD INTELECTUAL:
   - Convención de París
   - Convención de Berna
   - Acuerdo sobre los Aspectos de los Derechos de Propiedad Intelectual
   - Patentes
   - Marcas
   - Derechos de autor

6. SERVICIOS:
   - Acuerdo General sobre el Comercio de Servicios
   - Liberalización de servicios
   - Trato nacional
   - Acceso a mercados
   - Regulación nacional
   - Reconocimiento mutuo

7. SOLUCIÓN DE CONTROVERSIAS:
   - Mecanismo de solución de controversias de la OMC
   - Arbitraje internacional
   - Mediación
   - Conciliación
   - Procedimientos especiales
   - Ejecución de decisiones

8. ORGANIZACIONES INTERNACIONALES:
   - Organización Mundial del Comercio
   - Fondo Monetario Internacional
   - Banco Mundial
   - Organización para la Cooperación y el Desarrollo Económicos
   - Organización Internacional del Trabajo
   - Organización Mundial de la Propiedad Intelectual

9. JURISPRUDENCIA INTERNACIONAL:
   - Sentencias de tribunales económicos
   - Precedentes internacionales
   - Líneas jurisprudenciales
   - Criterios de interpretación
   - Evolución jurisprudencial

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento del derecho internacional económico
    - Mejora de los mecanismos de solución
    - Desarrollo de la cooperación internacional
    - Capacitación especializada
    - Investigación académica
    - Cooperación regional

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia internacional y explicaciones detalladas del procedimiento legal internacional.";
    }

    /**
     * Consolidar análisis internacional
     */
    protected function consolidarAnalisisInternacional(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_derecho_internacional',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'jurisprudencia_consultada' => [],
            'referencias_legales' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - DERECHO INTERNACIONAL\n\n";
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
            'Fortalecer el derecho internacional público',
            'Desarrollar el derecho internacional privado',
            'Mejorar el derecho internacional humanitario',
            'Proteger los derechos humanos internacionales',
            'Desarrollar el derecho internacional económico',
            'Capacitar en derecho internacional especializado'
        ];

        // Generar referencias legales
        $consolidado['referencias_legales'] = [
            'Carta de las Naciones Unidas',
            'Declaración Universal de Derechos Humanos',
            'Convención de Viena sobre el Derecho de los Tratados',
            'Convenios de Ginebra de 1949',
            'Convención Americana sobre Derechos Humanos',
            'Jurisprudencia de la Corte Internacional de Justicia',
            'Jurisprudencia de la Corte Interamericana de Derechos Humanos',
            'Jurisprudencia de la Corte Europea de Derechos Humanos'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis internacional
     */
    protected function guardarAnalisisInternacional(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis internacional', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de derecho internacional
     */
    public function obtenerEstadisticasDerechoInternacional(): array
    {
        return [
            'total_analisis_internacional' => AnalisisIA::whereIn('especialidad', [
                'derecho_internacional_publico',
                'derecho_internacional_privado',
                'derecho_internacional_humanitario',
                'derecho_internacional_derechos_humanos',
                'derecho_internacional_economico'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'derecho_internacional_publico',
                'derecho_internacional_privado',
                'derecho_internacional_humanitario',
                'derecho_internacional_derechos_humanos',
                'derecho_internacional_economico'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'derecho_internacional_publico',
                'derecho_internacional_privado',
                'derecho_internacional_humanitario',
                'derecho_internacional_derechos_humanos',
                'derecho_internacional_economico'
            ])->sum('tokens_usados'),
            'apis_publicas_consultadas' => array_keys($this->apisPublicas)
        ];
    }
}

