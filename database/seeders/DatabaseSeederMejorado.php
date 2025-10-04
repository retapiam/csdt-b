<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeederMejorado extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Iniciando seeders mejorados del sistema CSDT...');
        
        // Ejecutar seeders en orden de dependencia
        $this->call([
            // 1. Roles y permisos básicos
            RolesPermisosSeeder::class,
            
            // 2. Profesiones y niveles
            ProfesionesSeeder::class,
            NivelesUsuarioSeeder::class,
            
            // 3. Usuario administrador por defecto
            UsuarioAdminSeeder::class,
            
            // 4. Configuraciones del sistema
            ConfiguracionSistemaSeeder::class,
        ]);
        
        $this->command->info('✅ Seeders mejorados ejecutados exitosamente.');
        $this->command->info('');
        $this->command->info('📋 Resumen de lo creado:');
        $this->command->info('   • Roles del sistema (Cliente, Operador, Administrador, Administrador General)');
        $this->command->info('   • Profesiones organizadas por categorías (Legal, Administrativa, Técnica, Social, Especializada)');
        $this->command->info('   • Niveles de usuario con permisos y restricciones');
        $this->command->info('   • Usuario administrador por defecto');
        $this->command->info('   • Configuraciones básicas del sistema');
        $this->command->info('');
        $this->command->info('🚀 El sistema está listo para usar con las mejoras implementadas.');
    }
}
