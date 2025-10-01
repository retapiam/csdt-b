<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IAEtnicaEspecializada
{
    private $apiKey;
    private $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY'));
        $this->baseUrl = 'https://api.openai.com/v1';
    }

    public function analizarConsultaEtnica($consulta, $tipoPueblo = 'indigena', $areaEspecializada = 'derechos_etnicos')
    {
        $prompt = $this->generarPromptConsultaEtnica($consulta, $tipoPueblo, $areaEspecializada);
        
        return $this->procesarConIA($prompt, [
            'area' => 'consulta_etnica',
            'tipo_pueblo' => $tipoPueblo,
            'area_especializada' => $areaEspecializada,
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_etnico_internacional'
        ]);
    }

    public function analizarMarcoJuridico($consulta, $areaJuridica = 'derechos_etnicos')
    {
        $prompt = $this->generarPromptMarcoJuridico($consulta, $areaJuridica);
        
        return $this->procesarConIA($prompt, [
            'area' => 'marco_juridico_etnico',
            'area_juridica' => $areaJuridica,
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_constitucional_etnico'
        ]);
    }

    public function analizarImpactoTerritorial($consulta, $ubicacion = null)
    {
        $prompt = $this->generarPromptImpactoTerritorial($consulta, $ubicacion);
        
        return $this->procesarConIA($prompt, [
            'area' => 'impacto_territorial',
            'ubicacion' => $ubicacion,
            'nivel' => 'post_doctorado',
            'especializacion' => 'derecho_territorial_etnico'
        ]);
    }

    private function procesarConIA($prompt, $metadata = [])
    {
        try {
            $cacheKey = 'ia_etnica_' . md5($prompt . json_encode($metadata));
            
            return Cache::remember($cacheKey, 3600, function () use ($prompt, $metadata) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(60)->post($this->baseUrl . '/chat/completions', [
                    'model' => 'gpt-4',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Eres un especialista en Derecho Étnico con nivel post-doctorado, experto en derechos de pueblos indígenas y afrodescendientes, con conocimiento profundo del marco jurídico nacional e internacional aplicable en Colombia.'
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
            Log::error('Error procesando con IA étnica: ' . $e->getMessage());
            return $this->generarAnalisisFallback($prompt, $metadata);
        }
    }

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

    private function generarPromptConsultaEtnica($consulta, $tipoPueblo, $areaEspecializada)
    {
        $puebloText = $tipoPueblo === 'indigena' ? 'pueblo indígena' : 'comunidad afrodescendiente';
        
        return "Como especialista en Derecho Étnico con nivel post-doctorado, analiza la siguiente consulta desde la perspectiva de los derechos de los pueblos {$puebloText}:

CONSULTA:
{$consulta}

ÁREA ESPECIALIZADA: {$areaEspecializada}

Proporciona un análisis exhaustivo que incluya:
1. Identificación de derechos étnicos involucrados
2. Marco jurídico aplicable (Constitución Nacional, Convenio 169 OIT, Declaración ONU)
3. Jurisprudencia constitucional relevante
4. Análisis de la consulta previa si aplica
5. Protección territorial y cultural
6. Autonomía y autodeterminación
7. Recomendaciones específicas
8. Estrategias de protección de derechos
9. Procedimientos de reclamación
10. Medidas de reparación si aplica

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Étnico Internacional
Jurisdicción: Colombia
Fecha: " . now()->format('d/m/Y');
    }

    private function generarPromptMarcoJuridico($consulta, $areaJuridica)
    {
        return "Como especialista en Derecho Constitucional Étnico con nivel post-doctorado, analiza el marco jurídico aplicable:

CONSULTA:
{$consulta}

ÁREA JURÍDICA: {$areaJuridica}

Proporciona un análisis exhaustivo del marco jurídico que incluya:
1. Constitución Nacional (Artículos 7, 8, 10, 70, 246, 330, 329)
2. Convenio 169 OIT sobre Pueblos Indígenas y Tribales
3. Declaración de las Naciones Unidas sobre los Derechos de los Pueblos Indígenas
4. Ley 21 de 1991 (Ratificación Convenio 169 OIT)
5. Ley 70 de 1993 (Comunidades Negras)
6. Ley 89 de 1890 (Resguardos Indígenas)
7. Decreto 2164 de 1995 (Resguardos)
8. Jurisprudencia de la Corte Constitucional
9. Jurisprudencia de la Corte Interamericana de Derechos Humanos
10. Normativa internacional aplicable

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Constitucional Étnico
Jurisdicción: Colombia
Fecha: " . now()->format('d/m/Y');
    }

    private function generarPromptImpactoTerritorial($consulta, $ubicacion)
    {
        $ubicacionText = $ubicacion ? "UBICACIÓN: {$ubicacion}" : "UBICACIÓN: No especificada";
        
        return "Como especialista en Derecho Territorial Étnico con nivel post-doctorado, analiza el impacto territorial:

CONSULTA:
{$consulta}

{$ubicacionText}

Proporciona un análisis exhaustivo del impacto territorial que incluya:
1. Análisis de territorio ancestral y resguardos
2. Impacto en sitios sagrados y culturales
3. Afectación de recursos naturales
4. Derechos territoriales colectivos
5. Consulta previa territorial
6. Protección ambiental étnica
7. Conflictos territoriales
8. Medidas de protección territorial
9. Gobernanza territorial ancestral
10. Recomendaciones de protección

Nivel de análisis: POST-DOCTORADO
Especialización: Derecho Territorial Étnico
Jurisdicción: Colombia
Fecha: " . now()->format('d/m/Y');
    }
}