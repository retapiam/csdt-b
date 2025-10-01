<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        // Eliminar tablas existentes si existen (en orden inverso por dependencias)
        Schema::dropIfExists('ai_nar');
        Schema::dropIfExists('ai_ana');
        Schema::dropIfExists('log');
        Schema::dropIfExists('cfg');
        Schema::dropIfExists('arc');
        Schema::dropIfExists('tar');
        Schema::dropIfExists('don');
        Schema::dropIfExists('vee');
        Schema::dropIfExists('rol_perm');
        Schema::dropIfExists('perm');
        Schema::dropIfExists('usu_rol');
        Schema::dropIfExists('rol');
        Schema::dropIfExists('usu');
        Schema::dropIfExists('usu_sis');
        Schema::dropIfExists('reg_pen');
        Schema::dropIfExists('pqrsfd');
        Schema::dropIfExists('ope');
        Schema::dropIfExists('cli');
        Schema::dropIfExists('adm');
        Schema::dropIfExists('adm_gen');
        Schema::dropIfExists('pag');
        Schema::dropIfExists('pag_perm');
        Schema::dropIfExists('pag_rol');

        // 1. TABLA DE USUARIOS PRINCIPAL (usu)
        Schema::create('usu', function (Blueprint $table) {
            $table->id('id')->comment('ID único del usuario');
            $table->string('nom', 100)->comment('Nombre');
            $table->string('ape', 100)->comment('Apellidos');
            $table->string('cor', 150)->unique()->comment('Correo electrónico');
            $table->string('con', 255)->comment('Contraseña hasheada');
            $table->string('tel', 20)->nullable()->comment('Teléfono');
            $table->string('doc', 20)->unique()->nullable()->comment('Número de documento');
            $table->enum('tip_doc', ['cc', 'ce', 'ti', 'pp', 'nit'])->nullable()->comment('Tipo de documento');
            $table->date('fec_nac')->nullable()->comment('Fecha de nacimiento');
            $table->string('dir', 200)->nullable()->comment('Dirección');
            $table->string('ciu', 100)->nullable()->comment('Ciudad');
            $table->string('dep', 100)->nullable()->comment('Departamento');
            $table->enum('gen', ['m', 'f', 'o', 'n'])->nullable()->comment('Género');
            $table->enum('rol', ['cli', 'ope', 'adm', 'adm_gen'])->comment('Rol del usuario');
            $table->enum('est', ['act', 'ina', 'sus', 'pen'])->default('pen')->comment('Estado del usuario');
            $table->boolean('cor_ver')->default(false)->comment('Correo verificado');
            $table->timestamp('cor_ver_en')->nullable()->comment('Fecha verificación correo');
            $table->timestamp('ult_acc')->nullable()->comment('Último acceso');
            $table->text('not')->nullable()->comment('Notas adicionales');
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['cor', 'est'], 'idx_usu_cor_est');
            $table->index(['doc'], 'idx_usu_doc');
            $table->index(['rol', 'est'], 'idx_usu_rol_est');
            $table->index(['ciu', 'dep'], 'idx_usu_ciu_dep');
        });

        // 2. TABLA DE ROLES SIMPLES (rol)
        Schema::create('rol', function (Blueprint $table) {
            $table->id('id')->comment('ID único del rol');
            $table->string('nom', 100)->comment('Nombre del rol');
            $table->string('des', 255)->nullable()->comment('Descripción del rol');
            $table->enum('est', ['act', 'ina'])->default('act')->comment('Estado del rol');
            $table->integer('niv')->default(1)->comment('Nivel de acceso del rol (1-4)');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['est'], 'idx_rol_est');
            $table->index(['niv'], 'idx_rol_niv');
        });

        // 6. TABLA DE VEEDURÍAS (vee)
        Schema::create('vee', function (Blueprint $table) {
            $table->id('id')->comment('ID único de la veeduría');
            $table->foreignId('usu_id')->constrained('usu')->onDelete('cascade')->comment('Usuario que crea la veeduría');
            $table->foreignId('ope_id')->nullable()->constrained('usu')->onDelete('set null')->comment('Operador asignado');
            $table->string('tit', 200)->comment('Título de la veeduría');
            $table->text('des')->comment('Descripción detallada');
            $table->enum('tip', ['pet', 'que', 'rec', 'sug', 'fel', 'den'])->comment('Tipo de veeduría');
            $table->enum('est', ['pen', 'pro', 'rad', 'cer', 'can'])->default('pen')->comment('Estado de la veeduría');
            $table->enum('pri', ['baj', 'med', 'alt', 'urg'])->default('med')->comment('Prioridad');
            $table->enum('cat', ['inf', 'ser', 'seg', 'edu', 'sal', 'tra', 'amb', 'otr'])->nullable()->comment('Categoría');
            $table->string('ubi', 200)->nullable()->comment('Ubicación geográfica');
            $table->decimal('pre', 15, 2)->nullable()->comment('Presupuesto estimado');
            $table->timestamp('fec_reg')->useCurrent()->comment('Fecha de registro');
            $table->timestamp('fec_rad')->nullable()->comment('Fecha de radicación');
            $table->timestamp('fec_cer')->nullable()->comment('Fecha de cierre');
            $table->string('num_rad', 50)->nullable()->unique()->comment('Número de radicación');
            $table->text('not_ope')->nullable()->comment('Notas del operador');
            $table->json('rec_ia')->nullable()->comment('Recomendaciones de IA');
            $table->json('arc')->nullable()->comment('Archivos adjuntos');
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['usu_id', 'est'], 'idx_vee_usu_est');
            $table->index(['ope_id', 'est'], 'idx_vee_ope_est');
            $table->index(['tip', 'est'], 'idx_vee_tip_est');
            $table->index(['pri', 'est'], 'idx_vee_pri_est');
            $table->index(['fec_reg'], 'idx_vee_fec_reg');
            $table->index(['num_rad'], 'idx_vee_num_rad');
        });

        // 7. TABLA DE DONACIONES (don)
        Schema::create('don', function (Blueprint $table) {
            $table->id('id')->comment('ID único de la donación');
            $table->foreignId('usu_id')->constrained('usu')->onDelete('cascade')->comment('Usuario que dona');
            $table->foreignId('vee_id')->nullable()->constrained('vee')->onDelete('set null')->comment('Veeduría relacionada');
            $table->decimal('mon', 15, 2)->comment('Monto de la donación');
            $table->enum('tip', ['efec', 'tran', 'cheq', 'otr'])->comment('Tipo de pago');
            $table->enum('est', ['pen', 'pro', 'con', 'rej', 'can'])->default('pen')->comment('Estado del pago');
            $table->string('ref', 100)->nullable()->comment('Referencia de pago');
            $table->text('des')->nullable()->comment('Descripción de la donación');
            $table->timestamp('fec_don')->useCurrent()->comment('Fecha de donación');
            $table->timestamp('fec_con')->nullable()->comment('Fecha de confirmación');
            $table->text('not')->nullable()->comment('Notas adicionales');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['usu_id', 'est'], 'idx_don_usu_est');
            $table->index(['vee_id'], 'idx_don_vee');
            $table->index(['fec_don'], 'idx_don_fec_don');
            $table->index(['tip', 'est'], 'idx_don_tip_est');
        });

        // 8. TABLA DE TAREAS (tar)
        Schema::create('tar', function (Blueprint $table) {
            $table->id('id')->comment('ID único de la tarea');
            $table->foreignId('vee_id')->nullable()->constrained('vee')->onDelete('cascade')->comment('Veeduría relacionada');
            $table->foreignId('asig_por')->constrained('usu')->onDelete('cascade')->comment('Usuario que asigna');
            $table->foreignId('asig_a')->constrained('usu')->onDelete('cascade')->comment('Usuario asignado');
            $table->string('tit', 200)->comment('Título de la tarea');
            $table->text('des')->comment('Descripción de la tarea');
            $table->enum('est', ['pen', 'pro', 'com', 'can', 'sus'])->default('pen')->comment('Estado de la tarea');
            $table->enum('pri', ['baj', 'med', 'alt', 'urg'])->default('med')->comment('Prioridad');
            $table->timestamp('fec_ini')->nullable()->comment('Fecha de inicio');
            $table->timestamp('fec_ven')->nullable()->comment('Fecha de vencimiento');
            $table->timestamp('fec_com')->nullable()->comment('Fecha de completado');
            $table->text('not')->nullable()->comment('Notas de la tarea');
            $table->json('arc')->nullable()->comment('Archivos adjuntos');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['asig_a', 'est'], 'idx_tar_asig_est');
            $table->index(['vee_id'], 'idx_tar_vee');
            $table->index(['fec_ven'], 'idx_tar_fec_ven');
            $table->index(['pri', 'est'], 'idx_tar_pri_est');
        });

        // 9. TABLA DE ARCHIVOS (arc)
        Schema::create('arc', function (Blueprint $table) {
            $table->id('id')->comment('ID único del archivo');
            $table->foreignId('usu_id')->constrained('usu')->onDelete('cascade')->comment('Usuario propietario');
            $table->foreignId('vee_id')->nullable()->constrained('vee')->onDelete('cascade')->comment('Veeduría relacionada');
            $table->foreignId('tar_id')->nullable()->constrained('tar')->onDelete('cascade')->comment('Tarea relacionada');
            $table->string('nom_ori', 255)->comment('Nombre original del archivo');
            $table->string('nom_arc', 255)->comment('Nombre del archivo en el sistema');
            $table->string('rut', 500)->comment('Ruta del archivo');
            $table->string('tip', 100)->comment('Tipo MIME del archivo');
            $table->bigInteger('tam')->comment('Tamaño en bytes');
            $table->enum('est', ['act', 'ina', 'eli'])->default('act')->comment('Estado del archivo');
            $table->text('des')->nullable()->comment('Descripción del archivo');
            $table->string('hash_archivo', 64)->nullable()->comment('Hash del archivo');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['usu_id'], 'idx_arc_usu');
            $table->index(['vee_id'], 'idx_arc_vee');
            $table->index(['tar_id'], 'idx_arc_tar');
            $table->index(['tip', 'est'], 'idx_arc_tip_est');
            $table->index(['hash_archivo'], 'idx_arc_hash');
        });

        // 10. TABLA DE CONFIGURACIONES (cfg)
        Schema::create('cfg', function (Blueprint $table) {
            $table->id('id')->comment('ID único de la configuración');
            $table->string('cla', 100)->unique()->comment('Clave de configuración');
            $table->text('val')->comment('Valor de la configuración');
            $table->string('des', 255)->nullable()->comment('Descripción');
            $table->string('cat', 50)->nullable()->comment('Categoría');
            $table->enum('tip', ['str', 'int', 'bool', 'json'])->default('str')->comment('Tipo de dato');
            $table->enum('est', ['act', 'ina'])->default('act')->comment('Estado');
            $table->foreignId('usu_id')->nullable()->constrained('usu')->onDelete('set null')->comment('Usuario que configuró');
            $table->timestamps();

            $table->index(['cla'], 'idx_cfg_cla');
            $table->index(['cat', 'est'], 'idx_cfg_cat_est');
        });

        // 11. TABLA DE LOGS (log)
        Schema::create('log', function (Blueprint $table) {
            $table->id('id')->comment('ID único del log');
            $table->foreignId('usu_id')->nullable()->constrained('usu')->onDelete('set null')->comment('Usuario que ejecutó la acción');
            $table->string('acc', 100)->comment('Acción realizada');
            $table->string('tab', 50)->comment('Tabla afectada');
            $table->unsignedBigInteger('reg_id')->nullable()->comment('ID del registro afectado');
            $table->text('des')->nullable()->comment('Descripción detallada');
            $table->json('dat_ant')->nullable()->comment('Datos anteriores');
            $table->json('dat_nue')->nullable()->comment('Datos nuevos');
            $table->string('ip', 45)->nullable()->comment('Dirección IP');
            $table->text('age_usu')->nullable()->comment('User Agent');
            $table->timestamps();

            $table->index(['usu_id'], 'idx_log_usu');
            $table->index(['acc'], 'idx_log_acc');
            $table->index(['tab', 'reg_id'], 'idx_log_tab_reg');
            $table->index(['created_at'], 'idx_log_created');
        });

        // 12. TABLA DE ANÁLISIS DE IA (ai_ana)
        Schema::create('ai_ana', function (Blueprint $table) {
            $table->id('id')->comment('ID único del análisis');
            $table->foreignId('usu_id')->constrained('usu')->onDelete('cascade')->comment('Usuario que solicita');
            $table->foreignId('vee_id')->nullable()->constrained('vee')->onDelete('cascade')->comment('Veeduría relacionada');
            $table->string('tip', 50)->comment('Tipo de análisis');
            $table->text('tex')->comment('Texto analizado');
            $table->json('res')->comment('Resultado del análisis');
            $table->decimal('con', 5, 2)->comment('Nivel de confianza');
            $table->enum('est', ['pen', 'pro', 'com', 'err'])->default('pen')->comment('Estado del análisis');
            $table->json('met')->nullable()->comment('Metadatos adicionales');
            $table->timestamps();

            $table->index(['usu_id'], 'idx_ai_ana_usu');
            $table->index(['vee_id'], 'idx_ai_ana_vee');
            $table->index(['tip', 'est'], 'idx_ai_ana_tip_est');
        });

        // 13. TABLA DE NARRACIONES DE IA (ai_nar)
        Schema::create('ai_nar', function (Blueprint $table) {
            $table->id('id')->comment('ID único de la narración');
            $table->foreignId('usu_id')->constrained('usu')->onDelete('cascade')->comment('Usuario que crea');
            $table->string('cod', 50)->unique()->comment('Código único de narración');
            $table->enum('tip', ['act', 'res', 'inf', 'com'])->comment('Tipo de narración');
            $table->text('tex')->comment('Texto de la narración');
            $table->longText('nar_gen')->nullable()->comment('Narración generada por IA');
            $table->integer('con')->default(0)->comment('Nivel de confianza');
            $table->json('dat_cli')->nullable()->comment('Datos del cliente');
            $table->json('ubi')->nullable()->comment('Datos de ubicación');
            $table->json('res_ai')->nullable()->comment('Respuestas de IA');
            $table->enum('est', ['pen', 'pro', 'com', 'can'])->default('pen')->comment('Estado');
            $table->timestamps();

            $table->index(['usu_id'], 'idx_ai_nar_usu');
            $table->index(['cod'], 'idx_ai_nar_cod');
            $table->index(['tip', 'est'], 'idx_ai_nar_tip_est');
        });

        // 14. TABLA DE PQRSFD (pqrsfd)
        Schema::create('pqrsfd', function (Blueprint $table) {
            $table->id('id')->comment('ID único del PQRSFD');
            $table->foreignId('usu_id')->constrained('usu')->onDelete('cascade')->comment('Usuario que crea');
            $table->foreignId('ope_id')->nullable()->constrained('usu')->onDelete('set null')->comment('Operador asignado');
            $table->string('tit', 200)->comment('Título');
            $table->text('des')->comment('Descripción');
            $table->enum('tip', ['pet', 'que', 'rec', 'sug', 'fel', 'den'])->comment('Tipo');
            $table->enum('est', ['pen', 'pro', 'res', 'cer'])->default('pen')->comment('Estado');
            $table->enum('pri', ['baj', 'med', 'alt', 'urg'])->default('med')->comment('Prioridad');
            $table->string('num_rad', 50)->nullable()->unique()->comment('Número radicación');
            $table->timestamp('fec_reg')->useCurrent()->comment('Fecha registro');
            $table->timestamp('fec_rad')->nullable()->comment('Fecha radicación');
            $table->timestamp('fec_res')->nullable()->comment('Fecha respuesta');
            $table->text('res')->nullable()->comment('Respuesta');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['usu_id', 'est'], 'idx_pqrsfd_usu_est');
            $table->index(['tip', 'est'], 'idx_pqrsfd_tip_est');
            $table->index(['num_rad'], 'idx_pqrsfd_num_rad');
        });

        // 15. TABLA DE PÁGINAS (pag)
        Schema::create('pag', function (Blueprint $table) {
            $table->id('id')->comment('ID único de la página');
            $table->string('nom', 100)->comment('Nombre de la página');
            $table->string('rut', 200)->comment('Ruta de la página');
            $table->string('des', 255)->nullable()->comment('Descripción');
            $table->enum('est', ['act', 'ina'])->default('act')->comment('Estado');
            $table->integer('ord')->default(0)->comment('Orden de aparición');
            $table->timestamps();

            $table->index(['rut'], 'idx_pag_rut');
            $table->index(['est', 'ord'], 'idx_pag_est_ord');
        });

        // 16. TABLA PIVOT PÁGINAS-PERMISOS (pag_perm)
        Schema::create('pag_perm', function (Blueprint $table) {
            $table->id('id')->comment('ID único de la relación');
            $table->foreignId('pag_id')->constrained('pag')->onDelete('cascade')->comment('ID de la página');
            $table->foreignId('perm_id')->constrained('perm')->onDelete('cascade')->comment('ID del permiso');
            $table->timestamps();

            $table->unique(['pag_id', 'perm_id'], 'uk_pag_perm');
        });

        // 17. TABLA PIVOT PÁGINAS-ROLES (pag_rol)
        Schema::create('pag_rol', function (Blueprint $table) {
            $table->id('id')->comment('ID único de la relación');
            $table->foreignId('pag_id')->constrained('pag')->onDelete('cascade')->comment('ID de la página');
            $table->foreignId('rol_id')->constrained('rol')->onDelete('cascade')->comment('ID del rol');
            $table->timestamps();

            $table->unique(['pag_id', 'rol_id'], 'uk_pag_rol');
        });

        // 18. TABLA DE REGISTROS PENDIENTES (reg_pen)
        Schema::create('reg_pen', function (Blueprint $table) {
            $table->id('id')->comment('ID único del registro');
            $table->string('nom', 100)->comment('Nombre');
            $table->string('ape', 100)->comment('Apellidos');
            $table->string('cor', 150)->comment('Correo electrónico');
            $table->string('doc', 20)->comment('Documento');
            $table->enum('tip_doc', ['cc', 'ce', 'ti', 'pp', 'nit'])->comment('Tipo documento');
            $table->enum('est', ['pen', 'apr', 'rej'])->default('pen')->comment('Estado');
            $table->text('not')->nullable()->comment('Notas');
            $table->foreignId('rev_por')->nullable()->constrained('usu')->onDelete('set null')->comment('Revisado por');
            $table->timestamp('fec_rev')->nullable()->comment('Fecha revisión');
            $table->timestamps();

            $table->index(['cor'], 'idx_reg_pen_cor');
            $table->index(['est'], 'idx_reg_pen_est');
        });

        // 19. TABLA DE ESTADÍSTICAS DE IA (est_ai)
        Schema::create('est_ai', function (Blueprint $table) {
            $table->id('id')->comment('ID único de la estadística');
            $table->date('fec')->comment('Fecha de la estadística');
            $table->string('tip_met', 50)->comment('Tipo de métrica');
            $table->string('cat', 100)->nullable()->comment('Categoría');
            $table->decimal('val', 15, 2)->comment('Valor de la métrica');
            $table->text('des')->nullable()->comment('Descripción');
            $table->json('met')->nullable()->comment('Metadatos adicionales');
            $table->timestamps();

            $table->index(['fec', 'tip_met'], 'idx_est_ai_fec_tip');
            $table->index(['cat'], 'idx_est_ai_cat');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        // Eliminar en orden inverso por dependencias
        Schema::dropIfExists('est_ai');
        Schema::dropIfExists('reg_pen');
        Schema::dropIfExists('pag_rol');
        Schema::dropIfExists('pag_perm');
        Schema::dropIfExists('pag');
        Schema::dropIfExists('pqrsfd');
        Schema::dropIfExists('ai_nar');
        Schema::dropIfExists('ai_ana');
        Schema::dropIfExists('log');
        Schema::dropIfExists('cfg');
        Schema::dropIfExists('arc');
        Schema::dropIfExists('tar');
        Schema::dropIfExists('don');
        Schema::dropIfExists('vee');
        Schema::dropIfExists('rol_perm');
        Schema::dropIfExists('perm');
        Schema::dropIfExists('usu_rol');
        Schema::dropIfExists('rol');
        Schema::dropIfExists('usu');
    }
};
