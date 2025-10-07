<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListarUsuarios extends Command
{
    protected $signature = 'users:list';
    protected $description = 'Listar todos los usuarios del sistema';

    public function handle()
    {
        $usuarios = User::all(['id', 'name', 'email', 'rol', 'estado']);

        if ($usuarios->isEmpty()) {
            $this->info('No hay usuarios en el sistema.');
            return 0;
        }

        $this->info('=== USUARIOS DEL SISTEMA ===');
        $this->newLine();

        foreach ($usuarios as $usuario) {
            $this->line("ID: {$usuario->id}");
            $this->line("Nombre: {$usuario->name}");
            $this->line("Email: {$usuario->email}");
            $this->line("Rol: {$usuario->rol}");
            $this->line("Estado: {$usuario->estado}");
            $this->line(str_repeat('-', 50));
        }

        $this->newLine();
        $this->info("Total de usuarios: " . $usuarios->count());

        return 0;
    }
}

