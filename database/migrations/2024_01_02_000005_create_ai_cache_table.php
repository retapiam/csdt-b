<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_cache', function (Blueprint $table) {
            $table->id();
            $table->string('hash_consulta', 255)->unique();
            $table->string('tipo_consulta', 100);
            $table->text('consulta');
            $table->longText('respuesta');
            $table->json('metadata')->nullable();
            $table->integer('hits')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['tipo_consulta', 'expires_at']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_cache');
    }
};

