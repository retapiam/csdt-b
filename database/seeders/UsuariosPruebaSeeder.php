<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SuperAdmin - Acceso total al sistema
        User::create([
            'name' => 'Super Administrador',
            'email' => 'superadmin@csdt.test',
            'password' => Hash::make('password123'),
            'rol' => 'superadmin',
            'estado' => 'activo',
            'telefono' => '3001234567',
            'documento' => '1234567890',
            'tipo_documento' => 'CC',
            'email_verified_at' => now(),
        ]);

        // Administrador - Gestión completa
        User::create([
            'name' => 'Administrador Test',
            'email' => 'admin@csdt.test',
            'password' => Hash::make('password123'),
            'rol' => 'administrador',
            'estado' => 'activo',
            'telefono' => '3001234568',
            'documento' => '1234567891',
            'tipo_documento' => 'CC',
            'email_verified_at' => now(),
        ]);

        // Operador - Usuario operativo del sistema
        User::create([
            'name' => 'Operador Test',
            'email' => 'operador@csdt.test',
            'password' => Hash::make('password123'),
            'rol' => 'operador',
            'estado' => 'activo',
            'telefono' => '3001234569',
            'documento' => '1234567892',
            'tipo_documento' => 'CC',
            'email_verified_at' => now(),
        ]);

        // Cliente - Usuario básico
        User::create([
            'name' => 'Cliente Test',
            'email' => 'cliente@csdt.test',
            'password' => Hash::make('password123'),
            'rol' => 'cliente',
            'estado' => 'activo',
            'telefono' => '3001234570',
            'documento' => '1234567893',
            'tipo_documento' => 'CC',
            'email_verified_at' => now(),
        ]);

        // Usuario adicional para pruebas
        User::create([
            'name' => 'María González',
            'email' => 'maria@csdt.test',
            'password' => Hash::make('password123'),
            'rol' => 'operador',
            'estado' => 'activo',
            'telefono' => '3007654321',
            'documento' => '9876543210',
            'tipo_documento' => 'CC',
            'email_verified_at' => now(),
        ]);

        // Usuario inactivo para pruebas de estado
        User::create([
            'name' => 'Usuario Inactivo',
            'email' => 'inactivo@csdt.test',
            'password' => Hash::make('password123'),
            'rol' => 'cliente',
            'estado' => 'inactivo',
            'telefono' => '3009999999',
            'documento' => '1111111111',
            'tipo_documento' => 'CC',
            'email_verified_at' => now(),
        ]);
    }
}

