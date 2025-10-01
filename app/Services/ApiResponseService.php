<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ApiResponseService
{
    /**
     * Respuesta exitosa
     */
    public static function success($data = null, string $message = 'Operación exitosa', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Respuesta de error
     */
    public static function error(string $message = 'Error en la operación', $errors = null, int $statusCode = 400, string $errorCode = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        if ($errorCode !== null) {
            $response['error_code'] = $errorCode;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Respuesta de validación
     */
    public static function validationError($errors, string $message = 'Datos de validación incorrectos'): JsonResponse
    {
        return self::error($message, $errors, 422, 'VALIDATION_ERROR');
    }

    /**
     * Respuesta de no encontrado
     */
    public static function notFound(string $message = 'Recurso no encontrado'): JsonResponse
    {
        return self::error($message, null, 404, 'NOT_FOUND');
    }

    /**
     * Respuesta de no autorizado
     */
    public static function unauthorized(string $message = 'No autorizado'): JsonResponse
    {
        return self::error($message, null, 401, 'UNAUTHORIZED');
    }

    /**
     * Respuesta de prohibido
     */
    public static function forbidden(string $message = 'Acceso prohibido'): JsonResponse
    {
        return self::error($message, null, 403, 'FORBIDDEN');
    }

    /**
     * Respuesta de conflicto
     */
    public static function conflict(string $message = 'Conflicto en la operación'): JsonResponse
    {
        return self::error($message, null, 409, 'CONFLICT');
    }

    /**
     * Respuesta de error interno del servidor
     */
    public static function internalError(string $message = 'Error interno del servidor'): JsonResponse
    {
        return self::error($message, null, 500, 'INTERNAL_ERROR');
    }

    /**
     * Respuesta de demasiadas solicitudes
     */
    public static function tooManyRequests(string $message = 'Demasiadas solicitudes'): JsonResponse
    {
        return self::error($message, null, 429, 'TOO_MANY_REQUESTS');
    }

    /**
     * Respuesta de recurso creado
     */
    public static function created($data = null, string $message = 'Recurso creado exitosamente'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * Respuesta de recurso actualizado
     */
    public static function updated($data = null, string $message = 'Recurso actualizado exitosamente'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de recurso eliminado
     */
    public static function deleted(string $message = 'Recurso eliminado exitosamente'): JsonResponse
    {
        return self::success(null, $message, 200);
    }

    /**
     * Respuesta de lista paginada
     */
    public static function paginated($data, string $message = 'Datos obtenidos exitosamente'): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'has_more_pages' => $data->hasMorePages(),
            ],
            'timestamp' => now()->toISOString(),
        ];

        return response()->json($response, 200);
    }

    /**
     * Respuesta de estadísticas
     */
    public static function statistics($data, string $message = 'Estadísticas obtenidas exitosamente'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de búsqueda
     */
    public static function search($data, string $message = 'Búsqueda realizada exitosamente'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de exportación
     */
    public static function export($data, string $message = 'Datos exportados exitosamente'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de importación
     */
    public static function import($data, string $message = 'Datos importados exitosamente'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de autenticación exitosa
     */
    public static function authenticated($user, $token, string $message = 'Autenticación exitosa'): JsonResponse
    {
        $data = [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];

        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de logout
     */
    public static function loggedOut(string $message = 'Sesión cerrada exitosamente'): JsonResponse
    {
        return self::success(null, $message, 200);
    }

    /**
     * Respuesta de verificación
     */
    public static function verified($data = null, string $message = 'Verificación exitosa'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de activación
     */
    public static function activated($data = null, string $message = 'Activación exitosa'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de desactivación
     */
    public static function deactivated($data = null, string $message = 'Desactivación exitosa'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de restauración
     */
    public static function restored($data = null, string $message = 'Restauración exitosa'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de aprobación
     */
    public static function approved($data = null, string $message = 'Aprobación exitosa'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de rechazo
     */
    public static function rejected($data = null, string $message = 'Rechazo exitoso'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de cancelación
     */
    public static function cancelled($data = null, string $message = 'Cancelación exitosa'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de confirmación
     */
    public static function confirmed($data = null, string $message = 'Confirmación exitosa'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de procesamiento
     */
    public static function processed($data = null, string $message = 'Procesamiento exitoso'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de notificación
     */
    public static function notified($data = null, string $message = 'Notificación enviada exitosamente'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de backup
     */
    public static function backedUp($data = null, string $message = 'Respaldo creado exitosamente'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Respuesta de restauración de backup
     */
    public static function restoredFromBackup($data = null, string $message = 'Restauración desde respaldo exitosa'): JsonResponse
    {
        return self::success($data, $message, 200);
    }
}
