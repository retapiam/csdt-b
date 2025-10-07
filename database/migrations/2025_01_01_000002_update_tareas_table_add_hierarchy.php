<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Actualiza tabla tareas para soportar jerarquía tipo MS Project
     */
    public function up(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            // Relación con actividades
            $table->foreignId('actividad_id')->nullable()->after('proyecto_id')
                ->constrained('actividades')->onDelete('cascade')
                ->comment('Actividad a la que pertenece esta tarea');
            
            // Nivel jerárquico de la tarea
            $table->enum('nivel_tarea', [
                'admin',      // Tarea creada por administrador
                'operador',   // Tarea creada por operador para cliente
                'cliente'     // Tarea auto-asignada por cliente
            ])->default('admin')->after('tipo');
            
            // Color de visualización por nivel
            $table->string('color', 20)->nullable()->after('nivel_tarea')
                ->comment('Color hex según nivel: admin=#8B5CF6, operador=#10B981, cliente=#F59E0B');
            
            // Tarea padre (para sub-tareas)
            $table->foreignId('tarea_padre_id')->nullable()->after('actividad_id')
                ->constrained('tareas')->onDelete('cascade')
                ->comment('Para sub-tareas creadas por operador');
            
            // Documentos PDF específicos de la tarea
            $table->json('pdfs_adjuntos')->nullable()->after('documentos_entregados')
                ->comment('PDFs generados específicamente para esta tarea');
            
            // Soporte documental
            $table->json('soportes')->nullable()->after('pdfs_adjuntos')
                ->comment('Documentos de soporte adjuntos');
            
            // Índices adicionales
            $table->index('actividad_id');
            $table->index(['proyecto_id', 'actividad_id', 'nivel_tarea']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropForeign(['actividad_id']);
            $table->dropForeign(['tarea_padre_id']);
            $table->dropColumn([
                'actividad_id',
                'nivel_tarea',
                'color',
                'tarea_padre_id',
                'pdfs_adjuntos',
                'soportes'
            ]);
        });
    }
};

