<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analisis_etnico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('grupo_etnico', ['indigenas', 'negritudes', 'raizales', 'rom']);
            $table->string('comunidad', 255);
            $table->string('ubicacion', 255);
            $table->text('narracion');
            $table->string('tipo_etnico_detectado', 100)->nullable();
            $table->decimal('confianza_tipo', 5, 2)->nullable();
            $table->json('derechos_afectados')->nullable();
            $table->boolean('requiere_consulta_previa')->default(false);
            $table->enum('nivel_urgencia', ['bajo', 'medio', 'alto', 'critico'])->default('medio');
            $table->json('impacto_territorial')->nullable();
            $table->json('impacto_cultural')->nullable();
            $table->json('impacto_autonomia')->nullable();
            $table->json('recomendaciones')->nullable();
            $table->json('procedimientos_sugeridos')->nullable();
            $table->json('normativas_aplicables')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'grupo_etnico']);
            $table->index(['requiere_consulta_previa', 'nivel_urgencia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analisis_etnico');
    }
};

