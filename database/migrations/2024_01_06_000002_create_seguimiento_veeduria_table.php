<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguimiento_veeduria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veeduria_id')->constrained('veedurias')->onDelete('cascade');
            $table->date('fecha_seguimiento');
            $table->string('tipo_actividad', 100);
            $table->text('descripcion');
            $table->json('hallazgos')->nullable();
            $table->json('evidencias')->nullable();
            $table->enum('nivel_riesgo', ['bajo', 'medio', 'alto', 'critico'])->default('medio');
            $table->json('acciones_recomendadas')->nullable();
            $table->string('responsable', 255);
            $table->enum('estado', ['pendiente', 'en_revision', 'completado'])->default('pendiente');
            $table->timestamps();

            $table->index(['veeduria_id', 'fecha_seguimiento']);
            $table->index(['nivel_riesgo', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimiento_veeduria');
    }
};

