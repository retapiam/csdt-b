<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para Investigación Forense
 * Nivel Post-Doctorado especializado en análisis forense, evidencia digital y criminología
 */
class IAInvestigacionForense
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelo;
    protected CircuitBreaker $circuitBreaker;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->modelo = config('services.openai.model', 'gpt-4');
        $this->circuitBreaker = new CircuitBreaker('openai', 5, 60, 3);
    }

    /**
     * Análisis Forense Digital
     */
    public function analizarForenseDigital(array $datos): array
    {
        $cacheKey = 'ia_forense_digital_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptForenseDigital($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisForense('forense_digital', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Evidencia Digital
     */
    public function analizarEvidenciaDigital(array $datos): array
    {
        $cacheKey = 'ia_evidencia_digital_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptEvidenciaDigital($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisForense('evidencia_digital', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Cibercrimen
     */
    public function analizarCibercrimen(array $datos): array
    {
        $cacheKey = 'ia_cibercrimen_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptCibercrimen($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisForense('cibercrimen', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Perfilación Criminal
     */
    public function analizarPerfilacionCriminal(array $datos): array
    {
        $cacheKey = 'ia_perfilacion_criminal_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptPerfilacionCriminal($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisForense('perfilacion_criminal', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Criminología
     */
    public function analizarCriminologia(array $datos): array
    {
        $cacheKey = 'ia_criminologia_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptCriminologia($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisForense('criminologia', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Victimología
     */
    public function analizarVictimologia(array $datos): array
    {
        $cacheKey = 'ia_victimologia_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptVictimologia($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisForense('victimologia', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Psicología Forense
     */
    public function analizarPsicologiaForense(array $datos): array
    {
        $cacheKey = 'ia_psicologia_forense_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptPsicologiaForense($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisForense('psicologia_forense', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de investigación forense
     */
    public function analisisIntegralInvestigacionForense(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'forense_digital',
            'evidencia_digital',
            'cibercrimen',
            'perfilacion_criminal',
            'criminologia',
            'victimologia',
            'psicologia_forense'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisForense($resultados, $datos);
    }

    /**
     * Ejecutar análisis forense
     */
    protected function ejecutarAnalisisForense(string $tipoAnalisis, string $prompt, array $datos): array
    {
        $response = Http::timeout(90)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $this->modelo,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Eres un experto en investigación forense de nivel post-doctorado. " .
                                   "Especializado en análisis forense digital, evidencia digital, " .
                                   "cibercrimen, perfilación criminal, criminología, victimología y " .
                                   "psicología forense. Proporciona análisis exhaustivos con " .
                                   "fundamentación académica sólida y referencias a normativa vigente."
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 5000,
                'temperature' => 0.1,
            ]);

        if ($response->successful()) {
            $resultado = $response->json();
            $analisis = $resultado['choices'][0]['message']['content'];

            $this->guardarAnalisisForense($tipoAnalisis, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'tipo_analisis' => $tipoAnalisis,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $this->modelo,
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis forense: ' . $response->body());
    }

    /**
     * Construir prompt para forense digital
     */
    protected function construirPromptForenseDigital(array $datos): string
    {
        $tipoForense = $datos['tipo_forense'] ?? 'general';
        $datosForense = $datos['datos_forense'] ?? '';
        $caso = $datos['caso'] ?? 'Caso Forense';

        return "ANÁLISIS POST-DOCTORADO - FORENSE DIGITAL

TIPO DE FORENSE: {$tipoForense}
CASO: {$caso}

DATOS FORENSES:
{$datosForense}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE FORENSE DIGITAL:
   - Código de Procedimiento Penal (Art. 250-252)
   - Ley 906 de 2004 (Código de Procedimiento Penal)
   - Ley 1273 de 2009 (Delitos Informáticos)
   - Constitución Política (Art. 29, 33)
   - Convenio de Budapest sobre Ciberdelincuencia
   - Estándares internacionales de evidencia digital

2. CONCEPTOS DE FORENSE DIGITAL:
   - Concepto de forense digital
   - Objetivos de la forense digital
   - Principios de la forense digital
   - Características de la forense digital
   - Alcance de la forense digital
   - Limitaciones de la forense digital

3. TIPOS DE FORENSE DIGITAL:
   - Forense de computadoras
   - Forense de dispositivos móviles
   - Forense de redes
   - Forense de comunicaciones
   - Forense de bases de datos
   - Forense de sistemas embebidos

4. METODOLOGÍA FORENSE:
   - Planificación de la investigación
   - Preservación de la evidencia
   - Adquisición de la evidencia
   - Análisis de la evidencia
   - Documentación de hallazgos
   - Presentación de resultados

5. TÉCNICAS FORENSES:
   - Técnicas de adquisición
   - Técnicas de análisis
   - Técnicas de recuperación
   - Técnicas de verificación
   - Técnicas de documentación
   - Técnicas de presentación

6. HERRAMIENTAS FORENSES:
   - Herramientas de adquisición
   - Herramientas de análisis
   - Herramientas de recuperación
   - Herramientas de verificación
   - Herramientas de documentación
   - Herramientas de presentación

7. EVIDENCIA DIGITAL:
   - Concepto de evidencia digital
   - Tipos de evidencia digital
   - Características de la evidencia
   - Volatilidad de la evidencia
   - Integridad de la evidencia
   - Cadena de custodia

8. PERITAZGO INFORMÁTICO:
   - Peritos informáticos
   - Certificación de peritos
   - Metodología pericial
   - Informes periciales
   - Contradicción pericial
   - Valoración de la prueba

9. TECNOLOGÍAS EMERGENTES:
   - Inteligencia artificial forense
   - Machine learning en análisis
   - Blockchain para integridad
   - Internet de las cosas forense
   - Computación en la nube
   - Edge computing

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer capacidades forenses
    - Mejorar metodologías
    - Desarrollar tecnología
    - Capacitar especialistas
    - Promover cooperación
    - Actualizar normativa

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para evidencia digital
     */
    protected function construirPromptEvidenciaDigital(array $datos): string
    {
        $tipoEvidencia = $datos['tipo_evidencia'] ?? 'general';
        $datosEvidencia = $datos['datos_evidencia'] ?? '';
        $caso = $datos['caso'] ?? 'Caso Penal';

        return "ANÁLISIS POST-DOCTORADO - EVIDENCIA DIGITAL

TIPO DE EVIDENCIA: {$tipoEvidencia}
CASO: {$caso}

DATOS DE EVIDENCIA:
{$datosEvidencia}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE EVIDENCIA DIGITAL:
   - Código de Procedimiento Penal (Art. 250-252)
   - Ley 906 de 2004 (Código de Procedimiento Penal)
   - Constitución Política (Art. 29, 33)
   - Convenio de Budapest sobre Ciberdelincuencia
   - Estándares internacionales de evidencia digital
   - Jurisprudencia constitucional

2. CONCEPTOS DE EVIDENCIA DIGITAL:
   - Concepto de evidencia digital
   - Características de la evidencia digital
   - Tipos de evidencia digital
   - Fuentes de evidencia digital
   - Volatilidad de la evidencia digital
   - Integridad de la evidencia digital

3. TIPOS DE EVIDENCIA DIGITAL:
   - Evidencia de computadora
   - Evidencia de dispositivos móviles
   - Evidencia de redes
   - Evidencia de comunicaciones
   - Evidencia de bases de datos
   - Evidencia de sistemas embebidos

4. CADENA DE CUSTODIA:
   - Concepto de cadena de custodia
   - Procedimientos de preservación
   - Documentación de la evidencia
   - Transporte y almacenamiento
   - Acceso controlado
   - Destrucción segura

5. ANÁLISIS FORENSE:
   - Metodología de análisis
   - Herramientas forenses
   - Técnicas de recuperación
   - Análisis de metadatos
   - Análisis de contenido
   - Análisis de comportamiento

6. PERITAZGO INFORMÁTICO:
   - Peritos informáticos
   - Certificación de peritos
   - Metodología pericial
   - Informes periciales
   - Contradicción pericial
   - Valoración de la prueba

7. ADMISIBILIDAD DE LA PRUEBA:
   - Criterios de admisibilidad
   - Autenticidad de la evidencia
   - Integridad de la evidencia
   - Relevancia de la evidencia
   - Legalidad de la obtención
   - Valor probatorio

8. TECNOLOGÍAS EMERGENTES:
   - Inteligencia artificial forense
   - Machine learning en análisis
   - Blockchain para integridad
   - Internet de las cosas forense
   - Computación en la nube
   - Edge computing

9. COOPERACIÓN INTERNACIONAL:
   - Tratados de cooperación
   - Asistencia mutua
   - Extradición
   - Intercambio de información
   - Capacitación internacional
   - Estándares globales

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer capacidades forenses
    - Mejorar procedimientos
    - Desarrollar tecnología
    - Capacitar especialistas
    - Promover cooperación
    - Actualizar normativa

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para cibercrimen
     */
    protected function construirPromptCibercrimen(array $datos): string
    {
        $tipoCibercrimen = $datos['tipo_cibercrimen'] ?? 'general';
        $datosCibercrimen = $datos['datos_cibercrimen'] ?? '';
        $caso = $datos['caso'] ?? 'Caso de Cibercrimen';

        return "ANÁLISIS POST-DOCTORADO - CIBERCRIMEN

TIPO DE CIBERCRIMEN: {$tipoCibercrimen}
CASO: {$caso}

DATOS DE CIBERCRIMEN:
{$datosCibercrimen}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE CIBERCRIMEN:
   - Ley 1273 de 2009 (Delitos Informáticos)
   - Código Penal (Art. 269A-269L)
   - Constitución Política (Art. 15, 16)
   - Convenio de Budapest sobre Ciberdelincuencia
   - Protocolo Adicional al Convenio de Budapest
   - Jurisprudencia constitucional

2. CONCEPTOS DE CIBERCRIMEN:
   - Concepto de cibercrimen
   - Características del cibercrimen
   - Tipos de cibercrimen
   - Modalidades del cibercrimen
   - Perfil del ciberdelincuente
   - Víctimas del cibercrimen

3. TIPOS DE CIBERCRIMEN:
   - Delitos contra la confidencialidad
   - Delitos contra la integridad
   - Delitos contra la disponibilidad
   - Delitos contra la autenticidad
   - Delitos contra la no repudio
   - Delitos contra la privacidad

4. DELITOS INFORMÁTICOS:
   - Acceso abusivo a sistemas informáticos
   - Interceptación de datos informáticos
   - Ataque a la integridad de datos
   - Ataque a la integridad del sistema
   - Falsificación informática
   - Fraude informático

5. DELITOS CONTRA LA INTIMIDAD:
   - Violación de datos personales
   - Violación de comunicaciones
   - Interceptación de comunicaciones
   - Acceso no autorizado a datos
   - Difusión de datos personales
   - Suplantación de identidad

6. DELITOS CONTRA LA PROPIEDAD INTELECTUAL:
   - Violación de derechos de autor
   - Piratería de software
   - Distribución ilegal de contenidos
   - Plagio digital
   - Falsificación de obras
   - Uso no autorizado de marcas

7. DELITOS CONTRA MENORES:
   - Pornografía infantil
   - Grooming
   - Sexting
   - Ciberbullying
   - Explotación sexual
   - Trata de personas

8. INVESTIGACIÓN Y PERSECUCIÓN:
   - Policía judicial especializada
   - Fiscalía especializada
   - Peritos informáticos
   - Evidencia digital
   - Cadena de custodia
   - Cooperación internacional

9. PREVENCIÓN Y EDUCACIÓN:
   - Programas de prevención
   - Educación digital
   - Concienciación ciudadana
   - Capacitación especializada
   - Políticas públicas
   - Cooperación internacional

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer la investigación
    - Mejorar la persecución penal
    - Desarrollar capacidades técnicas
    - Promover la cooperación
    - Fomentar la prevención
    - Actualizar la normativa

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para perfilación criminal
     */
    protected function construirPromptPerfilacionCriminal(array $datos): string
    {
        $tipoPerfilacion = $datos['tipo_perfilacion'] ?? 'general';
        $datosPerfilacion = $datos['datos_perfilacion'] ?? '';
        $caso = $datos['caso'] ?? 'Caso Criminal';

        return "ANÁLISIS POST-DOCTORADO - PERFILACIÓN CRIMINAL

TIPO DE PERFILACIÓN: {$tipoPerfilacion}
CASO: {$caso}

DATOS DE PERFILACIÓN:
{$datosPerfilacion}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO CONCEPTUAL DE PERFILACIÓN CRIMINAL:
   - Concepto de perfilación criminal
   - Objetivos de la perfilación
   - Principios de la perfilación
   - Características de la perfilación
   - Alcance de la perfilación
   - Limitaciones de la perfilación

2. TIPOS DE PERFILACIÓN:
   - Perfilación inductiva
   - Perfilación deductiva
   - Perfilación estadística
   - Perfilación geográfica
   - Perfilación psicológica
   - Perfilación conductual

3. METODOLOGÍA DE PERFILACIÓN:
   - Planificación de la perfilación
   - Recopilación de información
   - Análisis de la información
   - Elaboración del perfil
   - Validación del perfil
   - Actualización del perfil

4. TÉCNICAS DE PERFILACIÓN:
   - Técnicas de análisis
   - Técnicas de evaluación
   - Técnicas de comparación
   - Técnicas de validación
   - Técnicas de documentación
   - Técnicas de presentación

5. FACTORES DE PERFILACIÓN:
   - Factores psicológicos
   - Factores sociales
   - Factores económicos
   - Factores culturales
   - Factores geográficos
   - Factores temporales

6. PERFIL DEL DELINCUENTE:
   - Características demográficas
   - Características psicológicas
   - Características conductuales
   - Características sociales
   - Características económicas
   - Características culturales

7. PERFIL DE LA VÍCTIMA:
   - Características demográficas
   - Características psicológicas
   - Características conductuales
   - Características sociales
   - Características económicas
   - Características culturales

8. PERFIL DEL DELITO:
   - Características del delito
   - Modalidades del delito
   - Patrones del delito
   - Escalación del delito
   - Evolución del delito
   - Tendencias del delito

9. APLICACIONES DE LA PERFILACIÓN:
   - Investigación criminal
   - Prevención del delito
   - Política criminal
   - Justicia penal
   - Rehabilitación
   - Reinserción social

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer la perfilación
    - Mejorar la metodología
    - Desarrollar capacidades
    - Fomentar la investigación
    - Promover la cooperación
    - Actualizar la normativa

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para criminología
     */
    protected function construirPromptCriminologia(array $datos): string
    {
        $tipoCriminologia = $datos['tipo_criminologia'] ?? 'general';
        $datosCriminologia = $datos['datos_criminologia'] ?? '';
        $caso = $datos['caso'] ?? 'Caso Criminológico';

        return "ANÁLISIS POST-DOCTORADO - CRIMINOLOGÍA

TIPO DE CRIMINOLOGÍA: {$tipoCriminologia}
CASO: {$caso}

DATOS CRIMINOLÓGICOS:
{$datosCriminologia}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO CONCEPTUAL DE CRIMINOLOGÍA:
   - Concepto de criminología
   - Objetivos de la criminología
   - Principios de la criminología
   - Características de la criminología
   - Alcance de la criminología
   - Limitaciones de la criminología

2. TIPOS DE CRIMINOLOGÍA:
   - Criminología clásica
   - Criminología positivista
   - Criminología crítica
   - Criminología cultural
   - Criminología ambiental
   - Criminología digital

3. TEORÍAS CRIMINOLÓGICAS:
   - Teorías del control social
   - Teorías del aprendizaje social
   - Teorías de la tensión
   - Teorías de la desorganización social
   - Teorías de la anomia
   - Teorías de la subcultura

4. FACTORES CRIMINÓGENOS:
   - Factores individuales
   - Factores familiares
   - Factores sociales
   - Factores económicos
   - Factores culturales
   - Factores ambientales

5. DELINCUENCIA:
   - Concepto de delincuencia
   - Tipos de delincuencia
   - Características de la delincuencia
   - Patrones de la delincuencia
   - Tendencias de la delincuencia
   - Prevención de la delincuencia

6. VICTIMOLOGÍA:
   - Concepto de victimología
   - Tipos de víctimas
   - Características de las víctimas
   - Factores de victimización
   - Prevención de la victimización
   - Atención a las víctimas

7. PREVENCIÓN DEL DELITO:
   - Concepto de prevención
   - Tipos de prevención
   - Estrategias de prevención
   - Programas de prevención
   - Evaluación de la prevención
   - Mejora de la prevención

8. POLÍTICA CRIMINAL:
   - Concepto de política criminal
   - Objetivos de la política criminal
   - Estrategias de la política criminal
   - Programas de la política criminal
   - Evaluación de la política criminal
   - Mejora de la política criminal

9. INVESTIGACIÓN CRIMINOLÓGICA:
   - Metodología de investigación
   - Técnicas de investigación
   - Herramientas de investigación
   - Análisis de datos
   - Interpretación de resultados
   - Aplicación de resultados

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer la criminología
    - Mejorar la investigación
    - Desarrollar capacidades
    - Fomentar la prevención
    - Promover la cooperación
    - Actualizar la normativa

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para victimología
     */
    protected function construirPromptVictimologia(array $datos): string
    {
        $tipoVictimologia = $datos['tipo_victimologia'] ?? 'general';
        $datosVictimologia = $datos['datos_victimologia'] ?? '';
        $caso = $datos['caso'] ?? 'Caso Victimológico';

        return "ANÁLISIS POST-DOCTORADO - VICTIMOLOGÍA

TIPO DE VICTIMOLOGÍA: {$tipoVictimologia}
CASO: {$caso}

DATOS VICTIMOLÓGICOS:
{$datosVictimologia}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO CONCEPTUAL DE VICTIMOLOGÍA:
   - Concepto de victimología
   - Objetivos de la victimología
   - Principios de la victimología
   - Características de la victimología
   - Alcance de la victimología
   - Limitaciones de la victimología

2. TIPOS DE VICTIMOLOGÍA:
   - Victimología clásica
   - Victimología crítica
   - Victimología feminista
   - Victimología cultural
   - Victimología ambiental
   - Victimología digital

3. CONCEPTOS DE VÍCTIMA:
   - Concepto de víctima
   - Tipos de víctimas
   - Características de las víctimas
   - Perfil de las víctimas
   - Factores de victimización
   - Proceso de victimización

4. FACTORES DE VICTIMIZACIÓN:
   - Factores individuales
   - Factores familiares
   - Factores sociales
   - Factores económicos
   - Factores culturales
   - Factores ambientales

5. TIPOS DE VICTIMIZACIÓN:
   - Victimización primaria
   - Victimización secundaria
   - Victimización terciaria
   - Victimización directa
   - Victimización indirecta
   - Victimización institucional

6. CONSECUENCIAS DE LA VICTIMIZACIÓN:
   - Consecuencias físicas
   - Consecuencias psicológicas
   - Consecuencias sociales
   - Consecuencias económicas
   - Consecuencias culturales
   - Consecuencias ambientales

7. ATENCIÓN A LAS VÍCTIMAS:
   - Concepto de atención
   - Tipos de atención
   - Estrategias de atención
   - Programas de atención
   - Evaluación de la atención
   - Mejora de la atención

8. PREVENCIÓN DE LA VICTIMIZACIÓN:
   - Concepto de prevención
   - Tipos de prevención
   - Estrategias de prevención
   - Programas de prevención
   - Evaluación de la prevención
   - Mejora de la prevención

9. INVESTIGACIÓN VICTIMOLÓGICA:
   - Metodología de investigación
   - Técnicas de investigación
   - Herramientas de investigación
   - Análisis de datos
   - Interpretación de resultados
   - Aplicación de resultados

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer la victimología
    - Mejorar la atención
    - Desarrollar capacidades
    - Fomentar la prevención
    - Promover la cooperación
    - Actualizar la normativa

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para psicología forense
     */
    protected function construirPromptPsicologiaForense(array $datos): string
    {
        $tipoPsicologia = $datos['tipo_psicologia'] ?? 'general';
        $datosPsicologia = $datos['datos_psicologia'] ?? '';
        $caso = $datos['caso'] ?? 'Caso Psicológico';

        return "ANÁLISIS POST-DOCTORADO - PSICOLOGÍA FORENSE

TIPO DE PSICOLOGÍA: {$tipoPsicologia}
CASO: {$caso}

DATOS PSICOLÓGICOS:
{$datosPsicologia}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO CONCEPTUAL DE PSICOLOGÍA FORENSE:
   - Concepto de psicología forense
   - Objetivos de la psicología forense
   - Principios de la psicología forense
   - Características de la psicología forense
   - Alcance de la psicología forense
   - Limitaciones de la psicología forense

2. TIPOS DE PSICOLOGÍA FORENSE:
   - Psicología forense clínica
   - Psicología forense experimental
   - Psicología forense social
   - Psicología forense cognitiva
   - Psicología forense del desarrollo
   - Psicología forense cultural

3. ÁREAS DE APLICACIÓN:
   - Evaluación psicológica
   - Peritaje psicológico
   - Intervención psicológica
   - Investigación psicológica
   - Consultoría psicológica
   - Formación psicológica

4. EVALUACIÓN PSICOLÓGICA:
   - Concepto de evaluación
   - Tipos de evaluación
   - Metodología de evaluación
   - Técnicas de evaluación
   - Herramientas de evaluación
   - Interpretación de resultados

5. PERITAJE PSICOLÓGICO:
   - Concepto de peritaje
   - Tipos de peritaje
   - Metodología de peritaje
   - Técnicas de peritaje
   - Herramientas de peritaje
   - Presentación de resultados

6. INTERVENCIÓN PSICOLÓGICA:
   - Concepto de intervención
   - Tipos de intervención
   - Estrategias de intervención
   - Programas de intervención
   - Evaluación de la intervención
   - Mejora de la intervención

7. INVESTIGACIÓN PSICOLÓGICA:
   - Metodología de investigación
   - Técnicas de investigación
   - Herramientas de investigación
   - Análisis de datos
   - Interpretación de resultados
   - Aplicación de resultados

8. ASPECTOS ÉTICOS:
   - Principios éticos
   - Código de ética
   - Confidencialidad
   - Consentimiento informado
   - Competencia profesional
   - Responsabilidad profesional

9. FORMACIÓN Y CAPACITACIÓN:
   - Formación académica
   - Capacitación profesional
   - Certificación profesional
   - Desarrollo profesional
   - Actualización profesional
   - Excelencia profesional

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer la psicología forense
    - Mejorar la formación
    - Desarrollar capacidades
    - Fomentar la investigación
    - Promover la cooperación
    - Actualizar la normativa

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Consolidar análisis forense
     */
    protected function consolidarAnalisisForense(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_investigacion_forense',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'riesgos_identificados' => [],
            'oportunidades' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - INVESTIGACIÓN FORENSE\n\n";
        foreach ($resultados as $tipoAnalisis => $resultado) {
            if ($resultado['exito']) {
                $analisisConsolidado .= "=== {$tipoAnalisis} ===\n";
                $analisisConsolidado .= $resultado['analisis'] . "\n\n";
            }
        }

        $consolidado['analisis_consolidado'] = $analisisConsolidado;

        // Generar recomendaciones integrales
        $consolidado['recomendaciones_integrales'] = [
            'Fortalecer las capacidades de análisis forense digital',
            'Mejorar la preservación y análisis de evidencia digital',
            'Desarrollar capacidades de investigación de cibercrimen',
            'Establecer sistemas de perfilación criminal avanzados',
            'Implementar programas de criminología aplicada',
            'Fortalecer la atención y prevención victimológica',
            'Desarrollar capacidades de psicología forense'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis forense
     */
    protected function guardarAnalisisForense(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis forense', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de investigación forense
     */
    public function obtenerEstadisticasInvestigacionForense(): array
    {
        return [
            'total_analisis_forense' => AnalisisIA::whereIn('especialidad', [
                'forense_digital',
                'evidencia_digital',
                'cibercrimen',
                'perfilacion_criminal',
                'criminologia',
                'victimologia',
                'psicologia_forense'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'forense_digital',
                'evidencia_digital',
                'cibercrimen',
                'perfilacion_criminal',
                'criminologia',
                'victimologia',
                'psicologia_forense'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'forense_digital',
                'evidencia_digital',
                'cibercrimen',
                'perfilacion_criminal',
                'criminologia',
                'victimologia',
                'psicologia_forense'
            ])->sum('tokens_usados')
        ];
    }
}
