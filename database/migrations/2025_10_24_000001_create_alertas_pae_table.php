<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas_pae', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_id')->nullable();
            $table->unsignedBigInteger('actividad_id')->nullable();
            $table->unsignedBigInteger('asignado_a')->nullable();
            $table->string('tipo', 100);
            $table->string('severidad', 20)->default('media');
            $table->string('estado', 20)->default('abierta');
            $table->string('mensaje', 255);
            $table->json('data')->nullable();
            $table->timestamp('sla_at')->nullable();
            $table->timestamps();

            $table->index(['proyecto_id', 'actividad_id']);
            $table->index(['estado', 'severidad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas_pae');
    }
};


