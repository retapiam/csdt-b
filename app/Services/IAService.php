<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IAService
{
    private $apiKey;
    private $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY'));
        $this->baseUrl = 'https://api.openai.com/v1';
    }

    /**
     * Análisis de Naturaleza y Justicia Ordinaria
     */
    public function analizarNaturalezaJusticia($hechos, $tipoCaso = 'penal')
    {
        $prompt = $this->generarPromptNaturalezaJusticia($hechos, $tipoCaso);
        
        return $this->procesarConIA($prompt, [
            'area' => 'naturaleza_justicia',
            'tipo' => $tipoCaso,
            'nivel' => 'post_doctorado'
        ]);
    }

    /**
     * Análisis de Derechos Étnicos
     */
    public function analizarDerechosEtnicos($hechos, $puebloIndigena = null)
    {
        $prompt = $this->generarPromptDerechosEtnicos($hechos, $puebloIndigena);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_etnicos',
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_etnico_internacional'
        ]);
    }

    /**
     * Análisis de Derecho Constitucional
     */
    public function analizarDerechoConstitucional($hechos, $articulos = [])
    {
        $prompt = $this->generarPromptDerechoConstitucional($hechos, $articulos);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derecho_constitucional',
            'nivel' => 'post_doctorado',
            'especializacion' => 'constitucional_avanzado'
        ]);
    }

    /**
     * Análisis de Derecho Administrativo
     */
    public function analizarDerechoAdministrativo($hechos, $procedimiento = null)
    {
        $prompt = $this->generarPromptDerechoAdministrativo($hechos, $procedimiento);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derecho_administrativo',
            'nivel' => 'post_doctorado',
            'especializacion' => 'administrativo_avanzado'
        ]);
    }

    /**
     * Análisis de Derecho Penal
     */
    public function analizarDerechoPenal($hechos, $tipoDelito = null)
    {
        $prompt = $this->generarPromptDerechoPenal($hechos, $tipoDelito);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derecho_penal',
            'nivel' => 'post_doctorado',
            'especializacion' => 'penal_avanzado'
        ]);
    }

    /**
     * Análisis de Derecho Civil
     */
    public function analizarDerechoCivil($hechos, $materia = null)
    {
        $prompt = $this->generarPromptDerechoCivil($hechos, $materia);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derecho_civil',
            'nivel' => 'post_doctorado',
            'especializacion' => 'civil_avanzado'
        ]);
    }

    /**
     * Análisis de Derecho Laboral
     */
    public function analizarDerechoLaboral($hechos, $tipoRelacion = null)
    {
        $prompt = $this->generarPromptDerechoLaboral($hechos, $tipoRelacion);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derecho_laboral',
            'nivel' => 'post_doctorado',
            'especializacion' => 'laboral_avanzado'
        ]);
    }

    /**
     * Análisis de Medicina Natural
     */
    public function analizarMedicinaNatural($sintomas, $plantas = [])
    {
        $prompt = $this->generarPromptMedicinaNatural($sintomas, $plantas);
        
        return $this->procesarConIA($prompt, [
            'area' => 'medicina_natural',
            'nivel' => 'post_doctorado',
            'especializacion' => 'medicina_tradicional'
        ]);
    }

    /**
     * Análisis de Derechos Ambientales
     */
    public function analizarDerechosAmbientales($hechos, $ecosistema = null)
    {
        $prompt = $this->generarPromptDerechosAmbientales($hechos, $ecosistema);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_ambientales',
            'nivel' => 'post_doctorado',
            'especializacion' => 'ambiental_avanzado'
        ]);
    }

    /**
     * Análisis de Derechos Mineros
     */
    public function analizarDerechosMineros($hechos, $tipoMineria = null)
    {
        $prompt = $this->generarPromptDerechosMineros($hechos, $tipoMineria);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_mineros',
            'nivel' => 'post_doctorado',
            'especializacion' => 'minero_internacional'
        ]);
    }

    /**
     * Análisis de Peritajes Catastrales
     */
    public function analizarPeritajeCatastral($datos, $tipoPeritaje = 'avaluo')
    {
        $prompt = $this->generarPromptPeritajeCatastral($datos, $tipoPeritaje);
        
        return $this->procesarConIA($prompt, [
            'area' => 'peritaje_catastral',
            'nivel' => 'post_doctorado',
            'especializacion' => 'catastral_avanzado'
        ]);
    }

    /**
     * Análisis de Metodología Ágil
     */
    public function analizarMetodologiaAgil($proyecto, $metodologia = 'scrum')
    {
        $prompt = $this->generarPromptMetodologiaAgil($proyecto, $metodologia);
        
        return $this->procesarConIA($prompt, [
            'area' => 'metodologia_agil',
            'nivel' => 'post_doctorado',
            'especializacion' => 'agil_avanzado'
        ]);
    }

    /**
     * Análisis de Reseña Histórica
     */
    public function analizarResenaHistorica($eventos, $periodo = null)
    {
        $prompt = $this->generarPromptResenaHistorica($eventos, $periodo);
        
        return $this->procesarConIA($prompt, [
            'area' => 'resena_historica',
            'nivel' => 'post_doctorado',
            'especializacion' => 'historia_avanzada'
        ]);
    }

    /**
     * Generar Prompt para Naturaleza y Justicia
     */
    private function generarPromptNaturalezaJusticia($hechos, $tipoCaso)
    {
        return "Como especialista en Derecho con nivel post-doctorado, analiza el siguiente caso de {$tipoCaso}:

HECHOS:
{$hechos}

Proporciona un análisis exhaustivo que incluya:
1. Clasificación jurídica del caso
2. Fundamentos legales aplicables (nacionales e internacionales)
3. Jurisprudencia relevante
4. Análisis de la naturaleza jurídica
5. Procedimiento aplicable
6. Recomendaciones específicas
7. Evaluación de riesgos
8. Estrategias de defensa o acción

Nivel de análisis: POST-DOCTORADO
Especialización: Naturaleza y Justicia Ordinaria
Jurisdicción: Colombia
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Generar Prompt para Derechos Étnicos
     */
    private function generarPromptDerechosEtnicos($hechos, $puebloIndigena)
    {
        return "Como especialista en Derecho Étnico con nivel post-doctorado, analiza el siguiente caso:

HECHOS:
{$hechos}

PUEBLO INDÍGENA: " . ($puebloIndigena ?? 'No especificado') . "

Proporciona un análisis exhaustivo que incluya:
1. Análisis desde la perspectiva del Convenio 169 de la OIT
2. Aplicación de la Declaración de las Naciones Unidas sobre Derechos de los Pueblos Indígenas
3. Marco constitucional colombiano (Art. 7, 8, 10, 70, 72, 246, 286, 329, 330)
4. Jurisprudencia de la Corte Constitucional
5. Derechos territoriales y consulta previa
6. Derechos culturales y lingüísticos
7. Mecanismos de protección
8. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Étnico Internacional
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Generar Prompt para Derecho Constitucional
     */
    private function generarPromptDerechoConstitucional($hechos, $articulos)
    {
        $articulosStr = !empty($articulos) ? implode(', ', $articulos) : 'Relevantes al caso';
        
        return "Como especialista en Derecho Constitucional con nivel post-doctorado, analiza el siguiente caso:

HECHOS:
{$hechos}

ARTÍCULOS CONSTITUCIONALES: {$articulosStr}

Proporciona un análisis exhaustivo que incluya:
1. Análisis constitucional detallado
2. Principios constitucionales aplicables
3. Derechos fundamentales involucrados
4. Jurisprudencia de la Corte Constitucional
5. Control de constitucionalidad
6. Acciones constitucionales procedentes
7. Análisis de proporcionalidad
8. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Constitucional Avanzado
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Generar Prompt para Derecho Administrativo
     */
    private function generarPromptDerechoAdministrativo($hechos, $procedimiento)
    {
        return "Como especialista en Derecho Administrativo con nivel post-doctorado, analiza el siguiente caso:

HECHOS:
{$hechos}

PROCEDIMIENTO: " . ($procedimiento ?? 'No especificado') . "

Proporciona un análisis exhaustivo que incluya:
1. Clasificación del acto administrativo
2. Procedimiento administrativo aplicable
3. Principios de la función administrativa
4. Control de legalidad
5. Recursos administrativos procedentes
6. Acciones judiciales aplicables
7. Responsabilidad del Estado
8. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Administrativo Avanzado
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Generar Prompt para Derecho Penal
     */
    private function generarPromptDerechoPenal($hechos, $tipoDelito)
    {
        return "Como especialista en Derecho Penal con nivel post-doctorado, analiza el siguiente caso:

HECHOS:
{$hechos}

TIPO DE DELITO: " . ($tipoDelito ?? 'Por determinar') . "

Proporciona un análisis exhaustivo que incluya:
1. Tipicidad del delito
2. Antijuridicidad
3. Culpabilidad
4. Circunstancias modificativas
5. Penas aplicables
6. Medidas de aseguramiento
7. Recursos procedentes
8. Estrategias de defensa

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Penal Avanzado
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Generar Prompt para Derecho Civil
     */
    private function generarPromptDerechoCivil($hechos, $materia)
    {
        return "Como especialista en Derecho Civil con nivel post-doctorado, analiza el siguiente caso:

HECHOS:
{$hechos}

MATERIA: " . ($materia ?? 'No especificada') . "

Proporciona un análisis exhaustivo que incluya:
1. Clasificación de la relación jurídica
2. Derechos subjetivos involucrados
3. Obligaciones y responsabilidades
4. Prescripción y caducidad
5. Acciones judiciales procedentes
6. Medidas cautelares
7. Pruebas necesarias
8. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Civil Avanzado
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Generar Prompt para Derecho Laboral
     */
    private function generarPromptDerechoLaboral($hechos, $tipoRelacion)
    {
        return "Como especialista en Derecho Laboral con nivel post-doctorado, analiza el siguiente caso:

HECHOS:
{$hechos}

TIPO DE RELACIÓN: " . ($tipoRelacion ?? 'No especificada') . "

Proporciona un análisis exhaustivo que incluya:
1. Naturaleza de la relación laboral
2. Derechos y obligaciones de las partes
3. Normas laborales aplicables
4. Procedimientos administrativos
5. Acciones judiciales procedentes
6. Medidas de protección
7. Indemnizaciones aplicables
8. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Laboral Avanzado
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Generar Prompt para Medicina Natural
     */
    private function generarPromptMedicinaNatural($sintomas, $plantas)
    {
        $plantasStr = !empty($plantas) ? implode(', ', $plantas) : 'No especificadas';
        
        return "Como especialista en Medicina Natural con nivel post-doctorado, analiza el siguiente caso:

SÍNTOMAS:
{$sintomas}

PLANTAS MENCIONADAS: {$plantasStr}

Proporciona un análisis exhaustivo que incluya:
1. Diagnóstico desde la medicina tradicional
2. Propiedades medicinales de las plantas
3. Preparaciones y dosificaciones
4. Contraindicaciones y precauciones
5. Interacciones medicamentosas
6. Recomendaciones de estilo de vida
7. Seguimiento y monitoreo
8. Referencias a especialistas

Nivel de análisis: POST-DOCTORADO
Especialización: Medicina Tradicional
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Generar Prompt para Derechos Ambientales
     */
    private function generarPromptDerechosAmbientales($hechos, $ecosistema)
    {
        return "Como especialista en Derecho Ambiental con nivel post-doctorado, analiza el siguiente caso:

HECHOS:
{$hechos}

ECOSISTEMA: " . ($ecosistema ?? 'No especificado') . "

Proporciona un análisis exhaustivo que incluya:
1. Marco normativo ambiental aplicable
2. Principios ambientales involucrados
3. Evaluación de impacto ambiental
4. Licencias y permisos requeridos
5. Acciones de protección ambiental
6. Responsabilidad ambiental
7. Mecanismos de participación
8. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Ambiental Avanzado
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Generar Prompt para Derechos Mineros
     */
    private function generarPromptDerechosMineros($hechos, $tipoMineria)
    {
        return "Como especialista en Derecho Minero con nivel post-doctorado, analiza el siguiente caso:

HECHOS:
{$hechos}

TIPO DE MINERÍA: " . ($tipoMineria ?? 'No especificado') . "

Proporciona un análisis exhaustivo que incluye:
1. Marco normativo minero nacional e internacional
2. Títulos mineros y contratos
3. Derechos y obligaciones de las partes
4. Consulta previa y participación
5. Impacto ambiental minero
6. Regalías y tributos
7. Conflictos y controversias
8. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Minero Internacional
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Generar Prompt para Peritaje Catastral
     */
    private function generarPromptPeritajeCatastral($datos, $tipoPeritaje)
    {
        return "Como especialista en Peritaje Catastral con nivel post-doctorado, analiza el siguiente caso:

DATOS:
{$datos}

TIPO DE PERITAJE: {$tipoPeritaje}

Proporciona un análisis exhaustivo que incluya:
1. Metodología de avalúo aplicable
2. Análisis de mercado inmobiliario
3. Factores de valoración
4. Comparables y ajustes
5. Valoración final
6. Documentación requerida
7. Certificación profesional
8. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Peritaje Catastral Avanzado
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Generar Prompt para Metodología Ágil
     */
    private function generarPromptMetodologiaAgil($proyecto, $metodologia)
    {
        return "Como especialista en Metodologías Ágiles con nivel post-doctorado, analiza el siguiente proyecto:

PROYECTO:
{$proyecto}

METODOLOGÍA: {$metodologia}

Proporciona un análisis exhaustivo que incluya:
1. Análisis de viabilidad del proyecto
2. Metodología ágil más adecuada
3. Planificación y estimación
4. Roles y responsabilidades
5. Herramientas y tecnologías
6. Gestión de riesgos
7. Métricas y KPIs
8. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Metodologías Ágiles Avanzadas
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Generar Prompt para Reseña Histórica
     */
    private function generarPromptResenaHistorica($eventos, $periodo)
    {
        return "Como especialista en Historia con nivel post-doctorado, analiza el siguiente período:

EVENTOS:
{$eventos}

PERÍODO: " . ($periodo ?? 'No especificado') . "

Proporciona un análisis exhaustivo que incluya:
1. Contexto histórico del período
2. Análisis de los eventos principales
3. Causas y consecuencias
4. Figuras históricas relevantes
5. Impacto en la sociedad actual
6. Fuentes históricas
7. Interpretaciones historiográficas
8. Recomendaciones de investigación

Nivel de análisis: POST-DOCTORADO
Especialización: Historia Avanzada
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * Procesar con IA
     */
    private function procesarConIA($prompt, $metadata = [])
    {
        try {
            $cacheKey = 'ia_analysis_' . md5($prompt);
            
            // Verificar cache
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un especialista jurídico con nivel post-doctorado, experto en múltiples áreas del derecho. Proporciona análisis exhaustivos, precisos y actualizados con fundamentos legales sólidos.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 4000,
                'temperature' => 0.3,
                'top_p' => 0.9
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $analysis = $result['choices'][0]['message']['content'];
                
                $formattedResult = [
                    'success' => true,
                    'analysis' => $analysis,
                    'metadata' => $metadata,
                    'timestamp' => now()->toISOString(),
                    'model' => 'gpt-4',
                    'tokens_used' => $result['usage']['total_tokens'] ?? 0
                ];

                // Guardar en cache por 24 horas
                Cache::put($cacheKey, $formattedResult, 86400);
                
                return $formattedResult;
            } else {
                Log::error('Error en API de IA: ' . $response->body());
                return $this->generarAnalisisFallback($prompt, $metadata);
            }
        } catch (\Exception $e) {
            Log::error('Error procesando con IA: ' . $e->getMessage());
            return $this->generarAnalisisFallback($prompt, $metadata);
        }
    }

    // NUEVAS ESPECIALIDADES POST-DOCTORADO

    /**
     * Análisis de Derechos Catastrales y Territoriales
     */
    public function analizarDerechosCatastrales($datosCatastrales)
    {
        $prompt = "Analiza los siguientes datos catastrales desde la perspectiva de los derechos territoriales y prediales. Identifica la normativa catastral aplicable (Ley 14 de 1983, Decreto 1077 de 2015, etc.), derechos de propiedad, servidumbres, limitaciones y recomendaciones para la protección de los derechos territoriales. Datos: {$datosCatastrales}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_catastrales',
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_inmobiliario_catastral'
        ]);
    }

    /**
     * Análisis de Derechos Mineros Avanzados
     */
    public function analizarDerechosMinerosAvanzado($datosMineros)
    {
        $prompt = "Analiza los siguientes datos mineros desde la perspectiva de los derechos mineros nacionales e internacionales. Incluye análisis de títulos mineros, concesiones, contratos de concesión, regalías, impactos socioambientales, derechos de comunidades étnicas, normativa internacional (Convenio 169 OIT, Declaración ONU), y conflictos territoriales. Datos: {$datosMineros}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_mineros_avanzados',
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_minero_internacional_etnicos'
        ]);
    }

    /**
     * Análisis de Dictámenes Ambientales
     */
    public function analizarDictamenAmbiental($datosAmbientales)
    {
        $prompt = "Realiza un dictamen ambiental especializado basado en los siguientes datos. Incluye análisis de impacto ambiental, evaluación de riesgos, normativa ambiental aplicable (Ley 99 de 1993, Decreto 1076 de 2015, etc.), licencias ambientales, consulta previa, y recomendaciones técnicas para la protección del medio ambiente. Datos: {$datosAmbientales}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'dictamenes_ambientales',
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_ambiental_evaluacion_impactos'
        ]);
    }

    /**
     * Análisis de Derecho Informático
     */
    public function analizarDerechoInformatico($casoInformatico)
    {
        $prompt = "Analiza el siguiente caso desde la perspectiva del derecho informático y forense digital. Incluye análisis de delitos informáticos (Ley 1273 de 2009), protección de datos personales (Ley 1581 de 2012), evidencia digital, cadena de custodia, peritaje informático, y recomendaciones para la investigación forense. Caso: {$casoInformatico}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'derecho_informatico',
            'nivel' => 'post_doctorado',
            'especializacion' => 'ciberderecho_forensia_digital'
        ]);
    }

    /**
     * Análisis de Geoportales y Normativa Geoespacial
     */
    public function analizarGeoportales($datosGeoespaciales)
    {
        $prompt = "Analiza los siguientes datos geoespaciales y de geoportales desde la perspectiva de la normativa geoespacial. Incluye análisis de la normativa de IDE (Infraestructura de Datos Espaciales), estándares OGC, interoperabilidad, metadatos geográficos, y uso adecuado de geoportales según la normativa colombiana. Datos: {$datosGeoespaciales}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'geoportales_normativa_geoespacial',
            'nivel' => 'post_doctorado',
            'especializacion' => 'geomatica_derecho_geoespacial'
        ]);
    }

    /**
     * Análisis de Georreferenciación Catastral
     */
    public function analizarGeorreferenciacion($datosPredio)
    {
        $prompt = "Realiza un análisis de georreferenciación para catastro predial y minero basado en los siguientes datos. Incluye análisis de coordenadas, colindancias, linderos, mediciones, cálculos de área, verificación de límites, y recomendaciones técnicas para la delimitación predial. Datos del predio: {$datosPredio}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'georreferenciacion_catastral',
            'nivel' => 'post_doctorado',
            'especializacion' => 'geomatica_aplicada_catastro'
        ]);
    }

    /**
     * Análisis de Participación Ciudadana
     */
    public function analizarParticipacionCiudadana($datosParticipacion)
    {
        $prompt = "Analiza los siguientes datos de participación ciudadana desde la perspectiva de los mecanismos de participación (Ley 134 de 1994, Ley 1757 de 2015). Incluye análisis de consultas populares, referendos, revocatorias, cabildos abiertos, audiencias públicas, y recomendaciones para la implementación efectiva de la participación ciudadana. Datos: {$datosParticipacion}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'participacion_ciudadana',
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_constitucional_democracia_participativa'
        ]);
    }

    /**
     * Análisis de Acción Popular
     */
    public function analizarAccionPopular($casoAccionPopular)
    {
        $prompt = "Analiza el siguiente caso de acción popular desde la perspectiva de la protección de derechos e intereses colectivos. Incluye análisis de legitimación, requisitos de procedencia, derechos colectivos involucrados, jurisprudencia constitucional, y estrategias procesales para la protección efectiva. Caso: {$casoAccionPopular}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'accion_popular',
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_constitucional_acciones_populares'
        ]);
    }

    /**
     * Análisis de Reforma Constitucional
     */
    public function analizarReformaConstitucional($propuestaReforma)
    {
        $prompt = "Analiza la siguiente propuesta de reforma constitucional desde la perspectiva del derecho constitucional y la politología. Incluye análisis de procedimientos de reforma (Art. 374-379 C.P.), control de constitucionalidad, implicaciones políticas, sociales y jurídicas, y recomendaciones para la implementación. Propuesta: {$propuestaReforma}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'reforma_constitucional',
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_constitucional_ciencia_politica'
        ]);
    }

    /**
     * Análisis de Politología
     */
    public function analizarPolitologia($fenomenoPolitico)
    {
        $prompt = "Analiza el siguiente fenómeno político desde la perspectiva de la politología y la ciencia política. Incluye análisis de sistemas políticos, comportamiento electoral, políticas públicas, gobernanza, democracia, y recomendaciones para la comprensión y manejo del fenómeno político. Fenómeno: {$fenomenoPolitico}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'politologia',
            'nivel' => 'post_doctorado',
            'especializacion' => 'ciencia_politica_analisis_politico'
        ]);
    }

    /**
     * Análisis Forense Informático
     */
    public function analizarForenseInformatico($evidenciaDigital)
    {
        $prompt = "Realiza un análisis forense informático especializado de la siguiente evidencia digital. Incluye análisis de dispositivos, sistemas operativos, redes, bases de datos, metadatos, trazabilidad, autenticidad, integridad, y recomendaciones técnicas para la investigación forense. Evidencia: {$evidenciaDigital}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'forense_informatico',
            'nivel' => 'post_doctorado',
            'especializacion' => 'cibercrimen_forensia_digital'
        ]);
    }

    /**
     * Análisis de Cruces de Datos
     */
    public function analizarCrucesDatos($datosCruce)
    {
        $prompt = "Realiza un análisis especializado de cruces de datos desde la perspectiva de la protección de datos personales y la investigación forense. Incluye análisis de correlaciones, patrones, trazabilidad, privacidad, normativa de protección de datos, y recomendaciones técnicas para el manejo seguro de la información. Datos: {$datosCruce}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'cruces_datos',
            'nivel' => 'post_doctorado',
            'especializacion' => 'proteccion_datos_analisis_forense'
        ]);
    }

    /**
     * Análisis de Ubicación de Predios por Colindantes
     */
    public function analizarUbicacionPredios($datosColindantes)
    {
        $prompt = "Realiza un análisis especializado de ubicación de predios por colindantes basado en los siguientes datos. Incluye análisis de coordenadas, mediciones, cálculos geométricos, verificación de linderos, identificación de conflictos territoriales, y recomendaciones técnicas para la delimitación precisa. Datos de colindantes: {$datosColindantes}";
        
        return $this->procesarConIA($prompt, [
            'area' => 'ubicacion_predios',
            'nivel' => 'post_doctorado',
            'especializacion' => 'geomatica_topografia_legal'
        ]);
    }

    /**
     * Generar análisis de fallback
     */
    private function generarAnalisisFallback($prompt, $metadata)
    {
        return [
            'success' => false,
            'analysis' => 'Análisis no disponible en este momento. Por favor, intente nuevamente más tarde.',
            'metadata' => $metadata,
            'timestamp' => now()->toISOString(),
            'model' => 'fallback',
            'tokens_used' => 0,
            'error' => 'Servicio de IA temporalmente no disponible'
        ];
    }
}
