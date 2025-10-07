<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analisis_veeduria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('entidad', 255);
            $table->string('proyecto', 255);
            $table->string('tipo_veeduria', 100);
            $table->text('narracion');
            $table->json('analisis_transparencia')->nullable();
            $table->json('analisis_contratacion')->nullable();
            $table->json('analisis_participacion')->nullable();
            $table->enum('nivel_transparencia', ['bajo', 'medio', 'alto'])->default('medio');
            $table->enum('nivel_riesgo', ['bajo', 'medio', 'alto', 'critico'])->default('medio');
            $table->json('hallazgos')->nullable();
            $table->json('recomendaciones')->nullable();
            $table->json('alertas')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'tipo_veeduria']);
            $table->index(['nivel_riesgo', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analisis_veeduria');
    }
};

