<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cae_comites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institucion_id');
            $table->string('nombre');
            $table->json('miembros')->nullable();
            $table->timestamps();
            $table->index('institucion_id');
        });

        Schema::create('cae_actas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comite_id');
            $table->date('fecha');
            $table->text('acuerdos')->nullable();
            $table->json('evidencias')->nullable();
            $table->timestamps();
            $table->index(['comite_id','fecha']);
        });

        Schema::create('cae_seguimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comite_id');
            $table->date('fecha');
            $table->text('hallazgos')->nullable();
            $table->json('checklist')->nullable();
            $table->json('evidencias')->nullable();
            $table->timestamps();
            $table->index(['comite_id','fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cae_seguimientos');
        Schema::dropIfExists('cae_actas');
        Schema::dropIfExists('cae_comites');
    }
};


