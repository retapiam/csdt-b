<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vee', function (Blueprint $table) {
            $table->id('id');
            $table->string('cod', 20)->unique()->comment('Código único de la veeduría');
            $table->foreignId('usu_id')->constrained('usu')->comment('Usuario que crea la veeduría');
            $table->foreignId('ope_id')->nullable()->constrained('usu')->comment('Operador asignado');
            $table->string('tit', 200)->comment('Título de la veeduría');
            $table->text('des')->comment('Descripción detallada');
            $table->enum('tip', ['pet', 'que', 'rec', 'sug', 'fel', 'den'])->comment('Tipo de veeduría');
            $table->enum('est', ['pen', 'pro', 'rad', 'cer', 'can'])->default('pen')->comment('Estado de la veeduría');
            $table->enum('pri', ['baj', 'med', 'alt', 'urg'])->default('med')->comment('Prioridad');
            $table->enum('cat', ['inf', 'ser', 'seg', 'edu', 'sal', 'tra', 'amb', 'otr'])->comment('Categoría');
            $table->string('ciu', 100)->comment('Ciudad');
            $table->string('dep', 100)->comment('Departamento');
            $table->text('dir')->nullable()->comment('Dirección específica');
            $table->decimal('lat', 10, 8)->nullable()->comment('Latitud');
            $table->decimal('lng', 11, 8)->nullable()->comment('Longitud');
            $table->decimal('pre', 15, 2)->nullable()->comment('Presupuesto estimado');
            $table->date('fec_ini')->nullable()->comment('Fecha de inicio');
            $table->date('fec_fin')->nullable()->comment('Fecha de finalización');
            $table->date('fec_ven')->nullable()->comment('Fecha de vencimiento');
            $table->text('obs')->nullable()->comment('Observaciones');
            $table->json('met')->nullable()->comment('Metadatos adicionales');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['usu_id', 'est']);
            $table->index(['ope_id', 'est']);
            $table->index(['tip', 'est']);
            $table->index(['pri', 'est']);
            $table->index(['cat', 'est']);
            $table->index(['ciu', 'dep']);
            $table->index('fec_ven');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vee');
    }
};
