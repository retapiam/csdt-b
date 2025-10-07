<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunidades_afro', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->enum('tipo', ['consejo_comunitario', 'comunidad_negra', 'palenque', 'raizal']);
            $table->string('departamento', 100);
            $table->string('municipio', 100);
            $table->string('territorio_colectivo', 255)->nullable();
            $table->string('titulo_colectivo', 100)->nullable();
            $table->decimal('extension_hectareas', 15, 2)->nullable();
            $table->integer('poblacion')->nullable();
            $table->string('representante_legal', 255)->nullable();
            $table->string('contacto', 255)->nullable();
            $table->enum('estado', ['activo', 'en_proceso_titulacion', 'inactivo'])->default('activo');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['departamento', 'municipio']);
            $table->index(['tipo', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunidades_afro');
    }
};

