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
        Schema::create('permisos_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Tipo de permiso
            $table->enum('tipo_permiso', [
                'ver_proyectos',
                'crear_proyectos',
                'editar_proyectos',
                'eliminar_proyectos',
                'ver_tareas',
                'crear_tareas',
                'editar_tareas',
                'eliminar_tareas',
                'gestionar_usuarios',
                'gestionar_roles',
                'gestionar_permisos',
                'ver_reportes',
                'exportar_datos',
                'configurar_sistema',
                'acceso_dashboard_admin',
                'acceso_dashboard_operador',
                'acceso_dashboard_cliente'
            ]);
            
            // Estado del permiso
            $table->enum('estado', ['activo', 'inactivo', 'vetado', 'temporal'])->default('activo');
            
            // Control temporal
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->boolean('es_temporal')->default(false);
            
            // Restricciones adicionales
            $table->json('restricciones')->nullable(); // Para restricciones personalizadas
            $table->text('motivo_veto')->nullable(); // Motivo si está vetado
            
            // Auditoría
            $table->foreignId('otorgado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('modificado_por')->nullable()->constrained('users')->onDelete('set null');
            
            // Metadatos
            $table->text('notas')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('user_id');
            $table->index('tipo_permiso');
            $table->index('estado');
            $table->index(['user_id', 'tipo_permiso', 'estado']);
        });

        // Tabla de historial de cambios de permisos
        Schema::create('historial_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permiso_id')->constrained('permisos_usuarios')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('modificado_por')->constrained('users')->onDelete('cascade');
            
            $table->string('accion'); // crear, actualizar, eliminar, vetar, activar
            $table->enum('estado_anterior', ['activo', 'inactivo', 'vetado', 'temporal'])->nullable();
            $table->enum('estado_nuevo', ['activo', 'inactivo', 'vetado', 'temporal']);
            
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->text('motivo')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('permiso_id');
            $table->index('user_id');
            $table->index('modificado_por');
        });

        // Tabla de permisos por rol (plantillas)
        Schema::create('permisos_roles', function (Blueprint $table) {
            $table->id();
            $table->enum('rol', ['cli', 'ope', 'adm', 'adm_gen']);
            $table->enum('tipo_permiso', [
                'ver_proyectos',
                'crear_proyectos',
                'editar_proyectos',
                'eliminar_proyectos',
                'ver_tareas',
                'crear_tareas',
                'editar_tareas',
                'eliminar_tareas',
                'gestionar_usuarios',
                'gestionar_roles',
                'gestionar_permisos',
                'ver_reportes',
                'exportar_datos',
                'configurar_sistema',
                'acceso_dashboard_admin',
                'acceso_dashboard_operador',
                'acceso_dashboard_cliente'
            ]);
            $table->boolean('activo')->default(true);
            $table->text('descripcion')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->unique(['rol', 'tipo_permiso']);
            $table->index('rol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_permisos');
        Schema::dropIfExists('permisos_usuarios');
        Schema::dropIfExists('permisos_roles');
    }
};

