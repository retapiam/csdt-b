<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profesion;

class ProfesionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profesiones = [
            // Profesiones Legales
            [
                'codigo' => 'ABOGADO',
                'nombre' => 'Abogado',
                'descripcion' => 'Profesional del derecho con título universitario',
                'categoria' => 'legal',
                'nivel_minimo' => 1,
                'nivel_maximo' => 5,
                'habilidades_requeridas' => ['derecho', 'litigio', 'asesoria_legal'],
                'permisos_especiales' => ['ejercer_abogacia', 'representar_clientes'],
                'requiere_matricula' => true,
                'entidad_matricula' => 'Consejo Superior de la Judicatura',
                'estado' => 'activo'
            ],
            [
                'codigo' => 'NOTARIO',
                'nombre' => 'Notario',
                'descripcion' => 'Funcionario público autorizado para dar fe de actos jurídicos',
                'categoria' => 'legal',
                'nivel_minimo' => 3,
                'nivel_maximo' => 5,
                'habilidades_requeridas' => ['derecho_notarial', 'documentacion', 'protocolo'],
                'permisos_especiales' => ['dar_fe_publica', 'protocolizar_actos'],
                'requiere_matricula' => true,
                'entidad_matricula' => 'Ministerio de Justicia',
                'estado' => 'activo'
            ],
            [
                'codigo' => 'CONSULTOR_LEGAL',
                'nombre' => 'Consultor Legal',
                'descripcion' => 'Especialista en asesoría legal especializada',
                'categoria' => 'legal',
                'nivel_minimo' => 2,
                'nivel_maximo' => 5,
                'habilidades_requeridas' => ['asesoria_especializada', 'investigacion_legal'],
                'permisos_especiales' => ['asesorar_empresas', 'consultoria_legal'],
                'requiere_matricula' => false,
                'estado' => 'activo'
            ],

            // Profesiones Administrativas
            [
                'codigo' => 'ADMINISTRADOR',
                'nombre' => 'Administrador de Empresas',
                'descripcion' => 'Profesional en administración y gestión empresarial',
                'categoria' => 'administrativa',
                'nivel_minimo' => 1,
                'nivel_maximo' => 5,
                'habilidades_requeridas' => ['gestion_empresarial', 'planeacion', 'organizacion'],
                'permisos_especiales' => ['gestionar_empresas', 'dirigir_equipos'],
                'requiere_matricula' => false,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'CONTADOR',
                'nombre' => 'Contador Público',
                'descripcion' => 'Profesional en contaduría pública y auditoría',
                'categoria' => 'administrativa',
                'nivel_minimo' => 1,
                'nivel_maximo' => 5,
                'habilidades_requeridas' => ['contabilidad', 'auditoria', 'finanzas'],
                'permisos_especiales' => ['certificar_estados_financieros', 'auditar'],
                'requiere_matricula' => true,
                'entidad_matricula' => 'Junta Central de Contadores',
                'estado' => 'activo'
            ],
            [
                'codigo' => 'GESTOR_PUBLICO',
                'nombre' => 'Gestor Público',
                'descripcion' => 'Especialista en gestión y administración pública',
                'categoria' => 'administrativa',
                'nivel_minimo' => 2,
                'nivel_maximo' => 5,
                'habilidades_requeridas' => ['gestion_publica', 'politicas_publicas', 'presupuesto'],
                'permisos_especiales' => ['gestionar_entidades_publicas'],
                'requiere_matricula' => false,
                'estado' => 'activo'
            ],

            // Profesiones Técnicas
            [
                'codigo' => 'INGENIERO',
                'nombre' => 'Ingeniero',
                'descripcion' => 'Profesional en ingeniería con especialización',
                'categoria' => 'tecnica',
                'nivel_minimo' => 1,
                'nivel_maximo' => 5,
                'habilidades_requeridas' => ['ingenieria', 'proyectos', 'diseno'],
                'permisos_especiales' => ['diseñar_proyectos', 'supervisar_obras'],
                'requiere_matricula' => true,
                'entidad_matricula' => 'Consejo Nacional Profesional de Ingeniería',
                'estado' => 'activo'
            ],
            [
                'codigo' => 'ARQUITECTO',
                'nombre' => 'Arquitecto',
                'descripcion' => 'Profesional en arquitectura y diseño urbano',
                'categoria' => 'tecnica',
                'nivel_minimo' => 1,
                'nivel_maximo' => 5,
                'habilidades_requeridas' => ['arquitectura', 'diseno_urbano', 'planificacion'],
                'permisos_especiales' => ['diseñar_edificios', 'planificar_espacios'],
                'requiere_matricula' => true,
                'entidad_matricula' => 'Consejo Profesional Nacional de Arquitectura',
                'estado' => 'activo'
            ],
            [
                'codigo' => 'TECNICO',
                'nombre' => 'Técnico Profesional',
                'descripcion' => 'Profesional técnico con formación especializada',
                'categoria' => 'tecnica',
                'nivel_minimo' => 1,
                'nivel_maximo' => 3,
                'habilidades_requeridas' => ['tecnica_especializada', 'mantenimiento'],
                'permisos_especiales' => ['ejecutar_trabajos_tecnicos'],
                'requiere_matricula' => false,
                'estado' => 'activo'
            ],

            // Profesiones Sociales
            [
                'codigo' => 'TRABAJADOR_SOCIAL',
                'nombre' => 'Trabajador Social',
                'descripcion' => 'Profesional en trabajo social y desarrollo comunitario',
                'categoria' => 'social',
                'nivel_minimo' => 1,
                'nivel_maximo' => 4,
                'habilidades_requeridas' => ['trabajo_social', 'desarrollo_comunitario', 'intervencion_social'],
                'permisos_especiales' => ['intervenir_comunidades', 'asesorar_familias'],
                'requiere_matricula' => true,
                'entidad_matricula' => 'Colegio Nacional de Trabajadores Sociales',
                'estado' => 'activo'
            ],
            [
                'codigo' => 'PSICOLOGO',
                'nombre' => 'Psicólogo',
                'descripcion' => 'Profesional en psicología y salud mental',
                'categoria' => 'social',
                'nivel_minimo' => 1,
                'nivel_maximo' => 5,
                'habilidades_requeridas' => ['psicologia', 'terapia', 'evaluacion'],
                'permisos_especiales' => ['evaluar_psicologicamente', 'brindar_terapia'],
                'requiere_matricula' => true,
                'entidad_matricula' => 'Colegio Colombiano de Psicólogos',
                'estado' => 'activo'
            ],

            // Profesiones Especializadas
            [
                'codigo' => 'ESPECIALISTA_IA',
                'nombre' => 'Especialista en Inteligencia Artificial',
                'descripcion' => 'Profesional especializado en IA y tecnologías emergentes',
                'categoria' => 'especializada',
                'nivel_minimo' => 2,
                'nivel_maximo' => 5,
                'habilidades_requeridas' => ['inteligencia_artificial', 'machine_learning', 'data_science'],
                'permisos_especiales' => ['desarrollar_sistemas_ia', 'asesorar_tecnologia'],
                'requiere_matricula' => false,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'CONSULTOR_ETNICO',
                'nombre' => 'Consultor Étnico',
                'descripcion' => 'Especialista en asuntos étnicos y comunidades ancestrales',
                'categoria' => 'especializada',
                'nivel_minimo' => 2,
                'nivel_maximo' => 5,
                'habilidades_requeridas' => ['derecho_etnico', 'cultura_ancestral', 'mediacion'],
                'permisos_especiales' => ['asesorar_comunidades_etnicas', 'mediar_conflictos'],
                'requiere_matricula' => false,
                'estado' => 'activo'
            ]
        ];

        foreach ($profesiones as $profesionData) {
            Profesion::firstOrCreate(
                ['codigo' => $profesionData['codigo']],
                $profesionData
            );
        }

        $this->command->info('Profesiones creadas exitosamente.');
    }
}
