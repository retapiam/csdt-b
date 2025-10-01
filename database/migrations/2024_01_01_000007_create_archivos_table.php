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
        Schema::create('arc', function (Blueprint $table) {
            $table->id('id');
            $table->string('cod', 20)->unique()->comment('Código único del archivo');
            $table->foreignId('usu_id')->constrained('usu')->comment('Usuario que subió el archivo');
            $table->foreignId('vee_id')->nullable()->constrained('vee')->comment('Veeduría asociada');
            $table->foreignId('tar_id')->nullable()->constrained('tar')->comment('Tarea asociada');
            $table->string('nom_ori', 255)->comment('Nombre original del archivo');
            $table->string('nom_arc', 255)->comment('Nombre del archivo en el servidor');
            $table->string('rut', 500)->comment('Ruta del archivo');
            $table->string('ext', 10)->comment('Extensión del archivo');
            $table->enum('tip', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'zip', 'rar', 'otr'])->comment('Tipo de archivo');
            $table->bigInteger('tam')->comment('Tamaño del archivo en bytes');
            $table->string('mim', 100)->comment('Tipo MIME');
            $table->string('has', 64)->nullable()->comment('Hash del archivo');
            $table->text('des')->nullable()->comment('Descripción del archivo');
            $table->enum('est', ['act', 'ina', 'eli'])->default('act')->comment('Estado del archivo');
            $table->boolean('pub')->default(false)->comment('Es público');
            $table->integer('des_can')->default(0)->comment('Cantidad de descargas');
            $table->json('met')->nullable()->comment('Metadatos adicionales');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['usu_id', 'est']);
            $table->index(['vee_id', 'est']);
            $table->index(['tar_id', 'est']);
            $table->index(['tip', 'est']);
            $table->index('has');
            $table->index('pub');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arc');
    }
};
