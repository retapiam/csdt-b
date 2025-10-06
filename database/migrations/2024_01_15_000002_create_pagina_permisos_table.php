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
        Schema::create('pagina_permisos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pagina_id');
            $table->unsignedBigInteger('permiso_id');
            $table->timestamps();

            $table->unique(['pagina_id', 'permiso_id']);
            
            $table->foreign('pagina_id')->references('id')->on('paginas')->onDelete('cascade');
            $table->foreign('permiso_id')->references('id')->on('per')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagina_permisos');
    }
};
