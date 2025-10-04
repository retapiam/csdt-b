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
        Schema::create('niveles_usuario', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique()->comment('Código único del nivel');
            $table->string('nombre', 100)->comment('Nombre del nivel');
            $table->text('descripcion')->nullable()->comment('Descripción del nivel');
            $table->integer('numero_nivel')->comment('Número del nivel (1-5)');
            $table->string('categoria', 50)->comment('Categoría del nivel');
            $table->json('permisos_por_defecto')->nullable()->comment('Permisos que otorga por defecto');
            $table->json('restricciones')->nullable()->comment('Restricciones del nivel');
            $table->integer('experiencia_requerida')->default(0)->comment('Años de experiencia requeridos');
            $table->boolean('requiere_aprobacion')->default(false)->comment('Requiere aprobación para obtener');
            $table->enum('estado', ['activo', 'inactivo', 'beta'])->default('activo');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['categoria', 'numero_nivel']);
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('niveles_usuario');
    }
};
