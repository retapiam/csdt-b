<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dependencias_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarea_origen_id')->constrained('tareas')->onDelete('cascade');
            $table->foreignId('tarea_destino_id')->constrained('tareas')->onDelete('cascade');
            $table->enum('tipo_dependencia', ['secuencial', 'paralela', 'condicional'])->default('secuencial');
            $table->text('condicion')->nullable();
            $table->timestamps();

            $table->index(['tarea_origen_id', 'tarea_destino_id']);
            $table->unique(['tarea_origen_id', 'tarea_destino_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dependencias_tareas');
    }
};

