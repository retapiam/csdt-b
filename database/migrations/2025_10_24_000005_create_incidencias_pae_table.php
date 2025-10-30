<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidencias_pae', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institucion_id');
            $table->date('fecha');
            $table->string('tipo');
            $table->string('severidad')->default('media');
            $table->text('descripcion')->nullable();
            $table->json('evidencias')->nullable();
            $table->timestamps();

            $table->index(['institucion_id','fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencias_pae');
    }
};


