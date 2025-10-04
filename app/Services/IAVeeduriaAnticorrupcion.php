<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para Veeduría Ciudadana y Anti-Corrupción
 * Nivel Post-Doctorado especializado en control social y transparencia
 */
class IAVeeduriaAnticorrupcion
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
     * Análisis de Veeduría Ciudadana
     */
    public function analizarVeeduriaCiudadana(array $datos): array
    {
        $cacheKey = 'ia_veeduria_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptVeeduria($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisVeeduria('veeduria_ciudadana', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Control Social
     */
    public function analizarControlSocial(array $datos): array
    {
        $cacheKey = 'ia_control_social_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptControlSocial($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisVeeduria('control_social', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Transparencia Pública
     */
    public function analizarTransparenciaPublica(array $datos): array
    {
        $cacheKey = 'ia_transparencia_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptTransparencia($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisVeeduria('transparencia_publica', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Rendición de Cuentas
     */
    public function analizarRendicionCuentas(array $datos): array
    {
        $cacheKey = 'ia_rendicion_cuentas_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptRendicionCuentas($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisVeeduria('rendicion_cuentas', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Participación Ciudadana
     */
    public function analizarParticipacionCiudadana(array $datos): array
    {
        $cacheKey = 'ia_participacion_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptParticipacion($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisVeeduria('participacion_ciudadana', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Contratación Pública
     */
    public function analizarContratacionPublica(array $datos): array
    {
        $cacheKey = 'ia_contratacion_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptContratacion($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisVeeduria('contratacion_publica', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Gestión de Riesgos
     */
    public function analizarGestionRiesgos(array $datos): array
    {
        $cacheKey = 'ia_gestion_riesgos_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptGestionRiesgos($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisVeeduria('gestion_riesgos', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de veeduría y anti-corrupción
     */
    public function analisisIntegralVeeduriaAnticorrupcion(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'veeduria_ciudadana', 
            'control_social', 
            'transparencia_publica', 
            'rendicion_cuentas',
            'participacion_ciudadana',
            'contratacion_publica',
            'gestion_riesgos'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisVeeduria($resultados, $datos);
    }

    /**
     * Ejecutar análisis de veeduría
     */
    protected function ejecutarAnalisisVeeduria(string $tipoAnalisis, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en veeduría ciudadana y anti-corrupción de nivel post-doctorado. " .
                                   "Especializado en control social, transparencia pública, rendición de cuentas " .
                                   "y participación ciudadana. Proporciona análisis exhaustivos con " .
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

            $this->guardarAnalisisVeeduria($tipoAnalisis, $datos, $analisis, $resultado);

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

        throw new \RuntimeException('Error en análisis de veeduría: ' . $response->body());
    }

    /**
     * Construir prompt para veeduría ciudadana
     */
    protected function construirPromptVeeduria(array $datos): string
    {
        $tipoVeeduria = $datos['tipo_veeduria'] ?? 'general';
        $datosVeeduria = $datos['datos_veeduria'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';
        $municipio = $datos['municipio'] ?? 'Municipio';

        return "ANÁLISIS POST-DOCTORADO - VEEDURÍA CIUDADANA

TIPO DE VEEDURÍA: {$tipoVeeduria}
ENTIDAD: {$entidad}
MUNICIPIO: {$municipio}

DATOS DE LA VEEDURÍA:
{$datosVeeduria}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE VEEDURÍA:
   - Ley 850 de 2003 (Ley de Veedurías Ciudadanas)
   - Constitución Política (Art. 270)
   - Ley 1757 de 2015 (Ley de Participación Democrática)
   - Decreto 953 de 2013 (Reglamento de Veedurías)
   - Jurisprudencia constitucional sobre veedurías

2. TIPOS DE VEEDURÍA:
   - Veeduría de gestión pública
   - Veeduría de contratación
   - Veeduría de servicios públicos
   - Veeduría de obras públicas
   - Veeduría de programas sociales
   - Veeduría de recursos naturales

3. PROCEDIMIENTO DE VEEDURÍA:
   - Inscripción de veeduría
   - Conformación del comité
   - Plan de trabajo
   - Seguimiento y monitoreo
   - Informes de veeduría
   - Rendición de cuentas

4. DERECHOS DE LOS VEDEADORES:
   - Derecho a la información
   - Derecho a la participación
   - Derecho a la consulta
   - Derecho a la réplica
   - Derecho a la protección
   - Derecho a la capacitación

5. OBLIGACIONES DE LAS ENTIDADES:
   - Suministrar información
   - Facilitar acceso a documentos
   - Responder consultas
   - Permitir seguimiento
   - Atender recomendaciones
   - Rendir cuentas

6. MECANISMOS DE CONTROL:
   - Control social
   - Control político
   - Control fiscal
   - Control disciplinario
   - Control penal
   - Control administrativo

7. PARTICIPACIÓN CIUDADANA:
   - Mecanismos de participación
   - Consultas ciudadanas
   - Audiencias públicas
   - Cabildos abiertos
   - Iniciativas populares
   - Referendos

8. TRANSPARENCIA Y ACCESO A LA INFORMACIÓN:
   - Ley 1712 de 2014 (Transparencia)
   - Transparencia activa
   - Transparencia pasiva
   - Derecho de petición
   - Habeas data
   - Protección de datos

9. RENDICIÓN DE CUENTAS:
   - Obligación de rendir cuentas
   - Informes de gestión
   - Audiencias de rendición
   - Evaluación ciudadana
   - Control social
   - Seguimiento

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de la veeduría
    - Mejora de la participación
    - Optimización de la transparencia
    - Gestión de riesgos
    - Capacitación ciudadana
    - Innovación en control social

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para control social
     */
    protected function construirPromptControlSocial(array $datos): string
    {
        $tipoControl = $datos['tipo_control'] ?? 'general';
        $datosControl = $datos['datos_control'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - CONTROL SOCIAL

TIPO DE CONTROL: {$tipoControl}
ENTIDAD: {$entidad}

DATOS DEL CONTROL:
{$datosControl}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO CONCEPTUAL DEL CONTROL SOCIAL:
   - Concepto de control social
   - Enfoque de derechos
   - Enfoque territorial
   - Enfoque participativo
   - Enfoque de género
   - Enfoque generacional

2. TIPOS DE CONTROL SOCIAL:
   - Control preventivo
   - Control concurrente
   - Control posterior
   - Control ciudadano
   - Control comunitario
   - Control territorial

3. MECANISMOS DE CONTROL SOCIAL:
   - Veedurías ciudadanas
   - Contralorías ciudadanas
   - Observatorios ciudadanos
   - Comités de control
   - Redes de control
   - Plataformas digitales

4. PARTICIPACIÓN EN EL CONTROL:
   - Participación individual
   - Participación colectiva
   - Participación organizada
   - Participación comunitaria
   - Participación territorial
   - Participación sectorial

5. INFORMACIÓN Y TRANSPARENCIA:
   - Acceso a la información
   - Transparencia activa
   - Transparencia pasiva
   - Datos abiertos
   - Portales de transparencia
   - Sistemas de información

6. RENDICIÓN DE CUENTAS:
   - Obligación de rendir cuentas
   - Informes de gestión
   - Audiencias públicas
   - Evaluación ciudadana
   - Seguimiento y monitoreo
   - Control de resultados

7. CAPACITACIÓN Y FORMACIÓN:
   - Capacitación ciudadana
   - Formación en control social
   - Educación cívica
   - Alfabetización digital
   - Formación de líderes
   - Escuelas de control

8. TECNOLOGÍA Y CONTROL SOCIAL:
   - Plataformas digitales
   - Aplicaciones móviles
   - Sistemas de información
   - Big data
   - Inteligencia artificial
   - Blockchain

9. EVALUACIÓN Y SEGUIMIENTO:
   - Indicadores de control
   - Métricas de participación
   - Evaluación de impacto
   - Seguimiento de resultados
   - Mejora continua
   - Innovación

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento del control social
    - Mejora de la participación
    - Optimización de la transparencia
    - Gestión de riesgos
    - Capacitación ciudadana
    - Innovación tecnológica

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para transparencia pública
     */
    protected function construirPromptTransparencia(array $datos): string
    {
        $tipoTransparencia = $datos['tipo_transparencia'] ?? 'general';
        $datosTransparencia = $datos['datos_transparencia'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - TRANSPARENCIA PÚBLICA

TIPO DE TRANSPARENCIA: {$tipoTransparencia}
ENTIDAD: {$entidad}

DATOS DE TRANSPARENCIA:
{$datosTransparencia}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE TRANSPARENCIA:
   - Ley 1712 de 2014 (Transparencia y Acceso a la Información)
   - Constitución Política (Art. 23, 74)
   - Ley 1755 de 2015 (Ley de Participación Democrática)
   - Decreto 103 de 2015 (Reglamento de Transparencia)
   - Jurisprudencia constitucional sobre transparencia

2. PRINCIPIOS DE TRANSPARENCIA:
   - Principio de publicidad
   - Principio de transparencia
   - Principio de acceso a la información
   - Principio de participación
   - Principio de rendición de cuentas
   - Principio de responsabilidad

3. TIPOS DE TRANSPARENCIA:
   - Transparencia activa
   - Transparencia pasiva
   - Transparencia proactiva
   - Transparencia digital
   - Transparencia presupuestal
   - Transparencia contractual

4. INFORMACIÓN PÚBLICA:
   - Concepto de información pública
   - Excepciones a la publicidad
   - Clasificación de información
   - Protección de datos personales
   - Seguridad de la información
   - Archivo y conservación

5. MECANISMOS DE ACCESO:
   - Derecho de petición
   - Habeas data
   - Acceso directo
   - Portales de transparencia
   - Sistemas de información
   - Plataformas digitales

6. OBLIGACIONES DE TRANSPARENCIA:
   - Publicación de información
   - Actualización de datos
   - Respuesta a solicitudes
   - Capacitación ciudadana
   - Rendición de cuentas
   - Evaluación de transparencia

7. TECNOLOGÍA Y TRANSPARENCIA:
   - Gobierno digital
   - Datos abiertos
   - Portales web
   - Aplicaciones móviles
   - Sistemas de información
   - Inteligencia artificial

8. PARTICIPACIÓN CIUDADANA:
   - Mecanismos de participación
   - Consultas ciudadanas
   - Audiencias públicas
   - Cabildos abiertos
   - Iniciativas populares
   - Referendos

9. RENDICIÓN DE CUENTAS:
   - Obligación de rendir cuentas
   - Informes de gestión
   - Audiencias de rendición
   - Evaluación ciudadana
   - Control social
   - Seguimiento

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de la transparencia
    - Mejora del acceso a la información
    - Optimización de la participación
    - Gestión de riesgos
    - Capacitación ciudadana
    - Innovación tecnológica

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para rendición de cuentas
     */
    protected function construirPromptRendicionCuentas(array $datos): string
    {
        $tipoRendicion = $datos['tipo_rendicion'] ?? 'general';
        $datosRendicion = $datos['datos_rendicion'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - RENDICIÓN DE CUENTAS

TIPO DE RENDICIÓN: {$tipoRendicion}
ENTIDAD: {$entidad}

DATOS DE RENDICIÓN:
{$datosRendicion}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE RENDICIÓN DE CUENTAS:
   - Constitución Política (Art. 270)
   - Ley 1757 de 2015 (Ley de Participación Democrática)
   - Ley 1712 de 2014 (Transparencia y Acceso a la Información)
   - Decreto 953 de 2013 (Reglamento de Veedurías)
   - Jurisprudencia constitucional sobre rendición

2. CONCEPTOS DE RENDICIÓN DE CUENTAS:
   - Concepto de rendición de cuentas
   - Enfoque de derechos
   - Enfoque participativo
   - Enfoque territorial
   - Enfoque de género
   - Enfoque generacional

3. TIPOS DE RENDICIÓN DE CUENTAS:
   - Rendición de cuentas obligatoria
   - Rendición de cuentas voluntaria
   - Rendición de cuentas sectorial
   - Rendición de cuentas territorial
   - Rendición de cuentas temática
   - Rendición de cuentas digital

4. SUJETOS DE RENDICIÓN:
   - Entidades públicas
   - Servidores públicos
   - Contratistas
   - Concesionarios
   - Empresas mixtas
   - Organizaciones sociales

5. CONTENIDO DE LA RENDICIÓN:
   - Informes de gestión
   - Informes financieros
   - Informes de resultados
   - Informes de impacto
   - Informes de cumplimiento
   - Informes de evaluación

6. MECANISMOS DE RENDICIÓN:
   - Audiencias públicas
   - Informes escritos
   - Portales web
   - Medios de comunicación
   - Asambleas ciudadanas
   - Plataformas digitales

7. PARTICIPACIÓN CIUDADANA:
   - Mecanismos de participación
   - Consultas ciudadanas
   - Evaluación ciudadana
   - Control social
   - Seguimiento
   - Monitoreo

8. EVALUACIÓN Y SEGUIMIENTO:
   - Indicadores de rendición
   - Métricas de participación
   - Evaluación de impacto
   - Seguimiento de resultados
   - Mejora continua
   - Innovación

9. TECNOLOGÍA Y RENDICIÓN:
   - Gobierno digital
   - Datos abiertos
   - Portales web
   - Aplicaciones móviles
   - Sistemas de información
   - Inteligencia artificial

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de la rendición
    - Mejora de la participación
    - Optimización de la transparencia
    - Gestión de riesgos
    - Capacitación ciudadana
    - Innovación tecnológica

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para participación ciudadana
     */
    protected function construirPromptParticipacion(array $datos): string
    {
        $tipoParticipacion = $datos['tipo_participacion'] ?? 'general';
        $datosParticipacion = $datos['datos_participacion'] ?? '';
        $municipio = $datos['municipio'] ?? 'Municipio';

        return "ANÁLISIS POST-DOCTORADO - PARTICIPACIÓN CIUDADANA

TIPO DE PARTICIPACIÓN: {$tipoParticipacion}
MUNICIPIO: {$municipio}

DATOS DE PARTICIPACIÓN:
{$datosParticipacion}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE PARTICIPACIÓN:
   - Constitución Política (Art. 1, 2, 40, 103)
   - Ley 1757 de 2015 (Ley de Participación Democrática)
   - Ley 850 de 2003 (Ley de Veedurías Ciudadanas)
   - Decreto 953 de 2013 (Reglamento de Veedurías)
   - Jurisprudencia constitucional sobre participación

2. CONCEPTOS DE PARTICIPACIÓN:
   - Concepto de participación ciudadana
   - Enfoque de derechos
   - Enfoque territorial
   - Enfoque de género
   - Enfoque generacional
   - Enfoque intercultural

3. TIPOS DE PARTICIPACIÓN:
   - Participación individual
   - Participación colectiva
   - Participación organizada
   - Participación comunitaria
   - Participación territorial
   - Participación sectorial

4. MECANISMOS DE PARTICIPACIÓN:
   - Veedurías ciudadanas
   - Consultas populares
   - Referendos
   - Plebiscitos
   - Revocatorias de mandato
   - Cabildos abiertos

5. PARTICIPACIÓN EN LA GESTIÓN PÚBLICA:
   - Participación en la planificación
   - Participación en la ejecución
   - Participación en el seguimiento
   - Participación en la evaluación
   - Participación en el control
   - Participación en la rendición

6. PARTICIPACIÓN EN LA CONTRATACIÓN:
   - Participación en la planeación
   - Participación en la selección
   - Participación en la ejecución
   - Participación en el seguimiento
   - Participación en la evaluación
   - Participación en el control

7. PARTICIPACIÓN EN LA RENDICIÓN DE CUENTAS:
   - Participación en la evaluación
   - Participación en el control
   - Participación en el seguimiento
   - Participación en la mejora
   - Participación en la innovación
   - Participación en la transformación

8. TECNOLOGÍA Y PARTICIPACIÓN:
   - Gobierno digital
   - Plataformas digitales
   - Aplicaciones móviles
   - Redes sociales
   - Sistemas de información
   - Inteligencia artificial

9. CAPACITACIÓN Y FORMACIÓN:
   - Capacitación ciudadana
   - Formación en participación
   - Educación cívica
   - Alfabetización digital
   - Formación de líderes
   - Escuelas de participación

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de la participación
    - Mejora de los mecanismos
    - Optimización de la tecnología
    - Gestión de riesgos
    - Capacitación ciudadana
    - Innovación participativa

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para contratación pública
     */
    protected function construirPromptContratacion(array $datos): string
    {
        $tipoContratacion = $datos['tipo_contratacion'] ?? 'general';
        $datosContratacion = $datos['datos_contratacion'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - CONTRATACIÓN PÚBLICA

TIPO DE CONTRATACIÓN: {$tipoContratacion}
ENTIDAD: {$entidad}

DATOS DE CONTRATACIÓN:
{$datosContratacion}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE CONTRATACIÓN:
   - Ley 80 de 1993 (Estatuto de Contratación)
   - Ley 1150 de 2007 (Reforma Contractual)
   - Decreto 1082 de 2015 (Único Reglamentario)
   - Ley 1474 de 2011 (Estatuto Anticorrupción)
   - Jurisprudencia constitucional sobre contratación

2. PRINCIPIOS DE CONTRATACIÓN:
   - Principio de transparencia
   - Principio de economía
   - Principio de responsabilidad
   - Principio de eficiencia
   - Principio de imparcialidad
   - Principio de publicidad

3. TIPOS DE CONTRATACIÓN:
   - Contratación directa
   - Contratación por invitación
   - Contratación por licitación
   - Contratación por concurso
   - Contratación por subasta
   - Contratación por convenio

4. PROCEDIMIENTOS DE CONTRATACIÓN:
   - Planeación contractual
   - Presupuesto contractual
   - Estudio de mercado
   - Selección de contratista
   - Ejecución contractual
   - Liquidación contractual

5. PARTICIPACIÓN CIUDADANA:
   - Veedurías de contratación
   - Control social
   - Seguimiento ciudadano
   - Evaluación ciudadana
   - Rendición de cuentas
   - Transparencia

6. TRANSPARENCIA Y ACCESO A LA INFORMACIÓN:
   - Publicación de información
   - Portales de contratación
   - Datos abiertos
   - Sistemas de información
   - Plataformas digitales
   - Inteligencia artificial

7. CONTROL Y VIGILANCIA:
   - Control interno
   - Control externo
   - Control social
   - Control ciudadano
   - Control territorial
   - Control sectorial

8. GESTIÓN DE RIESGOS:
   - Identificación de riesgos
   - Evaluación de riesgos
   - Medidas de mitigación
   - Planes de contingencia
   - Monitoreo de riesgos
   - Actualización de riesgos

9. TECNOLOGÍA Y CONTRATACIÓN:
   - Gobierno digital
   - Plataformas digitales
   - Aplicaciones móviles
   - Sistemas de información
   - Big data
   - Inteligencia artificial

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de la contratación
    - Mejora de la transparencia
    - Optimización de la participación
    - Gestión de riesgos
    - Capacitación ciudadana
    - Innovación tecnológica

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para gestión de riesgos
     */
    protected function construirPromptGestionRiesgos(array $datos): string
    {
        $tipoRiesgo = $datos['tipo_riesgo'] ?? 'general';
        $datosRiesgo = $datos['datos_riesgo'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - GESTIÓN DE RIESGOS

TIPO DE RIESGO: {$tipoRiesgo}
ENTIDAD: {$entidad}

DATOS DE RIESGO:
{$datosRiesgo}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO CONCEPTUAL DE GESTIÓN DE RIESGOS:
   - Concepto de gestión de riesgos
   - Enfoque de derechos
   - Enfoque territorial
   - Enfoque participativo
   - Enfoque de género
   - Enfoque generacional

2. TIPOS DE RIESGOS:
   - Riesgos operacionales
   - Riesgos financieros
   - Riesgos legales
   - Riesgos reputacionales
   - Riesgos tecnológicos
   - Riesgos ambientales

3. IDENTIFICACIÓN DE RIESGOS:
   - Metodologías de identificación
   - Herramientas de identificación
   - Participación ciudadana
   - Análisis de contexto
   - Análisis de stakeholders
   - Análisis de vulnerabilidades

4. EVALUACIÓN DE RIESGOS:
   - Metodologías de evaluación
   - Herramientas de evaluación
   - Criterios de evaluación
   - Escalas de evaluación
   - Matrices de riesgo
   - Análisis de probabilidad

5. MEDIDAS DE MITIGACIÓN:
   - Estrategias de mitigación
   - Planes de mitigación
   - Medidas preventivas
   - Medidas correctivas
   - Medidas de contingencia
   - Medidas de recuperación

6. MONITOREO Y SEGUIMIENTO:
   - Sistemas de monitoreo
   - Indicadores de riesgo
   - Alertas tempranas
   - Seguimiento continuo
   - Evaluación periódica
   - Actualización de riesgos

7. PARTICIPACIÓN CIUDADANA:
   - Mecanismos de participación
   - Consultas ciudadanas
   - Veedurías de riesgo
   - Control social
   - Seguimiento ciudadano
   - Evaluación ciudadana

8. TECNOLOGÍA Y GESTIÓN DE RIESGOS:
   - Sistemas de información
   - Plataformas digitales
   - Aplicaciones móviles
   - Big data
   - Inteligencia artificial
   - Blockchain

9. CAPACITACIÓN Y FORMACIÓN:
   - Capacitación en gestión de riesgos
   - Formación ciudadana
   - Educación en prevención
   - Alfabetización digital
   - Formación de líderes
   - Escuelas de gestión

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento de la gestión
    - Mejora de la participación
    - Optimización de la tecnología
    - Gestión integral de riesgos
    - Capacitación ciudadana
    - Innovación en gestión

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Consolidar análisis de veeduría
     */
    protected function consolidarAnalisisVeeduria(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_veeduria',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'riesgos_identificados' => [],
            'oportunidades' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - VEEDURÍA Y ANTI-CORRUPCIÓN\n\n";
        foreach ($resultados as $tipoAnalisis => $resultado) {
            if ($resultado['exito']) {
                $analisisConsolidado .= "=== {$tipoAnalisis} ===\n";
                $analisisConsolidado .= $resultado['analisis'] . "\n\n";
            }
        }

        $consolidado['analisis_consolidado'] = $analisisConsolidado;

        // Generar recomendaciones integrales
        $consolidado['recomendaciones_integrales'] = [
            'Fortalecer los mecanismos de veeduría ciudadana',
            'Mejorar la transparencia y acceso a la información',
            'Implementar sistemas de rendición de cuentas efectivos',
            'Promover la participación ciudadana activa',
            'Establecer controles robustos en la contratación pública',
            'Desarrollar sistemas de gestión de riesgos integrales'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis de veeduría
     */
    protected function guardarAnalisisVeeduria(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis de veeduría', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de veeduría
     */
    public function obtenerEstadisticasVeeduria(): array
    {
        return [
            'total_analisis_veeduria' => AnalisisIA::whereIn('especialidad', [
                'veeduria_ciudadana',
                'control_social',
                'transparencia_publica',
                'rendicion_cuentas',
                'participacion_ciudadana',
                'contratacion_publica',
                'gestion_riesgos'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'veeduria_ciudadana',
                'control_social',
                'transparencia_publica',
                'rendicion_cuentas',
                'participacion_ciudadana',
                'contratacion_publica',
                'gestion_riesgos'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'veeduria_ciudadana',
                'control_social',
                'transparencia_publica',
                'rendicion_cuentas',
                'participacion_ciudadana',
                'contratacion_publica',
                'gestion_riesgos'
            ])->sum('tokens_usados')
        ];
    }
}
