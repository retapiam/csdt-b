<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para Fiscalización
 * Nivel Post-Doctorado especializado en control fiscal, auditoría y supervisión
 */
class IAFiscalizacion
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
     * Análisis de Control Fiscal
     */
    public function analizarControlFiscal(array $datos): array
    {
        $cacheKey = 'ia_control_fiscal_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptControlFiscal($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisFiscalizacion('control_fiscal', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Auditoría Pública
     */
    public function analizarAuditoriaPublica(array $datos): array
    {
        $cacheKey = 'ia_auditoria_publica_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptAuditoriaPublica($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisFiscalizacion('auditoria_publica', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Supervisión Financiera
     */
    public function analizarSupervisionFinanciera(array $datos): array
    {
        $cacheKey = 'ia_supervision_financiera_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptSupervisionFinanciera($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisFiscalizacion('supervision_financiera', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Gestión de Riesgos
     */
    public function analizarGestionRiesgosFiscal(array $datos): array
    {
        $cacheKey = 'ia_gestion_riesgos_fiscal_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptGestionRiesgosFiscal($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisFiscalizacion('gestion_riesgos_fiscal', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Cumplimiento Normativo
     */
    public function analizarCumplimientoNormativo(array $datos): array
    {
        $cacheKey = 'ia_cumplimiento_normativo_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptCumplimientoNormativo($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisFiscalizacion('cumplimiento_normativo', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Rendición de Cuentas Fiscal
     */
    public function analizarRendicionCuentasFiscal(array $datos): array
    {
        $cacheKey = 'ia_rendicion_cuentas_fiscal_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptRendicionCuentasFiscal($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisFiscalizacion('rendicion_cuentas_fiscal', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Control Interno
     */
    public function analizarControlInterno(array $datos): array
    {
        $cacheKey = 'ia_control_interno_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptControlInterno($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisFiscalizacion('control_interno', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de fiscalización
     */
    public function analisisIntegralFiscalizacion(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'control_fiscal',
            'auditoria_publica',
            'supervision_financiera',
            'gestion_riesgos_fiscal',
            'cumplimiento_normativo',
            'rendicion_cuentas_fiscal',
            'control_interno'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisFiscalizacion($resultados, $datos);
    }

    /**
     * Ejecutar análisis de fiscalización
     */
    protected function ejecutarAnalisisFiscalizacion(string $tipoAnalisis, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en fiscalización de nivel post-doctorado. " .
                                   "Especializado en control fiscal, auditoría pública, supervisión " .
                                   "financiera, gestión de riesgos, cumplimiento normativo y control " .
                                   "interno. Proporciona análisis exhaustivos con fundamentación " .
                                   "académica sólida y referencias a normativa vigente."
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

            $this->guardarAnalisisFiscalizacion($tipoAnalisis, $datos, $analisis, $resultado);

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

        throw new \RuntimeException('Error en análisis de fiscalización: ' . $response->body());
    }

    /**
     * Construir prompt para control fiscal
     */
    protected function construirPromptControlFiscal(array $datos): string
    {
        $tipoControl = $datos['tipo_control'] ?? 'general';
        $datosControl = $datos['datos_control'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - CONTROL FISCAL

TIPO DE CONTROL: {$tipoControl}
ENTIDAD: {$entidad}

DATOS DEL CONTROL:
{$datosControl}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL CONTROL FISCAL:
   - Constitución Política (Art. 267-269)
   - Ley 42 de 1993 (Ley Orgánica de Control Fiscal)
   - Decreto 267 de 2000 (Reglamento de Control Fiscal)
   - Ley 1474 de 2011 (Estatuto Anticorrupción)
   - Jurisprudencia constitucional sobre control fiscal
   - Estándares internacionales de auditoría

2. CONCEPTOS DE CONTROL FISCAL:
   - Concepto de control fiscal
   - Objetivos del control fiscal
   - Principios del control fiscal
   - Características del control fiscal
   - Alcance del control fiscal
   - Limitaciones del control fiscal

3. TIPOS DE CONTROL FISCAL:
   - Control previo
   - Control concurrente
   - Control posterior
   - Control selectivo
   - Control integral
   - Control especial

4. SUJETOS DEL CONTROL FISCAL:
   - Contraloría General de la República
   - Contralorías departamentales
   - Contralorías municipales
   - Contralorías distritales
   - Entidades de control fiscal
   - Organismos de control

5. PROCEDIMIENTOS DE CONTROL:
   - Planificación del control
   - Ejecución del control
   - Informes de control
   - Seguimiento de recomendaciones
   - Evaluación de resultados
   - Mejora continua

6. TÉCNICAS DE CONTROL:
   - Técnicas de auditoría
   - Técnicas de revisión
   - Técnicas de verificación
   - Técnicas de análisis
   - Técnicas de evaluación
   - Técnicas de seguimiento

7. INFORMES DE CONTROL:
   - Informes de auditoría
   - Informes de revisión
   - Informes especiales
   - Informes de seguimiento
   - Informes de evaluación
   - Informes de gestión

8. SEGUIMIENTO Y EVALUACIÓN:
   - Seguimiento de recomendaciones
   - Evaluación de impacto
   - Medición de resultados
   - Indicadores de gestión
   - Mejora continua
   - Innovación

9. PARTICIPACIÓN CIUDADANA:
   - Veedurías ciudadanas
   - Control social
   - Rendición de cuentas
   - Transparencia
   - Acceso a la información
   - Participación activa

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer el control fiscal
    - Mejorar la eficiencia
    - Optimizar los procedimientos
    - Desarrollar capacidades
    - Fomentar la participación
    - Promover la transparencia

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para auditoría pública
     */
    protected function construirPromptAuditoriaPublica(array $datos): string
    {
        $tipoAuditoria = $datos['tipo_auditoria'] ?? 'general';
        $datosAuditoria = $datos['datos_auditoria'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - AUDITORÍA PÚBLICA

TIPO DE AUDITORÍA: {$tipoAuditoria}
ENTIDAD: {$entidad}

DATOS DE AUDITORÍA:
{$datosAuditoria}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE AUDITORÍA PÚBLICA:
   - Ley 42 de 1993 (Ley Orgánica de Control Fiscal)
   - Decreto 267 de 2000 (Reglamento de Control Fiscal)
   - Estándares Internacionales de Auditoría (ISA)
   - Normas de Auditoría Gubernamental (NAG)
   - Estándares de Control Interno (COSO)
   - Marco de Referencia de Auditoría

2. CONCEPTOS DE AUDITORÍA PÚBLICA:
   - Concepto de auditoría pública
   - Objetivos de la auditoría pública
   - Principios de la auditoría pública
   - Características de la auditoría pública
   - Alcance de la auditoría pública
   - Limitaciones de la auditoría pública

3. TIPOS DE AUDITORÍA PÚBLICA:
   - Auditoría financiera
   - Auditoría de cumplimiento
   - Auditoría operacional
   - Auditoría de gestión
   - Auditoría de sistemas
   - Auditoría especial

4. METODOLOGÍA DE AUDITORÍA:
   - Planificación de la auditoría
   - Ejecución de la auditoría
   - Informes de auditoría
   - Seguimiento de hallazgos
   - Evaluación de resultados
   - Mejora continua

5. TÉCNICAS DE AUDITORÍA:
   - Técnicas de muestreo
   - Técnicas de verificación
   - Técnicas de análisis
   - Técnicas de evaluación
   - Técnicas de seguimiento
   - Técnicas de documentación

6. EVIDENCIA DE AUDITORÍA:
   - Concepto de evidencia
   - Tipos de evidencia
   - Criterios de evidencia
   - Evaluación de evidencia
   - Documentación de evidencia
   - Conservación de evidencia

7. HALLAZGOS DE AUDITORÍA:
   - Concepto de hallazgos
   - Tipos de hallazgos
   - Clasificación de hallazgos
   - Evaluación de hallazgos
   - Comunicación de hallazgos
   - Seguimiento de hallazgos

8. INFORMES DE AUDITORÍA:
   - Estructura del informe
   - Contenido del informe
   - Opinión del auditor
   - Recomendaciones
   - Seguimiento
   - Mejora continua

9. CALIDAD DE LA AUDITORÍA:
   - Control de calidad
   - Supervisión
   - Revisión
   - Evaluación
   - Mejora continua
   - Certificación

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer la auditoría pública
    - Mejorar la calidad
    - Optimizar la metodología
    - Desarrollar capacidades
    - Fomentar la innovación
    - Promover la excelencia

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para supervisión financiera
     */
    protected function construirPromptSupervisionFinanciera(array $datos): string
    {
        $tipoSupervision = $datos['tipo_supervision'] ?? 'general';
        $datosSupervision = $datos['datos_supervision'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - SUPERVISIÓN FINANCIERA

TIPO DE SUPERVISIÓN: {$tipoSupervision}
ENTIDAD: {$entidad}

DATOS DE SUPERVISIÓN:
{$datosSupervision}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE SUPERVISIÓN FINANCIERA:
   - Constitución Política (Art. 267-269)
   - Ley 42 de 1993 (Ley Orgánica de Control Fiscal)
   - Decreto 267 de 2000 (Reglamento de Control Fiscal)
   - Ley 1474 de 2011 (Estatuto Anticorrupción)
   - Estándares Internacionales de Supervisión
   - Marco de Basilea III

2. CONCEPTOS DE SUPERVISIÓN FINANCIERA:
   - Concepto de supervisión financiera
   - Objetivos de la supervisión
   - Principios de la supervisión
   - Características de la supervisión
   - Alcance de la supervisión
   - Limitaciones de la supervisión

3. TIPOS DE SUPERVISIÓN FINANCIERA:
   - Supervisión prudencial
   - Supervisión de conducta
   - Supervisión de mercado
   - Supervisión macroprudencial
   - Supervisión microprudencial
   - Supervisión integral

4. ÁREAS DE SUPERVISIÓN:
   - Supervisión de riesgos
   - Supervisión de capital
   - Supervisión de liquidez
   - Supervisión de operaciones
   - Supervisión de sistemas
   - Supervisión de cumplimiento

5. METODOLOGÍA DE SUPERVISIÓN:
   - Planificación de la supervisión
   - Ejecución de la supervisión
   - Evaluación de la supervisión
   - Seguimiento de la supervisión
   - Mejora continua
   - Innovación

6. TÉCNICAS DE SUPERVISIÓN:
   - Técnicas de análisis
   - Técnicas de evaluación
   - Técnicas de monitoreo
   - Técnicas de verificación
   - Técnicas de seguimiento
   - Técnicas de documentación

7. INDICADORES DE SUPERVISIÓN:
   - Indicadores de riesgo
   - Indicadores de capital
   - Indicadores de liquidez
   - Indicadores de operación
   - Indicadores de cumplimiento
   - Indicadores de calidad

8. GESTIÓN DE RIESGOS:
   - Identificación de riesgos
   - Evaluación de riesgos
   - Medidas de mitigación
   - Planes de contingencia
   - Monitoreo de riesgos
   - Actualización de riesgos

9. CUMPLIMIENTO NORMATIVO:
   - Normas aplicables
   - Procedimientos de cumplimiento
   - Evaluación de cumplimiento
   - Seguimiento de cumplimiento
   - Mejora del cumplimiento
   - Certificación

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer la supervisión
    - Mejorar la metodología
    - Optimizar los indicadores
    - Desarrollar capacidades
    - Fomentar la innovación
    - Promover la excelencia

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para gestión de riesgos fiscal
     */
    protected function construirPromptGestionRiesgosFiscal(array $datos): string
    {
        $tipoRiesgo = $datos['tipo_riesgo'] ?? 'general';
        $datosRiesgo = $datos['datos_riesgo'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - GESTIÓN DE RIESGOS FISCAL

TIPO DE RIESGO: {$tipoRiesgo}
ENTIDAD: {$entidad}

DATOS DE RIESGO:
{$datosRiesgo}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO CONCEPTUAL DE GESTIÓN DE RIESGOS:
   - Concepto de gestión de riesgos
   - Objetivos de la gestión de riesgos
   - Principios de la gestión de riesgos
   - Características de la gestión de riesgos
   - Alcance de la gestión de riesgos
   - Limitaciones de la gestión de riesgos

2. TIPOS DE RIESGOS FISCALES:
   - Riesgos operacionales
   - Riesgos financieros
   - Riesgos de cumplimiento
   - Riesgos reputacionales
   - Riesgos tecnológicos
   - Riesgos ambientales

3. IDENTIFICACIÓN DE RIESGOS:
   - Metodologías de identificación
   - Herramientas de identificación
   - Participación de stakeholders
   - Análisis de contexto
   - Análisis de vulnerabilidades
   - Análisis de amenazas

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
   - Formación especializada
   - Educación en prevención
   - Alfabetización digital
   - Formación de líderes
   - Escuelas de gestión

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer la gestión de riesgos
    - Mejorar la identificación
    - Optimizar la evaluación
    - Desarrollar capacidades
    - Fomentar la participación
    - Promover la innovación

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para cumplimiento normativo
     */
    protected function construirPromptCumplimientoNormativo(array $datos): string
    {
        $tipoCumplimiento = $datos['tipo_cumplimiento'] ?? 'general';
        $datosCumplimiento = $datos['datos_cumplimiento'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - CUMPLIMIENTO NORMATIVO

TIPO DE CUMPLIMIENTO: {$tipoCumplimiento}
ENTIDAD: {$entidad}

DATOS DE CUMPLIMIENTO:
{$datosCumplimiento}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE CUMPLIMIENTO:
   - Constitución Política
   - Leyes aplicables
   - Decretos reglamentarios
   - Resoluciones
   - Circulares
   - Directivas

2. CONCEPTOS DE CUMPLIMIENTO NORMATIVO:
   - Concepto de cumplimiento
   - Objetivos del cumplimiento
   - Principios del cumplimiento
   - Características del cumplimiento
   - Alcance del cumplimiento
   - Limitaciones del cumplimiento

3. TIPOS DE CUMPLIMIENTO:
   - Cumplimiento legal
   - Cumplimiento regulatorio
   - Cumplimiento contractual
   - Cumplimiento ético
   - Cumplimiento operacional
   - Cumplimiento integral

4. ÁREAS DE CUMPLIMIENTO:
   - Cumplimiento financiero
   - Cumplimiento operacional
   - Cumplimiento de recursos humanos
   - Cumplimiento de contratación
   - Cumplimiento ambiental
   - Cumplimiento de seguridad

5. METODOLOGÍA DE CUMPLIMIENTO:
   - Planificación del cumplimiento
   - Ejecución del cumplimiento
   - Evaluación del cumplimiento
   - Seguimiento del cumplimiento
   - Mejora continua
   - Innovación

6. TÉCNICAS DE CUMPLIMIENTO:
   - Técnicas de verificación
   - Técnicas de evaluación
   - Técnicas de monitoreo
   - Técnicas de seguimiento
   - Técnicas de documentación
   - Técnicas de reporte

7. INDICADORES DE CUMPLIMIENTO:
   - Indicadores de cumplimiento legal
   - Indicadores de cumplimiento regulatorio
   - Indicadores de cumplimiento contractual
   - Indicadores de cumplimiento ético
   - Indicadores de cumplimiento operacional
   - Indicadores de cumplimiento integral

8. GESTIÓN DE CUMPLIMIENTO:
   - Planes de cumplimiento
   - Procedimientos de cumplimiento
   - Políticas de cumplimiento
   - Capacitación en cumplimiento
   - Evaluación de cumplimiento
   - Mejora del cumplimiento

9. PARTICIPACIÓN CIUDADANA:
   - Mecanismos de participación
   - Consultas ciudadanas
   - Veedurías de cumplimiento
   - Control social
   - Seguimiento ciudadano
   - Evaluación ciudadana

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer el cumplimiento
    - Mejorar la metodología
    - Optimizar los indicadores
    - Desarrollar capacidades
    - Fomentar la participación
    - Promover la excelencia

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para rendición de cuentas fiscal
     */
    protected function construirPromptRendicionCuentasFiscal(array $datos): string
    {
        $tipoRendicion = $datos['tipo_rendicion'] ?? 'general';
        $datosRendicion = $datos['datos_rendicion'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - RENDICIÓN DE CUENTAS FISCAL

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
   - Objetivos de la rendición
   - Principios de la rendición
   - Características de la rendición
   - Alcance de la rendición
   - Limitaciones de la rendición

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
    - Fortalecer la rendición
    - Mejorar la participación
    - Optimizar la transparencia
    - Desarrollar capacidades
    - Fomentar la innovación
    - Promover la excelencia

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para control interno
     */
    protected function construirPromptControlInterno(array $datos): string
    {
        $tipoControl = $datos['tipo_control'] ?? 'general';
        $datosControl = $datos['datos_control'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - CONTROL INTERNO

TIPO DE CONTROL: {$tipoControl}
ENTIDAD: {$entidad}

DATOS DE CONTROL:
{$datosControl}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE CONTROL INTERNO:
   - Constitución Política (Art. 267-269)
   - Ley 42 de 1993 (Ley Orgánica de Control Fiscal)
   - Decreto 267 de 2000 (Reglamento de Control Fiscal)
   - Estándares de Control Interno (COSO)
   - Marco de Referencia de Control Interno
   - Normas Internacionales de Control Interno

2. CONCEPTOS DE CONTROL INTERNO:
   - Concepto de control interno
   - Objetivos del control interno
   - Principios del control interno
   - Características del control interno
   - Alcance del control interno
   - Limitaciones del control interno

3. TIPOS DE CONTROL INTERNO:
   - Control interno administrativo
   - Control interno contable
   - Control interno operacional
   - Control interno de cumplimiento
   - Control interno de información
   - Control interno integral

4. COMPONENTES DEL CONTROL INTERNO:
   - Ambiente de control
   - Evaluación de riesgos
   - Actividades de control
   - Información y comunicación
   - Monitoreo
   - Mejora continua

5. METODOLOGÍA DE CONTROL INTERNO:
   - Planificación del control
   - Ejecución del control
   - Evaluación del control
   - Seguimiento del control
   - Mejora continua
   - Innovación

6. TÉCNICAS DE CONTROL INTERNO:
   - Técnicas de verificación
   - Técnicas de evaluación
   - Técnicas de monitoreo
   - Técnicas de seguimiento
   - Técnicas de documentación
   - Técnicas de reporte

7. INDICADORES DE CONTROL INTERNO:
   - Indicadores de efectividad
   - Indicadores de eficiencia
   - Indicadores de cumplimiento
   - Indicadores de calidad
   - Indicadores de mejora
   - Indicadores de innovación

8. GESTIÓN DEL CONTROL INTERNO:
   - Planes de control interno
   - Procedimientos de control
   - Políticas de control
   - Capacitación en control
   - Evaluación de control
   - Mejora del control

9. PARTICIPACIÓN CIUDADANA:
   - Mecanismos de participación
   - Consultas ciudadanas
   - Veedurías de control
   - Control social
   - Seguimiento ciudadano
   - Evaluación ciudadana

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecer el control interno
    - Mejorar la metodología
    - Optimizar los indicadores
    - Desarrollar capacidades
    - Fomentar la participación
    - Promover la excelencia

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Consolidar análisis de fiscalización
     */
    protected function consolidarAnalisisFiscalizacion(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_fiscalizacion',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'riesgos_identificados' => [],
            'oportunidades' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - FISCALIZACIÓN\n\n";
        foreach ($resultados as $tipoAnalisis => $resultado) {
            if ($resultado['exito']) {
                $analisisConsolidado .= "=== {$tipoAnalisis} ===\n";
                $analisisConsolidado .= $resultado['analisis'] . "\n\n";
            }
        }

        $consolidado['analisis_consolidado'] = $analisisConsolidado;

        // Generar recomendaciones integrales
        $consolidado['recomendaciones_integrales'] = [
            'Fortalecer el control fiscal y la auditoría pública',
            'Mejorar la supervisión financiera y gestión de riesgos',
            'Establecer sistemas robustos de cumplimiento normativo',
            'Implementar rendición de cuentas efectiva',
            'Desarrollar capacidades de control interno',
            'Promover la participación ciudadana en la fiscalización'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis de fiscalización
     */
    protected function guardarAnalisisFiscalizacion(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis de fiscalización', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de fiscalización
     */
    public function obtenerEstadisticasFiscalizacion(): array
    {
        return [
            'total_analisis_fiscalizacion' => AnalisisIA::whereIn('especialidad', [
                'control_fiscal',
                'auditoria_publica',
                'supervision_financiera',
                'gestion_riesgos_fiscal',
                'cumplimiento_normativo',
                'rendicion_cuentas_fiscal',
                'control_interno'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'control_fiscal',
                'auditoria_publica',
                'supervision_financiera',
                'gestion_riesgos_fiscal',
                'cumplimiento_normativo',
                'rendicion_cuentas_fiscal',
                'control_interno'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'control_fiscal',
                'auditoria_publica',
                'supervision_financiera',
                'gestion_riesgos_fiscal',
                'cumplimiento_normativo',
                'rendicion_cuentas_fiscal',
                'control_interno'
            ])->sum('tokens_usados')
        ];
    }
}
