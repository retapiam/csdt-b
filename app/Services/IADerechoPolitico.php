<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para Derecho Político
 * Nivel Post-Doctorado con acceso a bases de datos políticas y jurisprudencia
 */
class IADerechoPolitico
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelo;
    protected CircuitBreaker $circuitBreaker;

    // APIs públicas políticas de Colombia
    protected $apisPublicas = [
        'congreso' => 'https://www.congreso.gov.co/',
        'presidencia' => 'https://www.presidencia.gov.co/',
        'corte_constitucional' => 'https://www.corteconstitucional.gov.co/relatoria/',
        'corte_suprema' => 'https://www.cortesuprema.gov.co/',
        'consejo_estado' => 'https://www.consejodeestado.gov.co/',
        'registraduria' => 'https://www.registraduria.gov.co/',
        'cne' => 'https://www.cne.gov.co/',
        'procuraduria' => 'https://www.procuraduria.gov.co/',
        'contraloria' => 'https://www.contraloria.gov.co/',
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
     * Análisis de Derecho Electoral
     */
    public function analizarDerechoElectoral(array $datos): array
    {
        $cacheKey = 'ia_derecho_electoral_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPolitica('derecho_electoral');
            $prompt = $this->construirPromptDerechoElectoral($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPolitico('derecho_electoral', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Derecho Parlamentario
     */
    public function analizarDerechoParlamentario(array $datos): array
    {
        $cacheKey = 'ia_derecho_parlamentario_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPolitica('derecho_parlamentario');
            $prompt = $this->construirPromptDerechoParlamentario($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPolitico('derecho_parlamentario', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Derecho Administrativo
     */
    public function analizarDerechoAdministrativo(array $datos): array
    {
        $cacheKey = 'ia_derecho_administrativo_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPolitica('derecho_administrativo');
            $prompt = $this->construirPromptDerechoAdministrativo($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPolitico('derecho_administrativo', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Participación Ciudadana
     */
    public function analizarParticipacionCiudadana(array $datos): array
    {
        $cacheKey = 'ia_participacion_ciudadana_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPolitica('participacion_ciudadana');
            $prompt = $this->construirPromptParticipacionCiudadana($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPolitico('participacion_ciudadana', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Control Político
     */
    public function analizarControlPolitico(array $datos): array
    {
        $cacheKey = 'ia_control_politico_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $jurisprudencia = $this->obtenerJurisprudenciaPolitica('control_politico');
            $prompt = $this->construirPromptControlPolitico($datos, $jurisprudencia);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPolitico('control_politico', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de derecho político
     */
    public function analisisIntegralDerechoPolitico(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'derecho_electoral',
            'derecho_parlamentario',
            'derecho_administrativo',
            'participacion_ciudadana',
            'control_politico'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisPolitico($resultados, $datos);
    }

    /**
     * Obtener jurisprudencia política de fuentes públicas
     */
    protected function obtenerJurisprudenciaPolitica(string $tipo): array
    {
        $cacheKey = "jurisprudencia_politica_{$tipo}";
        
        return Cache::remember($cacheKey, 3600, function () use ($tipo) {
            $jurisprudencia = [];
            
            try {
                // Consultar Congreso
                $response = Http::timeout(30)->get($this->apisPublicas['congreso']);
                if ($response->successful()) {
                    $jurisprudencia['congreso'] = $this->procesarRespuestaCongreso($response->body(), $tipo);
                }
                
                // Consultar Presidencia
                $response = Http::timeout(30)->get($this->apisPublicas['presidencia']);
                if ($response->successful()) {
                    $jurisprudencia['presidencia'] = $this->procesarRespuestaPresidencia($response->body(), $tipo);
                }
                
                // Consultar Corte Constitucional
                $response = Http::timeout(30)->get($this->apisPublicas['corte_constitucional']);
                if ($response->successful()) {
                    $jurisprudencia['corte_constitucional'] = $this->procesarRespuestaCorteConstitucional($response->body(), $tipo);
                }
                
                // Consultar Registraduría
                $response = Http::timeout(30)->get($this->apisPublicas['registraduria']);
                if ($response->successful()) {
                    $jurisprudencia['registraduria'] = $this->procesarRespuestaRegistraduria($response->body(), $tipo);
                }
                
            } catch (\Exception $e) {
                Log::error('Error obteniendo jurisprudencia política', [
                    'tipo' => $tipo,
                    'error' => $e->getMessage()
                ]);
            }
            
            return $jurisprudencia;
        });
    }

    /**
     * Procesar respuesta del Congreso
     */
    protected function procesarRespuestaCongreso(string $html, string $tipo): array
    {
        $patrones = [
            'leyes' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Ley[^<]*)<\/a>/i',
            'proyectos' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Proyecto[^<]*)<\/a>/i',
            'actos_legislativos' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Acto[^<]*)<\/a>/i',
            'resoluciones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Resolución[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'Congreso de la República'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Procesar respuesta de Presidencia
     */
    protected function procesarRespuestaPresidencia(string $html, string $tipo): array
    {
        $patrones = [
            'decretos' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Decreto[^<]*)<\/a>/i',
            'leyes' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Ley[^<]*)<\/a>/i',
            'resoluciones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Resolución[^<]*)<\/a>/i',
            'directivas' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Directiva[^<]*)<\/a>/i'
        ];
        
        $resultados = [];
        foreach ($patrones as $categoria => $patron) {
            preg_match_all($patron, $html, $matches);
            if (!empty($matches[1])) {
                $resultados[$categoria] = array_map(function($url, $titulo) {
                    return [
                        'url' => $url,
                        'titulo' => trim($titulo),
                        'fuente' => 'Presidencia de la República'
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
     * Procesar respuesta de Registraduría
     */
    protected function procesarRespuestaRegistraduria(string $html, string $tipo): array
    {
        $patrones = [
            'resoluciones' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Resolución[^<]*)<\/a>/i',
            'circulares' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Circular[^<]*)<\/a>/i',
            'conceptos' => '/<a[^>]*href="([^"]*)"[^>]*>([^<]*Concepto[^<]*)<\/a>/i',
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
                        'fuente' => 'Registraduría Nacional del Estado Civil'
                    ];
                }, $matches[1], $matches[2]);
            }
        }
        
        return $resultados;
    }

    /**
     * Ejecutar análisis político
     */
    protected function ejecutarAnalisisPolitico(string $tipoAnalisis, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en derecho político de nivel post-doctorado. " .
                                   "Especializado en derecho electoral, parlamentario, administrativo, " .
                                   "participación ciudadana y control político. Tienes acceso a jurisprudencia " .
                                   "actualizada del Congreso, Presidencia, Corte Constitucional y Registraduría. " .
                                   "Proporciona análisis exhaustivos con fundamentación académica sólida, " .
                                   "referencias específicas a jurisprudencia política y explicaciones detalladas " .
                                   "del procedimiento legal político."
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

            $this->guardarAnalisisPolitico($tipoAnalisis, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'tipo_analisis' => $tipoAnalisis,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $this->modelo,
                'jurisprudencia_consultada' => $this->obtenerJurisprudenciaPolitica($tipoAnalisis),
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis político: ' . $response->body());
    }

    /**
     * Construir prompt para derecho electoral
     */
    protected function construirPromptDerechoElectoral(array $datos, array $jurisprudencia): string
    {
        $tipoElectoral = $datos['tipo_electoral'] ?? 'general';
        $datosElectoral = $datos['datos_electoral'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Derecho Electoral';

        $jurisprudenciaText = $this->formatearJurisprudenciaPolitica($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - DERECHO ELECTORAL

TIPO ELECTORAL: {$tipoElectoral}
CASO: {$caso}

DATOS ELECTORALES:
{$datosElectoral}

JURISPRUDENCIA POLÍTICA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL DERECHO ELECTORAL:
   - Constitución Política (Art. 40, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113)
   - Ley 5 de 1992 (Ley Orgánica del Congreso)
   - Ley 130 de 1994 (Ley de Partidos y Movimientos Políticos)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de universalidad
   - Principio de igualdad
   - Principio de libertad
   - Principio de secreto
   - Principio de transparencia
   - Principio de publicidad

3. SUJETOS ELECTORALES:
   - Ciudadanos
   - Partidos políticos
   - Movimientos políticos
   - Grupos significativos de ciudadanos
   - Candidatos
   - Representantes

4. DERECHOS ELECTORALES:
   - Derecho al voto
   - Derecho a ser elegido
   - Derecho a la participación
   - Derecho a la información
   - Derecho a la transparencia
   - Derecho a la fiscalización

5. PROCEDIMIENTO ELECTORAL:
   - Inscripción de candidatos
   - Campaña electoral
   - Votación
   - Escrutinio
   - Proclamación
   - Recursos

6. ORGANIZACIÓN ELECTORAL:
   - Registraduría Nacional del Estado Civil
   - Consejo Nacional Electoral
   - Comisiones de escrutinio
   - Jurados de votación
   - Testigos electorales
   - Observadores

7. FINANCIACIÓN POLÍTICA:
   - Financiación pública
   - Financiación privada
   - Límites de gastos
   - Rendición de cuentas
   - Control y vigilancia
   - Sanciones

8. JURISPRUDENCIA POLÍTICA:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Resoluciones de la Registraduría
   - Resoluciones del CNE
   - Circulares electorales

9. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento democrático
    - Transparencia electoral
    - Participación ciudadana
    - Capacitación especializada
    - Asesoría profesional
    - Observación electoral

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia política y explicaciones detalladas del procedimiento legal político.";
    }

    /**
     * Formatear jurisprudencia política para el prompt
     */
    protected function formatearJurisprudenciaPolitica(array $jurisprudencia): string
    {
        $texto = "JURISPRUDENCIA POLÍTICA CONSULTADA:\n\n";
        
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
     * Construir prompt para derecho parlamentario
     */
    protected function construirPromptDerechoParlamentario(array $datos, array $jurisprudencia): string
    {
        $tipoParlamentario = $datos['tipo_parlamentario'] ?? 'general';
        $datosParlamentario = $datos['datos_parlamentario'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Derecho Parlamentario';

        $jurisprudenciaText = $this->formatearJurisprudenciaPolitica($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - DERECHO PARLAMENTARIO

TIPO PARLAMENTARIO: {$tipoParlamentario}
CASO: {$caso}

DATOS PARLAMENTARIOS:
{$datosParlamentario}

JURISPRUDENCIA POLÍTICA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL DERECHO PARLAMENTARIO:
   - Constitución Política (Art. 113-150)
   - Ley 5 de 1992 (Ley Orgánica del Congreso)
   - Reglamento del Congreso
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de representación
   - Principio de deliberación
   - Principio de publicidad
   - Principio de transparencia
   - Principio de responsabilidad
   - Principio de eficiencia

3. ESTRUCTURA PARLAMENTARIA:
   - Congreso de la República
   - Cámara de Representantes
   - Senado de la República
   - Comisiones
   - Mesa Directiva
   - Secretaría General

4. FUNCIONES PARLAMENTARIAS:
   - Función legislativa
   - Función de control político
   - Función judicial
   - Función electoral
   - Función administrativa
   - Función de protocolo

5. PROCEDIMIENTO LEGISLATIVO:
   - Iniciativa legislativa
   - Trámite en comisiones
   - Discusión en plenaria
   - Sanción presidencial
   - Promulgación
   - Publicación

6. CONTROL POLÍTICO:
   - Moción de censura
   - Interpelación
   - Citación
   - Comisión de investigación
   - Audiencias públicas
   - Informes

7. JURISPRUDENCIA POLÍTICA:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Resoluciones del Congreso
   - Reglamentos parlamentarios
   - Circulares parlamentarias

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento parlamentario
    - Transparencia legislativa
    - Participación ciudadana
    - Capacitación especializada
    - Asesoría profesional
    - Control democrático

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina parlamentaria
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia política y explicaciones detalladas del procedimiento legal político.";
    }

    /**
     * Construir prompt para derecho administrativo
     */
    protected function construirPromptDerechoAdministrativo(array $datos, array $jurisprudencia): string
    {
        $tipoAdministrativo = $datos['tipo_administrativo'] ?? 'general';
        $datosAdministrativo = $datos['datos_administrativo'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Derecho Administrativo';

        $jurisprudenciaText = $this->formatearJurisprudenciaPolitica($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - DERECHO ADMINISTRATIVO

TIPO ADMINISTRATIVO: {$tipoAdministrativo}
CASO: {$caso}

DATOS ADMINISTRATIVOS:
{$datosAdministrativo}

JURISPRUDENCIA POLÍTICA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL DERECHO ADMINISTRATIVO:
   - Constitución Política (Art. 113-150)
   - Ley 1437 de 2011 (Código de Procedimiento Administrativo)
   - Ley 489 de 1998 (Ley Orgánica de la Administración Pública)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia del Consejo de Estado

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de legalidad
   - Principio de igualdad
   - Principio de transparencia
   - Principio de eficiencia
   - Principio de responsabilidad
   - Principio de participación

3. ESTRUCTURA ADMINISTRATIVA:
   - Rama Ejecutiva
   - Ministerios
   - Departamentos Administrativos
   - Superintendencias
   - Unidades Administrativas Especiales
   - Entidades descentralizadas

4. ACTOS ADMINISTRATIVOS:
   - Concepto y naturaleza
   - Clasificación
   - Requisitos
   - Efectos
   - Nulidad
   - Revocación

5. PROCEDIMIENTO ADMINISTRATIVO:
   - Iniciación
   - Trámite
   - Resolución
   - Recursos
   - Ejecución
   - Control

6. CONTRATACIÓN PÚBLICA:
   - Ley 80 de 1993
   - Ley 1150 de 2007
   - Procedimientos de selección
   - Contratos estatales
   - Supervisión y control
   - Sanciones

7. JURISPRUDENCIA POLÍTICA:
   - Sentencias de la Corte Constitucional
   - Sentencias del Consejo de Estado
   - Resoluciones administrativas
   - Circulares administrativas
   - Conceptos administrativos

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
    - Cumplimiento normativo
    - Transparencia administrativa
    - Participación ciudadana
    - Capacitación especializada
    - Asesoría profesional
    - Control administrativo

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina administrativa
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia política y explicaciones detalladas del procedimiento legal político.";
    }

    /**
     * Construir prompt para participación ciudadana
     */
    protected function construirPromptParticipacionCiudadana(array $datos, array $jurisprudencia): string
    {
        $tipoParticipacion = $datos['tipo_participacion'] ?? 'general';
        $datosParticipacion = $datos['datos_participacion'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Participación Ciudadana';

        $jurisprudenciaText = $this->formatearJurisprudenciaPolitica($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - PARTICIPACIÓN CIUDADANA

TIPO DE PARTICIPACIÓN: {$tipoParticipacion}
CASO: {$caso}

DATOS DE PARTICIPACIÓN:
{$datosParticipacion}

JURISPRUDENCIA POLÍTICA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE PARTICIPACIÓN CIUDADANA:
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

7. JURISPRUDENCIA POLÍTICA:
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

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia política y explicaciones detalladas del procedimiento legal político.";
    }

    /**
     * Construir prompt para control político
     */
    protected function construirPromptControlPolitico(array $datos, array $jurisprudencia): string
    {
        $tipoControl = $datos['tipo_control'] ?? 'general';
        $datosControl = $datos['datos_control'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Control Político';

        $jurisprudenciaText = $this->formatearJurisprudenciaPolitica($jurisprudencia);

        return "ANÁLISIS POST-DOCTORADO - CONTROL POLÍTICO

TIPO DE CONTROL: {$tipoControl}
CASO: {$caso}

DATOS DEL CONTROL:
{$datosControl}

JURISPRUDENCIA POLÍTICA CONSULTADA:
{$jurisprudenciaText}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL CONTROL POLÍTICO:
   - Constitución Política (Art. 113-150)
   - Ley 5 de 1992 (Ley Orgánica del Congreso)
   - Ley 1757 de 2015 (Ley de Transparencia y Acceso a la Información)
   - Jurisprudencia constitucional actualizada
   - Jurisprudencia de la Corte Suprema

2. PRINCIPIOS FUNDAMENTALES:
   - Principio de separación de poderes
   - Principio de equilibrio de poderes
   - Principio de control mutuo
   - Principio de transparencia
   - Principio de responsabilidad
   - Principio de eficiencia

3. SUJETOS DEL CONTROL:
   - Congreso de la República
   - Cámara de Representantes
   - Senado de la República
   - Comisiones
   - Órganos de control
   - Ciudadanos

4. OBJETOS DEL CONTROL:
   - Rama Ejecutiva
   - Ministerios
   - Departamentos Administrativos
   - Superintendencias
   - Entidades descentralizadas
   - Funcionarios públicos

5. MECANISMOS DE CONTROL:
   - Moción de censura
   - Interpelación
   - Citación
   - Comisión de investigación
   - Audiencias públicas
   - Informes

6. PROCEDIMIENTO DE CONTROL:
   - Iniciación
   - Trámite
   - Resolución
   - Recursos
   - Ejecución
   - Seguimiento

7. JURISPRUDENCIA POLÍTICA:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Resoluciones del Congreso
   - Reglamentos de control
   - Circulares de control

8. CASOS PRÁCTICOS:
   - Análisis de casos similares
   - Soluciones jurisprudenciales
   - Criterios de aplicación
   - Interpretación judicial
   - Efectos de las sentencias

9. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento del control
    - Transparencia del control
    - Participación ciudadana
    - Capacitación especializada
    - Asesoría profesional
    - Control democrático

10. BIBLIOGRAFÍA Y REFERENCIAS:
    - Doctrina de control
    - Tratados internacionales
    - Sentencias relevantes
    - Estudios especializados
    - Fuentes académicas

Proporciona un análisis detallado con fundamentación académica sólida, referencias específicas a jurisprudencia política y explicaciones detalladas del procedimiento legal político.";
    }

    /**
     * Consolidar análisis político
     */
    protected function consolidarAnalisisPolitico(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_derecho_politico',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'jurisprudencia_consultada' => [],
            'referencias_legales' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - DERECHO POLÍTICO\n\n";
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
            'Fortalecer la democracia electoral',
            'Desarrollar el derecho parlamentario',
            'Mejorar la administración pública',
            'Promover la participación ciudadana',
            'Fortalecer el control político',
            'Capacitar en derecho político especializado'
        ];

        // Generar referencias legales
        $consolidado['referencias_legales'] = [
            'Constitución Política de Colombia',
            'Ley 5 de 1992 (Ley Orgánica del Congreso)',
            'Ley 130 de 1994 (Ley de Partidos y Movimientos Políticos)',
            'Ley 134 de 1994 (Ley de Mecanismos de Participación Ciudadana)',
            'Jurisprudencia de la Corte Constitucional',
            'Jurisprudencia de la Corte Suprema',
            'Resoluciones del Congreso',
            'Resoluciones de la Registraduría'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis político
     */
    protected function guardarAnalisisPolitico(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis político', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de derecho político
     */
    public function obtenerEstadisticasDerechoPolitico(): array
    {
        return [
            'total_analisis_politico' => AnalisisIA::whereIn('especialidad', [
                'derecho_electoral',
                'derecho_parlamentario',
                'derecho_administrativo',
                'participacion_ciudadana',
                'control_politico'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'derecho_electoral',
                'derecho_parlamentario',
                'derecho_administrativo',
                'participacion_ciudadana',
                'control_politico'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'derecho_electoral',
                'derecho_parlamentario',
                'derecho_administrativo',
                'participacion_ciudadana',
                'control_politico'
            ])->sum('tokens_usados'),
            'apis_publicas_consultadas' => array_keys($this->apisPublicas)
        ];
    }
}

