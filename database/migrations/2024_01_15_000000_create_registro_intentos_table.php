<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registro_intentos', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('documento')->nullable()->index();
            $table->string('tipo_documento')->nullable();
            $table->string('nombre')->nullable();
            $table->string('telefono')->nullable();
            $table->string('rol')->nullable();
            $table->string('tipo_intento')->default('registro'); // registro, duplicado_email, duplicado_documento
            $table->string('estado')->default('fallido'); // fallido, exitoso, duplicado
            $table->text('mensaje')->nullable();
            $table->text('errores')->nullable(); // JSON de errores
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index(['email', 'estado']);
            $table->index(['documento', 'estado']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_intentos');
    }
};

