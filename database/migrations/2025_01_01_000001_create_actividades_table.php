<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla de actividades - Similar a Microsoft Project
     * Cada módulo/página puede generar una actividad
     * Las actividades contienen tareas
     */
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('modulo_id')->nullable()->comment('ID del módulo/página que generó la actividad');
            
            // Información básica de la actividad
            $table->string('codigo', 50)->unique()->comment('Código único de la actividad');
            $table->string('nombre', 255);
            $table->string('nombre_modulo', 100)->nullable()->comment('Nombre del módulo origen');
            $table->text('descripcion');
            $table->string('tipo_actividad', 100)->comment('Tipo según módulo: tutela, consulta_previa, veeduria, etc.');
            
            // Categorización y estado
            $table->enum('categoria', [
                'juridica', 
                'etnica', 
                'veeduria', 
                'administrativa', 
                'tecnica',
                'financiera'
            ])->default('administrativa');
            
            $table->enum('estado', [
                'planificada',
                'en_progreso',
                'completada',
                'pausada',
                'cancelada'
            ])->default('planificada');
            
            $table->enum('prioridad', [
                'baja',
                'media',
                'alta',
                'critica'
            ])->default('media');
            
            // Responsables
            $table->foreignId('creada_por')->constrained('users')->comment('Admin que creó la actividad');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->comment('Responsable principal');
            
            // Fechas y duración
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin_planeada');
            $table->date('fecha_fin_real')->nullable();
            $table->integer('duracion_dias')->default(1);
            
            // Progreso y esfuerzo
            $table->integer('progreso')->default(0)->comment('0-100');
            $table->decimal('horas_estimadas', 10, 2)->default(0);
            $table->decimal('horas_reales', 10, 2)->default(0);
            
            // Costos
            $table->decimal('costo_estimado', 15, 2)->default(0);
            $table->decimal('costo_real', 15, 2)->default(0);
            
            // Relaciones jerárquicas (como MS Project)
            $table->foreignId('actividad_padre_id')->nullable()->constrained('actividades')->onDelete('cascade');
            $table->integer('nivel')->default(1)->comment('1=principal, 2=sub-actividad, 3=sub-sub-actividad');
            $table->integer('orden')->default(0)->comment('Orden de visualización');
            
            // Documentos y entregables
            $table->json('documentos_requeridos')->nullable();
            $table->json('documentos_entregados')->nullable();
            $table->json('pdfs_generados')->nullable()->comment('PDFs asociados a esta actividad');
            
            // Metadata
            $table->json('configuracion')->nullable();
            $table->json('metadata')->nullable();
            $table->text('notas')->nullable();
            
            // Color para visualización
            $table->string('color', 20)->default('#3B82F6')->comment('Color hex para identificación visual');
            
            $table->timestamps();
            
            // Índices
            $table->index(['proyecto_id', 'estado']);
            $table->index(['creada_por', 'estado']);
            $table->index(['fecha_inicio', 'fecha_fin_planeada']);
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};

