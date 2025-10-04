<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnalisisIA;
use App\Services\CircuitBreaker;

/**
 * Servicio de IA para Derecho Informático
 * Nivel Post-Doctorado especializado en ciberseguridad, protección de datos y delitos informáticos
 */
class IADerechoInformatico
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
     * Análisis de Ciberseguridad
     */
    public function analizarCiberseguridad(array $datos): array
    {
        $cacheKey = 'ia_ciberseguridad_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptCiberseguridad($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisInformatico('ciberseguridad', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Protección de Datos
     */
    public function analizarProteccionDatos(array $datos): array
    {
        $cacheKey = 'ia_proteccion_datos_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptProteccionDatos($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisInformatico('proteccion_datos', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Delitos Informáticos
     */
    public function analizarDelitosInformaticos(array $datos): array
    {
        $cacheKey = 'ia_delitos_informaticos_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptDelitosInformaticos($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisInformatico('delitos_informaticos', $prompt, $datos);
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
                return $this->ejecutarAnalisisInformatico('evidencia_digital', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Comercio Electrónico
     */
    public function analizarComercioElectronico(array $datos): array
    {
        $cacheKey = 'ia_comercio_electronico_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptComercioElectronico($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisInformatico('comercio_electronico', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis de Inteligencia Artificial y Derecho
     */
    public function analizarIAyDerecho(array $datos): array
    {
        $cacheKey = 'ia_ia_derecho_' . md5(json_encode($datos));
        
        return Cache::remember($cacheKey, 7200, function () use ($datos) {
            $prompt = $this->construirPromptIAyDerecho($datos);
            
            return $this->circuitBreaker->execute(function () use ($prompt, $datos) {
                return $this->ejecutarAnalisisInformatico('ia_derecho', $prompt, $datos);
            });
        });
    }

    /**
     * Análisis integral de derecho informático
     */
    public function analisisIntegralDerechoInformatico(array $datos): array
    {
        $tiposAnalisis = $datos['tipos_analisis'] ?? [
            'ciberseguridad',
            'proteccion_datos',
            'delitos_informaticos',
            'evidencia_digital',
            'comercio_electronico',
            'ia_derecho'
        ];
        
        $resultados = [];

        foreach ($tiposAnalisis as $tipo) {
            $metodo = 'analizar' . str_replace('_', '', ucwords($tipo, '_'));
            if (method_exists($this, $metodo)) {
                $resultados[$tipo] = $this->$metodo($datos);
            }
        }

        return $this->consolidarAnalisisInformatico($resultados, $datos);
    }

    /**
     * Ejecutar análisis informático
     */
    protected function ejecutarAnalisisInformatico(string $tipoAnalisis, string $prompt, array $datos): array
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
                        'content' => "Eres un experto en derecho informático de nivel post-doctorado. " .
                                   "Especializado en ciberseguridad, protección de datos, delitos informáticos, " .
                                   "evidencia digital, comercio electrónico y regulación de IA. Proporciona " .
                                   "análisis exhaustivos con fundamentación académica sólida y referencias " .
                                   "a normativa vigente nacional e internacional."
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

            $this->guardarAnalisisInformatico($tipoAnalisis, $datos, $analisis, $resultado);

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

        throw new \RuntimeException('Error en análisis informático: ' . $response->body());
    }

    /**
     * Construir prompt para ciberseguridad
     */
    protected function construirPromptCiberseguridad(array $datos): string
    {
        $tipoCiberseguridad = $datos['tipo_ciberseguridad'] ?? 'general';
        $datosCiberseguridad = $datos['datos_ciberseguridad'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - CIBERSEGURIDAD

TIPO DE CIBERSEGURIDAD: {$tipoCiberseguridad}
ENTIDAD: {$entidad}

DATOS DE CIBERSEGURIDAD:
{$datosCiberseguridad}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE CIBERSEGURIDAD:
   - Ley 1273 de 2009 (Delitos Informáticos)
   - Decreto 1377 de 2013 (Protección de Datos)
   - Ley 1581 de 2012 (Habeas Data)
   - Decreto 2364 de 2012 (Reglamento Habeas Data)
   - ISO/IEC 27001 (Gestión de Seguridad)
   - NIST Cybersecurity Framework

2. AMENAZAS CIBERNÉTICAS:
   - Malware y ransomware
   - Phishing y ingeniería social
   - Ataques DDoS
   - Ataques de fuerza bruta
   - Vulnerabilidades de software
   - Amenazas internas

3. VULNERABILIDADES:
   - Vulnerabilidades de red
   - Vulnerabilidades de aplicación
   - Vulnerabilidades de base de datos
   - Vulnerabilidades de sistema operativo
   - Vulnerabilidades de hardware
   - Vulnerabilidades humanas

4. MEDIDAS DE PROTECCIÓN:
   - Firewalls y sistemas de detección
   - Antivirus y antimalware
   - Cifrado de datos
   - Autenticación multifactor
   - Backup y recuperación
   - Monitoreo continuo

5. GESTIÓN DE INCIDENTES:
   - Plan de respuesta a incidentes
   - Equipo de respuesta a incidentes
   - Procedimientos de contención
   - Análisis forense
   - Comunicación de incidentes
   - Recuperación y lecciones aprendidas

6. CUMPLIMIENTO NORMATIVO:
   - Cumplimiento de leyes locales
   - Cumplimiento de estándares internacionales
   - Auditorías de seguridad
   - Certificaciones de seguridad
   - Reportes de cumplimiento
   - Mejora continua

7. CONCIENTIZACIÓN Y CAPACITACIÓN:
   - Programas de concienciación
   - Capacitación en seguridad
   - Simulacros de phishing
   - Políticas de seguridad
   - Procedimientos de seguridad
   - Cultura de seguridad

8. TECNOLOGÍAS EMERGENTES:
   - Inteligencia artificial en seguridad
   - Machine learning para detección
   - Blockchain para seguridad
   - Internet de las cosas (IoT)
   - Computación en la nube
   - Edge computing

9. ASPECTOS LEGALES:
   - Responsabilidad legal
   - Obligaciones de notificación
   - Protección de datos personales
   - Derecho a la privacidad
   - Secreto profesional
   - Confidencialidad

10. RECOMENDACIONES ESTRATÉGICAS:
    - Implementar framework de ciberseguridad
    - Establecer programa de gestión de riesgos
    - Desarrollar capacidades de respuesta
    - Mejorar la concienciación
    - Adoptar tecnologías emergentes
    - Fortalecer el cumplimiento normativo

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para protección de datos
     */
    protected function construirPromptProteccionDatos(array $datos): string
    {
        $tipoProteccion = $datos['tipo_proteccion'] ?? 'general';
        $datosProteccion = $datos['datos_proteccion'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - PROTECCIÓN DE DATOS

TIPO DE PROTECCIÓN: {$tipoProteccion}
ENTIDAD: {$entidad}

DATOS DE PROTECCIÓN:
{$datosProteccion}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE PROTECCIÓN DE DATOS:
   - Ley 1581 de 2012 (Habeas Data)
   - Decreto 1377 de 2013 (Reglamento Habeas Data)
   - Decreto 2364 de 2012 (Reglamento Habeas Data)
   - Constitución Política (Art. 15)
   - GDPR (Reglamento General de Protección de Datos)
   - Convenio 108 del Consejo de Europa

2. PRINCIPIOS DE PROTECCIÓN DE DATOS:
   - Principio de finalidad
   - Principio de libertad
   - Principio de veracidad o calidad
   - Principio de acceso y circulación restringida
   - Principio de seguridad
   - Principio de confidencialidad

3. TIPOS DE DATOS PERSONALES:
   - Datos públicos
   - Datos semiprivados
   - Datos privados
   - Datos sensibles
   - Datos de menores
   - Datos biométricos

4. DERECHOS DE LOS TITULARES:
   - Derecho de acceso
   - Derecho de rectificación
   - Derecho de cancelación
   - Derecho de oposición
   - Derecho a la información
   - Derecho a la portabilidad

5. OBLIGACIONES DE LOS RESPONSABLES:
   - Obtención de autorización
   - Información al titular
   - Adopción de medidas de seguridad
   - Registro de bases de datos
   - Política de tratamiento
   - Procedimientos de atención

6. MEDIDAS DE SEGURIDAD:
   - Medidas técnicas
   - Medidas humanas
   - Medidas administrativas
   - Cifrado de datos
   - Control de acceso
   - Monitoreo y auditoría

7. TRANSFERENCIA INTERNACIONAL:
   - Países con nivel adecuado
   - Cláusulas contractuales
   - Certificaciones de cumplimiento
   - Autorización previa
   - Excepciones legales
   - Procedimientos especiales

8. VULNERACIÓN DE DATOS:
   - Concepto de vulneración
   - Tipos de vulneraciones
   - Notificación a autoridades
   - Notificación a titulares
   - Medidas de mitigación
   - Registro de vulneraciones

9. SANCIONES Y RESPONSABILIDADES:
   - Sanciones administrativas
   - Sanciones penales
   - Sanciones disciplinarias
   - Responsabilidad civil
   - Daños y perjuicios
   - Medidas correctivas

10. RECOMENDACIONES ESTRATÉGICAS:
    - Implementar programa de cumplimiento
    - Establecer medidas de seguridad
    - Capacitar al personal
    - Realizar auditorías periódicas
    - Mantener actualización normativa
    - Desarrollar cultura de protección

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para delitos informáticos
     */
    protected function construirPromptDelitosInformaticos(array $datos): string
    {
        $tipoDelito = $datos['tipo_delito'] ?? 'general';
        $datosDelito = $datos['datos_delito'] ?? '';
        $entidad = $datos['entidad'] ?? 'Entidad Pública';

        return "ANÁLISIS POST-DOCTORADO - DELITOS INFORMÁTICOS

TIPO DE DELITO: {$tipoDelito}
ENTIDAD: {$entidad}

DATOS DEL DELITO:
{$datosDelito}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE DELITOS INFORMÁTICOS:
   - Ley 1273 de 2009 (Delitos Informáticos)
   - Código Penal (Art. 269A-269L)
   - Constitución Política (Art. 15, 16)
   - Convenio de Budapest sobre Ciberdelincuencia
   - Protocolo Adicional al Convenio de Budapest
   - Jurisprudencia constitucional

2. TIPOS DE DELITOS INFORMÁTICOS:
   - Acceso abusivo a sistemas informáticos
   - Interceptación de datos informáticos
   - Ataque a la integridad de datos
   - Ataque a la integridad del sistema
   - Falsificación informática
   - Fraude informático

3. DELITOS CONTRA LA INTIMIDAD:
   - Violación de datos personales
   - Violación de comunicaciones
   - Interceptación de comunicaciones
   - Acceso no autorizado a datos
   - Difusión de datos personales
   - Suplantación de identidad

4. DELITOS CONTRA LA PROPIEDAD INTELECTUAL:
   - Violación de derechos de autor
   - Piratería de software
   - Distribución ilegal de contenidos
   - Plagio digital
   - Falsificación de obras
   - Uso no autorizado de marcas

5. DELITOS CONTRA LA SEGURIDAD:
   - Terrorismo informático
   - Sabotaje informático
   - Espionaje informático
   - Ciberterrorismo
   - Ataques a infraestructura crítica
   - Propaganda terrorista

6. DELITOS CONTRA MENORES:
   - Pornografía infantil
   - Grooming
   - Sexting
   - Ciberbullying
   - Explotación sexual
   - Trata de personas

7. INVESTIGACIÓN Y PERSECUCIÓN:
   - Policía judicial especializada
   - Fiscalía especializada
   - Peritos informáticos
   - Evidencia digital
   - Cadena de custodia
   - Cooperación internacional

8. JURISPRUDENCIA Y DOCTRINA:
   - Sentencias de la Corte Constitucional
   - Sentencias de la Corte Suprema
   - Sentencias de tribunales superiores
   - Doctrina especializada
   - Comentarios de expertos
   - Análisis comparado

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
     * Construir prompt para comercio electrónico
     */
    protected function construirPromptComercioElectronico(array $datos): string
    {
        $tipoComercio = $datos['tipo_comercio'] ?? 'general';
        $datosComercio = $datos['datos_comercio'] ?? '';
        $empresa = $datos['empresa'] ?? 'Empresa Digital';

        return "ANÁLISIS POST-DOCTORADO - COMERCIO ELECTRÓNICO

TIPO DE COMERCIO: {$tipoComercio}
EMPRESA: {$empresa}

DATOS DE COMERCIO:
{$datosComercio}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE COMERCIO ELECTRÓNICO:
   - Ley 527 de 1999 (Comercio Electrónico)
   - Decreto 1747 de 2000 (Reglamento Comercio Electrónico)
   - Ley 1266 de 2008 (Habeas Data Financiero)
   - Constitución Política (Art. 15, 16)
   - Modelo de Ley de Comercio Electrónico UNCITRAL
   - Directiva 2000/31/CE (Comercio Electrónico UE)

2. CONCEPTOS DE COMERCIO ELECTRÓNICO:
   - Concepto de comercio electrónico
   - Tipos de comercio electrónico
   - Modalidades de comercio electrónico
   - Características del comercio electrónico
   - Ventajas y desventajas
   - Tendencias del comercio electrónico

3. CONTRATACIÓN ELECTRÓNICA:
   - Contratos electrónicos
   - Formación del consentimiento
   - Manifestación de voluntad
   - Capacidad contractual
   - Objeto del contrato
   - Causa del contrato

4. FIRMA DIGITAL Y ELECTRÓNICA:
   - Concepto de firma digital
   - Tipos de firma digital
   - Certificados digitales
   - Autoridades certificadoras
   - Infraestructura de clave pública
   - Validez jurídica

5. PROTECCIÓN AL CONSUMIDOR:
   - Derechos del consumidor digital
   - Información precontractual
   - Derecho de retracto
   - Garantías y devoluciones
   - Resolución de controversias
   - Protección de datos

6. PAGOS ELECTRÓNICOS:
   - Medios de pago electrónico
   - Transferencias electrónicas
   - Tarjetas de crédito/débito
   - Monedas digitales
   - Criptomonedas
   - Regulación financiera

7. PROPIEDAD INTELECTUAL:
   - Derechos de autor digitales
   - Marcas en internet
   - Patentes de software
   - Secretos comerciales
   - Dominios de internet
   - Contenido generado por usuarios

8. RESPONSABILIDAD CIVIL:
   - Responsabilidad del proveedor
   - Responsabilidad del intermediario
   - Exclusión de responsabilidad
   - Limitación de responsabilidad
   - Seguros digitales
   - Daños y perjuicios

9. JURISDICCIÓN Y LEY APLICABLE:
   - Competencia territorial
   - Ley aplicable
   - Resolución de conflictos
   - Arbitraje electrónico
   - Mediación online
   - Cooperación internacional

10. RECOMENDACIONES ESTRATÉGICAS:
    - Desarrollar marco normativo
    - Fortalecer protección al consumidor
    - Mejorar seguridad digital
    - Promover innovación
    - Fomentar competencia
    - Facilitar comercio internacional

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Construir prompt para IA y derecho
     */
    protected function construirPromptIAyDerecho(array $datos): string
    {
        $tipoIA = $datos['tipo_ia'] ?? 'general';
        $datosIA = $datos['datos_ia'] ?? '';
        $aplicacion = $datos['aplicacion'] ?? 'Aplicación Legal';

        return "ANÁLISIS POST-DOCTORADO - INTELIGENCIA ARTIFICIAL Y DERECHO

TIPO DE IA: {$tipoIA}
APLICACIÓN: {$aplicacion}

DATOS DE IA:
{$datosIA}

Realiza un análisis exhaustivo de nivel post-doctorado que incluya:

1. MARCO NORMATIVO DE IA Y DERECHO:
   - Constitución Política (Art. 15, 16, 20)
   - Ley 1581 de 2012 (Habeas Data)
   - Ley 1273 de 2009 (Delitos Informáticos)
   - Reglamento General de Protección de Datos (GDPR)
   - Ley de IA de la Unión Europea
   - Principios de IA de la OCDE

2. CONCEPTOS DE INTELIGENCIA ARTIFICIAL:
   - Concepto de inteligencia artificial
   - Tipos de inteligencia artificial
   - Machine learning y deep learning
   - Algoritmos de decisión
   - Sistemas expertos
   - Redes neuronales

3. APLICACIONES DE IA EN EL DERECHO:
   - Análisis de jurisprudencia
   - Predicción de sentencias
   - Análisis de contratos
   - Investigación legal
   - Asistencia en decisiones
   - Automatización de procesos

4. DERECHOS FUNDAMENTALES Y IA:
   - Derecho a la privacidad
   - Derecho a la protección de datos
   - Derecho a la no discriminación
   - Derecho a la igualdad
   - Derecho a la información
   - Derecho a la transparencia

5. RESPONSABILIDAD POR IA:
   - Responsabilidad civil
   - Responsabilidad penal
   - Responsabilidad administrativa
   - Responsabilidad del desarrollador
   - Responsabilidad del usuario
   - Responsabilidad del algoritmo

6. TRANSPARENCIA Y EXPLICABILIDAD:
   - Principio de transparencia
   - Derecho a la explicación
   - Algoritmos explicables
   - Auditoría de algoritmos
   - Sesgo algorítmico
   - Equidad algorítmica

7. PROTECCIÓN DE DATOS EN IA:
   - Minimización de datos
   - Finalidad del tratamiento
   - Consentimiento informado
   - Anonimización
   - Pseudonimización
   - Portabilidad de datos

8. REGULACIÓN DE IA:
   - Enfoques regulatorios
   - Regulación por sectores
   - Regulación horizontal
   - Autorregulación
   - Co-regulación
   - Regulación internacional

9. ÉTICA Y IA:
   - Principios éticos
   - Comités de ética
   - Evaluación de impacto ético
   - Diseño ético
   - Uso responsable
   - Supervisión ética

10. RECOMENDACIONES ESTRATÉGICAS:
    - Desarrollar marco regulatorio
    - Fortalecer protección de derechos
    - Promover transparencia
    - Fomentar ética en IA
    - Capacitar especialistas
    - Facilitar innovación responsable

Proporciona un análisis detallado con fundamentación académica sólida y referencias específicas.";
    }

    /**
     * Consolidar análisis informático
     */
    protected function consolidarAnalisisInformatico(array $resultados, array $datos): array
    {
        $consolidado = [
            'exito' => true,
            'tipo' => 'analisis_integral_derecho_informatico',
            'nivel' => 'post_doctorado',
            'analisis_realizados' => array_keys($resultados),
            'analisis_consolidado' => '',
            'recomendaciones_integrales' => [],
            'riesgos_identificados' => [],
            'oportunidades' => [],
            'timestamp' => now()->toISOString()
        ];

        // Consolidar análisis
        $analisisConsolidado = "ANÁLISIS INTEGRAL POST-DOCTORADO - DERECHO INFORMÁTICO\n\n";
        foreach ($resultados as $tipoAnalisis => $resultado) {
            if ($resultado['exito']) {
                $analisisConsolidado .= "=== {$tipoAnalisis} ===\n";
                $analisisConsolidado .= $resultado['analisis'] . "\n\n";
            }
        }

        $consolidado['analisis_consolidado'] = $analisisConsolidado;

        // Generar recomendaciones integrales
        $consolidado['recomendaciones_integrales'] = [
            'Implementar programa integral de ciberseguridad',
            'Establecer medidas robustas de protección de datos',
            'Desarrollar capacidades de investigación de delitos informáticos',
            'Fortalecer la evidencia digital y peritazgo',
            'Regular el comercio electrónico y pagos digitales',
            'Establecer marco ético y legal para IA'
        ];

        return $consolidado;
    }

    /**
     * Guardar análisis informático
     */
    protected function guardarAnalisisInformatico(string $tipoAnalisis, array $datos, string $analisis, array $resultado): void
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
            Log::error('Error guardando análisis informático', [
                'tipo_analisis' => $tipoAnalisis,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de derecho informático
     */
    public function obtenerEstadisticasDerechoInformatico(): array
    {
        return [
            'total_analisis_informatico' => AnalisisIA::whereIn('especialidad', [
                'ciberseguridad',
                'proteccion_datos',
                'delitos_informaticos',
                'evidencia_digital',
                'comercio_electronico',
                'ia_derecho'
            ])->count(),
            'analisis_por_tipo' => AnalisisIA::whereIn('especialidad', [
                'ciberseguridad',
                'proteccion_datos',
                'delitos_informaticos',
                'evidencia_digital',
                'comercio_electronico',
                'ia_derecho'
            ])->selectRaw('especialidad, COUNT(*) as total')
                ->groupBy('especialidad')
                ->get()
                ->pluck('total', 'especialidad'),
            'tokens_totales' => AnalisisIA::whereIn('especialidad', [
                'ciberseguridad',
                'proteccion_datos',
                'delitos_informaticos',
                'evidencia_digital',
                'comercio_electronico',
                'ia_derecho'
            ])->sum('tokens_usados')
        ];
    }
}
