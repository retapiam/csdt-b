<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesPermisosSeeder::class, // Crear roles y permisos
            OperadoresSeeder::class,    // Crear operadores
            UsuariosSistemaSeeder::class, // Crear usuarios del sistema
        ]);

        $this->command->info('🎉 Base de datos poblada exitosamente.');
        $this->command->info('🚀 Sistema listo para uso.');
    }
}
