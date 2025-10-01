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
        Schema::create('log', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('usu_id')->nullable()->constrained('usu')->comment('Usuario que realizó la acción');
            $table->string('acc', 50)->comment('Acción realizada');
            $table->string('tab', 50)->comment('Tabla afectada');
            $table->unsignedBigInteger('reg_id')->nullable()->comment('ID del registro afectado');
            $table->text('des')->nullable()->comment('Descripción de la acción');
            $table->json('dat_ant')->nullable()->comment('Datos anteriores');
            $table->json('dat_nue')->nullable()->comment('Datos nuevos');
            $table->string('ip', 45)->nullable()->comment('Dirección IP');
            $table->text('user_agent')->nullable()->comment('User Agent del navegador');
            $table->enum('niv', ['info', 'warn', 'error', 'debug'])->default('info')->comment('Nivel del log');
            $table->json('met')->nullable()->comment('Metadatos adicionales');
            $table->timestamps();
            
            // Índices
            $table->index(['usu_id', 'created_at']);
            $table->index(['acc', 'tab']);
            $table->index(['tab', 'reg_id']);
            $table->index(['niv', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log');
    }
};
