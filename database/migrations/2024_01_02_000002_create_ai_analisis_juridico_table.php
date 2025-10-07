<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analisis_juridico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->nullable()->constrained('ai_consultas')->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tipo_caso', 100);
            $table->string('categoria_juridica', 100);
            $table->text('texto_analizado');
            $table->json('clasificaciones')->nullable();
            $table->text('resumen')->nullable();
            $table->json('fundamentos_legales')->nullable();
            $table->json('jurisprudencia')->nullable();
            $table->json('normativa_aplicable')->nullable();
            $table->json('recomendaciones')->nullable();
            $table->json('evaluacion_riesgos')->nullable();
            $table->decimal('confianza_promedio', 5, 2)->nullable();
            $table->json('proveedores_utilizados')->nullable();
            $table->enum('nivel_analisis', ['basico', 'intermedio', 'avanzado', 'completo'])->default('basico');
            $table->timestamps();

            $table->index(['user_id', 'tipo_caso']);
            $table->index(['nivel_analisis', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analisis_juridico');
    }
};

