<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Para SQLite, no necesitamos modificar el ENUM ya que SQLite no tiene restricciones ENUM
        // Solo verificamos que la tabla existe
        if (Schema::hasTable('usu')) {
            // La columna rol ya existe y SQLite no valida ENUMs
            // No necesitamos hacer nada específico
        }
    }

    public function down(): void
    {
        // Para SQLite, no hay nada que revertir
        // La columna rol mantiene su estructura original
    }
};