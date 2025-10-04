<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NivelUsuario;

class NivelesUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $niveles = [
            // Niveles para categoría legal
            [
                'codigo' => 'LEGAL_1',
                'nombre' => 'Practicante Legal',
                'descripcion' => 'Nivel inicial para profesionales del derecho',
                'numero_nivel' => 1,
                'categoria' => 'legal',
                'permisos_por_defecto' => ['consultar_casos', 'asistir_audiencias'],
                'restricciones' => ['no_puede_firmar_documentos_oficiales'],
                'experiencia_requerida' => 0,
                'requiere_aprobacion' => false,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'LEGAL_2',
                'nombre' => 'Abogado Junior',
                'descripcion' => 'Abogado con experiencia básica',
                'numero_nivel' => 2,
                'categoria' => 'legal',
                'permisos_por_defecto' => ['representar_clientes', 'asesorar_legalmente', 'preparar_documentos'],
                'restricciones' => ['casos_limitados_complejidad'],
                'experiencia_requerida' => 1,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'LEGAL_3',
                'nombre' => 'Abogado Senior',
                'descripcion' => 'Abogado con experiencia intermedia',
                'numero_nivel' => 3,
                'categoria' => 'legal',
                'permisos_por_defecto' => ['casos_complejos', 'supervisar_junior', 'asesorar_empresas'],
                'restricciones' => [],
                'experiencia_requerida' => 3,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'LEGAL_4',
                'nombre' => 'Abogado Especialista',
                'descripcion' => 'Abogado especializado en áreas específicas',
                'numero_nivel' => 4,
                'categoria' => 'legal',
                'permisos_por_defecto' => ['casos_especializados', 'formar_equipos', 'asesorar_gobierno'],
                'restricciones' => [],
                'experiencia_requerida' => 5,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'LEGAL_5',
                'nombre' => 'Magistrado / Experto Legal',
                'descripcion' => 'Nivel máximo de experticia legal',
                'numero_nivel' => 5,
                'categoria' => 'legal',
                'permisos_por_defecto' => ['todos_permisos_legales', 'formar_politicas', 'presidir_tribunales'],
                'restricciones' => [],
                'experiencia_requerida' => 10,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],

            // Niveles para categoría administrativa
            [
                'codigo' => 'ADMIN_1',
                'nombre' => 'Asistente Administrativo',
                'descripcion' => 'Nivel inicial en administración',
                'numero_nivel' => 1,
                'categoria' => 'administrativa',
                'permisos_por_defecto' => ['gestionar_documentos', 'asistir_gerencias'],
                'restricciones' => ['no_puede_aprobar_gastos_mayores'],
                'experiencia_requerida' => 0,
                'requiere_aprobacion' => false,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'ADMIN_2',
                'nombre' => 'Coordinador',
                'descripcion' => 'Coordinador de procesos administrativos',
                'numero_nivel' => 2,
                'categoria' => 'administrativa',
                'permisos_por_defecto' => ['coordinar_equipos', 'aprobar_procesos', 'gestionar_presupuestos'],
                'restricciones' => ['presupuesto_limitado'],
                'experiencia_requerida' => 2,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'ADMIN_3',
                'nombre' => 'Gerente',
                'descripcion' => 'Gerente de área o departamento',
                'numero_nivel' => 3,
                'categoria' => 'administrativa',
                'permisos_por_defecto' => ['gestionar_departamentos', 'aprobar_contrataciones', 'definir_estrategias'],
                'restricciones' => [],
                'experiencia_requerida' => 5,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'ADMIN_4',
                'nombre' => 'Director',
                'descripcion' => 'Director ejecutivo o de área',
                'numero_nivel' => 4,
                'categoria' => 'administrativa',
                'permisos_por_defecto' => ['dirigir_organizaciones', 'tomar_decisiones_ejecutivas', 'representar_entidad'],
                'restricciones' => [],
                'experiencia_requerida' => 8,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'ADMIN_5',
                'nombre' => 'Presidente / CEO',
                'descripcion' => 'Máximo nivel directivo',
                'numero_nivel' => 5,
                'categoria' => 'administrativa',
                'permisos_por_defecto' => ['todos_permisos_administrativos', 'definir_vision', 'tomar_decisiones_finales'],
                'restricciones' => [],
                'experiencia_requerida' => 12,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],

            // Niveles para categoría técnica
            [
                'codigo' => 'TEC_1',
                'nombre' => 'Técnico Auxiliar',
                'descripcion' => 'Nivel inicial técnico',
                'numero_nivel' => 1,
                'categoria' => 'tecnica',
                'permisos_por_defecto' => ['ejecutar_tareas_tecnicas', 'mantener_equipos'],
                'restricciones' => ['supervision_requerida'],
                'experiencia_requerida' => 0,
                'requiere_aprobacion' => false,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'TEC_2',
                'nombre' => 'Técnico Especializado',
                'descripcion' => 'Técnico con especialización',
                'numero_nivel' => 2,
                'categoria' => 'tecnica',
                'permisos_por_defecto' => ['supervisar_trabajos', 'certificar_instalaciones'],
                'restricciones' => ['proyectos_limitados'],
                'experiencia_requerida' => 2,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'TEC_3',
                'nombre' => 'Ingeniero Junior',
                'descripcion' => 'Ingeniero con experiencia básica',
                'numero_nivel' => 3,
                'categoria' => 'tecnica',
                'permisos_por_defecto' => ['diseñar_proyectos', 'supervisar_obras', 'certificar_proyectos'],
                'restricciones' => [],
                'experiencia_requerida' => 3,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],

            // Niveles para categoría social
            [
                'codigo' => 'SOC_1',
                'nombre' => 'Practicante Social',
                'descripcion' => 'Nivel inicial en trabajo social',
                'numero_nivel' => 1,
                'categoria' => 'social',
                'permisos_por_defecto' => ['asistir_intervenciones', 'realizar_diagnosticos'],
                'restricciones' => ['supervision_requerida'],
                'experiencia_requerida' => 0,
                'requiere_aprobacion' => false,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'SOC_2',
                'nombre' => 'Trabajador Social',
                'descripcion' => 'Trabajador social profesional',
                'numero_nivel' => 2,
                'categoria' => 'social',
                'permisos_por_defecto' => ['intervenir_comunidades', 'asesorar_familias', 'coordinar_programas'],
                'restricciones' => [],
                'experiencia_requerida' => 1,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'SOC_3',
                'nombre' => 'Coordinador Social',
                'descripcion' => 'Coordinador de programas sociales',
                'numero_nivel' => 3,
                'categoria' => 'social',
                'permisos_por_defecto' => ['dirigir_programas', 'supervisar_equipos', 'evaluar_proyectos'],
                'restricciones' => [],
                'experiencia_requerida' => 3,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'SOC_4',
                'nombre' => 'Director Social',
                'descripcion' => 'Director de políticas sociales',
                'numero_nivel' => 4,
                'categoria' => 'social',
                'permisos_por_defecto' => ['definir_politicas', 'representar_entidad', 'coordinar_multiples_programas'],
                'restricciones' => [],
                'experiencia_requerida' => 6,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],

            // Niveles para categoría especializada
            [
                'codigo' => 'ESP_2',
                'nombre' => 'Especialista',
                'descripcion' => 'Especialista en área específica',
                'numero_nivel' => 2,
                'categoria' => 'especializada',
                'permisos_por_defecto' => ['asesorar_especializado', 'desarrollar_soluciones'],
                'restricciones' => [],
                'experiencia_requerida' => 3,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'ESP_3',
                'nombre' => 'Consultor Senior',
                'descripcion' => 'Consultor senior especializado',
                'numero_nivel' => 3,
                'categoria' => 'especializada',
                'permisos_por_defecto' => ['consultoria_avanzada', 'supervisar_especialistas'],
                'restricciones' => [],
                'experiencia_requerida' => 5,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'ESP_4',
                'nombre' => 'Experto',
                'descripcion' => 'Experto reconocido en el área',
                'numero_nivel' => 4,
                'categoria' => 'especializada',
                'permisos_por_defecto' => ['definir_estandares', 'formar_politicas_especializadas'],
                'restricciones' => [],
                'experiencia_requerida' => 8,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'ESP_5',
                'nombre' => 'Maestro / Líder de Pensamiento',
                'descripcion' => 'Máximo nivel de especialización',
                'numero_nivel' => 5,
                'categoria' => 'especializada',
                'permisos_por_defecto' => ['todos_permisos_especializados', 'liderar_investigacion', 'definir_futuro_area'],
                'restricciones' => [],
                'experiencia_requerida' => 12,
                'requiere_aprobacion' => true,
                'estado' => 'activo'
            ]
        ];

        foreach ($niveles as $nivelData) {
            NivelUsuario::firstOrCreate(
                ['codigo' => $nivelData['codigo']],
                $nivelData
            );
        }

        $this->command->info('Niveles de usuario creados exitosamente.');
    }
}
