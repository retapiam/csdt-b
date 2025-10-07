<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear SuperAdmin
        User::create([
            'name' => 'Super Administrador',
            'email' => 'superadmin@csdt.com',
            'password' => Hash::make('super123'),
            'rol' => 'superadmin',
            'estado' => 'activo',
            'documento' => '1000000001',
            'tipo_documento' => 'CC',
            'telefono' => '3001234567',
        ]);

        // Crear Administrador
        User::create([
            'name' => 'Juan Carlos Pérez',
            'email' => 'admin@csdt.com',
            'password' => Hash::make('admin123'),
            'rol' => 'administrador',
            'estado' => 'activo',
            'documento' => '1000000002',
            'tipo_documento' => 'CC',
            'telefono' => '3002345678',
        ]);

        // Crear Operador
        User::create([
            'name' => 'María González',
            'email' => 'operador@csdt.com',
            'password' => Hash::make('operador123'),
            'rol' => 'operador',
            'estado' => 'activo',
            'documento' => '1000000003',
            'tipo_documento' => 'CC',
            'telefono' => '3003456789',
        ]);

        // Crear otro Operador
        User::create([
            'name' => 'Pedro Martínez',
            'email' => 'operador2@csdt.com',
            'password' => Hash::make('operador123'),
            'rol' => 'operador',
            'estado' => 'activo',
            'documento' => '1000000004',
            'tipo_documento' => 'CC',
            'telefono' => '3004567890',
        ]);

        // Crear Cliente
        User::create([
            'name' => 'Ana Rodríguez',
            'email' => 'cliente@csdt.com',
            'password' => Hash::make('cliente123'),
            'rol' => 'cliente',
            'estado' => 'activo',
            'documento' => '1000000005',
            'tipo_documento' => 'CC',
            'telefono' => '3005678901',
        ]);

        // Crear otro Cliente
        User::create([
            'name' => 'Carlos Sánchez',
            'email' => 'cliente2@csdt.com',
            'password' => Hash::make('password123'),
            'rol' => 'cliente',
            'estado' => 'activo',
            'documento' => '1000000006',
            'tipo_documento' => 'CC',
            'telefono' => '3006789012',
        ]);

        // Crear Cliente (Comunidad Indígena)
        User::create([
            'name' => 'Comunidad Wayuu',
            'email' => 'wayuu@csdt.com',
            'password' => Hash::make('password123'),
            'rol' => 'cliente',
            'estado' => 'activo',
            'documento' => '9001234567',
            'tipo_documento' => 'NIT',
            'telefono' => '3007890123',
        ]);

        // Crear Cliente (Comunidad Afro)
        User::create([
            'name' => 'Consejo Comunitario del Pacífico',
            'email' => 'pacifico@csdt.com',
            'password' => Hash::make('password123'),
            'rol' => 'cliente',
            'estado' => 'activo',
            'documento' => '9002345678',
            'tipo_documento' => 'NIT',
            'telefono' => '3008901234',
        ]);

        $this->command->info('✅ Usuarios de prueba creados exitosamente');
        $this->command->info('');
        $this->command->info('📋 Credenciales de acceso:');
        $this->command->info('');
        $this->command->info('SuperAdmin - Control absoluto, auditoría:');
        $this->command->info('  Email: superadmin@csdt.com | Password: super123');
        $this->command->info('');
        $this->command->info('Administrador - Control total, gestión usuarios:');
        $this->command->info('  Email: admin@csdt.com | Password: admin123');
        $this->command->info('');
        $this->command->info('Operadores - Gestionar tareas, asignar proyectos:');
        $this->command->info('  Email: operador@csdt.com | Password: operador123');
        $this->command->info('  Email: operador2@csdt.com | Password: operador123');
        $this->command->info('');
        $this->command->info('Clientes - Ver proyectos, crear solicitudes:');
        $this->command->info('  Email: cliente@csdt.com | Password: cliente123');
        $this->command->info('  Email: cliente2@csdt.com | Password: password123');
        $this->command->info('  Email: wayuu@csdt.com | Password: password123');
        $this->command->info('  Email: pacifico@csdt.com | Password: password123');
    }
}
