<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para Análisis de Planes de Gobierno y Desarrollo Territorial
 * Nivel Post-Doctorado especializado en planificación territorial y gobernanza
 */
class IAPlanesGobiernoTerritorial
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
     * Análisis de Plan de Desarrollo Municipal
     */
    public function analizarPlanDesarrolloMunicipal(array $datos): array
    {
        $cacheKey = 'ia_plan_municipal_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 14400, function () use ($datos) {
            $prompt = $this->construirPromptPlanMunicipal($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPlan('plan_desarrollo_municipal', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Plan de Desarrollo Departamental
     */
    public function analizarPlanDesarrolloDepartamental(array $datos): array
    {
        $cacheKey = 'ia_plan_departamental_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 14400, function () use ($datos) {
            $prompt = $this->construirPromptPlanDepartamental($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPlan('plan_desarrollo_departamental', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Plan de Ordenamiento Territorial
     */
    public function analizarPlanOrdenamientoTerritorial(array $datos): array
    {
        $cacheKey = 'ia_pot_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 14400, function () use ($datos) {
            $prompt = $this->construirPromptPOT($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPlan('plan_ordenamiento_territorial', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Plan de Gobierno Étnico
     */
    public function analizarPlanGobiernoEtnico(array $datos): array
    {
        $cacheKey = 'ia_plan_etnico_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 14400, function () use ($datos) {
            $prompt = $this->construirPromptPlanEtnico($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPlan('plan_gobierno_etnico', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Plan de Vida Comunitario
     */
    public function analizarPlanVidaComunitario(array $datos): array
    {
        $cacheKey = 'ia_plan_vida_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 14400, function () use ($datos) {
            $prompt = $this->construirPromptPlanVida($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPlan('plan_vida_comunitario', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Plan Etnodesarrollo
     */
    public function analizarPlanEtnodesarrollo(array $datos): array
    {
        $cacheKey = 'ia_etnodesarrollo_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 14400, function () use ($datos) {
            $prompt = $this->construirPromptEtnodesarrollo($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPlan('plan_etnodesarrollo', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Plan Anti-Corrupción
     */
    public function analizarPlanAnticorrupcion(array $datos): array
    {
        $cacheKey = 'ia_plan_anticorrupcion_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 14400, function () use ($datos) {
            $prompt = $this->construirPromptPlanAnticorrupcion($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPlan('plan_anticorrupcion', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Plan de Ética y Transparencia
     */
    public function analizarPlanEticaTransparencia(array $datos): array
    {
        $cacheKey = 'ia_plan_etica_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 14400, function () use ($datos) {
            $prompt = $this->construirPromptPlanEtica($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisPlan('plan_etica_transparencia', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de planes de gobierno
     */
    public function analisisIntegralPlanesGobierno(array $datos): array
    {
        $tiposPlan = $datos['tipos_plan'] ?? ['desarrollo_municipal', 'ordenamiento_territorial', 'etnico'];
        $resultados = [];

        foreach ($tiposPlan as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisPlanes($resultados, $datos);
    }

    /**
     * Ejecutar análisis de plan
     */
    protected function ejecutarAnalisisPlan(string $tipoPlan, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en planificación territorial y gobernanza de nivel post-doctorado. " .
                                   "Especializado en análisis de planes de desarrollo, ordenamiento territorial, " .
                                   "gobernanza étnica y políticas públicas. Proporciona análisis exhaustivos con " .
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

            $this->guardarAnalisisPlan($tipoPlan, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'tipo_plan' => $tipoPlan,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $this->modelo,
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis de plan: ' . $response->body());
    }

    /**
     * Construir prompt para plan de desarrollo municipal
     */
    protected function construirPromptPlanMunicipal(array $datos): string
    {
        $municipio = $datos['municipio'] ?? 'Municipio';
        $periodo = $datos['periodo'] ?? '2024-2027';
        $contenidoPlan = $datos['contenido_plan'] ?? '';
        $poblacion = $datos['poblacion'] ?? 'No especificada';

        return "ANÁLISIS POST-DOCTORADO - PLAN DE DESARROLLO MUNICIPAL

MUNICIPIO: {$municipio}
PERÍODO: {$periodo}
POBLACIÓN: {$poblacion}

CONTENIDO DEL PLAN:
{$contenidoPlan}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO Y LEGAL:
   - Ley 152 de 1994 (Ley Orgánica del Plan de Desarrollo)
   - Constitución Política (Art. 339-344)
   - Ley 388 de 1997 (Ley de Desarrollo Territorial)
   - Jurisprudencia constitucional sobre planes de desarrollo
   - Normativa departamental aplicable

2. DIAGNÓSTICO TERRITORIAL:
   - Análisis de la situación actual
   - Identificación de problemas estructurales
   - Fortalezas y oportunidades
   - Debilidades y amenazas
   - Participación ciudadana en el diagnóstico

3. ESTRUCTURA DEL PLAN:
   - Visión territorial
   - Misión institucional
   - Objetivos estratégicos
   - Programas y subprogramas
   - Proyectos prioritarios
   - Metas e indicadores

4. DIMENSIONES DE DESARROLLO:
   - Dimensión económica
   - Dimensión social
   - Dimensión ambiental
   - Dimensión institucional
   - Dimensión territorial
   - Dimensión cultural

5. FINANCIACIÓN Y PRESUPUESTO:
   - Recursos propios municipales
   - Transferencias nacionales
   - Transferencias departamentales
   - Recursos de crédito
   - Recursos de cooperación
   - Sostenibilidad fiscal

6. PARTICIPACIÓN CIUDADANA:
   - Mecanismos de participación
   - Consultas ciudadanas
   - Veedurías ciudadanas
   - Rendición de cuentas
   - Control social
   - Evaluación ciudadana

7. COORDINACIÓN INTERINSTITUCIONAL:
   - Coordinación con departamento
   - Coordinación con nación
   - Coordinación con otros municipios
   - Coordinación con sector privado
   - Coordinación con sociedad civil
   - Alianzas estratégicas

8. SEGUIMIENTO Y EVALUACIÓN:
   - Sistema de seguimiento
   - Indicadores de gestión
   - Evaluación de resultados
   - Evaluación de impacto
   - Ajustes y modificaciones
   - Rendición de cuentas

9. ANÁLISIS DE COHERENCIA:
   - Coherencia interna del plan
   - Coherencia con planes superiores
   - Coherencia con políticas sectoriales
   - Coherencia con instrumentos de planificación
   - Coherencia con presupuesto
   - Coherencia con competencias

10. RECOMENDACIONES ESTRATÉGICAS:
    - Fortalecimiento institucional
    - Mejora de la participación
    - Optimización de recursos
    - Gestión del riesgo
    - Sostenibilidad ambiental
    - Innovación y tecnología

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para plan de desarrollo departamental
     */
    protected function construirPromptPlanDepartamental(array $datos): string
    {
        $departamento = $datos['departamento'] ?? 'Departamento';
        $periodo = $datos['periodo'] ?? '2024-2027';
        $contenidoPlan = $datos['contenido_plan'] ?? '';
        $municipios = $datos['municipios'] ?? 'No especificados';

        return "ANÁLISIS POST-DOCTORADO - PLAN DE DESARROLLO DEPARTAMENTAL

DEPARTAMENTO: {$departamento}
PERÍODO: {$periodo}
MUNICIPIOS: {$municipios}

CONTENIDO DEL PLAN:
{$contenidoPlan}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEPARTAMENTAL:
   - Ley 152 de 1994 (Ley Orgánica del Plan de Desarrollo)
   - Constitución Política (Art. 339-344)
   - Ley 1454 de 2011 (Ley Orgánica de Ordenamiento Territorial)
   - Jurisprudencia constitucional departamental
   - Normativa departamental específica

2. COMPETENCIAS DEPARTAMENTALES:
   - Competencias propias
   - Competencias compartidas
   - Competencias delegadas
   - Competencias concurrentes
   - Competencias de coordinación
   - Competencias de complementariedad

3. COORDINACIÓN TERRITORIAL:
   - Coordinación con municipios
   - Coordinación con nación
   - Coordinación regional
   - Coordinación interdepartamental
   - Coordinación con entidades territoriales indígenas
   - Coordinación con áreas metropolitanas

4. DIMENSIONES REGIONALES:
   - Desarrollo económico regional
   - Integración territorial
   - Conectividad regional
   - Servicios públicos regionales
   - Gestión ambiental regional
   - Cultura regional

5. FINANCIACIÓN REGIONAL:
   - Sistema General de Participaciones
   - Sistema General de Regalías
   - Recursos propios departamentales
   - Recursos de crédito
   - Recursos de cooperación
   - Fondos de compensación

6. PLANIFICACIÓN REGIONAL:
   - Planes de desarrollo departamental
   - Planes de ordenamiento territorial departamental
   - Planes sectoriales departamentales
   - Planes de inversión departamental
   - Planes de gestión del riesgo
   - Planes de competitividad

7. GESTIÓN PÚBLICA DEPARTAMENTAL:
   - Modernización administrativa
   - Gestión por resultados
   - Gobierno digital
   - Transparencia y rendición de cuentas
   - Participación ciudadana
   - Control social

8. DESARROLLO SECTORIAL:
   - Sector salud
   - Sector educación
   - Sector vivienda
   - Sector transporte
   - Sector ambiente
   - Sector agropecuario

9. INTEGRACIÓN Y COMPETITIVIDAD:
   - Estrategias de integración
   - Clusters productivos
   - Corredores logísticos
   - Innovación y tecnología
   - Emprendimiento
   - Turismo regional

10. SEGUIMIENTO Y EVALUACIÓN REGIONAL:
    - Sistema de seguimiento departamental
    - Indicadores regionales
    - Evaluación de impacto regional
    - Rendición de cuentas departamental
    - Control social regional
    - Evaluación ciudadana

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para Plan de Ordenamiento Territorial
     */
    protected function construirPromptPOT(array $datos): string
    {
        $municipio = $datos['municipio'] ?? 'Municipio';
        $periodo = $datos['periodo'] ?? '2024-2035';
        $contenidoPOT = $datos['contenido_pot'] ?? '';
        $categoria = $datos['categoria'] ?? 'Municipio de categoría especial';

        return "ANÁLISIS POST-DOCTORADO - PLAN DE ORDENAMIENTO TERRITORIAL (POT)

MUNICIPIO: {$municipio}
PERÍODO: {$periodo}
CATEGORÍA: {$categoria}

CONTENIDO DEL POT:
{$contenidoPOT}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DEL POT:
   - Ley 388 de 1997 (Ley de Desarrollo Territorial)
   - Decreto 1077 de 2015 (Único Reglamentario del Sector Vivienda)
   - Decreto 400 de 2012 (Reglamento Catastral)
   - Jurisprudencia constitucional territorial
   - Normativa departamental aplicable

2. COMPONENTES DEL POT:
   - Componente general
   - Componente urbano
   - Componente rural
   - Componente de gestión del riesgo
   - Componente de cambio climático
   - Componente de movilidad

3. CLASIFICACIÓN DEL SUELO:
   - Suelo urbano
   - Suelo de expansión urbana
   - Suelo rural
   - Suelo de protección
   - Suelo de conservación
   - Suelo de riesgo

4. DESTINACIÓN ECONÓMICA:
   - Zonas residenciales
   - Zonas comerciales
   - Zonas industriales
   - Zonas de equipamiento
   - Zonas de espacio público
   - Zonas de servicios

5. NORMAS URBANÍSTICAS:
   - Usos del suelo
   - Intensidad de ocupación
   - Altura máxima
   - Área mínima de lote
   - Retiros y aislamientos
   - Áreas verdes

6. SISTEMA DE ESPACIOS PÚBLICOS:
   - Parques y zonas verdes
   - Plazas y plazoletas
   - Vías peatonales
   - Ciclorrutas
   - Equipamientos colectivos
   - Áreas de recreación

7. SISTEMA DE MOVILIDAD:
   - Red vial primaria
   - Red vial secundaria
   - Red vial terciaria
   - Sistema de transporte público
   - Cicloinfraestructura
   - Peatonalización

8. GESTIÓN DEL RIESGO:
   - Zonas de amenaza
   - Zonas de vulnerabilidad
   - Zonas de riesgo
   - Medidas de mitigación
   - Planes de contingencia
   - Sistemas de alerta temprana

9. PARTICIPACIÓN CIUDADANA:
   - Audiencias públicas
   - Consultas ciudadanas
   - Veedurías ciudadanas
   - Control social
   - Rendición de cuentas
   - Evaluación ciudadana

10. IMPLEMENTACIÓN Y SEGUIMIENTO:
    - Plan de implementación
    - Cronograma de ejecución
    - Recursos financieros
    - Seguimiento y monitoreo
    - Evaluación y ajustes
    - Actualización del POT

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para plan de gobierno étnico
     */
    protected function construirPromptPlanEtnico(array $datos): string
    {
        $comunidad = $datos['comunidad'] ?? 'Comunidad Étnica';
        $tipoComunidad = $datos['tipo_comunidad'] ?? 'indigena';
        $periodo = $datos['periodo'] ?? '2024-2027';
        $contenidoPlan = $datos['contenido_plan'] ?? '';

        return "ANÁLISIS POST-DOCTORADO - PLAN DE GOBIERNO ÉTNICO

COMUNIDAD: {$comunidad}
TIPO: {$tipoComunidad}
PERÍODO: {$periodo}

CONTENIDO DEL PLAN:
{$contenidoPlan}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO ÉTNICO:
   - Convenio 169 OIT sobre Pueblos Indígenas y Tribales
   - Declaración ONU sobre Derechos de Pueblos Indígenas
   - Constitución Política (Art. 7, 8, 10, 70, 72, 329-330)
   - Ley 21 de 1991 (Convenio 169 OIT)
   - Ley 70 de 1993 (Comunidades Negras)
   - Jurisprudencia constitucional étnica

2. AUTONOMÍA Y AUTOGOBIERNO:
   - Derecho a la autonomía
   - Sistemas de gobierno propio
   - Autoridades tradicionales
   - Procedimientos de elección
   - Competencias propias
   - Coordinación con autoridades estatales

3. TERRITORIO Y RECURSOS:
   - Derechos territoriales
   - Territorio ancestral
   - Recursos naturales
   - Biodiversidad
   - Conocimientos tradicionales
   - Patrimonio cultural

4. DESARROLLO PROPIO:
   - Planes de vida
   - Etnodesarrollo
   - Proyectos productivos
   - Economía propia
   - Sostenibilidad ambiental
   - Identidad cultural

5. PARTICIPACIÓN Y CONSULTA:
   - Consulta previa, libre e informada
   - Consentimiento previo
   - Participación en decisiones
   - Representación política
   - Veedurías étnicas
   - Control social

6. EDUCACIÓN Y CULTURA:
   - Educación propia
   - Lenguas nativas
   - Conocimientos tradicionales
   - Medicina tradicional
   - Rituales y ceremonias
   - Patrimonio inmaterial

7. SALUD Y BIENESTAR:
   - Salud propia
   - Medicina tradicional
   - Sistemas de salud
   - Bienestar comunitario
   - Seguridad alimentaria
   - Vivienda tradicional

8. JUSTICIA Y DERECHOS:
   - Jurisdicción especial indígena
   - Sistemas de justicia propia
   - Derechos humanos
   - Protección de derechos
   - Acceso a la justicia
   - Reparación integral

9. COORDINACIÓN INTERINSTITUCIONAL:
   - Coordinación con estado
   - Coordinación con municipios
   - Coordinación con departamentos
   - Coordinación interétnica
   - Cooperación internacional
   - Alianzas estratégicas

10. SEGUIMIENTO Y EVALUACIÓN:
    - Sistema de seguimiento propio
    - Indicadores culturales
    - Evaluación comunitaria
    - Rendición de cuentas
    - Control social
    - Evaluación de impacto

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para plan de vida comunitario
     */
    protected function construirPromptPlanVida(array $datos): string
    {
        $comunidad = $datos['comunidad'] ?? 'Comunidad';
        $tipoComunidad = $datos['tipo_comunidad'] ?? 'indigena';
        $periodo = $datos['periodo'] ?? '2024-2030';
        $contenidoPlan = $datos['contenido_plan'] ?? '';

        return "ANÁLISIS POST-DOCTORADO - PLAN DE VIDA COMUNITARIO

COMUNIDAD: {$comunidad}
TIPO: {$tipoComunidad}
PERÍODO: {$periodo}

CONTENIDO DEL PLAN:
{$contenidoPlan}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO CONCEPTUAL DEL PLAN DE VIDA:
   - Concepto de plan de vida
   - Enfoque de derechos
   - Enfoque territorial
   - Enfoque cultural
   - Enfoque de género
   - Enfoque generacional

2. DIAGNÓSTICO COMUNITARIO:
   - Situación actual de la comunidad
   - Fortalezas comunitarias
   - Debilidades identificadas
   - Oportunidades de desarrollo
   - Amenazas externas
   - Participación comunitaria

3. VISIÓN Y MISIÓN COMUNITARIA:
   - Visión de futuro
   - Misión comunitaria
   - Valores comunitarios
   - Principios rectores
   - Objetivos estratégicos
   - Metas comunitarias

4. DIMENSIONES DEL DESARROLLO COMUNITARIO:
   - Dimensión cultural
   - Dimensión social
   - Dimensión económica
   - Dimensión ambiental
   - Dimensión política
   - Dimensión espiritual

5. TERRITORIO Y RECURSOS:
   - Territorio ancestral
   - Recursos naturales
   - Biodiversidad
   - Conocimientos tradicionales
   - Sitios sagrados
   - Áreas de conservación

6. PROYECTOS COMUNITARIOS:
   - Proyectos productivos
   - Proyectos culturales
   - Proyectos ambientales
   - Proyectos sociales
   - Proyectos de infraestructura
   - Proyectos de capacitación

7. GOBERNANZA COMUNITARIA:
   - Autoridades tradicionales
   - Sistemas de decisión
   - Procedimientos comunitarios
   - Resolución de conflictos
   - Participación comunitaria
   - Control social

8. SOSTENIBILIDAD:
   - Sostenibilidad ambiental
   - Sostenibilidad cultural
   - Sostenibilidad económica
   - Sostenibilidad social
   - Sostenibilidad política
   - Sostenibilidad espiritual

9. COORDINACIÓN EXTERNA:
   - Relación con estado
   - Relación con municipios
   - Relación con departamentos
   - Relación con ONGs
   - Relación con sector privado
   - Relación internacional

10. SEGUIMIENTO Y EVALUACIÓN:
    - Sistema de seguimiento
    - Indicadores comunitarios
    - Evaluación participativa
    - Rendición de cuentas
    - Control social
    - Ajustes y mejoras

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para plan etnodesarrollo
     */
    protected function construirPromptEtnodesarrollo(array $datos): string
    {
        $comunidad = $datos['comunidad'] ?? 'Comunidad Étnica';
        $tipoComunidad = $datos['tipo_comunidad'] ?? 'indigena';
        $periodo = $datos['periodo'] ?? '2024-2027';
        $contenidoPlan = $datos['contenido_plan'] ?? '';

        return "ANÁLISIS POST-DOCTORADO - PLAN DE ETNODESARROLLO

COMUNIDAD: {$comunidad}
TIPO: {$tipo_comunidad}
PERÍODO: {$periodo}

CONTENIDO DEL PLAN:
{$contenidoPlan}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO CONCEPTUAL DEL ETNODESARROLLO:
   - Concepto de etnodesarrollo
   - Enfoque de derechos étnicos
   - Enfoque territorial
   - Enfoque cultural
   - Enfoque de género
   - Enfoque generacional

2. DIAGNÓSTICO ÉTNICO:
   - Situación actual de la comunidad
   - Fortalezas étnicas
   - Debilidades identificadas
   - Oportunidades de desarrollo
   - Amenazas externas
   - Participación étnica

3. VISIÓN Y MISIÓN ÉTNICA:
   - Visión étnica de futuro
   - Misión comunitaria
   - Valores étnicos
   - Principios rectores
   - Objetivos estratégicos
   - Metas étnicas

4. DIMENSIONES DEL ETNODESARROLLO:
   - Dimensión cultural étnica
   - Dimensión social étnica
   - Dimensión económica étnica
   - Dimensión ambiental étnica
   - Dimensión política étnica
   - Dimensión espiritual étnica

5. TERRITORIO Y RECURSOS ÉTNICOS:
   - Territorio ancestral
   - Recursos naturales étnicos
   - Biodiversidad étnica
   - Conocimientos tradicionales
   - Sitios sagrados
   - Áreas de conservación étnica

6. PROYECTOS DE ETNODESARROLLO:
   - Proyectos productivos étnicos
   - Proyectos culturales étnicos
   - Proyectos ambientales étnicos
   - Proyectos sociales étnicos
   - Proyectos de infraestructura étnica
   - Proyectos de capacitación étnica

7. GOBERNANZA ÉTNICA:
   - Autoridades tradicionales
   - Sistemas de decisión étnica
   - Procedimientos comunitarios
   - Resolución de conflictos
   - Participación étnica
   - Control social étnico

8. SOSTENIBILIDAD ÉTNICA:
   - Sostenibilidad ambiental étnica
   - Sostenibilidad cultural étnica
   - Sostenibilidad económica étnica
   - Sostenibilidad social étnica
   - Sostenibilidad política étnica
   - Sostenibilidad espiritual étnica

9. COORDINACIÓN ÉTNICA:
   - Relación con estado
   - Relación con municipios
   - Relación con departamentos
   - Relación con ONGs
   - Relación con sector privado
   - Relación internacional

10. SEGUIMIENTO Y EVALUACIÓN ÉTNICA:
    - Sistema de seguimiento étnico
    - Indicadores étnicos
    - Evaluación participativa étnica
    - Rendición de cuentas étnica
    - Control social étnico
    - Ajustes y mejoras étnicas

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para plan anti-corrupción
     */
    protected function construirPromptPlanAnticorrupcion(array $datos): string
    {
        $entidad = $datos['entidad'] ?? 'Entidad Pública';
        $nivel = $datos['nivel'] ?? 'municipal';
        $periodo = $datos['periodo'] ?? '2024-2027';
        $contenidoPlan = $datos['contenido_plan'] ?? '';

        return "ANÁLISIS POST-DOCTORADO - PLAN ANTI-CORRUPCIÓN

ENTIDAD: {$entidad}
NIVEL: {$nivel}
PERÍODO: {$periodo}

CONTENIDO DEL PLAN:
{$contenidoPlan}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO ANTI-CORRUPCIÓN:
   - Convención de las Naciones Unidas contra la Corrupción
   - Convención Interamericana contra la Corrupción
   - Ley 1474 de 2011 (Estatuto Anticorrupción)
   - Ley 190 de 1995 (Código Disciplinario Único)
   - Jurisprudencia constitucional anticorrupción

2. DIAGNÓSTICO DE CORRUPCIÓN:
   - Tipos de corrupción identificados
   - Áreas de mayor riesgo
   - Factores de riesgo
   - Vulnerabilidades institucionales
   - Casos de corrupción
   - Impacto de la corrupción

3. ESTRATEGIAS DE PREVENCIÓN:
   - Políticas de integridad
   - Códigos de ética
   - Programas de prevención
   - Capacitación anticorrupción
   - Sistemas de alertas
   - Medidas de mitigación

4. TRANSPARENCIA Y ACCESO A LA INFORMACIÓN:
   - Ley 1712 de 2014 (Transparencia y Acceso a la Información)
   - Transparencia activa
   - Transparencia pasiva
   - Derecho de petición
   - Habeas data
   - Protección de datos personales

5. CONTROL Y VIGILANCIA:
   - Control interno
   - Control externo
   - Veedurías ciudadanas
   - Control social
   - Auditorías
   - Evaluaciones

6. PARTICIPACIÓN CIUDADANA:
   - Mecanismos de participación
   - Denuncias ciudadanas
   - Veedurías ciudadanas
   - Control social
   - Rendición de cuentas
   - Evaluación ciudadana

7. GESTIÓN DE RIESGOS:
   - Identificación de riesgos
   - Evaluación de riesgos
   - Medidas de mitigación
   - Planes de contingencia
   - Monitoreo de riesgos
   - Actualización de riesgos

8. CAPACITACIÓN Y FORMACIÓN:
   - Programas de capacitación
   - Formación en ética
   - Sensibilización anticorrupción
   - Capacitación en transparencia
   - Formación en control social
   - Capacitación en rendición de cuentas

9. SEGUIMIENTO Y EVALUACIÓN:
   - Sistema de seguimiento
   - Indicadores de gestión
   - Evaluación de resultados
   - Evaluación de impacto
   - Rendición de cuentas
   - Control social

10. COORDINACIÓN INTERINSTITUCIONAL:
    - Coordinación con entidades de control
    - Coordinación con fiscalía
    - Coordinación con procuraduría
    - Coordinación con contraloría
    - Coordinación con sociedad civil
    - Coordinación internacional

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para plan de ética y transparencia
     */
    protected function construirPromptPlanEtica(array $datos): string
    {
        $entidad = $datos['entidad'] ?? 'Entidad Pública';
        $nivel = $datos['nivel'] ?? 'municipal';
        $periodo = $datos['periodo'] ?? '2024-2027';
        $contenidoPlan = $datos['contenido_plan'] ?? '';

        return "ANÁLISIS POST-DOCTORADO - PLAN DE ÉTICA Y TRANSPARENCIA

ENTIDAD: {$entidad}
NIVEL: {$nivel}
PERÍODO: {$periodo}

CONTENIDO DEL PLAN:
{$contenidoPlan}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO ÉTICO:
   - Código Disciplinario Único (Ley 734 de 2002)
   - Ley 190 de 1995 (Código Disciplinario Único)
   - Ley 1712 de 2014 (Transparencia y Acceso a la Información)
   - Jurisprudencia constitucional ética
   - Doctrina ética pública

2. PRINCIPIOS ÉTICOS:
   - Principio de legalidad
   - Principio de imparcialidad
   - Principio de publicidad
   - Principio de eficiencia
   - Principio de economía
   - Principio de celeridad

3. VALORES ÉTICOS:
   - Honestidad
   - Transparencia
   - Responsabilidad
   - Integridad
   - Respeto
   - Solidaridad

4. CÓDIGOS DE ÉTICA:
   - Código de ética institucional
   - Código de conducta
   - Código de buen gobierno
   - Código de transparencia
   - Código de integridad
   - Código de valores

5. TRANSPARENCIA Y ACCESO A LA INFORMACIÓN:
   - Transparencia activa
   - Transparencia pasiva
   - Derecho de petición
   - Habeas data
   - Protección de datos personales
   - Rendición de cuentas

6. PARTICIPACIÓN CIUDADANA:
   - Mecanismos de participación
   - Veedurías ciudadanas
   - Control social
   - Rendición de cuentas
   - Evaluación ciudadana
   - Denuncias ciudadanas

7. GESTIÓN DE CONFLICTOS DE INTERÉS:
   - Identificación de conflictos
   - Evaluación de conflictos
   - Medidas de mitigación
   - Planes de prevención
   - Monitoreo de conflictos
   - Actualización de conflictos

8. CAPACITACIÓN ÉTICA:
   - Programas de capacitación
   - Formación en ética
   - Sensibilización ética
   - Capacitación en transparencia
   - Formación en integridad
   - Capacitación en valores

9. SEGUIMIENTO Y EVALUACIÓN:
   - Sistema de seguimiento
   - Indicadores éticos
   - Evaluación de resultados
   - Evaluación de impacto
   - Rendición de cuentas
   - Control social

10. COORDINACIÓN ÉTICA:
    - Coordinación interinstitucional
    - Coordinación con entidades de control
    - Coordinación con sociedad civil
    - Coordinación con sector privado
    - Coordinación internacional
    - Alianzas éticas

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Consolidar análisis de planes
     */
    protected function consolidarAnalisisPlanes(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_planes',
            'nivel' => 'post_doctorado',
            'planes_analizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'riesgos_identificados' => [],
            'oportunidades' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - PLANES DE GOBIERNO\n\n";
        foreach ($resultados as $tipoPlan => $resultado) {
            if ($resultado['exito']) {
                $analisisConsolidado .= "=== {$tipoPlan} ===\n";
                $analisisConsolidado .= $resultado['analisis'] . "\n\n";
            }
        }

        $consolidado['analisis_consolidado'] = $analisisConsolidado;

        // Generar recomendaciones integrales
        $consolidado['recomendaciones_integrales'] = [
            'Fortalecer la coordinación entre diferentes tipos de planes',
            'Implementar sistemas de seguimiento y evaluación integrados',
            'Mejorar la participación ciudadana en todos los niveles de planificación',
            'Establecer mecanismos de articulación interinstitucional',
            'Desarrollar indicadores de gestión integrales',
            'Implementar sistemas de transparencia y rendición de cuentas'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis de plan
     */
    protected function guardarAnalisisPlan(string $tipoPlan, array $datos, string $analisis, array $resultado): void
    {
        try {
            AnalisisIA::create([
                'especialidad' => $tipoPlan,
                'nivel' => 'post_doctorado',
                'datos_entrada' => json_encode($datos),
                'analisis_completo' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo_ia' => $this->modelo,
                'estado' => 'completado',
                'fecha_analisis' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Error guardando análisis de plan', [
                'tipo_plan' => $tipoPlan,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de planes
     */
    public function obtenerEstadisticasPlanes(): array
    {
        return [
            'total_planes_analizados' => AnalisisIA::whereIn('especialidad', [
                'plan_desarrollo_municipal',
                'plan_desarrollo_departamental',
                'plan_ordenamiento_territorial',
                'plan_gobierno_etnico',
                'plan_vida_comunitario',
                'plan_etnodesarrollo',
                'plan_anticorrupcion',
                'plan_etica_transparencia'
            ])->count(),
            'planes_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'plan_desarrollo_municipal',
                'plan_desarrollo_departamental',
                'plan_ordenamiento_territorial',
                'plan_gobierno_etnico',
                'plan_vida_comunitario',
                'plan_etnodesarrollo',
                'plan_anticorrupcion',
                'plan_etica_transparencia'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'plan_desarrollo_municipal',
                'plan_desarrollo_departamental',
                'plan_ordenamiento_territorial',
                'plan_gobierno_etnico',
                'plan_vida_comunitario',
                'plan_etnodesarrollo',
                'plan_anticorrupcion',
                'plan_etica_transparencia'
            ])->sum('tokens_usados')
        ];
    }
}
