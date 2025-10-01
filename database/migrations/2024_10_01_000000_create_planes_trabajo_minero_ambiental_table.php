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
        Schema::create('planes_trabajo_minero_ambiental', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Tipo de plan
            $table->enum('tipo_plan', ['minero', 'ambiental', 'integrado'])->index();
            
            // Información del proyecto
            $table->string('nombre_proyecto', 500);
            $table->string('tipo_mineria', 100)->nullable()->index();
            $table->string('ubicacion', 500);
            $table->string('duracion', 200);
            $table->text('descripcion');
            
            // Datos y resultados
            $table->json('datos_entrada')->nullable();
            $table->json('plan_generado')->nullable();
            
            // Estado del plan
            $table->enum('estado', [
                'generado',
                'en_revision',
                'aprobado',
                'rechazado',
                'en_ejecucion',
                'finalizado'
            ])->default('generado')->index();
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->text('observaciones')->nullable();
            
            // Aprobación
            $table->foreignId('aprobado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_aprobacion')->nullable();
            
            // Timestamps y soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Índices adicionales
            $table->index('created_at');
            $table->index(['user_id', 'tipo_plan']);
            $table->index(['user_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planes_trabajo_minero_ambiental');
    }
};

