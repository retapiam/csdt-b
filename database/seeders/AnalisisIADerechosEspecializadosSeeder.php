<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnalisisIADerechosEspecializados;
use App\Models\Usuario;

class AnalisisIADerechosEspecializadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos usuarios para asociar los análisis
        $usuarios = Usuario::limit(5)->get();
        
        if ($usuarios->isEmpty()) {
            $this->command->warn('No hay usuarios en la base de datos. Creando análisis sin usuario asociado.');
            $usuarioId = null;
        } else {
            $usuarioId = $usuarios->first()->id;
        }

        $analisisEjemplo = [
            [
                'usuario_id' => $usuarioId,
                'area_derecho' => 'derechos_mineros',
                'tipo_analisis' => 'general',
                'datos_entrada' => [
                    'descripcion' => 'Análisis de derechos mineros en territorio indígena',
                    'ubicacion' => 'Resguardo Indígena de la Sierra Nevada',
                    'tipo_mineria' => 'oro',
                    'comunidad_afectada' => 'Comunidad Kogui'
                ],
                'resultado_ia' => [
                    'analisis' => 'Análisis completo de derechos mineros aplicables...',
                    'recomendaciones' => 'Se recomienda consulta previa y evaluación de impacto...',
                    'normativa_aplicable' => 'Ley 685 de 2001, Convenio 169 OIT...'
                ],
                'metadata' => [
                    'nivel' => 'post_doctorado',
                    'especializacion' => 'derecho_minero_internacional',
                    'jurisdiccion' => 'Colombia'
                ],
                'tokens_usados' => 1250,
                'modelo_ia' => 'gpt-4',
                'tiempo_procesamiento' => 2.5,
                'estado' => 'completado',
                'observaciones' => 'Análisis completado exitosamente'
            ],
            [
                'usuario_id' => $usuarioId,
                'area_derecho' => 'derechos_catastrales',
                'tipo_analisis' => 'urbano',
                'datos_entrada' => [
                    'descripcion' => 'Análisis de derechos catastrales en predio urbano',
                    'ubicacion' => 'Bogotá, Localidad de Chapinero',
                    'tipo_predio' => 'residencial',
                    'area_predio' => '120 m²'
                ],
                'resultado_ia' => [
                    'analisis' => 'Análisis de derechos de propiedad y servidumbres...',
                    'recomendaciones' => 'Se recomienda actualización catastral...',
                    'normativa_aplicable' => 'Ley 14 de 1983, Decreto 1077 de 2015...'
                ],
                'metadata' => [
                    'nivel' => 'post_doctorado',
                    'especializacion' => 'derecho_inmobiliario_catastral',
                    'jurisdiccion' => 'Colombia'
                ],
                'tokens_usados' => 980,
                'modelo_ia' => 'gpt-4',
                'tiempo_procesamiento' => 1.8,
                'estado' => 'completado',
                'observaciones' => 'Análisis completado exitosamente'
            ],
            [
                'usuario_id' => $usuarioId,
                'area_derecho' => 'desarrollo_territorial',
                'tipo_analisis' => 'municipal',
                'datos_entrada' => [
                    'descripcion' => 'Análisis de desarrollo territorial municipal',
                    'municipio' => 'Medellín',
                    'plan_desarrollo' => 'Plan de Desarrollo 2020-2023',
                    'objetivos' => 'Mejora de infraestructura y servicios públicos'
                ],
                'resultado_ia' => [
                    'analisis' => 'Análisis de competencias territoriales y planificación...',
                    'recomendaciones' => 'Se recomienda articulación interinstitucional...',
                    'normativa_aplicable' => 'Ley 152 de 1994, Ley 388 de 1997...'
                ],
                'metadata' => [
                    'nivel' => 'post_doctorado',
                    'especializacion' => 'derecho_administrativo_territorial',
                    'jurisdiccion' => 'Colombia'
                ],
                'tokens_usados' => 1100,
                'modelo_ia' => 'gpt-4',
                'tiempo_procesamiento' => 2.1,
                'estado' => 'completado',
                'observaciones' => 'Análisis completado exitosamente'
            ],
            [
                'usuario_id' => $usuarioId,
                'area_derecho' => 'derechos_internacionales',
                'tipo_analisis' => 'derechos_humanos',
                'datos_entrada' => [
                    'descripcion' => 'Análisis de derechos humanos internacionales',
                    'caso' => 'Violación de derechos de comunidades indígenas',
                    'pais' => 'Colombia',
                    'organismo' => 'Corte Interamericana de Derechos Humanos'
                ],
                'resultado_ia' => [
                    'analisis' => 'Análisis de derechos humanos internacionales aplicables...',
                    'recomendaciones' => 'Se recomienda implementación de medidas de protección...',
                    'normativa_aplicable' => 'Convención Americana de Derechos Humanos...'
                ],
                'metadata' => [
                    'nivel' => 'post_doctorado',
                    'especializacion' => 'derecho_internacional_publico',
                    'jurisdiccion' => 'Internacional'
                ],
                'tokens_usados' => 1350,
                'modelo_ia' => 'gpt-4',
                'tiempo_procesamiento' => 2.8,
                'estado' => 'completado',
                'observaciones' => 'Análisis completado exitosamente'
            ],
            [
                'usuario_id' => $usuarioId,
                'area_derecho' => 'derechos_comunidades_etnicas',
                'tipo_analisis' => 'indigena',
                'datos_entrada' => [
                    'descripcion' => 'Análisis de derechos de comunidades indígenas',
                    'comunidad' => 'Pueblo Wayuu',
                    'ubicacion' => 'La Guajira',
                    'derecho_afectado' => 'Derecho a la consulta previa'
                ],
                'resultado_ia' => [
                    'analisis' => 'Análisis de derechos étnicos y consulta previa...',
                    'recomendaciones' => 'Se recomienda implementación de consulta previa...',
                    'normativa_aplicable' => 'Convenio 169 OIT, Declaración ONU...'
                ],
                'metadata' => [
                    'nivel' => 'post_doctorado',
                    'especializacion' => 'derecho_etnico_internacional',
                    'jurisdiccion' => 'Colombia'
                ],
                'tokens_usados' => 1420,
                'modelo_ia' => 'gpt-4',
                'tiempo_procesamiento' => 3.2,
                'estado' => 'completado',
                'observaciones' => 'Análisis completado exitosamente'
            ]
        ];

        foreach ($analisisEjemplo as $analisis) {
            AnalisisIADerechosEspecializados::create($analisis);
        }

        $this->command->info('Análisis de IA especializados creados exitosamente.');
    }
}
