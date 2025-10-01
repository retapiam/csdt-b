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
        Schema::create('don', function (Blueprint $table) {
            $table->id('id');
            $table->string('cod', 20)->unique()->comment('Código único de la donación');
            $table->foreignId('usu_id')->constrained('usu')->comment('Usuario que realiza la donación');
            $table->foreignId('vee_id')->nullable()->constrained('vee')->comment('Veeduría asociada');
            $table->string('don_nom', 100)->comment('Nombre del donante');
            $table->string('don_cor', 150)->comment('Correo del donante');
            $table->string('don_tel', 20)->nullable()->comment('Teléfono del donante');
            $table->enum('tip', ['din', 'esp', 'ser', 'otr'])->comment('Tipo de donación');
            $table->decimal('mon', 15, 2)->nullable()->comment('Monto de la donación');
            $table->string('moneda', 3)->default('COP')->comment('Moneda');
            $table->text('des')->nullable()->comment('Descripción de la donación');
            $table->enum('est', ['pen', 'con', 'rec', 'can'])->default('pen')->comment('Estado de la donación');
            $table->enum('met_pag', ['ef', 'tr', 'ne', 'otr'])->nullable()->comment('Método de pago');
            $table->string('ref_pag', 100)->nullable()->comment('Referencia de pago');
            $table->text('com')->nullable()->comment('Comentarios');
            $table->date('fec_pro')->nullable()->comment('Fecha de procesamiento');
            $table->date('fec_con')->nullable()->comment('Fecha de confirmación');
            $table->json('met')->nullable()->comment('Metadatos adicionales');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['usu_id', 'est']);
            $table->index(['vee_id', 'est']);
            $table->index(['tip', 'est']);
            $table->index('fec_pro');
            $table->index('fec_con');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don');
    }
};
