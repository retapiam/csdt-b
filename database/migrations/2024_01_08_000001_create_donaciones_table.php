<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donaciones', function (Blueprint $table) {
            $table->id();
            $table->string('donante_nombre', 255);
            $table->string('donante_email', 255);
            $table->string('donante_telefono', 20)->nullable();
            $table->string('donante_documento', 50)->nullable();
            $table->decimal('monto', 15, 2);
            $table->string('moneda', 10)->default('COP');
            $table->enum('metodo_pago', ['nequi', 'tarjeta', 'transferencia', 'efectivo', 'pse']);
            $table->enum('estado', ['pendiente', 'completada', 'fallida', 'reembolsada'])->default('pendiente');
            $table->string('referencia_pago', 255)->nullable();
            $table->string('comprobante', 500)->nullable();
            $table->text('mensaje')->nullable();
            $table->boolean('es_recurrente')->default(false);
            $table->enum('frecuencia_recurrente', ['mensual', 'trimestral', 'anual'])->nullable();
            $table->string('destino', 255)->nullable();
            $table->timestamps();

            $table->index(['estado', 'created_at']);
            $table->index('donante_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donaciones');
    }
};

