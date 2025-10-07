<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tipo', 100);
            $table->string('titulo', 255);
            $table->text('mensaje');
            $table->string('url', 500)->nullable();
            $table->string('icono', 100)->nullable();
            $table->boolean('leida')->default(false);
            $table->boolean('importante')->default(false);
            $table->dateTime('fecha_leida')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'leida']);
            $table->index(['created_at', 'leida']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};

