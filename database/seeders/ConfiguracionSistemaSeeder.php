<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;

class ConfiguracionSistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuraciones = [
            // Configuraciones de autenticación
            [
                'clave' => 'auth_password_min_length',
                'valor' => '8',
                'descripcion' => 'Longitud mínima de contraseña',
                'categoria' => 'autenticacion',
                'tipo' => 'integer'
            ],
            [
                'clave' => 'auth_password_require_special_chars',
                'valor' => 'true',
                'descripcion' => 'Requerir caracteres especiales en contraseña',
                'categoria' => 'autenticacion',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'auth_max_login_attempts',
                'valor' => '5',
                'descripcion' => 'Máximo número de intentos de login',
                'categoria' => 'autenticacion',
                'tipo' => 'integer'
            ],
            [
                'clave' => 'auth_lockout_duration',
                'valor' => '300',
                'descripcion' => 'Duración del bloqueo por intentos fallidos (segundos)',
                'categoria' => 'autenticacion',
                'tipo' => 'integer'
            ],
            
            // Configuraciones de registro
            [
                'clave' => 'register_require_email_verification',
                'valor' => 'true',
                'descripcion' => 'Requerir verificación de email en registro',
                'categoria' => 'registro',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'register_require_profile_verification',
                'valor' => 'true',
                'descripcion' => 'Requerir verificación de perfil profesional',
                'categoria' => 'registro',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'register_allow_public_registration',
                'valor' => 'true',
                'descripcion' => 'Permitir registro público',
                'categoria' => 'registro',
                'tipo' => 'boolean'
            ],
            
            // Configuraciones de profesiones
            [
                'clave' => 'professions_require_matricula',
                'valor' => 'true',
                'descripcion' => 'Requerir matrícula profesional para ciertas profesiones',
                'categoria' => 'profesiones',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'professions_min_experience_verification',
                'valor' => '2',
                'descripcion' => 'Años mínimos de experiencia para verificación automática',
                'categoria' => 'profesiones',
                'tipo' => 'integer'
            ],
            
            // Configuraciones de niveles
            [
                'clave' => 'levels_auto_approval_threshold',
                'valor' => '3',
                'descripcion' => 'Nivel máximo para aprobación automática',
                'categoria' => 'niveles',
                'tipo' => 'integer'
            ],
            [
                'clave' => 'levels_require_admin_approval',
                'valor' => '4',
                'descripcion' => 'Niveles que requieren aprobación de administrador',
                'categoria' => 'niveles',
                'tipo' => 'json'
            ],
            
            // Configuraciones del sistema
            [
                'clave' => 'system_name',
                'valor' => 'Consejo Social de Veeduría y Desarrollo Territorial',
                'descripcion' => 'Nombre oficial del sistema',
                'categoria' => 'sistema',
                'tipo' => 'string'
            ],
            [
                'clave' => 'system_version',
                'valor' => '2.0.0',
                'descripcion' => 'Versión actual del sistema',
                'categoria' => 'sistema',
                'tipo' => 'string'
            ],
            [
                'clave' => 'system_maintenance_mode',
                'valor' => 'false',
                'descripcion' => 'Modo de mantenimiento del sistema',
                'categoria' => 'sistema',
                'tipo' => 'boolean'
            ],
            
            // Configuraciones de notificaciones
            [
                'clave' => 'notifications_email_enabled',
                'valor' => 'true',
                'descripcion' => 'Habilitar notificaciones por email',
                'categoria' => 'notificaciones',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'notifications_sms_enabled',
                'valor' => 'false',
                'descripcion' => 'Habilitar notificaciones por SMS',
                'categoria' => 'notificaciones',
                'tipo' => 'boolean'
            ],
            
            // Configuraciones de seguridad
            [
                'clave' => 'security_session_timeout',
                'valor' => '7200',
                'descripcion' => 'Tiempo de expiración de sesión (segundos)',
                'categoria' => 'seguridad',
                'tipo' => 'integer'
            ],
            [
                'clave' => 'security_require_2fa',
                'valor' => 'false',
                'descripcion' => 'Requerir autenticación de dos factores',
                'categoria' => 'seguridad',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'security_password_expiry_days',
                'valor' => '90',
                'descripcion' => 'Días de expiración de contraseña',
                'categoria' => 'seguridad',
                'tipo' => 'integer'
            ]
        ];

        foreach ($configuraciones as $config) {
            Configuracion::firstOrCreate(
                ['clave' => $config['clave']],
                $config
            );
        }

        $this->command->info('✅ Configuraciones del sistema creadas exitosamente.');
    }
}
