<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas_previas', function (Blueprint $table) {
            $table->id();
            $table->string('proyecto_nombre', 255);
            $table->string('entidad_solicitante', 255);
            $table->string('tipo_proyecto', 100);
            $table->unsignedBigInteger('comunidad_id')->nullable();
            $table->enum('tipo_comunidad', ['indigena', 'afro', 'raizal', 'rom']);
            $table->string('ubicacion', 255);
            $table->text('descripcion_proyecto');
            $table->json('impactos_identificados')->nullable();
            $table->enum('estado', [
                'pre_consulta', 'apertura', 'talleres', 'protocolo', 
                'acuerdos', 'seguimiento', 'finalizada'
            ])->default('pre_consulta');
            $table->date('fecha_inicio');
            $table->date('fecha_finalizacion')->nullable();
            $table->enum('resultado', [
                'aprobado', 'aprobado_con_condiciones', 'no_aprobado', 'en_proceso'
            ])->default('en_proceso');
            $table->json('acuerdos')->nullable();
            $table->json('seguimiento')->nullable();
            $table->string('responsable', 255);
            $table->foreignId('analisis_ia_id')->nullable()->constrained('ai_analisis_etnico')->onDelete('set null');
            $table->timestamps();

            $table->index(['tipo_comunidad', 'estado']);
            $table->index(['fecha_inicio', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas_previas');
    }
};

