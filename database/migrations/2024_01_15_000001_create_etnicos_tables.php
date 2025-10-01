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
        // Tabla de comunidades étnicas
        Schema::create('comunidades_etnicas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['indigena', 'afrodescendiente', 'raizal', 'rom']);
            $table->string('ubicacion');
            $table->integer('poblacion')->nullable();
            $table->string('territorio')->nullable();
            $table->string('representante')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('idioma')->nullable();
            $table->string('tradicion')->nullable();
            $table->text('descripcion')->nullable();
            $table->json('coordenadas')->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'suspendido'])->default('activo');
            $table->timestamps();
        });

        // Tabla de patrimonio cultural étnico
        Schema::create('patrimonio_cultural_etnico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidad_id')->constrained('comunidades_etnicas');
            $table->enum('tipo', [
                'saberes_tradicionales', 'lenguas_nativas', 'rituales_ceremonias',
                'medicina_tradicional', 'artesania', 'musica_danza', 'gastronomia',
                'vestimenta', 'arquitectura', 'agricultura', 'caza_pesca', 'navegacion',
                'astronomia', 'matematicas', 'calendarios'
            ]);
            $table->string('nombre');
            $table->text('descripcion');
            $table->string('ubicacion')->nullable();
            $table->enum('estado_conservacion', ['excelente', 'bueno', 'regular', 'malo', 'critico']);
            $table->text('importancia')->nullable();
            $table->text('transmision')->nullable();
            $table->string('portadores')->nullable();
            $table->text('amenazas')->nullable();
            $table->json('archivos')->nullable();
            $table->timestamps();
        });

        // Tabla de planes educativos étnicos
        Schema::create('planes_educativos_etnicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidad_id')->constrained('comunidades_etnicas');
            $table->string('nombre');
            $table->enum('nivel', ['inicial', 'basica', 'media', 'superior', 'adultos', 'especializada']);
            $table->enum('area', [
                'saberes_tradicionales', 'lenguas_nativas', 'historia_ancestral',
                'territorio', 'medicina_tradicional', 'agricultura', 'artesania',
                'musica_danza', 'cosmovision', 'derechos'
            ]);
            $table->text('descripcion');
            $table->text('objetivos')->nullable();
            $table->string('metodologia')->nullable();
            $table->string('duracion')->nullable();
            $table->integer('participantes')->nullable();
            $table->text('recursos')->nullable();
            $table->text('evaluacion')->nullable();
            $table->text('cosmovision')->nullable();
            $table->text('saberes')->nullable();
            $table->enum('estado', ['planificacion', 'en_desarrollo', 'activo', 'completado', 'suspendido'])->default('planificacion');
            $table->timestamps();
        });

        // Tabla de casos de justicia indígena
        Schema::create('casos_justicia_indigena', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidad_id')->constrained('comunidades_etnicas');
            $table->text('descripcion');
            $table->string('pueblo_indigena');
            $table->enum('tipo_conflicto', [
                'territorial', 'recursos', 'cultural', 'familiar', 'comercial',
                'sucesion', 'violencia', 'administrativo'
            ]);
            $table->enum('gravedad', ['leve', 'moderado', 'grave', 'muy_grave']);
            $table->string('partes_involucradas');
            $table->string('ubicacion');
            $table->date('fecha_ocurrencia')->nullable();
            $table->text('testigos')->nullable();
            $table->text('antecedentes')->nullable();
            $table->text('contexto')->nullable();
            $table->text('solicitud')->nullable();
            $table->text('evidencia')->nullable();
            $table->string('procedimiento')->nullable();
            $table->text('resolucion')->nullable();
            $table->json('archivos')->nullable();
            $table->enum('estado', ['pendiente', 'en_proceso', 'resuelto', 'cerrado', 'suspendido'])->default('pendiente');
            $table->timestamps();
        });

        // Tabla de territorios ancestrales
        Schema::create('territorios_ancestrales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidad_id')->constrained('comunidades_etnicas');
            $table->string('nombre');
            $table->enum('tipo', [
                'resguardo', 'consejo_comunitario', 'territorio_tradicional',
                'sitio_sagrado', 'zona_reserva', 'territorio_ancestral'
            ]);
            $table->string('ubicacion');
            $table->string('extension')->nullable();
            $table->text('limites')->nullable();
            $table->text('descripcion');
            $table->text('importancia')->nullable();
            $table->text('amenazas')->nullable();
            $table->enum('estado_proteccion', ['protegido', 'vulnerable', 'amenazado', 'critico']);
            $table->text('sitios_sagrados')->nullable();
            $table->text('recursos')->nullable();
            $table->text('historia')->nullable();
            $table->text('cosmovision')->nullable();
            $table->json('coordenadas')->nullable();
            $table->json('archivos')->nullable();
            $table->timestamps();
        });

        // Tabla de consultas étnicas
        Schema::create('consultas_etnicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidad_id')->constrained('comunidades_etnicas');
            $table->enum('tipo', [
                'consulta_previa', 'proteccion_territorial', 'derechos_culturales',
                'autonomia', 'desarrollo'
            ]);
            $table->text('descripcion');
            $table->text('contexto')->nullable();
            $table->text('impacto')->nullable();
            $table->string('territorio')->nullable();
            $table->integer('version')->default(1);
            $table->json('archivos')->nullable();
            $table->enum('estado', ['borrador', 'enviada', 'en_revision', 'respondida', 'cerrada'])->default('borrador');
            $table->timestamps();
        });

        // Tabla de análisis de IA étnicos
        Schema::create('analisis_ia_etnicos', function (Blueprint $table) {
            $table->id();
            $table->morphs('analizable'); // Polimórfico para diferentes tipos de análisis
            $table->string('tipo_ia');
            $table->string('clasificacion');
            $table->text('respuesta');
            $table->integer('confianza');
            $table->json('recomendaciones')->nullable();
            $table->text('analisis_general')->nullable();
            $table->timestamps();
        });

        // Tabla de notificaciones étnicas
        Schema::create('notificaciones_etnicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidad_id')->nullable()->constrained('comunidades_etnicas');
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('tipo', [
                'consulta_previa', 'proteccion_territorial', 'derechos_culturales',
                'autonomia', 'desarrollo', 'justicia', 'educacion', 'patrimonio'
            ]);
            $table->enum('prioridad', ['urgente', 'alta', 'media', 'baja']);
            $table->enum('estado', ['pendiente', 'activa', 'en_proceso', 'completada', 'cancelada'])->default('pendiente');
            $table->string('territorio')->nullable();
            $table->json('acciones')->nullable();
            $table->timestamp('fecha_vencimiento')->nullable();
            $table->timestamps();
        });

        // Tabla de estadísticas étnicas
        Schema::create('estadisticas_etnicas', function (Blueprint $table) {
            $table->id();
            $table->string('metrica');
            $table->string('valor');
            $table->string('categoria')->nullable();
            $table->json('datos_adicionales')->nullable();
            $table->date('fecha_medicion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estadisticas_etnicas');
        Schema::dropIfExists('notificaciones_etnicas');
        Schema::dropIfExists('analisis_ia_etnicos');
        Schema::dropIfExists('consultas_etnicas');
        Schema::dropIfExists('territorios_ancestrales');
        Schema::dropIfExists('casos_justicia_indigena');
        Schema::dropIfExists('planes_educativos_etnicos');
        Schema::dropIfExists('patrimonio_cultural_etnico');
        Schema::dropIfExists('comunidades_etnicas');
    }
};
