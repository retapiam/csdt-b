<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('casos_legales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('tipo_caso', [
                'tutela', 'cumplimiento', 'popular', 'grupo', 'habeas_corpus', 
                'habeas_data', 'penal', 'civil', 'laboral', 'administrativo'
            ]);
            $table->string('numero_radicado', 50)->nullable()->unique();
            $table->string('demandante', 255);
            $table->string('demandado', 255);
            $table->text('hechos');
            $table->text('pretensiones');
            $table->text('fundamentos_legales');
            $table->enum('estado_caso', [
                'draft', 'presentado', 'admitido', 'en_tramite', 
                'fallo_primera_instancia', 'apelacion', 'finalizado'
            ])->default('draft');
            $table->date('fecha_presentacion')->nullable();
            $table->date('fecha_admision')->nullable();
            $table->date('fecha_fallo')->nullable();
            $table->string('juzgado', 255)->nullable();
            $table->string('juez', 255)->nullable();
            $table->text('resultado')->nullable();
            $table->json('archivos_adjuntos')->nullable();
            $table->foreignId('analisis_ia_id')->nullable()->constrained('ai_analisis_juridico')->onDelete('set null');
            $table->timestamps();

            $table->index(['user_id', 'tipo_caso']);
            $table->index(['estado_caso', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casos_legales');
    }
};

