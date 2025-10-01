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
            $table->foreignId('pagina_id')->constrained('paginas')->onDelete('cascade');
            $table->foreignId('permiso_id')->constrained('perm')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['pagina_id', 'permiso_id']);
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
