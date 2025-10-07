<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('entidad_tipo', 100);
            $table->unsignedBigInteger('entidad_id');
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->string('tipo_documento', 100);
            $table->string('mime_type', 100);
            $table->string('ruta_archivo', 500);
            $table->unsignedBigInteger('tamano_bytes');
            $table->string('hash_archivo', 255);
            $table->integer('version')->default(1);
            $table->enum('estado', ['activo', 'archivado', 'eliminado'])->default('activo');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['entidad_tipo', 'entidad_id']);
            $table->index(['user_id', 'estado']);
            $table->index('hash_archivo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};

