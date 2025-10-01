<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Permiso;
use App\Models\Pagina;
use App\Models\Configuracion;

class EstructuraCompletaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // 1. Crear permisos
            $this->crearPermisos();
            
            // 2. Crear roles
            $this->crearRoles();
            
            // 3. Asignar permisos a roles
            $this->asignarPermisosARoles();
            
            // 4. Crear usuarios del sistema
            $this->crearUsuarios();
            
            // 5. Crear páginas del sistema
            $this->crearPaginas();
            
            // 6. Crear configuraciones
            $this->crearConfiguraciones();

            DB::commit();
            $this->command->info('✅ Estructura completa del sistema creada exitosamente');
        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('❌ Error creando estructura: ' . $e->getMessage());
            throw $e;
        }
    }

    private function crearPermisos()
    {
        $permisos = [
            // Usuarios
            ['nom' => 'usuarios.ver', 'des' => 'Ver usuarios', 'mod' => 'usuarios'],
            ['nom' => 'usuarios.crear', 'des' => 'Crear usuarios', 'mod' => 'usuarios'],
            ['nom' => 'usuarios.editar', 'des' => 'Editar usuarios', 'mod' => 'usuarios'],
            ['nom' => 'usuarios.eliminar', 'des' => 'Eliminar usuarios', 'mod' => 'usuarios'],
            ['nom' => 'usuarios.activar', 'des' => 'Activar/desactivar usuarios', 'mod' => 'usuarios'],
            
            // Veedurías
            ['nom' => 'veedurias.ver', 'des' => 'Ver veedurías', 'mod' => 'veedurias'],
            ['nom' => 'veedurias.crear', 'des' => 'Crear veedurías', 'mod' => 'veedurias'],
            ['nom' => 'veedurias.editar', 'des' => 'Editar veedurías', 'mod' => 'veedurias'],
            ['nom' => 'veedurias.eliminar', 'des' => 'Eliminar veedurías', 'mod' => 'veedurias'],
            ['nom' => 'veedurias.radicar', 'des' => 'Radicar veedurías', 'mod' => 'veedurias'],
            ['nom' => 'veedurias.cerrar', 'des' => 'Cerrar veedurías', 'mod' => 'veedurias'],
            
            // Donaciones
            ['nom' => 'donaciones.ver', 'des' => 'Ver donaciones', 'mod' => 'donaciones'],
            ['nom' => 'donaciones.crear', 'des' => 'Crear donaciones', 'mod' => 'donaciones'],
            ['nom' => 'donaciones.editar', 'des' => 'Editar donaciones', 'mod' => 'donaciones'],
            ['nom' => 'donaciones.eliminar', 'des' => 'Eliminar donaciones', 'mod' => 'donaciones'],
            ['nom' => 'donaciones.confirmar', 'des' => 'Confirmar donaciones', 'mod' => 'donaciones'],
            
            // Tareas
            ['nom' => 'tareas.ver', 'des' => 'Ver tareas', 'mod' => 'tareas'],
            ['nom' => 'tareas.crear', 'des' => 'Crear tareas', 'mod' => 'tareas'],
            ['nom' => 'tareas.editar', 'des' => 'Editar tareas', 'mod' => 'tareas'],
            ['nom' => 'tareas.eliminar', 'des' => 'Eliminar tareas', 'mod' => 'tareas'],
            ['nom' => 'tareas.asignar', 'des' => 'Asignar tareas', 'mod' => 'tareas'],
            ['nom' => 'tareas.completar', 'des' => 'Completar tareas', 'mod' => 'tareas'],
            
            // Archivos
            ['nom' => 'archivos.ver', 'des' => 'Ver archivos', 'mod' => 'archivos'],
            ['nom' => 'archivos.subir', 'des' => 'Subir archivos', 'mod' => 'archivos'],
            ['nom' => 'archivos.descargar', 'des' => 'Descargar archivos', 'mod' => 'archivos'],
            ['nom' => 'archivos.eliminar', 'des' => 'Eliminar archivos', 'mod' => 'archivos'],
            
            // Roles
            ['nom' => 'roles.ver', 'des' => 'Ver roles', 'mod' => 'roles'],
            ['nom' => 'roles.crear', 'des' => 'Crear roles', 'mod' => 'roles'],
            ['nom' => 'roles.editar', 'des' => 'Editar roles', 'mod' => 'roles'],
            ['nom' => 'roles.eliminar', 'des' => 'Eliminar roles', 'mod' => 'roles'],
            
            // Configuraciones
            ['nom' => 'configuraciones.ver', 'des' => 'Ver configuraciones', 'mod' => 'configuraciones'],
            ['nom' => 'configuraciones.editar', 'des' => 'Editar configuraciones', 'mod' => 'configuraciones'],
            
            // Logs
            ['nom' => 'logs.ver', 'des' => 'Ver logs', 'mod' => 'logs'],
            ['nom' => 'logs.exportar', 'des' => 'Exportar logs', 'mod' => 'logs'],
            
            // Dashboard
            ['nom' => 'dashboard.ver', 'des' => 'Ver dashboard', 'mod' => 'dashboard'],
            ['nom' => 'dashboard.estadisticas', 'des' => 'Ver estadísticas', 'mod' => 'dashboard'],
            
            // IA
            ['nom' => 'ia.analizar', 'des' => 'Usar análisis de IA', 'mod' => 'ia'],
            ['nom' => 'ia.generar', 'des' => 'Generar contenido con IA', 'mod' => 'ia'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::create([
                'nom' => $permiso['nom'],
                'des' => $permiso['des'],
                'mod' => $permiso['mod'],
                'act' => true
            ]);
        }

        $this->command->info('✅ Permisos creados: ' . count($permisos));
    }

    private function crearRoles()
    {
        $roles = [
            [
                'nom' => 'Cliente',
                'des' => 'Usuario básico del sistema',
                'niv' => 1,
                'act' => true
            ],
            [
                'nom' => 'Operador',
                'des' => 'Operador del sistema',
                'niv' => 2,
                'act' => true
            ],
            [
                'nom' => 'Administrador',
                'des' => 'Administrador del sistema',
                'niv' => 3,
                'act' => true
            ],
            [
                'nom' => 'Administrador General',
                'des' => 'Administrador general del sistema',
                'niv' => 4,
                'act' => true
            ]
        ];

        foreach ($roles as $rol) {
            Rol::create($rol);
        }

        $this->command->info('✅ Roles creados: ' . count($roles));
    }

    private function asignarPermisosARoles()
    {
        $cliente = Rol::where('nom', 'Cliente')->first();
        $operador = Rol::where('nom', 'Operador')->first();
        $admin = Rol::where('nom', 'Administrador')->first();
        $adminGen = Rol::where('nom', 'Administrador General')->first();

        // Permisos para Cliente
        $permisosCliente = [
            'veedurias.ver', 'veedurias.crear', 'veedurias.editar',
            'donaciones.ver', 'donaciones.crear',
            'tareas.ver',
            'archivos.ver', 'archivos.subir', 'archivos.descargar',
            'dashboard.ver'
        ];

        foreach ($permisosCliente as $permiso) {
            $perm = Permiso::where('nom', $permiso)->first();
            if ($perm) {
                $cliente->permisos()->attach($perm->id, ['act' => true, 'asig_por' => 1, 'asig_en' => now()]);
            }
        }

        // Permisos para Operador (hereda de Cliente + operaciones adicionales)
        $permisosOperador = array_merge($permisosCliente, [
            'veedurias.radicar', 'veedurias.cerrar',
            'donaciones.confirmar',
            'tareas.crear', 'tareas.editar', 'tareas.asignar', 'tareas.completar',
            'archivos.eliminar',
            'dashboard.estadisticas'
        ]);

        foreach ($permisosOperador as $permiso) {
            $perm = Permiso::where('nom', $permiso)->first();
            if ($perm) {
                $operador->permisos()->attach($perm->id, ['act' => true, 'asig_por' => 1, 'asig_en' => now()]);
            }
        }

        // Permisos para Administrador (hereda de Operador + administración)
        $permisosAdmin = array_merge($permisosOperador, [
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.activar',
            'veedurias.eliminar',
            'donaciones.eliminar',
            'tareas.eliminar',
            'roles.ver',
            'configuraciones.ver', 'configuraciones.editar',
            'logs.ver'
        ]);

        foreach ($permisosAdmin as $permiso) {
            $perm = Permiso::where('nom', $permiso)->first();
            if ($perm) {
                $admin->permisos()->attach($perm->id, ['act' => true, 'asig_por' => 1, 'asig_en' => now()]);
            }
        }

        // Permisos para Administrador General (todos los permisos)
        $todosPermisos = Permiso::all();
        foreach ($todosPermisos as $permiso) {
            $adminGen->permisos()->attach($permiso->id, ['act' => true, 'asig_por' => 1, 'asig_en' => now()]);
        }

        $this->command->info('✅ Permisos asignados a roles');
    }

    private function crearUsuarios()
    {
        $usuarios = [
            [
                'nom' => 'Administrador',
                'ape' => 'General',
                'cor' => 'admin@csdt.com',
                'con' => Hash::make('admin123'),
                'doc' => '12345678',
                'tip_doc' => 'cc',
                'rol' => 'adm_gen',
                'est' => 'act',
                'cor_ver' => true,
                'cor_ver_en' => now()
            ],
            [
                'nom' => 'Administrador',
                'ape' => 'Sistema',
                'cor' => 'admin.sistema@csdt.com',
                'con' => Hash::make('admin123'),
                'doc' => '87654321',
                'tip_doc' => 'cc',
                'rol' => 'adm',
                'est' => 'act',
                'cor_ver' => true,
                'cor_ver_en' => now()
            ],
            [
                'nom' => 'Operador',
                'ape' => 'Principal',
                'cor' => 'operador@csdt.com',
                'con' => Hash::make('operador123'),
                'doc' => '11223344',
                'tip_doc' => 'cc',
                'rol' => 'ope',
                'est' => 'act',
                'cor_ver' => true,
                'cor_ver_en' => now()
            ]
        ];

        foreach ($usuarios as $usuario) {
            Usuario::create($usuario);
        }

        $this->command->info('✅ Usuarios del sistema creados: ' . count($usuarios));
    }

    private function crearPaginas()
    {
        $paginas = [
            [
                'nom' => 'Dashboard',
                'des' => 'Panel principal del sistema',
                'ruta' => '/dashboard',
                'act' => true,
                'niv' => 1
            ],
            [
                'nom' => 'Usuarios',
                'des' => 'Gestión de usuarios',
                'ruta' => '/usuarios',
                'act' => true,
                'niv' => 3
            ],
            [
                'nom' => 'Veedurías',
                'des' => 'Gestión de veedurías',
                'ruta' => '/veedurias',
                'act' => true,
                'niv' => 2
            ],
            [
                'nom' => 'Donaciones',
                'des' => 'Gestión de donaciones',
                'ruta' => '/donaciones',
                'act' => true,
                'niv' => 1
            ],
            [
                'nom' => 'Tareas',
                'des' => 'Gestión de tareas',
                'ruta' => '/tareas',
                'act' => true,
                'niv' => 2
            ],
            [
                'nom' => 'Archivos',
                'des' => 'Gestión de archivos',
                'ruta' => '/archivos',
                'act' => true,
                'niv' => 2
            ],
            [
                'nom' => 'Roles',
                'des' => 'Gestión de roles',
                'ruta' => '/roles',
                'act' => true,
                'niv' => 3
            ],
            [
                'nom' => 'Configuraciones',
                'des' => 'Configuraciones del sistema',
                'ruta' => '/configuraciones',
                'act' => true,
                'niv' => 3
            ],
            [
                'nom' => 'Logs',
                'des' => 'Logs del sistema',
                'ruta' => '/logs',
                'act' => true,
                'niv' => 3
            ],
            [
                'nom' => 'Estadísticas',
                'des' => 'Estadísticas del sistema',
                'ruta' => '/estadisticas',
                'act' => true,
                'niv' => 2
            ],
            [
                'nom' => 'Consejo IA',
                'des' => 'Sistema de inteligencia artificial',
                'ruta' => '/consejo-ia',
                'act' => true,
                'niv' => 1
            ],
            [
                'nom' => 'PQRSFD',
                'des' => 'Sistema PQRSFD',
                'ruta' => '/pqrsfd',
                'act' => true,
                'niv' => 1
            ]
        ];

        foreach ($paginas as $pagina) {
            Pagina::create($pagina);
        }

        $this->command->info('✅ Páginas del sistema creadas: ' . count($paginas));
    }

    private function crearConfiguraciones()
    {
        $configuraciones = [
            [
                'cla' => 'app_nombre',
                'val' => 'Consejo Social de Veeduría y Desarrollo Territorial',
                'des' => 'Nombre de la aplicación',
                'tip' => 'str',
                'act' => true
            ],
            [
                'cla' => 'app_version',
                'val' => '1.0.0',
                'des' => 'Versión de la aplicación',
                'tip' => 'str',
                'act' => true
            ],
            [
                'cla' => 'app_debug',
                'val' => 'true',
                'des' => 'Modo debug de la aplicación',
                'tip' => 'bool',
                'act' => true
            ],
            [
                'cla' => 'registro_habilitado',
                'val' => 'true',
                'des' => 'Registro de usuarios habilitado',
                'tip' => 'bool',
                'act' => true
            ],
            [
                'cla' => 'registro_roles_habilitados',
                'val' => '["cli"]',
                'des' => 'Roles habilitados para registro',
                'tip' => 'json',
                'act' => true
            ],
            [
                'cla' => 'veedurias_auto_asignar',
                'val' => 'false',
                'des' => 'Asignación automática de veedurías',
                'tip' => 'bool',
                'act' => true
            ],
            [
                'cla' => 'tareas_vencimiento_dias',
                'val' => '7',
                'des' => 'Días de vencimiento por defecto para tareas',
                'tip' => 'int',
                'act' => true
            ],
            [
                'cla' => 'archivos_tamano_maximo',
                'val' => '10485760',
                'des' => 'Tamaño máximo de archivos en bytes (10MB)',
                'tip' => 'int',
                'act' => true
            ],
            [
                'cla' => 'archivos_tipos_permitidos',
                'val' => '["pdf","doc","docx","xls","xlsx","jpg","jpeg","png","gif"]',
                'des' => 'Tipos de archivos permitidos',
                'tip' => 'json',
                'act' => true
            ],
            [
                'cla' => 'notificaciones_email',
                'val' => 'true',
                'des' => 'Notificaciones por email habilitadas',
                'tip' => 'bool',
                'act' => true
            ],
            [
                'cla' => 'ia_habilitada',
                'val' => 'true',
                'des' => 'Sistema de IA habilitado',
                'tip' => 'bool',
                'act' => true
            ],
            [
                'cla' => 'logs_retencion_dias',
                'val' => '90',
                'des' => 'Días de retención de logs',
                'tip' => 'int',
                'act' => true
            ],
            [
                'cla' => 'backup_automatico',
                'val' => 'false',
                'des' => 'Backup automático habilitado',
                'tip' => 'bool',
                'act' => true
            ],
            [
                'cla' => 'sesion_tiempo_expiracion',
                'val' => '120',
                'des' => 'Tiempo de expiración de sesión en minutos',
                'tip' => 'int',
                'act' => true
            ],
            [
                'cla' => 'paginacion_elementos_por_pagina',
                'val' => '15',
                'des' => 'Elementos por página en listados',
                'tip' => 'int',
                'act' => true
            ]
        ];

        foreach ($configuraciones as $config) {
            Configuracion::create($config);
        }

        $this->command->info('✅ Configuraciones del sistema creadas: ' . count($configuraciones));
    }
}