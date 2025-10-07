<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdfs_generados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tipo_pdf', 100);
            $table->string('plantilla', 100);
            $table->json('datos_entrada');
            $table->string('ruta_archivo', 500);
            $table->unsignedBigInteger('tamano_bytes');
            $table->integer('paginas');
            $table->enum('calidad', ['baja', 'media', 'alta'])->default('media');
            $table->unsignedBigInteger('analisis_ia_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'tipo_pdf']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdfs_generados');
    }
};

