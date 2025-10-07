<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_consultas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tipo_consulta', 100);
            $table->text('texto_consulta');
            $table->enum('proveedor_ia', [
                'openai', 'anthropic', 'huggingface', 'lexisnexis', 
                'territorial_ai', 'constitutional_ai', 'veeduria_ai', 
                'legal_ai_library'
            ]);
            $table->string('modelo_utilizado', 100);
            $table->longText('respuesta');
            $table->decimal('confianza', 5, 2)->nullable();
            $table->integer('tiempo_procesamiento')->comment('en milisegundos');
            $table->integer('tokens_utilizados')->nullable();
            $table->decimal('costo_tokens', 10, 6)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'tipo_consulta']);
            $table->index(['proveedor_ia', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_consultas');
    }
};

