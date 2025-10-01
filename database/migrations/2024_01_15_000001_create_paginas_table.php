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
        Schema::create('paginas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('ruta', 255)->unique();
            $table->string('carpeta', 50);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['libre', 'compartida', 'privada', 'administrativa']);
            $table->enum('estado', ['activa', 'inactiva', 'bloqueada'])->default('activa');
            $table->integer('orden')->default(0);
            $table->string('icono', 50)->nullable();
            $table->boolean('es_publica')->default(false);
            $table->boolean('requiere_autenticacion')->default(false);
            $table->json('permisos_requeridos')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['carpeta', 'estado']);
            $table->index(['tipo', 'estado']);
            $table->index(['es_publica', 'requiere_autenticacion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paginas');
    }
};
