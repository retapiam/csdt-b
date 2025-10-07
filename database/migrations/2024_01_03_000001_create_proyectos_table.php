<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->text('descripcion');
            $table->string('tipo_caso', 100);
            $table->enum('estado', ['pendiente', 'en_progreso', 'completado', 'cancelado', 'pausado'])->default('pendiente');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->foreignId('administrador_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('operador_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_limite');
            $table->dateTime('fecha_completado')->nullable();
            $table->decimal('presupuesto_estimado', 15, 2);
            $table->decimal('presupuesto_ejecutado', 15, 2)->default(0);
            $table->integer('progreso')->default(0)->comment('0-100');
            $table->integer('tareas_completadas')->default(0);
            $table->integer('tareas_totales')->default(0);
            $table->foreignId('analisis_ia_id')->nullable()->constrained('ai_analisis_juridico')->onDelete('set null');
            $table->json('configuracion')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['estado', 'administrador_id']);
            $table->index(['tipo_caso', 'estado']);
            $table->index(['cliente_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};

