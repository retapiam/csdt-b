<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adaptar la estructura para MySQL específicamente
     */
    public function up(): void
    {
        // Solo ejecutar comandos específicos de MySQL si estamos usando MySQL
        if (DB::getDriverName() === 'mysql') {
            // Configurar MySQL para usar utf8mb4
            DB::statement('SET foreign_key_checks=0;');
            
            // Asegurar que las tablas usen el motor InnoDB y charset utf8mb4
            $tables = [
                'usu', 'rol', 'vee', 'don', 'tar', 'arc', 'cfg', 'log', 
                'ai_ana', 'ai_nar', 'pqrsfd', 'pag', 'pag_perm', 'pag_rol', 
                'reg_pen', 'est_ai'
            ];

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::statement("ALTER TABLE `$table` ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                }
            }
        }

        // Verificar y ajustar la tabla de usuarios para MySQL
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('usu')) {
            // Asegurar que la columna rol tenga los valores correctos para MySQL
            DB::statement("ALTER TABLE `usu` MODIFY COLUMN `rol` ENUM('cli', 'ope', 'adm', 'adm_gen') NOT NULL COMMENT 'Rol del usuario'");
            DB::statement("ALTER TABLE `usu` MODIFY COLUMN `est` ENUM('act', 'ina', 'sus', 'pen') DEFAULT 'pen' NOT NULL COMMENT 'Estado del usuario'");
            DB::statement("ALTER TABLE `usu` MODIFY COLUMN `gen` ENUM('m', 'f', 'o', 'n') NULL COMMENT 'Género'");
            DB::statement("ALTER TABLE `usu` MODIFY COLUMN `tip_doc` ENUM('cc', 'ce', 'ti', 'pp', 'nit') NULL COMMENT 'Tipo de documento'");
        }

        // Verificar y ajustar la tabla de veedurías
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('vee')) {
            DB::statement("ALTER TABLE `vee` MODIFY COLUMN `tip` ENUM('pet', 'que', 'rec', 'sug', 'fel', 'den') NOT NULL COMMENT 'Tipo de veeduría'");
            DB::statement("ALTER TABLE `vee` MODIFY COLUMN `est` ENUM('pen', 'pro', 'rad', 'cer', 'can') DEFAULT 'pen' NOT NULL COMMENT 'Estado de la veeduría'");
            DB::statement("ALTER TABLE `vee` MODIFY COLUMN `pri` ENUM('baj', 'med', 'alt', 'urg') DEFAULT 'med' NOT NULL COMMENT 'Prioridad'");
            DB::statement("ALTER TABLE `vee` MODIFY COLUMN `cat` ENUM('inf', 'ser', 'seg', 'edu', 'sal', 'tra', 'amb', 'otr') NULL COMMENT 'Categoría'");
        }

        // Verificar y ajustar la tabla de donaciones
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('don')) {
            DB::statement("ALTER TABLE `don` MODIFY COLUMN `tip` ENUM('efec', 'tran', 'cheq', 'otr') NOT NULL COMMENT 'Tipo de pago'");
            DB::statement("ALTER TABLE `don` MODIFY COLUMN `est` ENUM('pen', 'pro', 'con', 'rej', 'can') DEFAULT 'pen' NOT NULL COMMENT 'Estado del pago'");
        }

        // Verificar y ajustar la tabla de tareas
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('tar')) {
            DB::statement("ALTER TABLE `tar` MODIFY COLUMN `est` ENUM('pen', 'pro', 'com', 'can', 'sus') DEFAULT 'pen' NOT NULL COMMENT 'Estado de la tarea'");
            DB::statement("ALTER TABLE `tar` MODIFY COLUMN `pri` ENUM('baj', 'med', 'alt', 'urg') DEFAULT 'med' NOT NULL COMMENT 'Prioridad'");
        }

        // Verificar y ajustar la tabla de archivos
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('arc')) {
            DB::statement("ALTER TABLE `arc` MODIFY COLUMN `est` ENUM('act', 'ina', 'eli') DEFAULT 'act' NOT NULL COMMENT 'Estado del archivo'");
        }

        // Verificar y ajustar la tabla de configuraciones
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('cfg')) {
            DB::statement("ALTER TABLE `cfg` MODIFY COLUMN `tip` ENUM('str', 'int', 'bool', 'json') DEFAULT 'str' NOT NULL COMMENT 'Tipo de dato'");
            DB::statement("ALTER TABLE `cfg` MODIFY COLUMN `est` ENUM('act', 'ina') DEFAULT 'act' NOT NULL COMMENT 'Estado'");
        }

        // Verificar y ajustar la tabla de análisis de IA
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('ai_ana')) {
            DB::statement("ALTER TABLE `ai_ana` MODIFY COLUMN `est` ENUM('pen', 'pro', 'com', 'err') DEFAULT 'pen' NOT NULL COMMENT 'Estado del análisis'");
        }

        // Verificar y ajustar la tabla de narraciones de IA
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('ai_nar')) {
            DB::statement("ALTER TABLE `ai_nar` MODIFY COLUMN `tip` ENUM('act', 'res', 'inf', 'com') NOT NULL COMMENT 'Tipo de narración'");
            DB::statement("ALTER TABLE `ai_nar` MODIFY COLUMN `est` ENUM('pen', 'pro', 'com', 'can') DEFAULT 'pen' NOT NULL COMMENT 'Estado'");
        }

        // Verificar y ajustar la tabla de PQRSFD
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('pqrsfd')) {
            DB::statement("ALTER TABLE `pqrsfd` MODIFY COLUMN `tip` ENUM('pet', 'que', 'rec', 'sug', 'fel', 'den') NOT NULL COMMENT 'Tipo'");
            DB::statement("ALTER TABLE `pqrsfd` MODIFY COLUMN `est` ENUM('pen', 'pro', 'res', 'cer') DEFAULT 'pen' NOT NULL COMMENT 'Estado'");
            DB::statement("ALTER TABLE `pqrsfd` MODIFY COLUMN `pri` ENUM('baj', 'med', 'alt', 'urg') DEFAULT 'med' NOT NULL COMMENT 'Prioridad'");
        }

        // Verificar y ajustar la tabla de páginas
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('pag')) {
            DB::statement("ALTER TABLE `pag` MODIFY COLUMN `est` ENUM('act', 'ina') DEFAULT 'act' NOT NULL COMMENT 'Estado'");
        }

        // Verificar y ajustar la tabla de roles
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('rol')) {
            DB::statement("ALTER TABLE `rol` MODIFY COLUMN `est` ENUM('act', 'ina') DEFAULT 'act' NOT NULL COMMENT 'Estado del rol'");
        }

        // Verificar y ajustar la tabla de registros pendientes
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('reg_pen')) {
            DB::statement("ALTER TABLE `reg_pen` MODIFY COLUMN `tip_doc` ENUM('cc', 'ce', 'ti', 'pp', 'nit') NOT NULL COMMENT 'Tipo documento'");
            DB::statement("ALTER TABLE `reg_pen` MODIFY COLUMN `est` ENUM('pen', 'apr', 'rej') DEFAULT 'pen' NOT NULL COMMENT 'Estado'");
        }

        // Crear índices adicionales para optimizar consultas en MySQL
        if (DB::getDriverName() === 'mysql') {
            $this->createOptimizedIndexes();
            DB::statement('SET foreign_key_checks=1;');
        }
    }

    /**
     * Crear índices optimizados para MySQL
     */
    private function createOptimizedIndexes(): void
    {
        // Índices compuestos para consultas frecuentes
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('usu')) {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_usu_rol_est_act ON `usu` (`rol`, `est`, `created_at`)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_usu_cor_ver ON `usu` (`cor`, `cor_ver`)");
        }

        if (DB::getDriverName() === 'mysql' && Schema::hasTable('vee')) {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_vee_est_pri_fec ON `vee` (`est`, `pri`, `fec_reg`)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_vee_ope_est_fec ON `vee` (`ope_id`, `est`, `fec_reg`)");
        }

        if (DB::getDriverName() === 'mysql' && Schema::hasTable('don')) {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_don_est_fec ON `don` (`est`, `fec_don`)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_don_usu_est_fec ON `don` (`usu_id`, `est`, `fec_don`)");
        }

        if (DB::getDriverName() === 'mysql' && Schema::hasTable('tar')) {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_tar_asig_est_fec ON `tar` (`asig_a`, `est`, `fec_ven`)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_tar_vee_est ON `tar` (`vee_id`, `est`)");
        }

        if (DB::getDriverName() === 'mysql' && Schema::hasTable('log')) {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_log_usu_fec ON `log` (`usu_id`, `created_at`)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_log_acc_tab ON `log` (`acc`, `tab`, `created_at`)");
        }
    }

    /**
     * Revertir los cambios
     */
    public function down(): void
    {
        // Solo ejecutar comandos específicos de MySQL si estamos usando MySQL
        if (DB::getDriverName() === 'mysql') {
            // No revertimos los cambios de charset ya que son mejoras
            // Solo eliminamos los índices adicionales si es necesario
            if (DB::getDriverName() === 'mysql' && Schema::hasTable('usu')) {
                DB::statement("DROP INDEX IF EXISTS idx_usu_rol_est_act ON `usu`");
                DB::statement("DROP INDEX IF EXISTS idx_usu_cor_ver ON `usu`");
            }

            if (DB::getDriverName() === 'mysql' && Schema::hasTable('vee')) {
                DB::statement("DROP INDEX IF EXISTS idx_vee_est_pri_fec ON `vee`");
                DB::statement("DROP INDEX IF EXISTS idx_vee_ope_est_fec ON `vee`");
            }

            if (DB::getDriverName() === 'mysql' && Schema::hasTable('don')) {
                DB::statement("DROP INDEX IF EXISTS idx_don_est_fec ON `don`");
                DB::statement("DROP INDEX IF EXISTS idx_don_usu_est_fec ON `don`");
            }

            if (DB::getDriverName() === 'mysql' && Schema::hasTable('tar')) {
                DB::statement("DROP INDEX IF EXISTS idx_tar_asig_est_fec ON `tar`");
                DB::statement("DROP INDEX IF EXISTS idx_tar_vee_est ON `tar`");
            }

            if (DB::getDriverName() === 'mysql' && Schema::hasTable('log')) {
                DB::statement("DROP INDEX IF EXISTS idx_log_usu_fec ON `log`");
                DB::statement("DROP INDEX IF EXISTS idx_log_acc_tab ON `log`");
            }
        }
    }
};
