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
        Schema::create('pagina_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pagina_id')->constrained('paginas')->onDelete('cascade');
            $table->foreignId('rol_id')->constrained('rol')->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->foreignId('asignado_por')->nullable()->constrained('usu')->onDelete('set null');
            $table->timestamp('asignado_en')->nullable();
            $table->timestamps();

            $table->unique(['pagina_id', 'rol_id']);
            $table->index(['activo', 'asignado_por']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagina_roles');
    }
};
