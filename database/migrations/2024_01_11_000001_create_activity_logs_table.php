<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('accion', 100);
            $table->string('entidad_tipo', 100);
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->text('descripcion');
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->json('datos_antes')->nullable();
            $table->json('datos_despues')->nullable();
            $table->timestamp('created_at');

            $table->index(['user_id', 'created_at']);
            $table->index(['entidad_tipo', 'entidad_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

