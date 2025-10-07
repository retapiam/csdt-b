<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes_etnodesarrollo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comunidad_id')->nullable();
            $table->enum('tipo_comunidad', ['indigena', 'afro', 'raizal', 'rom']);
            $table->string('nombre_plan', 255);
            $table->text('descripcion');
            $table->text('vision');
            $table->json('objetivos')->nullable();
            $table->json('programas')->nullable();
            $table->json('proyectos')->nullable();
            $table->decimal('presupuesto', 15, 2);
            $table->json('fuentes_financiacion')->nullable();
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            $table->enum('estado', [
                'formulacion', 'aprobado', 'ejecucion', 'evaluacion', 'finalizado'
            ])->default('formulacion');
            $table->integer('avance_porcentaje')->default(0);
            $table->timestamps();

            $table->index(['tipo_comunidad', 'estado']);
            $table->index(['periodo_inicio', 'periodo_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes_etnodesarrollo');
    }
};

