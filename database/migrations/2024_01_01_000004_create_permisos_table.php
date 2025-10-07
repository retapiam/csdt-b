<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('modulo', 100);
            $table->string('permiso', 100);
            $table->boolean('puede_ver')->default(false);
            $table->boolean('puede_crear')->default(false);
            $table->boolean('puede_editar')->default(false);
            $table->boolean('puede_eliminar')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'modulo']);
            $table->unique(['user_id', 'modulo', 'permiso']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};

