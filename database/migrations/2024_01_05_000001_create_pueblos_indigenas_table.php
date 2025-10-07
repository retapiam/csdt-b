<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pueblos_indigenas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('pueblo', 255);
            $table->string('departamento', 100);
            $table->string('municipio', 100);
            $table->string('resguardo', 255)->nullable();
            $table->string('territorio_ancestral', 255)->nullable();
            $table->decimal('extension_hectareas', 15, 2)->nullable();
            $table->integer('poblacion')->nullable();
            $table->string('idioma', 100)->nullable();
            $table->json('autoridades_tradicionales')->nullable();
            $table->string('representante_legal', 255)->nullable();
            $table->string('contacto', 255)->nullable();
            $table->enum('estado', ['activo', 'en_proceso_reconocimiento', 'inactivo'])->default('activo');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['departamento', 'municipio']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pueblos_indigenas');
    }
};

