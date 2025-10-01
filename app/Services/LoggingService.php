<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoggingService
{
    /**
     * Registrar acción del usuario
     */
    public static function logUserAction(string $action, string $description, $model = null, array $data = [], string $level = 'info'): void
    {
        $user = Auth::user();
        
        Log::create([
            'acc' => $action,
            'des' => $description,
            'tab' => $model ? class_basename($model) : null,
            'reg_id' => $model ? $model->id : null,
            'usu_id' => $user ? $user->id : null,
            'dat_ant' => $model ? $model->getOriginal() : null,
            'dat_nue' => $data,
            'niv' => $level,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'met' => request()->method(),
        ]);
    }

    /**
     * Registrar login
     */
    public static function logLogin($user, bool $success = true): void
    {
        $action = $success ? 'login_exitoso' : 'login_fallido';
        $description = $success ? 'Usuario inició sesión' : 'Intento de login fallido';
        $level = $success ? 'info' : 'warning';

        self::logUserAction($action, $description, null, [
            'email' => $user->cor ?? 'unknown',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $level);
    }

    /**
     * Registrar logout
     */
    public static function logLogout($user): void
    {
        self::logUserAction('logout', 'Usuario cerró sesión', null, [
            'email' => $user->cor ?? 'unknown',
            'ip' => request()->ip(),
        ], 'info');
    }

    /**
     * Registrar creación de recurso
     */
    public static function logCreate($model, array $data = []): void
    {
        self::logUserAction('crear', 'Recurso creado', $model, $data, 'info');
    }

    /**
     * Registrar actualización de recurso
     */
    public static function logUpdate($model, array $oldData = [], array $newData = []): void
    {
        self::logUserAction('actualizar', 'Recurso actualizado', $model, [
            'datos_anteriores' => $oldData,
            'datos_nuevos' => $newData,
        ], 'info');
    }

    /**
     * Registrar eliminación de recurso
     */
    public static function logDelete($model, array $data = []): void
    {
        self::logUserAction('eliminar', 'Recurso eliminado', $model, $data, 'warning');
    }

    /**
     * Registrar restauración de recurso
     */
    public static function logRestore($model, array $data = []): void
    {
        self::logUserAction('restaurar', 'Recurso restaurado', $model, $data, 'info');
    }

    /**
     * Registrar activación de recurso
     */
    public static function logActivate($model, array $data = []): void
    {
        self::logUserAction('activar', 'Recurso activado', $model, $data, 'info');
    }

    /**
     * Registrar desactivación de recurso
     */
    public static function logDeactivate($model, array $data = []): void
    {
        self::logUserAction('desactivar', 'Recurso desactivado', $model, $data, 'warning');
    }

    /**
     * Registrar aprobación
     */
    public static function logApprove($model, array $data = []): void
    {
        self::logUserAction('aprobar', 'Recurso aprobado', $model, $data, 'info');
    }

    /**
     * Registrar rechazo
     */
    public static function logReject($model, array $data = []): void
    {
        self::logUserAction('rechazar', 'Recurso rechazado', $model, $data, 'warning');
    }

    /**
     * Registrar cancelación
     */
    public static function logCancel($model, array $data = []): void
    {
        self::logUserAction('cancelar', 'Recurso cancelado', $model, $data, 'warning');
    }

    /**
     * Registrar confirmación
     */
    public static function logConfirm($model, array $data = []): void
    {
        self::logUserAction('confirmar', 'Recurso confirmado', $model, $data, 'info');
    }

    /**
     * Registrar procesamiento
     */
    public static function logProcess($model, array $data = []): void
    {
        self::logUserAction('procesar', 'Recurso procesado', $model, $data, 'info');
    }

    /**
     * Registrar acceso denegado
     */
    public static function logAccessDenied(string $resource, string $action, array $data = []): void
    {
        self::logUserAction('acceso_denegado', "Acceso denegado a {$resource}/{$action}", null, $data, 'warning');
    }

    /**
     * Registrar error de validación
     */
    public static function logValidationError(string $resource, array $errors, array $data = []): void
    {
        self::logUserAction('error_validacion', "Error de validación en {$resource}", null, [
            'errores' => $errors,
            'datos' => $data,
        ], 'warning');
    }

    /**
     * Registrar error del sistema
     */
    public static function logSystemError(string $description, \Throwable $exception, array $data = []): void
    {
        self::logUserAction('error_sistema', $description, null, [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'datos' => $data,
        ], 'error');
    }

    /**
     * Registrar operación de archivo
     */
    public static function logFileOperation(string $operation, string $filename, array $data = []): void
    {
        self::logUserAction('archivo_' . $operation, "Operación de archivo: {$operation}", null, [
            'archivo' => $filename,
            'datos' => $data,
        ], 'info');
    }

    /**
     * Registrar operación de exportación
     */
    public static function logExport(string $type, array $data = []): void
    {
        self::logUserAction('exportar', "Exportación de {$type}", null, $data, 'info');
    }

    /**
     * Registrar operación de importación
     */
    public static function logImport(string $type, array $data = []): void
    {
        self::logUserAction('importar', "Importación de {$type}", null, $data, 'info');
    }

    /**
     * Registrar operación de backup
     */
    public static function logBackup(string $type, array $data = []): void
    {
        self::logUserAction('backup', "Respaldo de {$type}", null, $data, 'info');
    }

    /**
     * Registrar operación de restauración
     */
    public static function logRestore(string $type, array $data = []): void
    {
        self::logUserAction('restaurar', "Restauración de {$type}", null, $data, 'info');
    }

    /**
     * Registrar operación de configuración
     */
    public static function logConfigChange(string $config, $oldValue, $newValue, array $data = []): void
    {
        self::logUserAction('config_cambiar', "Configuración cambiada: {$config}", null, [
            'configuracion' => $config,
            'valor_anterior' => $oldValue,
            'valor_nuevo' => $newValue,
            'datos' => $data,
        ], 'info');
    }

    /**
     * Registrar operación de usuario
     */
    public static function logUserOperation(string $operation, $user, array $data = []): void
    {
        self::logUserAction('usuario_' . $operation, "Operación de usuario: {$operation}", $user, $data, 'info');
    }

    /**
     * Registrar operación de rol
     */
    public static function logRoleOperation(string $operation, $role, array $data = []): void
    {
        self::logUserAction('rol_' . $operation, "Operación de rol: {$operation}", $role, $data, 'info');
    }

    /**
     * Registrar operación de permiso
     */
    public static function logPermissionOperation(string $operation, $permission, array $data = []): void
    {
        self::logUserAction('permiso_' . $operation, "Operación de permiso: {$operation}", $permission, $data, 'info');
    }

    /**
     * Registrar operación de veeduría
     */
    public static function logVeeduriaOperation(string $operation, $veeduria, array $data = []): void
    {
        self::logUserAction('veeduria_' . $operation, "Operación de veeduría: {$operation}", $veeduria, $data, 'info');
    }

    /**
     * Registrar operación de donación
     */
    public static function logDonationOperation(string $operation, $donation, array $data = []): void
    {
        self::logUserAction('donacion_' . $operation, "Operación de donación: {$operation}", $donation, $data, 'info');
    }

    /**
     * Registrar operación de tarea
     */
    public static function logTaskOperation(string $operation, $task, array $data = []): void
    {
        self::logUserAction('tarea_' . $operation, "Operación de tarea: {$operation}", $task, $data, 'info');
    }

    /**
     * Registrar operación de archivo
     */
    public static function logFileOperation(string $operation, $file, array $data = []): void
    {
        self::logUserAction('archivo_' . $operation, "Operación de archivo: {$operation}", $file, $data, 'info');
    }

    /**
     * Registrar operación de IA
     */
    public static function logAIOperation(string $operation, $model, array $data = []): void
    {
        self::logUserAction('ia_' . $operation, "Operación de IA: {$operation}", $model, $data, 'info');
    }

    /**
     * Registrar operación de sistema
     */
    public static function logSystemOperation(string $operation, array $data = []): void
    {
        self::logUserAction('sistema_' . $operation, "Operación de sistema: {$operation}", null, $data, 'info');
    }

    /**
     * Registrar operación de seguridad
     */
    public static function logSecurityOperation(string $operation, array $data = []): void
    {
        self::logUserAction('seguridad_' . $operation, "Operación de seguridad: {$operation}", null, $data, 'warning');
    }

    /**
     * Registrar operación de auditoría
     */
    public static function logAuditOperation(string $operation, array $data = []): void
    {
        self::logUserAction('auditoria_' . $operation, "Operación de auditoría: {$operation}", null, $data, 'info');
    }
}
