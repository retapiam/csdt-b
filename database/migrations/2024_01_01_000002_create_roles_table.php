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
        Schema::create('rol', function (Blueprint $table) {
            $table->id('id');
            $table->string('cod', 20)->unique()->comment('Código único del rol');
            $table->string('nom', 100)->comment('Nombre del rol');
            $table->text('des')->nullable()->comment('Descripción del rol');
            $table->enum('est', ['act', 'ina'])->default('act')->comment('Estado del rol');
            $table->json('per')->nullable()->comment('Permisos del rol');
            $table->integer('niv')->default(1)->comment('Nivel de jerarquía');
            $table->boolean('sis')->default(false)->comment('Es rol del sistema');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['est', 'niv']);
            $table->index('sis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol');
    }
};
