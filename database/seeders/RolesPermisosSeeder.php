<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolesPermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles del sistema
        $roles = [
            [
                'nom' => 'Cliente',
                'des' => 'Usuario básico con acceso limitado a páginas públicas y compartidas',
                'est' => 'act',
                'niv' => 1
            ],
            [
                'nom' => 'Operador',
                'des' => 'Usuario con permisos para gestionar procesos y atención al cliente',
                'est' => 'act',
                'niv' => 2
            ],
            [
                'nom' => 'Administrador',
                'des' => 'Usuario con permisos administrativos para gestionar el sistema',
                'est' => 'act',
                'niv' => 3
            ],
            [
                'nom' => 'Administrador General',
                'des' => 'Usuario con control total del sistema y gestión de usuarios',
                'est' => 'act',
                'niv' => 4
            ]
        ];

        foreach ($roles as $rolData) {
            Rol::firstOrCreate(
                ['nom' => $rolData['nom']],
                $rolData
            );
        }

        $this->command->info('Roles básicos creados exitosamente.');
    }
}