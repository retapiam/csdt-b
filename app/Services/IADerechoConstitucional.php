<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para Derecho Constitucional
 * Nivel Post-Doctorado con acceso a bases de datos públicas y jurisprudencia
 */
class IADerechoConstitucional
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelo;
    protected CircuitBreaker $circuitBreaker;

    // APIs públicas de Colombia
    protected $apisPublicas = [
        'corte_constitucional' => 'https://www.corteconstitucional.gov.co/relatoria/',
        'corte_suprema' => 'https://www.cortesuprema.gov.co/',
        'consejo_estado' => 'https://www.consejodeestado.gov.co/',
        'fiscalia' => 'https://www.fiscalia.gov.co/',
        'procuraduria' => 'https://www.procuraduria.gov.co/',
        'defensoria' => 'https://www.defensoria.gov.co/',
        'contraloria' => 'https://www.contraloria.gov.co/',
        'congreso' => 'https://www.congreso.gov.co/',
        'presidencia' => 'https://www.presidencia.gov.co/',
        'minjusticia' => 'https://www.minjusticia.gov.co/'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->modelo = config('services.openai.model', 'gpt-4');
        $this->circuitBreaker = new CircuitBreaker('openai', 5, 60, 3);
    }

    /**
     * Análisis de Derechos Fundamentales
     */
    public function analizarDerechosFundamentales(array $datos): array
    {
        $cacheKey = 'ia_derechos_fundamentales_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaConstitucional('derechos_fundamentales');
            $prompt = $this->construirPromptDerechosFundamentales($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisConstitucional('derechos_fundamentales', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Control Constitucional
     */
    public function analizarControlConstitucional(array $datos): array
    {
        $cacheKey = 'ia_control_constitucional_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaConstitucional('control_constitucional');
            $prompt = $this->construirPromptControlConstitucional($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisConstitucional('control_constitucional', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Acciones Constitucionales
     */
    public function analizarAccionesConstitucionales(array $datos): array
    {
        $cacheKey = 'ia_acciones_constitucionales_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaConstitucional('acciones_constitucionales');
            $prompt = $this->construirPromptAccionesConstitucionales($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisConstitucional('acciones_constitucionales', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Estructura del Estado
     */
    public function analizarEstructuraEstado(array $datos): array
    {
        $cacheKey = 'ia_estructura_estado_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaConstitucional('estructura_estado');
            $prompt = $this->construirPromptEstructuraEstado($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisConstitucional('estructura_estado', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Reforma Constitucional
     */
    public function analizarReformaConstitucional(array $datos): array
    {
        $cacheKey = 'ia_reforma_constitucional_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaConstitucional('reforma_constitucional');
            $prompt = $this->construirPromptReformaConstitucional($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisConstitucional('reforma_constitucional', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de derecho constitucional
     */
    public function analisisIntegralDerechoConstitucional(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'derechos_fundamentales',
            'control_constitucional',
            'acciones_constitucionales',
            'estructura_estado',
            'reforma_constitucional'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisConstitucional($resultados, $datos);
    }

    /**
     * Obtener jurisprudencia constitucional de fuentes públicas
     */
    protected function obtenerJurisprudenciaConstitucional(string $tipo): array
    {
        $cacheKey = "jurisprudencia_constitucional_{$tipo}";
        
        return Cache::remember($cacheKey, 3600, function () use ($tipo) {
            $jurisprudencia = [];
            
            try {
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
                Log::error('Error obteniendo jurisprudencia constitucional', [
                    'tipo' => $tipo,
                    'error' => $e->getMessage()
                ]);
            }
            
            return $jurisprudencia;
        });
    }

    /**
     * Procesar respuesta de Corte Constitucional
     */
    protected function procesarRespuestaCorteConstitucional(string $html, string $tipo): array
    {
        // Extraer información relevante del HTML
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
        // Similar a Corte Constitucional pero con patrones específicos
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
        // Patrones específicos para Consejo de Estado
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
     * Ejecutar análisis constitucional
     */
    protected function ejecutarAnalisisConstitucional(string $tipoAnalisis, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en derecho constitucional de nivel post-doctorado. " .
                                   "Especializado en análisis de derechos fundamentales, control constitucional, " .
                                   "acciones constitucionales, estructura del estado y reforma constitucional. " .
                                   "Tienes acceso a jurisprudencia actualizada de la Corte Constitucional, " .
                                   "Corte Suprema y Consejo de Estado. Proporciona análisis exhaustivos con " .
                                   "fundamentación académica sólida, referencias específicas a sentencias y " .
                                   "jurisprudencia vigente, y explicaciones detalladas del procedimiento legal."
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

            $this->guardarAnalisisConstitucional($tipoAnalisis, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'tipo_analisis' => $tipoAnalisis,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $this->modelo,
                'jurisprudencia_consultada' => $this->obtenerJurisprudenciaConstitucional($tipoAnalisis),
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis constitucional: ' . $response->body());
    }

    /**
     * Construir prompt para derechos fundamentales
     */
    protected function construirPromptDerechosFundamentales(array $datos, array $jurisprudencia): string
    {
        $tipoDerecho = $datos['tipo_derecho'] ?? 'general';
        $datosDerecho = $datos['datos_derecho'] ?? '';
        $caso = $datos['caso'] ?? 'Caso Constitucional';

        $jurisprudenciaText = $this->formatearJurisprudencia($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - DERECHOS FUNDAMENTALES

TIPO DE DERECHO: {$tipoDerecho}
CASO: {$caso}

DATOS DEL CASO:
{$datosDerecho}

JURISPRUDENCIA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE DERECHOS FUNDAMENTALES:
   - Constitución Política (Art. 1-113)
   - Declaración Universal de Derechos Humanos
   - Pacto Internacional de Derechos Civiles y Políticos
   - Convención Americana sobre Derechos Humanos
   - Jurisprudencia constitucional actualizada

2. CLASIFICACIÓN DE DERECHOS FUNDAMENTALES:
   - Derechos de primera generación (civiles y políticos)
   - Derechos de segunda generación (económicos, sociales y culturales)
   - Derechos de tercera generación (solidaridad)
   - Derechos de cuarta generación (digitales y ambientales)

3. CARACTERÍSTICAS DE LOS DERECHOS FUNDAMENTALES:
   - Universalidad
   - Indivisibilidad
   - Interdependencia
   - Progresividad
   - Irreversibilidad
   - Efectividad

4. LÍMITES Y RESTRICCIONES:
   - Límites constitucionales
   - Restricciones legales
   - Estado de excepción
   - Proporcionalidad
   - Necesidad
   - Idoneidad

5. MECANISMOS DE PROTECCIÓN:
   - Acción de tutela
   - Acción de cumplimiento
   - Acción popular
   - Habeas corpus
   - Habeas data
   - Acción de grupo

6. JURISPRUDENCIA CONSTITUCIONAL:
   - Sentencias de la Corte Constitucional
   - Precedentes constitucionales
   - Líneas jurisprudenciales
   - Criterios de interpretación
   - Evolución jurisprudencial

7. ANÁLISIS COMPARADO:
   - Derecho comparado
   - Tribunales internacionales
   - Estándares internacionales
   - Buenas prácticas
   - Tendencias globales

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
   - Fortalecimiento de la protección
   - Mejora de los mecanismos
   - Desarrollo jurisprudencial
   - Capacitación especializada
   - Cooperación internacional

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina constitucional
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia y explicaciones detalladas del procedimiento legal.";
    }

    /**
     * Formatear jurisprudencia para el prompt
     */
    protected function formatearJurisprudencia(array $jurisprudencia): string
    {
        $texto = "JURISPRUDENCIA CONSULTADA:\n\n";
        
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
     * Construir prompt para control constitucional
     */
    protected function construirPromptControlConstitucional(array $datos, array $jurisprudencia): string
    {
        $tipoControl = $datos['tipo_control'] ?? 'general';
        $datosControl = $datos['datos_control'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Control Constitucional';

        $jurisprudenciaText = $this->formatearJurisprudencia($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - CONTROL CONSTITUCIONAL

TIPO DE CONTROL: {$tipoControl}
CASO: {$caso}

DATOS DEL CONTROL:
{$datosControl}

JURISPRUDENCIA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL CONTROL CONSTITUCIONAL:
   - Constitución Política (Art. 241-250)
   - Ley 270 de 1996 (Ley Estatutaria de la Administración de Justicia)
   - Jurisprudencia constitucional actualizada
   - Doctrina especializada

2. TIPOS DE CONTROL CONSTITUCIONAL:
   - Control por vía de acción
   - Control por vía de excepción
   - Control automático
   - Control previo
   - Control posterior

3. COMPETENCIAS DE LA CORTE CONSTITUCIONAL:
   - Guarda de la integridad y supremacía de la Constitución
   - Decidir sobre las demandas de inconstitucionalidad
   - Decidir sobre las demandas de nulidad
   - Decidir sobre las consultas previas
   - Decidir sobre las tutelas contra providencias judiciales

4. PROCEDIMIENTO DE CONTROL:
   - Demanda de inconstitucionalidad
   - Demanda de nulidad
   - Consulta previa
   - Tutela contra providencias judiciales
   - Revisión de tutelas

5. CRITERIOS DE DECISIÓN:
   - Criterios de interpretación constitucional
   - Principios de interpretación
   - Métodos de interpretación
   - Criterios de ponderación
   - Criterios de proporcionalidad

6. EFECTOS DE LAS SENTENCIAS:
   - Efectos erga omnes
   - Efectos inter partes
   - Efectos ex nunc
   - Efectos ex tunc
   - Efectos pro futuro

7. JURISPRUDENCIA CONSTITUCIONAL:
   - Sentencias de la Corte Constitucional
   - Precedentes constitucionales
   - Líneas jurisprudenciales
   - Criterios de interpretación
   - Evolución jurisprudencial

8. ANÁLISIS COMPARADO:
   - Derecho comparado
   - Tribunales constitucionales
   - Estándares internacionales
   - Buenas prácticas
   - Tendencias globales

9. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento del control
    - Mejora de los procedimientos
    - Desarrollo jurisprudencial
    - Capacitación especializada
    - Cooperación internacional

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia y explicaciones detalladas del procedimiento legal.";
    }

    /**
     * Construir prompt para acciones constitucionales
     */
    protected function construirPromptAccionesConstitucionales(array $datos, array $jurisprudencia): string
    {
        $tipoAccion = $datos['tipo_accion'] ?? 'general';
        $datosAccion = $datos['datos_accion'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Acción Constitucional';

        $jurisprudenciaText = $this->formatearJurisprudencia($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - ACCIONES CONSTITUCIONALES

TIPO DE ACCIÓN: {$tipoAccion}
CASO: {$caso}

DATOS DE LA ACCIÓN:
{$datosAccion}

JURISPRUDENCIA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE ACCIONES CONSTITUCIONALES:
   - Constitución Política (Art. 86-113)
   - Decreto 2591 de 1991 (Reglamentación de la Acción de Tutela)
   - Ley 472 de 1998 (Acción Popular y de Grupo)
   - Ley 393 de 1997 (Acción de Cumplimiento)
   - Jurisprudencia constitucional actualizada

2. TIPOS DE ACCIONES CONSTITUCIONALES:
   - Acción de tutela
   - Acción de cumplimiento
   - Acción popular
   - Acción de grupo
   - Habeas corpus
   - Habeas data

3. ACCIÓN DE TUTELA:
   - Concepto y naturaleza
   - Requisitos de procedencia
   - Procedimiento
   - Competencia
   - Efectos de la sentencia
   - Recursos

4. ACCIÓN DE CUMPLIMIENTO:
   - Concepto y naturaleza
   - Requisitos de procedencia
   - Procedimiento
   - Competencia
   - Efectos de la sentencia
   - Recursos

5. ACCIÓN POPULAR:
   - Concepto y naturaleza
   - Requisitos de procedencia
   - Procedimiento
   - Competencia
   - Efectos de la sentencia
   - Recursos

6. ACCIÓN DE GRUPO:
   - Concepto y naturaleza
   - Requisitos de procedencia
   - Procedimiento
   - Competencia
   - Efectos de la sentencia
   - Recursos

7. HABEAS CORPUS:
   - Concepto y naturaleza
   - Requisitos de procedencia
   - Procedimiento
   - Competencia
   - Efectos de la sentencia
   - Recursos

8. HABEAS DATA:
   - Concepto y naturaleza
   - Requisitos de procedencia
   - Procedimiento
   - Competencia
   - Efectos de la sentencia
   - Recursos

9. JURISPRUDENCIA CONSTITUCIONAL:
   - Sentencias de la Corte Constitucional
   - Precedentes constitucionales
   - Líneas jurisprudenciales
   - Criterios de interpretación
   - Evolución jurisprudencial

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de las acciones
    - Mejora de los procedimientos
    - Desarrollo jurisprudencial
    - Capacitación especializada
    - Cooperación internacional

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia y explicaciones detalladas del procedimiento legal.";
    }

    /**
     * Construir prompt para estructura del estado
     */
    protected function construirPromptEstructuraEstado(array $datos, array $jurisprudencia): string
    {
        $tipoEstructura = $datos['tipo_estructura'] ?? 'general';
        $datosEstructura = $datos['datos_estructura'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Estructura del Estado';

        $jurisprudenciaText = $this->formatearJurisprudencia($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - ESTRUCTURA DEL ESTADO

TIPO DE ESTRUCTURA: {$tipoEstructura}
CASO: {$caso}

DATOS DE LA ESTRUCTURA:
{$datosEstructura}

JURISPRUDENCIA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE LA ESTRUCTURA DEL ESTADO:
   - Constitución Política (Art. 113-150)
   - Ley 5 de 1992 (Ley Orgánica del Congreso)
   - Ley 270 de 1996 (Ley Estatutaria de la Administración de Justicia)
   - Jurisprudencia constitucional actualizada

2. RAMAS DEL PODER PÚBLICO:
   - Rama Legislativa
   - Rama Ejecutiva
   - Rama Judicial
   - Órganos de Control
   - Órganos Autónomos

3. RAMA LEGISLATIVA:
   - Congreso de la República
   - Cámara de Representantes
   - Senado de la República
   - Comisiones
   - Funciones
   - Competencias

4. RAMA EJECUTIVA:
   - Presidencia de la República
   - Vicepresidencia
   - Ministerios
   - Departamentos Administrativos
   - Superintendencias
   - Unidades Administrativas Especiales

5. RAMA JUDICIAL:
   - Corte Suprema de Justicia
   - Corte Constitucional
   - Consejo de Estado
   - Consejo Superior de la Judicatura
   - Fiscalía General de la Nación
   - Tribunales y Juzgados

6. ÓRGANOS DE CONTROL:
   - Contraloría General de la República
   - Procuraduría General de la Nación
   - Defensoría del Pueblo
   - Veedurías Ciudadanas
   - Contralorías Departamentales y Municipales

7. ÓRGANOS AUTÓNOMOS:
   - Banco de la República
   - Comisión Nacional de Televisión
   - Registraduría Nacional del Estado Civil
   - Organización Electoral
   - Comisión Nacional del Servicio Civil

8. PRINCIPIOS CONSTITUCIONALES:
   - Separación de poderes
   - Equilibrio de poderes
   - Control mutuo
   - Colaboración armónica
   - Responsabilidad

9. JURISPRUDENCIA CONSTITUCIONAL:
   - Sentencias de la Corte Constitucional
   - Precedentes constitucionales
   - Líneas jurisprudenciales
   - Criterios de interpretación
   - Evolución jurisprudencial

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de la estructura
    - Mejora de la coordinación
    - Desarrollo institucional
    - Capacitación especializada
    - Cooperación internacional

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia y explicaciones detalladas del procedimiento legal.";
    }

    /**
     * Construir prompt para reforma constitucional
     */
    protected function construirPromptReformaConstitucional(array $datos, array $jurisprudencia): string
    {
        $tipoReforma = $datos['tipo_reforma'] ?? 'general';
        $datosReforma = $datos['datos_reforma'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Reforma Constitucional';

        $jurisprudenciaText = $this->formatearJurisprudencia($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - REFORMA CONSTITUCIONAL

TIPO DE REFORMA: {$tipoReforma}
CASO: {$caso}

DATOS DE LA REFORMA:
{$datosReforma}

JURISPRUDENCIA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE LA REFORMA CONSTITUCIONAL:
   - Constitución Política (Art. 374-379)
   - Ley 5 de 1992 (Ley Orgánica del Congreso)
   - Jurisprudencia constitucional actualizada
   - Doctrina especializada

2. TIPOS DE REFORMA CONSTITUCIONAL:
   - Reforma por vía de acto legislativo
   - Reforma por vía de referendo
   - Reforma por vía de asamblea constituyente
   - Reforma por vía de convención constituyente

3. REFORMA POR VÍA DE ACTO LEGISLATIVO:
   - Concepto y naturaleza
   - Procedimiento
   - Requisitos
   - Competencia
   - Efectos
   - Control constitucional

4. REFORMA POR VÍA DE REFERENDO:
   - Concepto y naturaleza
   - Procedimiento
   - Requisitos
   - Competencia
   - Efectos
   - Control constitucional

5. REFORMA POR VÍA DE ASAMBLEA CONSTITUYENTE:
   - Concepto y naturaleza
   - Procedimiento
   - Requisitos
   - Competencia
   - Efectos
   - Control constitucional

6. REFORMA POR VÍA DE CONVENCIÓN CONSTITUYENTE:
   - Concepto y naturaleza
   - Procedimiento
   - Requisitos
   - Competencia
   - Efectos
   - Control constitucional

7. LÍMITES A LA REFORMA CONSTITUCIONAL:
   - Límites materiales
   - Límites formales
   - Límites temporales
   - Límites sustanciales
   - Límites procedimentales

8. CONTROL CONSTITUCIONAL DE LA REFORMA:
   - Competencia de la Corte Constitucional
   - Criterios de control
   - Efectos de las sentencias
   - Recursos
   - Ejecución

9. JURISPRUDENCIA CONSTITUCIONAL:
   - Sentencias de la Corte Constitucional
   - Precedentes constitucionales
   - Líneas jurisprudenciales
   - Criterios de interpretación
   - Evolución jurisprudencial

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de la reforma
    - Mejora de los procedimientos
    - Desarrollo institucional
    - Capacitación especializada
    - Cooperación internacional

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia y explicaciones detalladas del procedimiento legal.";
    }

    /**
     * Consolidar análisis constitucional
     */
    protected function consolidarAnalisisConstitucional(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_derecho_constitucional',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'jurisprudencia_consultada' => [],
            'referencias_legales' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - DERECHO CONSTITUCIONAL\n\n";
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
            'Fortalecer la protección de derechos fundamentales',
            'Mejorar los mecanismos de control constitucional',
            'Desarrollar las acciones constitucionales',
            'Optimizar la estructura del estado',
            'Modernizar los procedimientos de reforma constitucional',
            'Capacitar en derecho constitucional especializado'
        ];

        // Generar referencias legales
        $consolidado['referencias_legales'] = [
            'Constitución Política de Colombia',
            'Ley 270 de 1996 (Ley Estatutaria de la Administración de Justicia)',
            'Decreto 2591 de 1991 (Reglamentación de la Acción de Tutela)',
            'Ley 472 de 1998 (Acción Popular y de Grupo)',
            'Ley 393 de 1997 (Acción de Cumplimiento)',
            'Jurisprudencia de la Corte Constitucional',
            'Jurisprudencia de la Corte Suprema de Justicia',
            'Jurisprudencia del Consejo de Estado'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis constitucional
     */
    protected function guardarAnalisisConstitucional(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis constitucional', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de derecho constitucional
     */
    public function obtenerEstadisticasDerechoConstitucional(): array
    {
        return [
            'total_analisis_constitucional' => AnalisisIA::whereIn('especialidad', [
                'derechos_fundamentales',
                'control_constitucional',
                'acciones_constitucionales',
                'estructura_estado',
                'reforma_constitucional'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'derechos_fundamentales',
                'control_constitucional',
                'acciones_constitucionales',
                'estructura_estado',
                'reforma_constitucional'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'derechos_fundamentales',
                'control_constitucional',
                'acciones_constitucionales',
                'estructura_estado',
                'reforma_constitucional'
            ])->sum('tokens_usados'),
            'apis_publicas_consultadas' => array_keys($this->apisPublicas)
        ];
    }
}

