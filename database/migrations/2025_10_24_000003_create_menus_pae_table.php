<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus_pae', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institucion_id');
            $table->string('nombre');
            $table->json('componentes')->nullable();
            $table->integer('calorias')->nullable();
            $table->json('restricciones_culturales')->nullable();
            $table->timestamps();

            $table->index('institucion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus_pae');
    }
};


