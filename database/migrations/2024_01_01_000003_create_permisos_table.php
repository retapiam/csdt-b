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
        Schema::create('per', function (Blueprint $table) {
            $table->id('id');
            $table->string('cod', 50)->unique()->comment('Código único del permiso');
            $table->string('nom', 100)->comment('Nombre del permiso');
            $table->text('des')->nullable()->comment('Descripción del permiso');
            $table->string('mod', 50)->comment('Módulo al que pertenece');
            $table->string('rec', 50)->comment('Recurso al que se aplica');
            $table->string('acc', 50)->comment('Acción permitida');
            $table->enum('est', ['act', 'ina'])->default('act')->comment('Estado del permiso');
            $table->boolean('sis')->default(false)->comment('Es permiso del sistema');
            $table->json('met')->nullable()->comment('Metadatos adicionales');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['mod', 'rec', 'acc']);
            $table->index(['est', 'sis']);
            $table->index('cod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('per');
    }
};
