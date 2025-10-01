<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pagina;
use App\Models\Rol;

class PaginasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear páginas del sistema
        Pagina::crearPaginasSistema();

        // Obtener roles existentes
        $rolCliente = Rol::where('nom', 'Cliente')->first();
        $rolOperador = Rol::where('nom', 'Operador')->first();
        $rolAdministrador = Rol::where('nom', 'Administrador')->first();
        $rolAdministradorGeneral = Rol::where('nom', 'Administrador General')->first();

        // Asignar páginas a roles según los requerimientos

        // Páginas libres (carpetas 01-10) - Accesibles para todos
        $paginasLibres = Pagina::whereIn('carpeta', [
            Pagina::CARPETA_01, Pagina::CARPETA_02, Pagina::CARPETA_03,
            Pagina::CARPETA_04, Pagina::CARPETA_05, Pagina::CARPETA_06,
            Pagina::CARPETA_07, Pagina::CARPETA_08, Pagina::CARPETA_09,
            Pagina::CARPETA_10
        ])->get();

        // Páginas compartidas (carpeta 11-cliente) - Accesibles para clientes y superiores
        $paginasCompartidas = Pagina::where('carpeta', Pagina::CARPETA_CLIENTE)->get();

        // Páginas de operador (carpeta 12-operador) - Accesibles para operadores y superiores
        $paginasOperador = Pagina::where('carpeta', Pagina::CARPETA_OPERADOR)->get();

        // Páginas de administrador (carpeta 13-administrador) - Accesibles para administradores y superiores
        $paginasAdministrador = Pagina::where('carpeta', Pagina::CARPETA_ADMINISTRADOR)->get();

        // Páginas de administrador general (carpeta 14-administrador-general) - Solo administrador general
        $paginasAdministradorGeneral = Pagina::where('carpeta', Pagina::CARPETA_ADMINISTRADOR_GENERAL)->get();

        // Asignar páginas a roles
        if ($rolCliente) {
            // Cliente: páginas libres + páginas compartidas
            foreach ($paginasLibres as $pagina) {
                $pagina->roles()->syncWithoutDetaching([
                    $rolCliente->id => [
                        'activo' => true,
                        'asignado_por' => 1, // ID del administrador general
                        'asignado_en' => now()
                    ]
                ]);
            }
            
            foreach ($paginasCompartidas as $pagina) {
                $pagina->roles()->syncWithoutDetaching([
                    $rolCliente->id => [
                        'activo' => true,
                        'asignado_por' => 1,
                        'asignado_en' => now()
                    ]
                ]);
            }
        }

        if ($rolOperador) {
            // Operador: páginas libres + páginas compartidas + páginas de operador
            foreach ($paginasLibres as $pagina) {
                $pagina->roles()->syncWithoutDetaching([
                    $rolOperador->id => [
                        'activo' => true,
                        'asignado_por' => 1,
                        'asignado_en' => now()
                    ]
                ]);
            }
            
            foreach ($paginasCompartidas as $pagina) {
                $pagina->roles()->syncWithoutDetaching([
                    $rolOperador->id => [
                        'activo' => true,
                        'asignado_por' => 1,
                        'asignado_en' => now()
                    ]
                ]);
            }
            
            foreach ($paginasOperador as $pagina) {
                $pagina->roles()->syncWithoutDetaching([
                    $rolOperador->id => [
                        'activo' => true,
                        'asignado_por' => 1,
                        'asignado_en' => now()
                    ]
                ]);
            }
        }

        if ($rolAdministrador) {
            // Administrador: todas las páginas excepto las de administrador general
            $todasLasPaginas = $paginasLibres->concat($paginasCompartidas)
                ->concat($paginasOperador)
                ->concat($paginasAdministrador);
            
            foreach ($todasLasPaginas as $pagina) {
                $pagina->roles()->syncWithoutDetaching([
                    $rolAdministrador->id => [
                        'activo' => true,
                        'asignado_por' => 1,
                        'asignado_en' => now()
                    ]
                ]);
            }
        }

        if ($rolAdministradorGeneral) {
            // Administrador General: todas las páginas
            $todasLasPaginas = $paginasLibres->concat($paginasCompartidas)
                ->concat($paginasOperador)
                ->concat($paginasAdministrador)
                ->concat($paginasAdministradorGeneral);
            
            foreach ($todasLasPaginas as $pagina) {
                $pagina->roles()->syncWithoutDetaching([
                    $rolAdministradorGeneral->id => [
                        'activo' => true,
                        'asignado_por' => 1,
                        'asignado_en' => now()
                    ]
                ]);
            }
        }

        $this->command->info('Páginas del sistema creadas y asignadas a roles correctamente.');
    }
}
