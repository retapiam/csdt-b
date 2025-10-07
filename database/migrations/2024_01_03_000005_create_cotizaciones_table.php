<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->string('codigo', 50)->unique();
            $table->dateTime('fecha_generacion');
            $table->integer('validez_dias')->default(30);
            $table->json('desglose');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('iva', 15, 2);
            $table->decimal('total', 15, 2);
            $table->json('condiciones')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'vencida'])->default('pendiente');
            $table->foreignId('aprobada_por')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('fecha_aprobacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['proyecto_id', 'estado']);
            $table->index('codigo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};

