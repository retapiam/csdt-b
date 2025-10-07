<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metricas_sistema', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->unique();
            $table->integer('usuarios_activos')->default(0);
            $table->integer('proyectos_creados')->default(0);
            $table->integer('proyectos_completados')->default(0);
            $table->integer('tareas_creadas')->default(0);
            $table->integer('tareas_completadas')->default(0);
            $table->integer('consultas_ia')->default(0);
            $table->integer('analisis_generados')->default(0);
            $table->integer('pdfs_generados')->default(0);
            $table->integer('donaciones_recibidas')->default(0);
            $table->decimal('monto_donaciones', 15, 2)->default(0);
            $table->timestamps();

            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metricas_sistema');
    }
};

