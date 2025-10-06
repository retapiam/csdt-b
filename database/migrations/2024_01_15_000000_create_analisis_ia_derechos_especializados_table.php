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
        Schema::create('analisis_ia_derechos_especializados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usu')->onDelete('cascade');
            $table->string('area_derecho', 100)->index();
            $table->string('tipo_analisis', 100)->index();
            $table->json('datos_entrada');
            $table->json('resultado_ia');
            $table->json('metadata')->nullable();
            $table->integer('tokens_usados')->default(0);
            $table->string('modelo_ia', 50)->default('gpt-4');
            $table->decimal('tiempo_procesamiento', 8, 3)->default(0);
            $table->enum('estado', ['pendiente', 'procesando', 'completado', 'fallido'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices adicionales
            $table->index(['area_derecho', 'tipo_analisis'], 'idx_area_tipo');
            $table->index(['usuario_id', 'area_derecho'], 'idx_usuario_area');
            $table->index(['estado', 'created_at'], 'idx_estado_fecha');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisis_ia_derechos_especializados');
    }
};
