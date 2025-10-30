<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregas_pae', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institucion_id');
            $table->unsignedBigInteger('menu_id')->nullable();
            $table->date('fecha');
            $table->string('jornada')->nullable();
            $table->integer('planificado')->default(0);
            $table->integer('entregado')->default(0);
            $table->string('calidad')->nullable();
            $table->json('evidencias')->nullable();
            $table->timestamps();

            $table->index(['institucion_id','fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas_pae');
    }
};


