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
        Schema::table('usu', function (Blueprint $table) {
            // Agregar campos para profesión y nivel
            $table->foreignId('profesion_id')->nullable()->constrained('profesiones')->onDelete('set null');
            $table->foreignId('nivel_id')->nullable()->constrained('niveles_usuario')->onDelete('set null');
            $table->integer('años_experiencia')->default(0)->comment('Años de experiencia profesional');
            $table->string('numero_matricula', 50)->nullable()->comment('Número de matrícula profesional');
            $table->string('entidad_matricula', 100)->nullable()->comment('Entidad que otorgó la matrícula');
            $table->date('fecha_matricula')->nullable()->comment('Fecha de obtención de matrícula');
            $table->json('especializaciones')->nullable()->comment('Especializaciones del usuario');
            $table->json('certificaciones')->nullable()->comment('Certificaciones del usuario');
            $table->text('perfil_profesional')->nullable()->comment('Perfil profesional detallado');
            $table->boolean('perfil_verificado')->default(false)->comment('Perfil verificado por administrador');
            $table->timestamp('perfil_verificado_en')->nullable()->comment('Fecha de verificación del perfil');
            $table->foreignId('verificado_por')->nullable()->constrained('usu')->onDelete('set null');
            
            // Campos de validación mejorada
            $table->enum('estado_verificacion', ['pendiente', 'verificado', 'rechazado', 'en_revision'])->default('pendiente');
            $table->text('motivo_rechazo')->nullable()->comment('Motivo de rechazo si aplica');
            $table->json('documentos_adjuntos')->nullable()->comment('Documentos de respaldo');
            
            // Índices
            $table->index(['profesion_id', 'nivel_id']);
            $table->index('estado_verificacion');
            $table->index('perfil_verificado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usu', function (Blueprint $table) {
            $table->dropForeign(['profesion_id']);
            $table->dropForeign(['nivel_id']);
            $table->dropForeign(['verificado_por']);
            $table->dropColumn([
                'profesion_id', 'nivel_id', 'años_experiencia', 'numero_matricula',
                'entidad_matricula', 'fecha_matricula', 'especializaciones',
                'certificaciones', 'perfil_profesional', 'perfil_verificado',
                'perfil_verificado_en', 'verificado_por', 'estado_verificacion',
                'motivo_rechazo', 'documentos_adjuntos'
            ]);
        });
    }
};
