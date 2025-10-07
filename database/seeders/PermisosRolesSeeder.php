<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisosRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permisos por defecto para cada rol
        $permisosPorRol = [
            // CLIENTE (cli) - Nivel 1
            'cli' => [
                ['tipo_permiso' => 'ver_proyectos', 'descripcion' => 'Ver proyectos asignados'],
                ['tipo_permiso' => 'ver_tareas', 'descripcion' => 'Ver tareas del proyecto'],
                ['tipo_permiso' => 'acceso_dashboard_cliente', 'descripcion' => 'Acceso a dashboard de cliente'],
                ['tipo_permiso' => 'ver_reportes', 'descripcion' => 'Ver reportes de sus proyectos'],
            ],
            
            // OPERADOR (ope) - Nivel 2
            'ope' => [
                ['tipo_permiso' => 'ver_proyectos', 'descripcion' => 'Ver todos los proyectos'],
                ['tipo_permiso' => 'crear_proyectos', 'descripcion' => 'Crear nuevos proyectos'],
                ['tipo_permiso' => 'editar_proyectos', 'descripcion' => 'Editar proyectos asignados'],
                ['tipo_permiso' => 'ver_tareas', 'descripcion' => 'Ver todas las tareas'],
                ['tipo_permiso' => 'crear_tareas', 'descripcion' => 'Crear nuevas tareas'],
                ['tipo_permiso' => 'editar_tareas', 'descripcion' => 'Editar tareas asignadas'],
                ['tipo_permiso' => 'acceso_dashboard_operador', 'descripcion' => 'Acceso a dashboard de operador'],
                ['tipo_permiso' => 'acceso_dashboard_cliente', 'descripcion' => 'Acceso a vista de cliente'],
                ['tipo_permiso' => 'ver_reportes', 'descripcion' => 'Ver reportes operativos'],
                ['tipo_permiso' => 'exportar_datos', 'descripcion' => 'Exportar datos de proyectos'],
            ],
            
            // ADMINISTRADOR (adm) - Nivel 3
            'adm' => [
                ['tipo_permiso' => 'ver_proyectos', 'descripcion' => 'Ver todos los proyectos'],
                ['tipo_permiso' => 'crear_proyectos', 'descripcion' => 'Crear proyectos'],
                ['tipo_permiso' => 'editar_proyectos', 'descripcion' => 'Editar cualquier proyecto'],
                ['tipo_permiso' => 'eliminar_proyectos', 'descripcion' => 'Eliminar proyectos'],
                ['tipo_permiso' => 'ver_tareas', 'descripcion' => 'Ver todas las tareas'],
                ['tipo_permiso' => 'crear_tareas', 'descripcion' => 'Crear tareas'],
                ['tipo_permiso' => 'editar_tareas', 'descripcion' => 'Editar cualquier tarea'],
                ['tipo_permiso' => 'eliminar_tareas', 'descripcion' => 'Eliminar tareas'],
                ['tipo_permiso' => 'gestionar_usuarios', 'descripcion' => 'Gestionar usuarios del sistema'],
                ['tipo_permiso' => 'gestionar_permisos', 'descripcion' => 'Asignar permisos a usuarios'],
                ['tipo_permiso' => 'acceso_dashboard_admin', 'descripcion' => 'Acceso a dashboard de administrador'],
                ['tipo_permiso' => 'acceso_dashboard_operador', 'descripcion' => 'Acceso a dashboard de operador'],
                ['tipo_permiso' => 'acceso_dashboard_cliente', 'descripcion' => 'Acceso a dashboard de cliente'],
                ['tipo_permiso' => 'ver_reportes', 'descripcion' => 'Ver todos los reportes'],
                ['tipo_permiso' => 'exportar_datos', 'descripcion' => 'Exportar datos del sistema'],
            ],
            
            // ADMINISTRADOR GENERAL (adm_gen) - Nivel 4
            'adm_gen' => [
                ['tipo_permiso' => 'ver_proyectos', 'descripcion' => 'Ver todos los proyectos'],
                ['tipo_permiso' => 'crear_proyectos', 'descripcion' => 'Crear proyectos'],
                ['tipo_permiso' => 'editar_proyectos', 'descripcion' => 'Editar cualquier proyecto'],
                ['tipo_permiso' => 'eliminar_proyectos', 'descripcion' => 'Eliminar proyectos'],
                ['tipo_permiso' => 'ver_tareas', 'descripcion' => 'Ver todas las tareas'],
                ['tipo_permiso' => 'crear_tareas', 'descripcion' => 'Crear tareas'],
                ['tipo_permiso' => 'editar_tareas', 'descripcion' => 'Editar cualquier tarea'],
                ['tipo_permiso' => 'eliminar_tareas', 'descripcion' => 'Eliminar tareas'],
                ['tipo_permiso' => 'gestionar_usuarios', 'descripcion' => 'Control total de usuarios'],
                ['tipo_permiso' => 'gestionar_roles', 'descripcion' => 'Gestionar roles del sistema'],
                ['tipo_permiso' => 'gestionar_permisos', 'descripcion' => 'Control total de permisos'],
                ['tipo_permiso' => 'configurar_sistema', 'descripcion' => 'Configuración del sistema'],
                ['tipo_permiso' => 'acceso_dashboard_admin', 'descripcion' => 'Acceso a dashboard de administrador'],
                ['tipo_permiso' => 'acceso_dashboard_operador', 'descripcion' => 'Acceso a dashboard de operador'],
                ['tipo_permiso' => 'acceso_dashboard_cliente', 'descripcion' => 'Acceso a dashboard de cliente'],
                ['tipo_permiso' => 'ver_reportes', 'descripcion' => 'Ver todos los reportes'],
                ['tipo_permiso' => 'exportar_datos', 'descripcion' => 'Exportar datos del sistema'],
            ],
        ];

        // Insertar permisos por rol
        foreach ($permisosPorRol as $rol => $permisos) {
            foreach ($permisos as $permiso) {
                DB::table('permisos_roles')->insert([
                    'rol' => $rol,
                    'tipo_permiso' => $permiso['tipo_permiso'],
                    'activo' => true,
                    'descripcion' => $permiso['descripcion'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Permisos por rol creados exitosamente');
    }
}

