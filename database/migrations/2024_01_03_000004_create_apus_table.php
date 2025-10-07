<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apus', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 255);
            $table->text('descripcion');
            $table->string('tipo_proyecto', 100);
            $table->string('categoria', 100);
            $table->string('unidad_medida', 50);
            $table->decimal('costo_por_hora', 15, 2);
            $table->decimal('costo_materiales', 15, 2)->default(0);
            $table->decimal('costo_equipos', 15, 2)->default(0);
            $table->decimal('costo_total', 15, 2);
            $table->decimal('tiempo_ejecucion', 10, 2)->comment('en horas');
            $table->string('rendimiento', 100);
            $table->date('vigencia_desde');
            $table->date('vigencia_hasta')->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'obsoleto'])->default('activo');
            $table->timestamps();

            $table->index(['tipo_proyecto', 'estado']);
            $table->index(['codigo', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apus');
    }
};

