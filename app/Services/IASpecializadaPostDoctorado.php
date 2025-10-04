<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA Especializada Post-Doctorado
 * Proporciona análisis jurídicos de nivel post-doctorado para múltiples especialidades
 */
class IASpecializadaPostDoctorado
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelo;
    protected CircuitBreaker $circuitBreaker;
    protected array $especialidades;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->modelo = config('services.openai.model', 'gpt-4');

        $this->circuitBreaker = new CircuitBreaker('openai', 5, 60, 3);
        
        $this->especialidades = [
            'derecho_minero' => [
                'nombre' => 'Derecho Minero Internacional',
                'nivel' => 'post_doctorado',
                'modelo' => 'gpt-4',
                'temperatura' => 0.1,
                'max_tokens' => 4000
            ],
            'derecho_ambiental' => [
                'nombre' => 'Derecho Ambiental y Sostenibilidad',
                'nivel' => 'post_doctorado',
                'modelo' => 'gpt-4',
                'temperatura' => 0.1,
                'max_tokens' => 4000
            ],
            'derecho_etnico' => [
                'nombre' => 'Derecho Étnico y Pueblos Originarios',
                'nivel' => 'post_doctorado',
                'modelo' => 'gpt-4',
                'temperatura' => 0.1,
                'max_tokens' => 4000
            ],
            'derecho_catastral' => [
                'nombre' => 'Derecho Catastral y Territorial',
                'nivel' => 'post_doctorado',
                'modelo' => 'gpt-4',
                'temperatura' => 0.1,
                'max_tokens' => 4000
            ],
            'derecho_anticorrupcion' => [
                'nombre' => 'Derecho Anti-Corrupción y Ética Pública',
                'nivel' => 'post_doctorado',
                'modelo' => 'gpt-4',
                'temperatura' => 0.1,
                'max_tokens' => 4000
            ],
            'derecho_medico' => [
                'nombre' => 'Derecho Médico y Bioética',
                'nivel' => 'post_doctorado',
                'modelo' => 'gpt-4',
                'temperatura' => 0.1,
                'max_tokens' => 4000
            ],
            'derecho_penal_avanzado' => [
                'nombre' => 'Derecho Penal Avanzado y Criminología',
                'nivel' => 'post_doctorado',
                'modelo' => 'gpt-4',
                'temperatura' => 0.1,
                'max_tokens' => 4000
            ],
            'derecho_disciplinario' => [
                'nombre' => 'Derecho Disciplinario y Ética Profesional',
                'nivel' => 'post_doctorado',
                'modelo' => 'gpt-4',
                'temperatura' => 0.1,
                'max_tokens' => 4000
            ]
        ];
    }

    /**
     * Análisis especializado de derecho minero
     */
    public function analizarDerechoMinero(array $datos): array
    {
        $cacheKey = 'ia_minero_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptMinero($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisEspecializado('derecho_minero', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis especializado de derecho ambiental
     */
    public function analizarDerechoAmbiental(array $datos): array
    {
        $cacheKey = 'ia_ambiental_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptAmbiental($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisEspecializado('derecho_ambiental', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis especializado de derecho étnico
     */
    public function analizarDerechoEtnico(array $datos): array
    {
        $cacheKey = 'ia_etnico_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptEtnico($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisEspecializado('derecho_etnico', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis especializado de derecho catastral
     */
    public function analizarDerechoCatastral(array $datos): array
    {
        $cacheKey = 'ia_catastral_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptCatastral($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisEspecializado('derecho_catastral', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis especializado de derecho anti-corrupción
     */
    public function analizarDerechoAnticorrupcion(array $datos): array
    {
        $cacheKey = 'ia_anticorrupcion_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptAnticorrupcion($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisEspecializado('derecho_anticorrupcion', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis especializado de derecho médico
     */
    public function analizarDerechoMedico(array $datos): array
    {
        $cacheKey = 'ia_medico_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptMedico($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisEspecializado('derecho_medico', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis especializado de derecho penal avanzado
     */
    public function analizarDerechoPenalAvanzado(array $datos): array
    {
        $cacheKey = 'ia_penal_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptPenalAvanzado($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisEspecializado('derecho_penal_avanzado', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis especializado de derecho disciplinario
     */
    public function analizarDerechoDisciplinario(array $datos): array
    {
        $cacheKey = 'ia_disciplinario_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptDisciplinario($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisEspecializado('derecho_disciplinario', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral multi-especialidad
     */
    public function analisisIntegralMultiEspecialidad(array $datos): array
    {
        $especialidades = $datos['especialidades'] ?? ['derecho_minero', 'derecho_ambiental', 'derecho_etnico'];
        $resultados = [];

        foreach ($especialidades as $especialidad) {
            if (isset($this->especialidades[$especialidad])) {
                $metodo = 'analizar' . str_replace('_', '', ucwords($especialidad, '_'));
                if (method_exists($this, $metodo)) {
                    $resultados[$especialidad] = $this->$metodo($datos);
                }
            }
        }

        return $this->consolidarAnalisisIntegral($resultados, $datos);
    }

    /**
     * Ejecutar análisis especializado
     */
    protected function ejecutarAnalisisEspecializado(string $especialidad, string $prompt, array $datos): array
    {
        $config = $this->especialidades[$especialidad];
        
        $response = Http::timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $config['modelo'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Eres un experto jurista de nivel post-doctorado especializado en {$config['nombre']}. " .
                                   "Proporciona análisis jurídicos exhaustivos, precisos y actualizados con fundamentación " .
                                   "académica sólida y referencias a jurisprudencia, doctrina y normativa vigente."
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => $config['max_tokens'],
                'temperature' => $config['temperatura'],
            ]);

        if ($response->successful()) {
            $resultado = $response->json();
            $analisis = $resultado['choices'][0]['message']['content'];

            // Guardar en base de datos
            $this->guardarAnalisisEnBD($especialidad, $datos, $analisis, $resultado);

            return [
                'exito' => true,
                'especialidad' => $especialidad,
                'nivel' => 'post_doctorado',
                'analisis' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo' => $config['modelo'],
                'timestamp' => now()->toISOString()
            ];
        }

        throw new \RuntimeException('Error en análisis especializado: ' . $response->body());
    }

    /**
     * Construir prompt para derecho minero
     */
    protected function construirPromptMinero(array $datos): string
    {
        $tipoMineria = $datos['tipo_mineria'] ?? 'general';
        $datosMineros = $datos['datos_mineros'] ?? '';
        $jurisdiccion = $datos['jurisdiccion'] ?? 'colombia';

        return "ANÁLISIS JURÍDICO POST-DOCTORADO - DERECHO MINERO INTERNACIONAL

CASO: {$datosMineros}
TIPO DE MINERÍA: {$tipoMineria}
JURISDICCIÓN: {$jurisdiccion}

Realiza un análisis jurídico exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO APLICABLE:
   - Normativa nacional e internacional
   - Tratados y convenios internacionales
   - Jurisprudencia constitucional y administrativa
   - Doctrina especializada

2. ANÁLISIS DE TÍTULOS MINEROS:
   - Procedimiento de otorgamiento
   - Requisitos y condiciones
   - Derechos y obligaciones del titular
   - Duración y renovación

3. CONSULTA PREVIA Y DERECHOS ÉTNICOS:
   - Convenio 169 OIT
   - Declaración ONU sobre Derechos de Pueblos Indígenas
   - Jurisprudencia de la Corte Interamericana
   - Procedimientos de consulta

4. IMPACTO AMBIENTAL:
   - Licencias ambientales
   - Estudios de impacto ambiental
   - Planes de manejo ambiental
   - Restauración y compensación

5. REGALÍAS Y TRIBUTACIÓN:
   - Sistema de regalías mineras
   - Tributación especial
   - Distribución territorial
   - Fondos de compensación

6. ANÁLISIS DE RIESGOS LEGALES:
   - Riesgos contractuales
   - Riesgos regulatorios
   - Riesgos ambientales
   - Riesgos sociales

7. RECOMENDACIONES ESTRATÉGICAS:
   - Estrategia de cumplimiento
   - Mitigación de riesgos
   - Optimización legal
   - Mejores prácticas

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para derecho ambiental
     */
    protected function construirPromptAmbiental(array $datos): string
    {
        $tipoAmbiental = $datos['tipo_ambiental'] ?? 'general';
        $datosAmbientales = $datos['datos_ambientales'] ?? '';
        $jurisdiccion = $datos['jurisdiccion'] ?? 'colombia';

        return "ANÁLISIS JURÍDICO POST-DOCTORADO - DERECHO AMBIENTAL Y SOSTENIBILIDAD

CASO: {$datosAmbientales}
TIPO AMBIENTAL: {$tipoAmbiental}
JURISDICCIÓN: {$jurisdiccion}

Realiza un análisis jurídico exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO AMBIENTAL:
   - Constitución Política (Art. 79-82)
   - Código de Recursos Naturales
   - Ley 99 de 1993
   - Normativa internacional (Convenio de París, Protocolo de Kioto)
   - Jurisprudencia constitucional ambiental

2. PRINCIPIOS AMBIENTALES:
   - Principio de precaución
   - Principio de prevención
   - Principio de responsabilidad
   - Principio de participación
   - Principio de solidaridad

3. LICENCIAMIENTO AMBIENTAL:
   - Licencia ambiental
   - Permiso ambiental
   - Concesión de aguas
   - Permiso de emisiones
   - Procedimientos administrativos

4. EVALUACIÓN DE IMPACTO AMBIENTAL:
   - Estudio de impacto ambiental (EIA)
   - Plan de manejo ambiental (PMA)
   - Plan de compensación
   - Plan de restauración
   - Seguimiento y monitoreo

5. PROTECCIÓN DE ECOSISTEMAS:
   - Áreas protegidas
   - Reservas forestales
   - Parques naturales
   - Humedales Ramsar
   - Ecosistemas estratégicos

6. RESPONSABILIDAD AMBIENTAL:
   - Daño ambiental
   - Reparación integral
   - Restauración ecológica
   - Compensación ambiental
   - Seguro ambiental

7. PARTICIPACIÓN CIUDADANA:
   - Audiencias públicas
   - Consultas populares
   - Acciones populares
   - Acciones de grupo
   - Veedurías ambientales

8. ANÁLISIS DE CUMPLIMIENTO:
   - Obligaciones legales
   - Estándares técnicos
   - Normas de calidad
   - Límites permisibles
   - Sanciones y multas

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para derecho étnico
     */
    protected function construirPromptEtnico(array $datos): string
    {
        $tipoComunidad = $datos['tipo_comunidad'] ?? 'indigena';
        $datosEtnicos = $datos['datos_etnicos'] ?? '';
        $jurisdiccion = $datos['jurisdiccion'] ?? 'colombia';

        return "ANÁLISIS JURÍDICO POST-DOCTORADO - DERECHO ÉTNICO Y PUEBLOS ORIGINARIOS

CASO: {$datosEtnicos}
TIPO DE COMUNIDAD: {$tipoComunidad}
JURISDICCIÓN: {$jurisdiccion}

Realiza un análisis jurídico exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO ÉTNICO:
   - Convenio 169 OIT sobre Pueblos Indígenas y Tribales
   - Declaración ONU sobre Derechos de Pueblos Indígenas
   - Constitución Política (Art. 7, 8, 10, 70, 72, 329-330)
   - Ley 21 de 1991 (Convenio 169 OIT)
   - Ley 70 de 1993 (Comunidades Negras)
   - Jurisprudencia constitucional étnica

2. DERECHOS FUNDAMENTALES ÉTNICOS:
   - Derecho a la identidad cultural
   - Derecho a la autonomía
   - Derecho al territorio
   - Derecho a la consulta previa
   - Derecho a la participación
   - Derecho a la educación propia

3. TERRITORIOS ÉTNICOS:
   - Resguardos indígenas
   - Territorios colectivos afrodescendientes
   - Territorios ancestrales
   - Zonas de reserva campesina
   - Procedimientos de constitución
   - Límites y deslindes

4. CONSULTA PREVIA, LIBRE E INFORMADA:
   - Procedimiento de consulta
   - Consentimiento previo, libre e informado
   - Medidas de salvaguarda
   - Protocolos de consulta
   - Jurisprudencia interamericana
   - Casos emblemáticos

5. JURISDICCIÓN ESPECIAL INDÍGENA:
   - Autoridades tradicionales
   - Sistemas de justicia propia
   - Coordinación con justicia ordinaria
   - Límites de competencia
   - Procedimientos de coordinación

6. PROTECCIÓN CULTURAL:
   - Patrimonio cultural inmaterial
   - Conocimientos tradicionales
   - Medicina tradicional
   - Lenguas nativas
   - Rituales y ceremonias
   - Sitios sagrados

7. DESARROLLO PROPIO:
   - Planes de vida
   - Planes etnodesarrollo
   - Proyectos productivos
   - Educación propia
   - Salud propia
   - Vivienda tradicional

8. ANÁLISIS DE VULNERACIONES:
   - Desplazamiento forzado
   - Conflicto armado
   - Explotación de recursos
   - Contaminación ambiental
   - Pérdida cultural
   - Violencia de género

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para derecho catastral
     */
    protected function construirPromptCatastral(array $datos): string
    {
        $tipoPredio = $datos['tipo_predio'] ?? 'general';
        $datosCatastrales = $datos['datos_catastrales'] ?? '';
        $jurisdiccion = $datos['jurisdiccion'] ?? 'colombia';

        return "ANÁLISIS JURÍDICO POST-DOCTORADO - DERECHO CATASTRAL Y TERRITORIAL

CASO: {$datosCatastrales}
TIPO DE PREDIO: {$tipoPredio}
JURISDICCIÓN: {$jurisdiccion}

Realiza un análisis jurídico exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO CATASTRAL:
   - Ley 14 de 1983 (Código de Régimen Municipal)
   - Decreto 400 de 2012 (Reglamento Catastral)
   - Ley 1753 de 2015 (Ley de Formalización)
   - Decreto 1077 de 2015 (Único Reglamentario del Sector Vivienda)
   - Jurisprudencia administrativa catastral

2. SISTEMA CATASTRAL:
   - Catastro multipropósito
   - Información catastral
   - Actualización catastral
   - Levantamiento catastral
   - Georreferenciación
   - Coordenadas geográficas

3. DERECHOS DE PROPIEDAD:
   - Propiedad horizontal
   - Propiedad raíz
   - Servidumbres
   - Linderos y deslindes
   - Títulos de propiedad
   - Registro de instrumentos públicos

4. AVALÚOS CATASTRALES:
   - Avalúo comercial
   - Avalúo catastral
   - Metodologías de avalúo
   - Factores de valorización
   - Actualización de avalúos
   - Recurso de reposición

5. USO DEL SUELO:
   - Planes de ordenamiento territorial
   - Clasificación del suelo
   - Destinación económica
   - Zonificación
   - Normas urbanísticas
   - Licencias de construcción

6. PROCEDIMIENTOS ADMINISTRATIVOS:
   - Inscripción catastral
   - Actualización de información
   - Rectificación de linderos
   - División de predios
   - Fusión de predios
   - Recursos administrativos

7. TRIBUTACIÓN PREDIAL:
   - Impuesto predial
   - Contribución de valorización
   - Sobretasa ambiental
   - Exenciones y excepciones
   - Procedimientos de cobro
   - Recursos de reposición

8. CONFLICTOS CATASTRALES:
   - Conflictos de linderos
   - Doble numeración
   - Superposición de predios
   - Errores catastrales
   - Procedimientos de solución
   - Acciones judiciales

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para derecho anti-corrupción
     */
    protected function construirPromptAnticorrupcion(array $datos): string
    {
        $tipoCorrupcion = $datos['tipo_corrupcion'] ?? 'general';
        $datosAnticorrupcion = $datos['datos_anticorrupcion'] ?? '';
        $jurisdiccion = $datos['jurisdiccion'] ?? 'colombia';

        return "ANÁLISIS JURÍDICO POST-DOCTORADO - DERECHO ANTI-CORRUPCIÓN Y ÉTICA PÚBLICA

CASO: {$datosAnticorrupcion}
TIPO DE CORRUPCIÓN: {$tipoCorrupcion}
JURISDICCIÓN: {$jurisdiccion}

Realiza un análisis jurídico exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO ANTI-CORRUPCIÓN:
   - Convención de las Naciones Unidas contra la Corrupción
   - Convención Interamericana contra la Corrupción
   - Ley 1474 de 2011 (Estatuto Anticorrupción)
   - Ley 190 de 1995 (Código Disciplinario Único)
   - Jurisprudencia constitucional anticorrupción

2. TIPOS PENALES DE CORRUPCIÓN:
   - Cohecho por dar u ofrecer
   - Cohecho por recibir o solicitar
   - Peculado por apropiación
   - Peculado por uso
   - Concusión
   - Prevaricato por acción
   - Prevaricato por omisión

3. RÉGIMEN DISCIPLINARIO:
   - Código Disciplinario Único
   - Proceso disciplinario
   - Sanciones disciplinarias
   - Prescripción disciplinaria
   - Procedimiento sancionatorio
   - Recursos disciplinarios

4. TRANSPARENCIA Y ACCESO A LA INFORMACIÓN:
   - Ley 1712 de 2014 (Transparencia y Acceso a la Información)
   - Transparencia activa
   - Transparencia pasiva
   - Derecho de petición
   - Habeas data
   - Protección de datos personales

5. CONTRATACIÓN PÚBLICA:
   - Ley 80 de 1993 (Estatuto de Contratación)
   - Ley 1150 de 2007 (Reforma Contractual)
   - Procedimientos de selección
   - Inhabilidades e incompatibilidades
   - Prohibiciones contractuales
   - Sanciones contractuales

6. CONTROL FISCAL:
   - Función pública de control
   - Auditoría gubernamental
   - Responsabilidad fiscal
   - Proceso de responsabilidad fiscal
   - Sanciones fiscales
   - Recursos fiscales

7. PARTICIPACIÓN CIUDADANA:
   - Veedurías ciudadanas
   - Control social
   - Denuncias ciudadanas
   - Mecanismos de participación
   - Rendición de cuentas
   - Evaluación ciudadana

8. PREVENCIÓN DE CORRUPCIÓN:
   - Políticas de integridad
   - Códigos de ética
   - Programas de prevención
   - Capacitación anticorrupción
   - Sistemas de alertas
   - Medidas de mitigación

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para derecho médico
     */
    protected function construirPromptMedico(array $datos): string
    {
        $tipoMedico = $datos['tipo_medico'] ?? 'general';
        $datosMedicos = $datos['datos_medicos'] ?? '';
        $jurisdiccion = $datos['jurisdiccion'] ?? 'colombia';

        return "ANÁLISIS JURÍDICO POST-DOCTORADO - DERECHO MÉDICO Y BIOÉTICA

CASO: {$datosMedicos}
TIPO MÉDICO: {$tipoMedico}
JURISDICCIÓN: {$jurisdiccion}

Realiza un análisis jurídico exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO MÉDICO:
   - Ley 23 de 1981 (Ética Médica)
   - Ley 100 de 1993 (Sistema de Seguridad Social)
   - Ley 1122 de 2007 (Reforma Sistema de Salud)
   - Ley 1438 de 2011 (Reforma Sistema de Salud)
   - Código Penal (Art. 106-108)
   - Jurisprudencia médica

2. RESPONSABILIDAD MÉDICA:
   - Responsabilidad civil médica
   - Responsabilidad penal médica
   - Responsabilidad disciplinaria
   - Responsabilidad administrativa
   - Carga de la prueba
   - Presunción de responsabilidad

3. CONSENTIMIENTO INFORMADO:
   - Elementos del consentimiento
   - Información médica
   - Capacidad del paciente
   - Excepciones al consentimiento
   - Documentación médica
   - Historia clínica

4. SECRETO MÉDICO:
   - Confidencialidad médica
   - Excepciones al secreto
   - Revelación de información
   - Protección de datos
   - Historia clínica
   - Acceso a información

5. BIOÉTICA MÉDICA:
   - Principios bioéticos
   - Autonomía del paciente
   - Beneficencia médica
   - No maleficencia
   - Justicia distributiva
   - Comités de ética

6. INVESTIGACIÓN MÉDICA:
   - Ensayos clínicos
   - Consentimiento informado
   - Comités de ética
   - Protocolos de investigación
   - Protección de sujetos
   - Buenas prácticas clínicas

7. MEDICINA LEGAL:
   - Peritaje médico
   - Autopsia médico-legal
   - Lesiones personales
   - Muerte sospechosa
   - Violencia sexual
   - Maltrato infantil

8. SEGURIDAD SOCIAL:
   - Sistema de salud
   - Prestación de servicios
   - Calidad en salud
   - Acceso a medicamentos
   - Tecnologías en salud
   - Telemedicina

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para derecho penal avanzado
     */
    protected function construirPromptPenalAvanzado(array $datos): string
    {
        $tipoPenal = $datos['tipo_penal'] ?? 'general';
        $datosPenales = $datos['datos_penales'] ?? '';
        $jurisdiccion = $datos['jurisdiccion'] ?? 'colombia';

        return "ANÁLISIS JURÍDICO POST-DOCTORADO - DERECHO PENAL AVANZADO Y CRIMINOLOGÍA

CASO: {$datosPenales}
TIPO PENAL: {$tipoPenal}
JURISDICCIÓN: {$jurisdiccion}

Realiza un análisis jurídico exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO PENAL:
   - Código Penal (Ley 599 de 2000)
   - Código de Procedimiento Penal (Ley 906 de 2004)
   - Ley 1453 de 2011 (Seguridad Ciudadana)
   - Ley 1801 de 2016 (Código de Policía)
   - Jurisprudencia penal constitucional

2. TEORÍA DEL DELITO:
   - Tipicidad objetiva y subjetiva
   - Antijuridicidad
   - Culpabilidad
   - Imputabilidad
   - Inimputabilidad
   - Causas de justificación

3. PENAS Y MEDIDAS DE SEGURIDAD:
   - Penas principales
   - Penas accesorias
   - Medidas de seguridad
   - Suspensión de ejecución
   - Libertad condicional
   - Beneficios penitenciarios

4. PROCEDIMIENTO PENAL:
   - Investigación preliminar
   - Indagación
   - Imputación
   - Audiencia de formulación de cargos
   - Audiencia de juicio
   - Sentencia

5. GARANTÍAS PROCESALES:
   - Presunción de inocencia
   - Debido proceso
   - Derecho de defensa
   - Derecho a no autoincriminarse
   - Derecho a la intimidad
   - Derecho a la libertad

6. CRIMINOLOGÍA:
   - Teorías criminológicas
   - Factores criminógenos
   - Prevención del delito
   - Reinserción social
   - Victimología
   - Justicia restaurativa

7. DELITOS ESPECIALES:
   - Delitos contra la vida
   - Delitos contra la integridad personal
   - Delitos contra la libertad sexual
   - Delitos contra la libertad
   - Delitos contra el patrimonio
   - Delitos contra la administración pública

8. MEDIDAS CAUTELARES:
   - Detención preventiva
   - Medidas de aseguramiento
   - Libertad bajo fianza
   - Arresto domiciliario
   - Prohibición de salir del país
   - Suspensión de actividades

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para derecho disciplinario
     */
    protected function construirPromptDisciplinario(array $datos): string
    {
        $tipoDisciplinario = $datos['tipo_disciplinario'] ?? 'general';
        $datosDisciplinarios = $datos['datos_disciplinarios'] ?? '';
        $jurisdiccion = $datos['jurisdiccion'] ?? 'colombia';

        return "ANÁLISIS JURÍDICO POST-DOCTORADO - DERECHO DISCIPLINARIO Y ÉTICA PROFESIONAL

CASO: {$datosDisciplinarios}
TIPO DISCIPLINARIO: {$tipoDisciplinario}
JURISDICCIÓN: {$jurisdiccion}

Realiza un análisis jurídico exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DISCIPLINARIO:
   - Código Disciplinario Único (Ley 190 de 1995)
   - Ley 734 de 2002 (Código Disciplinario Único)
   - Decreto 1066 de 2015 (Reglamento Disciplinario)
   - Jurisprudencia disciplinaria constitucional
   - Doctrina disciplinaria

2. FUNCIÓN PÚBLICA:
   - Concepto de servidor público
   - Clases de servidores públicos
   - Carrera administrativa
   - Empleo público
   - Contratación estatal
   - Servidores de elección popular

3. FALTAS DISCIPLINARIAS:
   - Faltas gravísimas
   - Faltas graves
   - Faltas leves
   - Faltas muy graves
   - Faltas especiales
   - Faltas por omisión

4. PROCEDIMIENTO DISCIPLINARIO:
   - Investigación preliminar
   - Pliego de cargos
   - Descargos
   - Pruebas
   - Audiencia de fallo
   - Sentencia disciplinaria

5. SANCIONES DISCIPLINARIAS:
   - Destitución e inhabilidad
   - Suspensión en el empleo
   - Multa
   - Censura
   - Suspensión de funciones
   - Inhabilidad para contratar

6. GARANTÍAS DISCIPLINARIAS:
   - Debido proceso
   - Derecho de defensa
   - Presunción de inocencia
   - Derecho a la intimidad
   - Derecho a la igualdad
   - Derecho a la no autoincriminación

7. ÉTICA PÚBLICA:
   - Códigos de ética
   - Principios éticos
   - Conflictos de interés
   - Incompatibilidades
   - Inhabilidades
   - Prohibiciones

8. CONTROL DISCIPLINARIO:
   - Procuraduría General de la Nación
   - Procuradurías regionales
   - Contralorías
   - Personeros
   - Veedurías
   - Control social

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Consolidar análisis integral
     */
    protected function consolidarAnalisisIntegral(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral',
            'nivel' => 'post_doctorado',
            'especialidades_analizadas' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'riesgos_identificados' => [],
            'oportunidades' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO\n\n";
        foreach ($resultados as $especialidad => $resultado) {
            if ($resultado['exito']) {
                $analisisConsolidado .= "=== {$this->especialidades[$especialidad]['nombre']} ===\n";
                $analisisConsolidado .= $resultado['analisis'] . "\n\n";
            }
        }

        $consolidado['analisis_consolidado'] = $analisisConsolidado;

        // Generar recomendaciones integrales
        $consolidado['recomendaciones_integrales'] = [
            'Revisar cumplimiento normativo en todas las especialidades analizadas',
            'Implementar estrategia integral de mitigación de riesgos',
            'Establecer protocolos de coordinación interdisciplinaria',
            'Desarrollar plan de seguimiento y monitoreo continuo',
            'Capacitar equipos en las diferentes especialidades jurídicas'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis en base de datos
     */
    protected function guardarAnalisisEnBD(string $especialidad, array $datos, string $analisis, array $resultado): void
    {
        try {
            AnalisisIA::create([
                'especialidad' => $especialidad,
                'nivel' => 'post_doctorado',
                'datos_entrada' => json_encode($datos),
                'analisis_completo' => $analisis,
                'tokens_usados' => $resultado['usage']['total_tokens'] ?? 0,
                'modelo_ia' => $this->especialidades[$especialidad]['modelo'],
                'estado' => 'completado',
                'fecha_analisis' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Error guardando análisis en BD', [
                'especialidad' => $especialidad,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener especialidades disponibles
     */
    public function obtenerEspecialidadesDisponibles(): array
    {
        return $this->especialidades;
    }

    /**
     * Obtener estadísticas de análisis
     */
    public function obtenerEstadisticasAnalisis(): array
    {
        return [
            'total_analisis' => AnalisisIA::count(),
            'analisis_por_especialidad' => AnalisisIA::selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'analisis_por_estado' => AnalisisIA::selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->get()
                ->pluck('total', 'estado'),
            'tokens_totales' => AnalisisIA::sum('tokens_usados'),
            'analisis_recientes' => AnalisisIA::latest()
                ->limit(10)
                ->get(['especialidad', 'nivel', 'estado', 'fecha_analisis'])
        ];
    }
}
