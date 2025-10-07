<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('rol', ['administrador', 'operador', 'cliente', 'superadmin'])->default('cliente')->after('password');
            $table->enum('estado', ['activo', 'inactivo', 'suspendido'])->default('activo')->after('rol');
            $table->string('avatar', 255)->nullable()->after('estado');
            $table->string('telefono', 20)->nullable()->after('avatar');
            $table->string('documento', 50)->nullable()->after('telefono');
            $table->enum('tipo_documento', ['CC', 'CE', 'TI', 'NIT', 'Pasaporte'])->nullable()->after('documento');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rol', 'estado', 'avatar', 'telefono', 'documento', 'tipo_documento']);
        });
    }
};

