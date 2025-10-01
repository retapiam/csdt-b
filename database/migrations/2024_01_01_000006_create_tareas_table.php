<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tar', function (Blueprint $table) {
            $table->id('id');
            $table->string('cod', 20)->unique()->comment('Código único de la tarea');
            $table->foreignId('vee_id')->constrained('vee')->comment('Veeduría asociada');
            $table->foreignId('usu_id')->constrained('usu')->comment('Usuario asignado');
            $table->string('tit', 200)->comment('Título de la tarea');
            $table->text('des')->comment('Descripción de la tarea');
            $table->enum('est', ['pen', 'pro', 'com', 'can', 'sus'])->default('pen')->comment('Estado de la tarea');
            $table->enum('pri', ['baj', 'med', 'alt', 'urg'])->default('med')->comment('Prioridad');
            $table->date('fec_ini')->nullable()->comment('Fecha de inicio');
            $table->date('fec_fin')->nullable()->comment('Fecha de finalización');
            $table->date('fec_ven')->nullable()->comment('Fecha de vencimiento');
            $table->integer('pro_por')->default(0)->comment('Porcentaje de progreso');
            $table->text('obs')->nullable()->comment('Observaciones');
            $table->json('met')->nullable()->comment('Metadatos adicionales');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['vee_id', 'est']);
            $table->index(['usu_id', 'est']);
            $table->index(['pri', 'est']);
            $table->index('fec_ven');
            $table->index('pro_por');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tar');
    }
};
