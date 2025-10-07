<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuarios de prueba con diferentes roles
        $this->call([
            UsuariosPruebaSeeder::class,
        ]);

        // Seeders de datos étnicos
        $this->call([
            PueblosIndigenasSeeder::class,
            ComunidadesAfroSeeder::class,
        ]);
    }
}
