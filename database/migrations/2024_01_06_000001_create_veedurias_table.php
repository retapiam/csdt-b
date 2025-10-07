<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('veedurias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->enum('tipo', [
                'gestion_publica', 'contratacion', 'derechos_ambientales', 
                'rendicion_cuentas', 'participacion_ciudadana'
            ]);
            $table->string('entidad_vigilada', 255);
            $table->string('proyecto_vigilado', 255)->nullable();
            $table->text('descripcion');
            $table->json('objetivos')->nullable();
            $table->json('integrantes')->nullable();
            $table->string('representante_legal', 255);
            $table->string('contacto', 255);
            $table->string('departamento', 100);
            $table->string('municipio', 100);
            $table->date('fecha_constitucion');
            $table->enum('estado', ['activa', 'inactiva', 'suspendida'])->default('activa');
            $table->json('hallazgos')->nullable();
            $table->json('recomendaciones')->nullable();
            $table->foreignId('analisis_ia_id')->nullable()->constrained('ai_analisis_veeduria')->onDelete('set null');
            $table->timestamps();

            $table->index(['tipo', 'estado']);
            $table->index(['entidad_vigilada', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veedurias');
    }
};

