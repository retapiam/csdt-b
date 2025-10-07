<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metricas_ia', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('proveedor', 100);
            $table->integer('total_consultas')->default(0);
            $table->integer('consultas_exitosas')->default(0);
            $table->integer('consultas_fallidas')->default(0);
            $table->integer('tiempo_promedio_ms')->default(0);
            $table->integer('tokens_totales')->default(0);
            $table->decimal('costo_total', 10, 4)->default(0);
            $table->decimal('tasa_exito', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['fecha', 'proveedor']);
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metricas_ia');
    }
};

