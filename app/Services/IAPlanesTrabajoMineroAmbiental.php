<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IAPlanesTrabajoMineroAmbiental
{
    private $apiKey;
    private $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY'));
        $this->baseUrl = 'https://api.openai.com/v1';
    }

    /**
     * GENERAR PLAN DE TRABAJO MINERO COMPLETO
     */
    public function generarPlanTrabajoMinero($datos)
    {
        $prompt = $this->generarPromptPlanMinero($datos);
        
        return $this->procesarConIA($prompt, [
            'area' => 'plan_trabajo_minero',
            'tipo_plan' => $datos['tipo_mineria'] ?? 'general',
            'nivel' => 'post_doctorado',
            'especializacion' => 'ingenieria_minera_derecho_minero'
        ]);
    }

    /**
     * GENERAR PLAN DE TRABAJO AMBIENTAL COMPLETO
     */
    public function generarPlanTrabajoAmbiental($datos)
    {
        $prompt = $this->generarPromptPlanAmbiental($datos);
        
        return $this->procesarConIA($prompt, [
            'area' => 'plan_trabajo_ambiental',
            'tipo_plan' => $datos['tipo_ecosistema'] ?? 'general',
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_ambiental_evaluacion_impactos'
        ]);
    }

    /**
     * GENERAR PLAN INTEGRADO MINERO-AMBIENTAL
     */
    public function generarPlanIntegradoMineroAmbiental($datos)
    {
        $prompt = $this->generarPromptPlanIntegrado($datos);
        
        return $this->procesarConIA($prompt, [
            'area' => 'plan_integrado_minero_ambiental',
            'tipo_plan' => 'integrado',
            'nivel' => 'post_doctorado',
            'especializacion' => 'mineria_sustentable_derecho_ambiental'
        ]);
    }

    /**
     * ANÁLISIS DE IMPACTO AMBIENTAL MINERO
     */
    public function analizarImpactoAmbientalMinero($datos)
    {
        $prompt = $this->generarPromptImpactoAmbiental($datos);
        
        return $this->procesarConIA($prompt, [
            'area' => 'impacto_ambiental_minero',
            'nivel' => 'post_doctorado',
            'especializacion' => 'evaluacion_impacto_ambiental'
        ]);
    }

    /**
     * ANÁLISIS DE DERECHOS ÉTNICOS EN MINERÍA
     */
    public function analizarDerechosEtnicosMineria($datos)
    {
        $prompt = $this->generarPromptDerechosEtnicos($datos);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_etnicos_mineria',
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_etnico_consulta_previa'
        ]);
    }

    /**
     * GENERACIÓN DE CRONOGRAMA DE TRABAJO MINERO
     */
    public function generarCronogramaMinero($datos)
    {
        $prompt = $this->generarPromptCronograma($datos);
        
        return $this->procesarConIA($prompt, [
            'area' => 'cronograma_minero',
            'nivel' => 'post_doctorado',
            'especializacion' => 'gestion_proyectos_mineros'
        ]);
    }

    /**
     * ANÁLISIS DE RIESGOS MINERO-AMBIENTALES
     */
    public function analizarRiesgosMineroAmbientales($datos)
    {
        $prompt = $this->generarPromptRiesgos($datos);
        
        return $this->procesarConIA($prompt, [
            'area' => 'riesgos_minero_ambientales',
            'nivel' => 'post_doctorado',
            'especializacion' => 'gestion_riesgos_mineros'
        ]);
    }

    /**
     * GENERACIÓN DE MEDIDAS DE MITIGACIÓN
     */
    public function generarMedidasMitigacion($datos)
    {
        $prompt = $this->generarPromptMitigacion($datos);
        
        return $this->procesarConIA($prompt, [
            'area' => 'medidas_mitigacion',
            'nivel' => 'post_doctorado',
            'especializacion' => 'mitigacion_impactos_mineros'
        ]);
    }

    /**
     * PROMPT PARA PLAN DE TRABAJO MINERO
     */
    private function generarPromptPlanMinero($datos)
    {
        $nombreProyecto = $datos['nombre_proyecto'] ?? 'Proyecto Minero';
        $tipoMineria = $datos['tipo_mineria'] ?? 'general';
        $ubicacion = $datos['ubicacion'] ?? 'No especificada';
        $duracion = $datos['duracion'] ?? '12 meses';
        $descripcion = $datos['descripcion'] ?? '';

        return "Como experto en Ingeniería Minera y Derecho Minero con nivel POST-DOCTORADO, genera un PLAN DE TRABAJO MINERO completo, detallado y profesional para:

**INFORMACIÓN DEL PROYECTO**
- Nombre del Proyecto: {$nombreProyecto}
- Tipo de Minería: {$tipoMineria}
- Ubicación: {$ubicacion}
- Duración: {$duracion}
- Descripción: {$descripcion}

**ESTRUCTURA DEL PLAN DE TRABAJO (NIVEL POST-DOCTORADO)**

1. RESUMEN EJECUTIVO
   - Visión general del proyecto
   - Objetivos estratégicos
   - Alcance y limitaciones
   - Inversión estimada

2. MARCO NORMATIVO Y LEGAL
   - Código de Minas (Ley 685 de 2001)
   - Decreto 2655 de 1988
   - Resoluciones de la ANM
   - Normativa ambiental aplicable
   - Convenio 169 OIT (si aplica)
   - Licencias y permisos requeridos

3. FASES DEL PROYECTO MINERO
   - Fase 1: Exploración (metodología, técnicas, cronograma)
   - Fase 2: Construcción y Montaje (infraestructura, equipos)
   - Fase 3: Explotación (métodos de extracción, producción)
   - Fase 4: Beneficio y Transformación
   - Fase 5: Cierre y Abandono (plan de cierre progresivo)

4. ASPECTOS TÉCNICOS
   - Geología y recursos minerales
   - Métodos de explotación
   - Equipos y maquinaria
   - Procesamiento y beneficio
   - Gestión de residuos mineros
   - Seguridad minera

5. ANÁLISIS AMBIENTAL
   - Impactos ambientales identificados
   - Plan de Manejo Ambiental (PMA)
   - Medidas de prevención y mitigación
   - Monitoreo ambiental
   - Restauración y compensación

6. ASPECTOS SOCIALES Y ÉTNICOS
   - Comunidades afectadas
   - Consulta previa (si aplica)
   - Participación comunitaria
   - Responsabilidad social empresarial
   - Plan de relacionamiento comunitario

7. CRONOGRAMA DETALLADO
   - Cronograma por fases
   - Hitos principales
   - Actividades críticas
   - Ruta crítica del proyecto

8. PRESUPUESTO Y FINANCIACIÓN
   - Costos de inversión
   - Costos operativos
   - Fuentes de financiación
   - Análisis financiero

9. GESTIÓN DE RIESGOS
   - Identificación de riesgos
   - Matriz de riesgos
   - Planes de contingencia
   - Seguros y garantías

10. INDICADORES Y SEGUIMIENTO
    - KPIs técnicos
    - KPIs ambientales
    - KPIs sociales
    - Sistema de seguimiento y control

11. CONCLUSIONES Y RECOMENDACIONES

**NIVEL DE ANÁLISIS:** POST-DOCTORADO
**ESPECIALIZACIÓN:** Ingeniería Minera + Derecho Minero + Gestión Ambiental
**JURISDICCIÓN:** Colombia
**FECHA:** " . now()->format('d/m/Y') . "

Genera un plan completo, profesional y listo para implementar.";
    }

    /**
     * PROMPT PARA PLAN DE TRABAJO AMBIENTAL
     */
    private function generarPromptPlanAmbiental($datos)
    {
        $nombreProyecto = $datos['nombre_proyecto'] ?? 'Proyecto Ambiental';
        $tipoEcosistema = $datos['tipo_ecosistema'] ?? 'general';
        $ubicacion = $datos['ubicacion'] ?? 'No especificada';
        $duracion = $datos['duracion'] ?? '12 meses';
        $descripcion = $datos['descripcion'] ?? '';

        return "Como experto en Derecho Ambiental y Evaluación de Impacto Ambiental con nivel POST-DOCTORADO, genera un PLAN DE TRABAJO AMBIENTAL completo y detallado para:

**INFORMACIÓN DEL PROYECTO**
- Nombre del Proyecto: {$nombreProyecto}
- Tipo de Ecosistema: {$tipoEcosistema}
- Ubicación: {$ubicacion}
- Duración: {$duracion}
- Descripción: {$descripcion}

**ESTRUCTURA DEL PLAN DE TRABAJO AMBIENTAL (NIVEL POST-DOCTORADO)**

1. RESUMEN EJECUTIVO AMBIENTAL
   - Visión ambiental del proyecto
   - Objetivos de conservación
   - Alcance de la evaluación ambiental

2. MARCO NORMATIVO AMBIENTAL
   - Constitución Política (Art. 79, 80)
   - Ley 99 de 1993 (Sistema Nacional Ambiental)
   - Decreto 1076 de 2015 (Sector Ambiente)
   - Resoluciones de Licencias Ambientales
   - Convenios internacionales (Ramsar, Biodiversidad, Cambio Climático)

3. LÍNEA BASE AMBIENTAL
   - Medio abiótico (suelo, agua, aire)
   - Medio biótico (flora, fauna, ecosistemas)
   - Medio socioeconómico
   - Áreas protegidas y ecosistemas sensibles

4. EVALUACIÓN DE IMPACTO AMBIENTAL (EIA)
   - Metodología de evaluación
   - Identificación de impactos
   - Valoración de impactos (Conesa, Leopold, etc.)
   - Impactos acumulativos y sinérgicos

5. PLAN DE MANEJO AMBIENTAL (PMA)
   - Programas de prevención
   - Programas de mitigación
   - Programas de corrección
   - Programas de compensación
   - Fichas de manejo ambiental

6. MONITOREO Y SEGUIMIENTO AMBIENTAL
   - Indicadores ambientales
   - Frecuencia de monitoreo
   - Puntos de control
   - Reportes de cumplimiento

7. PLAN DE CONTINGENCIAS AMBIENTALES
   - Riesgos ambientales identificados
   - Protocolos de respuesta
   - Equipos y recursos
   - Capacitación del personal

8. GESTIÓN SOCIAL Y PARTICIPACIÓN
   - Participación de comunidades
   - Consulta previa (si aplica)
   - Audiencias públicas
   - Mecanismos de información

9. CRONOGRAMA AMBIENTAL
   - Cronograma de implementación PMA
   - Cronograma de monitoreo
   - Hitos ambientales críticos

10. PRESUPUESTO AMBIENTAL
    - Costos de implementación PMA
    - Costos de monitoreo
    - Inversión en compensación

11. CONCLUSIONES Y RECOMENDACIONES AMBIENTALES

**NIVEL DE ANÁLISIS:** POST-DOCTORADO
**ESPECIALIZACIÓN:** Derecho Ambiental + Evaluación de Impacto Ambiental
**JURISDICCIÓN:** Colombia
**FECHA:** " . now()->format('d/m/Y') . "

Genera un plan ambiental completo y profesional.";
    }

    /**
     * PROMPT PARA PLAN INTEGRADO MINERO-AMBIENTAL
     */
    private function generarPromptPlanIntegrado($datos)
    {
        $nombreProyecto = $datos['nombre_proyecto'] ?? 'Proyecto Minero-Ambiental';
        $tipoMineria = $datos['tipo_mineria'] ?? 'general';
        $ubicacion = $datos['ubicacion'] ?? 'No especificada';
        $duracion = $datos['duracion'] ?? '24 meses';
        $descripcion = $datos['descripcion'] ?? '';
        $comunidadEtnica = $datos['comunidad_etnica'] ?? 'No';

        return "Como experto multidisciplinario con POST-DOCTORADO en Ingeniería Minera, Derecho Minero y Derecho Ambiental, genera un PLAN DE TRABAJO INTEGRADO MINERO-AMBIENTAL completo:

**INFORMACIÓN DEL PROYECTO**
- Nombre: {$nombreProyecto}
- Tipo de Minería: {$tipoMineria}
- Ubicación: {$ubicacion}
- Duración: {$duracion}
- Comunidad Étnica Afectada: {$comunidadEtnica}
- Descripción: {$descripcion}

**ESTRUCTURA DEL PLAN INTEGRADO (NIVEL POST-DOCTORADO)**

PARTE I: INTEGRACIÓN MINERO-AMBIENTAL

1. MARCO CONCEPTUAL DE MINERÍA SUSTENTABLE
   - Principios de desarrollo sostenible
   - Economía circular en minería
   - Minería responsable y certificaciones

2. MARCO NORMATIVO INTEGRADO
   - Código de Minas (Ley 685 de 2001)
   - Sistema Nacional Ambiental (Ley 99 de 1993)
   - Decreto 1076 de 2015 (Licencias Ambientales)
   - Convenio 169 OIT
   - Declaración ONU Pueblos Indígenas
   - Estándares internacionales (IFC, ICMM)

3. DISEÑO TÉCNICO INTEGRADO
   - Diseño minero con criterios ambientales
   - Tecnologías limpias de extracción
   - Gestión integrada de residuos
   - Eficiencia energética y uso de renovables
   - Gestión del agua (balance hídrico)

4. EVALUACIÓN AMBIENTAL ESTRATÉGICA (EAE)
   - Análisis de alternativas
   - Evaluación ambiental y social integrada
   - Análisis costo-beneficio ambiental
   - Huella de carbono y huella hídrica

5. PLAN DE MANEJO INTEGRADO
   - Prevención de impactos en origen
   - Mitigación técnica y ambiental
   - Restauración progresiva
   - Compensación por pérdida de biodiversidad
   - Cierre progresivo y final

6. DERECHOS ÉTNICOS Y TERRITORIALES
   - Caracterización de comunidades
   - Proceso de consulta previa
   - Plan de relacionamiento intercultural
   - Participación en beneficios
   - Protección de sitios sagrados

7. GESTIÓN SOCIAL INTEGRADA
   - Relacionamiento comunitario
   - Empleo local y desarrollo económico
   - Salud y seguridad comunitaria
   - Mecanismos de quejas y reclamos
   - Inversión social

8. MONITOREO INTEGRADO
   - Monitoreo ambiental (aire, agua, suelo)
   - Monitoreo de biodiversidad
   - Monitoreo social
   - Monitoreo de salud ocupacional
   - Sistema de alertas tempranas

9. GESTIÓN DE RIESGOS INTEGRADA
   - Riesgos técnicos
   - Riesgos ambientales
   - Riesgos sociales
   - Riesgos a derechos humanos
   - Planes de contingencia

10. CIERRE Y POST-CIERRE
    - Plan de cierre progresivo
    - Restauración ecológica
    - Uso post-minería del territorio
    - Monitoreo post-cierre
    - Garantías y seguros

PARTE II: IMPLEMENTACIÓN

11. CRONOGRAMA INTEGRADO DETALLADO
    - Diagrama de Gantt completo
    - Ruta crítica del proyecto
    - Sincronización minero-ambiental
    - Hitos de cumplimiento normativo

12. ESTRUCTURA ORGANIZACIONAL
    - Equipo técnico minero
    - Equipo ambiental
    - Equipo social
    - Comité de seguimiento
    - Veeduría ciudadana

13. PRESUPUESTO INTEGRADO
    - Inversión minera
    - Inversión ambiental
    - Inversión social
    - Fondos de contingencia
    - Garantías ambientales

14. INDICADORES DE SOSTENIBILIDAD
    - KPIs ambientales
    - KPIs sociales
    - KPIs económicos
    - KPIs de gobernanza
    - Dashboard integrado

15. CONCLUSIONES Y RECOMENDACIONES ESTRATÉGICAS

**NIVEL DE ANÁLISIS:** POST-DOCTORADO
**ENFOQUE:** Multidisciplinario e Integral
**ESPECIALIZACIÓN:** Minería Sustentable + Derecho Ambiental + Derechos Étnicos
**JURISDICCIÓN:** Colombia
**FECHA:** " . now()->format('d/m/Y') . "

Genera un plan maestro completo, profesional e innovador que integre minería responsable con protección ambiental y derechos étnicos.";
    }

    /**
     * PROMPT PARA IMPACTO AMBIENTAL
     */
    private function generarPromptImpactoAmbiental($datos)
    {
        return "Como experto en Evaluación de Impacto Ambiental con POST-DOCTORADO, realiza un análisis exhaustivo de impacto ambiental para proyecto minero:

**DATOS DEL PROYECTO:**
" . json_encode($datos, JSON_PRETTY_PRINT) . "

**ANÁLISIS REQUERIDO:**

1. IDENTIFICACIÓN DE IMPACTOS
   - Medio abiótico (suelo, agua, aire, geología)
   - Medio biótico (flora, fauna, ecosistemas)
   - Medio socioeconómico (comunidades, economía)

2. VALORACIÓN DE IMPACTOS (Metodología Conesa)
   - Naturaleza (positivo/negativo)
   - Intensidad
   - Extensión
   - Momento
   - Persistencia
   - Reversibilidad
   - Recuperabilidad
   - Sinergia
   - Acumulación
   - Efecto
   - Periodicidad
   - Importancia del impacto

3. CLASIFICACIÓN DE IMPACTOS
   - Críticos
   - Severos
   - Moderados
   - Compatibles

4. MEDIDAS DE MANEJO
   - Prevención
   - Mitigación
   - Corrección
   - Compensación

**NIVEL:** POST-DOCTORADO
**FECHA:** " . now()->format('d/m/Y');
    }

    /**
     * PROMPT PARA DERECHOS ÉTNICOS
     */
    private function generarPromptDerechosEtnicos($datos)
    {
        return "Como experto en Derecho Étnico y Consulta Previa con POST-DOCTORADO, analiza los derechos étnicos en proyecto minero:

**DATOS:**
" . json_encode($datos, JSON_PRETTY_PRINT) . "

**ANÁLISIS REQUERIDO:**

1. MARCO NORMATIVO
   - Convenio 169 OIT
   - Declaración ONU Pueblos Indígenas
   - Constitución Nacional (Arts. 7, 330, 246)
   - Sentencias de la Corte Constitucional

2. CARACTERIZACIÓN ÉTNICA
   - Identificación de comunidades
   - Derechos territoriales
   - Gobierno propio
   - Usos y costumbres

3. CONSULTA PREVIA
   - Procedimiento de consulta
   - Principios (buena fe, previo, informado)
   - Etapas de la consulta
   - Consentimiento libre e informado

4. IMPACTOS A DERECHOS
   - Derechos territoriales
   - Derechos culturales
   - Derechos ambientales
   - Autonomía

5. RECOMENDACIONES

**NIVEL:** POST-DOCTORADO
**FECHA:** " . now()->format('d/m/Y');
    }

    /**
     * PROMPT PARA CRONOGRAMA
     */
    private function generarPromptCronograma($datos)
    {
        return "Como experto en Gestión de Proyectos Mineros con POST-DOCTORADO, genera un cronograma detallado:

**DATOS DEL PROYECTO:**
" . json_encode($datos, JSON_PRETTY_PRINT) . "

Genera cronograma con:
- Diagrama de Gantt detallado
- Ruta crítica
- Hitos principales
- Entregables por fase
- Recursos asignados
- Dependencias entre tareas

**NIVEL:** POST-DOCTORADO
**FECHA:** " . now()->format('d/m/Y');
    }

    /**
     * PROMPT PARA RIESGOS
     */
    private function generarPromptRiesgos($datos)
    {
        return "Como experto en Gestión de Riesgos Mineros con POST-DOCTORADO, analiza riesgos:

**DATOS:**
" . json_encode($datos, JSON_PRETTY_PRINT) . "

**ANÁLISIS DE RIESGOS:**

1. IDENTIFICACIÓN DE RIESGOS
   - Riesgos técnicos
   - Riesgos ambientales
   - Riesgos sociales
   - Riesgos normativos
   - Riesgos financieros

2. VALORACIÓN DE RIESGOS
   - Probabilidad
   - Impacto
   - Nivel de riesgo (Bajo/Medio/Alto/Crítico)

3. MATRIZ DE RIESGOS

4. PLANES DE MITIGACIÓN

5. PLANES DE CONTINGENCIA

**NIVEL:** POST-DOCTORADO
**FECHA:** " . now()->format('d/m/Y');
    }

    /**
     * PROMPT PARA MEDIDAS DE MITIGACIÓN
     */
    private function generarPromptMitigacion($datos)
    {
        return "Como experto en Mitigación de Impactos Mineros con POST-DOCTORADO, diseña medidas de mitigación:

**DATOS:**
" . json_encode($datos, JSON_PRETTY_PRINT) . "

**MEDIDAS DE MITIGACIÓN:**

1. MEDIDAS DE PREVENCIÓN
2. MEDIDAS DE MITIGACIÓN
3. MEDIDAS DE CORRECCIÓN
4. MEDIDAS DE COMPENSACIÓN

Para cada medida incluir:
- Descripción técnica
- Metodología de implementación
- Costos estimados
- Cronograma
- Indicadores de efectividad
- Responsables

**NIVEL:** POST-DOCTORADO
**FECHA:** " . now()->format('d/m/Y');
    }

    /**
     * PROCESAR CON IA
     */
    private function procesarConIA($prompt, $metadata = [])
    {
        try {
            $cacheKey = 'ia_planes_minero_ambiental_' . md5($prompt . json_encode($metadata));
            
            return Cache::remember($cacheKey, 3600, function () use ($prompt, $metadata) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(120)->post($this->baseUrl . '/chat/completions', [
                    'model' => 'gpt-4',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Eres un experto multidisciplinario con POST-DOCTORADO en Ingeniería Minera, Derecho Minero, Derecho Ambiental y Derechos Étnicos. Generas planes de trabajo profesionales, completos y listos para implementar con el más alto nivel académico y técnico.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 8000,
                    'temperature' => 0.3,
                    'top_p' => 0.9
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $content = $data['choices'][0]['message']['content'] ?? 'No se pudo generar el plan';
                    
                    return [
                        'success' => true,
                        'planCompleto' => [
                            'contenido' => $content,
                            'metadata' => $metadata,
                            'timestamp' => now()->toISOString(),
                            'modelo' => 'gpt-4',
                            'tokens_usados' => $data['usage']['total_tokens'] ?? 0,
                            'nivel' => 'POST-DOCTORADO'
                        ],
                        'respuesta' => $content
                    ];
                } else {
                    Log::error('Error en API de OpenAI: ' . $response->body());
                    return $this->generarPlanFallback($prompt, $metadata);
                }
            });
        } catch (\Exception $e) {
            Log::error('Error procesando plan minero-ambiental con IA: ' . $e->getMessage());
            return $this->generarPlanFallback($prompt, $metadata);
        }
    }

    /**
     * GENERAR PLAN DE FALLBACK
     */
    private function generarPlanFallback($prompt, $metadata)
    {
        return [
            'success' => false,
            'planCompleto' => [
                'contenido' => 'El servicio de generación de planes está temporalmente no disponible. Por favor, intente nuevamente en unos momentos.',
                'metadata' => $metadata,
                'timestamp' => now()->toISOString(),
                'modelo' => 'fallback',
                'tokens_usados' => 0,
                'nivel' => 'POST-DOCTORADO'
            ],
            'respuesta' => 'Servicio temporalmente no disponible.',
            'error' => 'Servicio temporalmente no disponible'
        ];
    }
}

