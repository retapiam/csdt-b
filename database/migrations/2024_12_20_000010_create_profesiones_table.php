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
        Schema::create('profesiones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique()->comment('Código único de la profesión');
            $table->string('nombre', 100)->comment('Nombre de la profesión');
            $table->text('descripcion')->nullable()->comment('Descripción de la profesión');
            $table->string('categoria', 50)->comment('Categoría de la profesión');
            $table->integer('nivel_minimo')->default(1)->comment('Nivel mínimo requerido');
            $table->integer('nivel_maximo')->default(5)->comment('Nivel máximo disponible');
            $table->json('habilidades_requeridas')->nullable()->comment('Habilidades requeridas');
            $table->json('permisos_especiales')->nullable()->comment('Permisos especiales de la profesión');
            $table->boolean('requiere_matricula')->default(false)->comment('Requiere matrícula profesional');
            $table->string('entidad_matricula', 100)->nullable()->comment('Entidad que otorga la matrícula');
            $table->enum('estado', ['activo', 'inactivo', 'en_revision'])->default('activo');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['categoria', 'estado']);
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesiones');
    }
};
