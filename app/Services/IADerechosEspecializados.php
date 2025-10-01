<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IADerechosEspecializados
{
    private $apiKey;
    private $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY'));
        $this->baseUrl = 'https://api.openai.com/v1';
    }

    /**
     * ANÁLISIS DE DERECHOS MINEROS
     */
    public function analizarDerechosMineros($datosMineros, $tipoMineria = 'general')
    {
        $prompt = $this->generarPromptDerechosMineros($datosMineros, $tipoMineria);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_mineros',
            'tipo_mineria' => $tipoMineria,
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_minero_internacional'
        ]);
    }

    /**
     * ANÁLISIS DE DERECHOS CATASTRALES
     */
    public function analizarDerechosCatastrales($datosCatastrales, $tipoPredio = 'general')
    {
        $prompt = $this->generarPromptDerechosCatastrales($datosCatastrales, $tipoPredio);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_catastrales',
            'tipo_predio' => $tipoPredio,
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_inmobiliario_catastral'
        ]);
    }

    /**
     * ANÁLISIS DE DERECHOS EN DESARROLLO TERRITORIAL
     */
    public function analizarDesarrolloTerritorial($datosTerritoriales, $nivelGobierno = 'municipal')
    {
        $prompt = $this->generarPromptDesarrolloTerritorial($datosTerritoriales, $nivelGobierno);
        
        return $this->procesarConIA($prompt, [
            'area' => 'desarrollo_territorial',
            'nivel_gobierno' => $nivelGobierno,
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_administrativo_territorial'
        ]);
    }

    /**
     * ANÁLISIS DE PLANES DE DESARROLLO Y GOBIERNO
     */
    public function analizarPlanesDesarrolloGobierno($datosPlanes, $tipoPlan = 'desarrollo')
    {
        $prompt = $this->generarPromptPlanesDesarrolloGobierno($datosPlanes, $tipoPlan);
        
        return $this->procesarConIA($prompt, [
            'area' => 'planes_desarrollo_gobierno',
            'tipo_plan' => $tipoPlan,
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_administrativo_planificacion'
        ]);
    }

    /**
     * ANÁLISIS DE DERECHOS INTERNACIONALES
     */
    public function analizarDerechosInternacionales($casoInternacional, $areaDerecho = 'derechos_humanos')
    {
        $prompt = $this->generarPromptDerechosInternacionales($casoInternacional, $areaDerecho);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_internacionales',
            'area_derecho' => $areaDerecho,
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_internacional_publico'
        ]);
    }

    /**
     * ANÁLISIS DE DERECHOS CAN E INCA
     */
    public function analizarDerechosCanInca($datosCanInca, $tipoDerecho = 'can')
    {
        $prompt = $this->generarPromptDerechosCanInca($datosCanInca, $tipoDerecho);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_can_inca',
            'tipo_derecho' => $tipoDerecho,
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_integracion_regional'
        ]);
    }

    /**
     * ANÁLISIS DE DERECHOS DE LATINOAMÉRICA Y AMERICANOS
     */
    public function analizarDerechosLatinoamericanos($casoLatinoamericano, $pais = 'colombia')
    {
        $prompt = $this->generarPromptDerechosLatinoamericanos($casoLatinoamericano, $pais);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_latinoamericanos',
            'pais' => $pais,
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_comparado_latinoamericano'
        ]);
    }

    /**
     * ANÁLISIS DE DERECHOS DE PROPIEDAD EN RAÍZ Y PROPIEDAD
     */
    public function analizarDerechosPropiedad($datosPropiedad, $tipoPropiedad = 'raiz')
    {
        $prompt = $this->generarPromptDerechosPropiedad($datosPropiedad, $tipoPropiedad);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_propiedad',
            'tipo_propiedad' => $tipoPropiedad,
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_inmobiliario_propiedad'
        ]);
    }

    /**
     * ANÁLISIS DE DERECHOS EN COMUNIDADES ÉTNICAS
     */
    public function analizarDerechosComunidadesEtnicas($datosEtnicos, $tipoComunidad = 'indigena')
    {
        $prompt = $this->generarPromptDerechosComunidadesEtnicas($datosEtnicos, $tipoComunidad);
        
        return $this->procesarConIA($prompt, [
            'area' => 'derechos_comunidades_etnicas',
            'tipo_comunidad' => $tipoComunidad,
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_etnico_internacional'
        ]);
    }

    /**
     * GENERAR PROMPT PARA DERECHOS MINEROS
     */
    private function generarPromptDerechosMineros($datosMineros, $tipoMineria)
    {
        return "Como especialista en Derecho Minero con nivel post-doctorado, analiza los siguientes datos mineros:

DATOS MINEROS:
{$datosMineros}

TIPO DE MINERÍA: {$tipoMineria}

Proporciona un análisis exhaustivo que incluya:
1. Marco normativo minero nacional e internacional
2. Títulos mineros y contratos de concesión
3. Derechos y obligaciones de las partes
4. Consulta previa y participación comunitaria
5. Impacto ambiental minero
6. Regalías, tributos y contribuciones
7. Conflictos territoriales y mineros
8. Derechos de comunidades étnicas
9. Normativa internacional aplicable (Convenio 169 OIT, Declaración ONU)
10. Recomendaciones específicas para la protección de derechos

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Minero Internacional
Jurisdicción: Colombia
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * GENERAR PROMPT PARA DERECHOS CATASTRALES
     */
    private function generarPromptDerechosCatastrales($datosCatastrales, $tipoPredio)
    {
        return "Como especialista en Derecho Catastral e Inmobiliario con nivel post-doctorado, analiza los siguientes datos catastrales:

DATOS CATASTRALES:
{$datosCatastrales}

TIPO DE PREDIO: {$tipoPredio}

Proporciona un análisis exhaustivo que incluya:
1. Marco normativo catastral (Ley 14 de 1983, Decreto 1077 de 2015)
2. Derechos de propiedad y posesión
3. Servidumbres y limitaciones
4. Avalúos y valoraciones
5. Procedimientos catastrales
6. Conflictos territoriales
7. Derechos de terceros
8. Normativa de actualización catastral
9. Protección de derechos prediales
10. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Inmobiliario Catastral
Jurisdicción: Colombia
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * GENERAR PROMPT PARA DESARROLLO TERRITORIAL
     */
    private function generarPromptDesarrolloTerritorial($datosTerritoriales, $nivelGobierno)
    {
        return "Como especialista en Derecho Administrativo Territorial con nivel post-doctorado, analiza el siguiente caso de desarrollo territorial:

DATOS TERRITORIALES:
{$datosTerritoriales}

NIVEL DE GOBIERNO: {$nivelGobierno}

Proporciona un análisis exhaustivo que incluya:
1. Marco normativo del desarrollo territorial
2. Competencias territoriales
3. Planificación territorial y urbana
4. Participación ciudadana territorial
5. Ordenamiento territorial
6. Desarrollo sostenible
7. Competencias de entidades territoriales
8. Mecanismos de coordinación interterritorial
9. Derechos territoriales colectivos
10. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Administrativo Territorial
Jurisdicción: Colombia
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * GENERAR PROMPT PARA PLANES DE DESARROLLO Y GOBIERNO
     */
    private function generarPromptPlanesDesarrolloGobierno($datosPlanes, $tipoPlan)
    {
        return "Como especialista en Derecho Administrativo y Planificación con nivel post-doctorado, analiza el siguiente plan:

DATOS DEL PLAN:
{$datosPlanes}

TIPO DE PLAN: {$tipoPlan}

Proporciona un análisis exhaustivo que incluya:
1. Marco normativo de la planificación
2. Competencias de planificación
3. Procedimientos de formulación
4. Participación ciudadana en la planificación
5. Evaluación y seguimiento
6. Control de legalidad
7. Articulación interinstitucional
8. Presupuesto y financiación
9. Indicadores de gestión
10. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Administrativo de Planificación
Jurisdicción: Colombia
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * GENERAR PROMPT PARA DERECHOS INTERNACIONALES
     */
    private function generarPromptDerechosInternacionales($casoInternacional, $areaDerecho)
    {
        return "Como especialista en Derecho Internacional Público con nivel post-doctorado, analiza el siguiente caso internacional:

CASO INTERNACIONAL:
{$casoInternacional}

ÁREA DE DERECHO: {$areaDerecho}

Proporciona un análisis exhaustivo que incluya:
1. Marco normativo internacional aplicable
2. Tratados y convenios internacionales
3. Jurisprudencia internacional
4. Derechos humanos internacionales
5. Derecho internacional humanitario
6. Derecho internacional del medio ambiente
7. Derecho internacional económico
8. Mecanismos de protección internacional
9. Aplicación en el derecho interno
10. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Internacional Público
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * GENERAR PROMPT PARA DERECHOS CAN E INCA
     */
    private function generarPromptDerechosCanInca($datosCanInca, $tipoDerecho)
    {
        $tipoTexto = $tipoDerecho === 'can' ? 'Comunidad Andina de Naciones (CAN)' : 'Instituto Nacional de Contratación Pública (INCA)';
        
        return "Como especialista en Derecho de Integración Regional con nivel post-doctorado, analiza el siguiente caso de {$tipoTexto}:

DATOS:
{$datosCanInca}

TIPO DE DERECHO: {$tipoDerecho}

Proporciona un análisis exhaustivo que incluya:
1. Marco normativo de integración regional
2. Tratados constitutivos
3. Derecho comunitario andino
4. Mecanismos de solución de controversias
5. Aplicación del derecho comunitario
6. Libre circulación de bienes y servicios
7. Derechos de los ciudadanos andinos
8. Instituciones de integración
9. Relación con el derecho interno
10. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho de Integración Regional
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * GENERAR PROMPT PARA DERECHOS LATINOAMERICANOS
     */
    private function generarPromptDerechosLatinoamericanos($casoLatinoamericano, $pais)
    {
        return "Como especialista en Derecho Comparado Latinoamericano con nivel post-doctorado, analiza el siguiente caso:

CASO LATINOAMERICANO:
{$casoLatinoamericano}

PAÍS: {$pais}

Proporciona un análisis exhaustivo que incluya:
1. Marco normativo del país específico
2. Derecho comparado latinoamericano
3. Sistemas jurídicos latinoamericanos
4. Derechos humanos regionales
5. Integración latinoamericana
6. Jurisprudencia regional
7. Tratados regionales
8. Sistemas de protección regional
9. Aplicación del derecho regional
10. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Comparado Latinoamericano
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * GENERAR PROMPT PARA DERECHOS DE PROPIEDAD
     */
    private function generarPromptDerechosPropiedad($datosPropiedad, $tipoPropiedad)
    {
        return "Como especialista en Derecho Inmobiliario y de Propiedad con nivel post-doctorado, analiza el siguiente caso de propiedad:

DATOS DE PROPIEDAD:
{$datosPropiedad}

TIPO DE PROPIEDAD: {$tipoPropiedad}

Proporciona un análisis exhaustivo que incluya:
1. Marco normativo de la propiedad
2. Derechos reales
3. Adquisición y transmisión de propiedad
4. Protección de la propiedad
5. Limitaciones y servidumbres
6. Conflictos de propiedad
7. Registro de la propiedad
8. Prescripción adquisitiva
9. Expropiación y expropiación
10. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Inmobiliario y de Propiedad
Jurisdicción: Colombia
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * GENERAR PROMPT PARA DERECHOS EN COMUNIDADES ÉTNICAS
     */
    private function generarPromptDerechosComunidadesEtnicas($datosEtnicos, $tipoComunidad)
    {
        $comunidadTexto = $tipoComunidad === 'indigena' ? 'pueblo indígena' : 'comunidad afrodescendiente';
        
        return "Como especialista en Derecho Étnico Internacional con nivel post-doctorado, analiza el siguiente caso de {$comunidadTexto}:

DATOS ÉTNICOS:
{$datosEtnicos}

TIPO DE COMUNIDAD: {$tipoComunidad}

Proporciona un análisis exhaustivo que incluya:
1. Marco normativo étnico nacional e internacional
2. Convenio 169 OIT sobre Pueblos Indígenas y Tribales
3. Declaración de las Naciones Unidas sobre Derechos de los Pueblos Indígenas
4. Constitución Nacional (Artículos 7, 8, 10, 70, 246, 330, 329)
5. Ley 21 de 1991 (Ratificación Convenio 169 OIT)
6. Ley 70 de 1993 (Comunidades Negras)
7. Derechos territoriales colectivos
8. Consulta previa y participación
9. Autonomía y autodeterminación
10. Recomendaciones específicas

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Étnico Internacional
Jurisdicción: Colombia
Fecha: " . now()->format('d/m/Y');
    }

    /**
     * PROCESAR CON IA
     */
    private function procesarConIA($prompt, $metadata = [])
    {
        try {
            $cacheKey = 'ia_derechos_' . md5($prompt . json_encode($metadata));
            
            return Cache::remember($cacheKey, 3600, function () use ($prompt, $metadata) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(60)->post($this->baseUrl . '/chat/completions', [
                    'model' => 'gpt-4',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Eres un especialista jurídico con nivel post-doctorado, experto en múltiples áreas del derecho. Proporciona análisis exhaustivos, precisos y actualizados con fundamentos legales sólidos y recomendaciones prácticas.'
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
                    $data = $response->json();
                    $content = $data['choices'][0]['message']['content'] ?? 'No se pudo generar respuesta';
                    
                    return [
                        'success' => true,
                        'analisisCompleto' => [
                            'conceptoGeneralConsolidado' => $content,
                            'metadata' => $metadata,
                            'timestamp' => now()->toISOString(),
                            'modelo' => 'gpt-4',
                            'tokens_usados' => $data['usage']['total_tokens'] ?? 0
                        ],
                        'respuesta' => $content
                    ];
                } else {
                    Log::error('Error en API de OpenAI: ' . $response->body());
                    return $this->generarAnalisisFallback($prompt, $metadata);
                }
            });
        } catch (\Exception $e) {
            Log::error('Error procesando con IA de derechos especializados: ' . $e->getMessage());
            return $this->generarAnalisisFallback($prompt, $metadata);
        }
    }

    /**
     * GENERAR ANÁLISIS DE FALLBACK
     */
    private function generarAnalisisFallback($prompt, $metadata)
    {
        return [
            'success' => false,
            'analisisCompleto' => [
                'conceptoGeneralConsolidado' => 'Análisis temporalmente no disponible. Por favor, intente nuevamente en unos momentos.',
                'metadata' => $metadata,
                'timestamp' => now()->toISOString(),
                'modelo' => 'fallback',
                'tokens_usados' => 0
            ],
            'respuesta' => 'Análisis temporalmente no disponible. Por favor, intente nuevamente en unos momentos.',
            'error' => 'Servicio temporalmente no disponible'
        ];
    }
}
