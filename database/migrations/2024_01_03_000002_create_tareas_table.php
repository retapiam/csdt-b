<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->string('nombre', 255);
            $table->text('descripcion');
            $table->string('tipo', 100);
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada', 'bloqueada', 'cancelada'])->default('pendiente');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->foreignId('asignado_a')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('creado_por')->constrained('users')->onDelete('cascade');
            $table->decimal('tiempo_estimado', 10, 2)->comment('en horas');
            $table->decimal('tiempo_invertido', 10, 2)->default(0);
            $table->decimal('costo_estimado', 15, 2);
            $table->decimal('costo_real', 15, 2)->default(0);
            $table->dateTime('fecha_asignacion')->nullable();
            $table->date('fecha_limite');
            $table->dateTime('fecha_completada')->nullable();
            $table->integer('progreso')->default(0)->comment('0-100');
            $table->text('motivo_bloqueo')->nullable();
            $table->json('documentos_requeridos')->nullable();
            $table->json('documentos_entregados')->nullable();
            $table->timestamps();

            $table->index(['proyecto_id', 'estado']);
            $table->index(['asignado_a', 'estado']);
            $table->index(['estado', 'fecha_limite']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};

