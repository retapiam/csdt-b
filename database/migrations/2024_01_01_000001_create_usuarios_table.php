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
        Schema::create('usu', function (Blueprint $table) {
            $table->id('id');
            $table->string('cod', 20)->unique()->comment('Código único del usuario');
            $table->string('nom', 100)->comment('Nombre');
            $table->string('ape', 100)->comment('Apellido');
            $table->string('cor', 150)->unique()->comment('Correo electrónico');
            $table->string('con', 255)->comment('Contraseña encriptada');
            $table->string('tel', 20)->nullable()->comment('Teléfono');
            $table->string('doc', 20)->unique()->comment('Documento de identidad');
            $table->enum('tip_doc', ['cc', 'ce', 'ti', 'pp', 'nit'])->comment('Tipo de documento');
            $table->date('fec_nac')->nullable()->comment('Fecha de nacimiento');
            $table->text('dir')->nullable()->comment('Dirección');
            $table->string('ciu', 100)->nullable()->comment('Ciudad');
            $table->string('dep', 100)->nullable()->comment('Departamento');
            $table->enum('gen', ['m', 'f', 'o', 'n'])->default('n')->comment('Género');
            $table->enum('rol', ['cli', 'ope', 'adm', 'adm_gen'])->default('cli')->comment('Rol del usuario');
            $table->enum('est', ['act', 'ina', 'pen', 'sus'])->default('pen')->comment('Estado del usuario');
            $table->boolean('cor_ver')->default(false)->comment('Correo verificado');
            $table->timestamp('cor_ver_at')->nullable()->comment('Fecha de verificación de correo');
            $table->timestamp('ult_ing')->nullable()->comment('Último ingreso');
            $table->text('fot')->nullable()->comment('Foto de perfil');
            $table->json('met')->nullable()->comment('Metadatos adicionales');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['rol', 'est']);
            $table->index(['ciu', 'dep']);
            $table->index('cor_ver');
            $table->index('ult_ing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usu');
    }
};
