<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProyectoController;
use App\Http\Controllers\Api\ActividadController;
use App\Http\Controllers\Api\TareaController;
use App\Http\Controllers\Api\DonacionController;
use App\Http\Controllers\Api\ConsultaPreviaController;
use App\Http\Controllers\Api\PuebloIndigenaController;
use App\Http\Controllers\Api\ComunidadAfroController;
use App\Http\Controllers\Api\VeeduriaController;
use App\Http\Controllers\Api\CasoLegalController;
use App\Http\Controllers\Api\IAController;
use App\Http\Controllers\Api\PermisosController;

// ==================== RUTAS PÚBLICAS ====================

// Health Check ultra-optimizado
Route::get('/health', function () {
    return response('OK', 200, [
        'Content-Type' => 'text/plain',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
});

// Health Check JSON (más detallado pero más lento)
Route::get('/health-json', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Servidor funcionando correctamente',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ], 200, [
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
});

// Autenticación
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/recuperar-contrasena', [AuthController::class, 'recuperarContrasena']);
Route::post('/auth/verificar-disponibilidad', [AuthController::class, 'verificarDisponibilidad']);

// Sin estadísticas públicas

// ==================== RUTAS PROTEGIDAS ====================

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Auth (usuario autenticado)
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::get('/auth/estadisticas-registros', [AuthController::class, 'estadisticasRegistros']);
    Route::post('/auth/limpiar-registros-antiguos', [AuthController::class, 'limpiarRegistrosAntiguos']);
    
    // Usuarios
    Route::apiResource('users', UserController::class);
    
    // Proyectos
    Route::apiResource('proyectos', ProyectoController::class);
    
    // Actividades (Estilo MS Project)
    Route::apiResource('actividades', ActividadController::class);
    Route::get('/proyectos/{proyecto}/actividades', [ActividadController::class, 'porProyecto']);
    Route::post('/actividades/{id}/agregar-pdf', [ActividadController::class, 'agregarPDF']);
    
    // Tareas (Jerárquicas)
    Route::apiResource('tareas', TareaController::class);
    Route::get('/proyectos/{proyecto}/tareas', [TareaController::class, 'tareasPorProyecto']);
    Route::get('/actividades/{actividad}/tareas', [TareaController::class, 'tareasPorActividad']);
    Route::post('/tareas/{id}/agregar-pdf', [TareaController::class, 'agregarPDF']);
    Route::post('/tareas/{id}/agregar-soporte', [TareaController::class, 'agregarSoporte']);
    
    // Casos Legales
    Route::apiResource('casos-legales', CasoLegalController::class);
    
    // Análisis de IA
    Route::prefix('ia')->group(function () {
        Route::post('/analizar-juridico', [IAController::class, 'analizarJuridico']);
        Route::post('/analizar-etnico', [IAController::class, 'analizarCasoEtnico']);
        Route::post('/analizar-veeduria', [IAController::class, 'analizarVeeduriaCiudadana']);
        Route::get('/consultas', [IAController::class, 'listarConsultas']);
        Route::get('/consultas/{id}', [IAController::class, 'obtenerConsulta']);
        
        // Estadísticas y monitoreo
        Route::get('/estadisticas-centro', [IAController::class, 'estadisticasCentroIA']);
        Route::get('/estadisticas-monitor', [IAController::class, 'estadisticasMonitorIA']);
        Route::get('/metricas-rendimiento', [IAController::class, 'metricasRendimiento']);
        Route::get('/servicios-estado', [IAController::class, 'estadoServicios']);
    });
    
    // Derechos Étnicos
    Route::prefix('etnicos')->group(function () {
        Route::apiResource('pueblos-indigenas', PuebloIndigenaController::class);
        Route::apiResource('comunidades-afro', ComunidadAfroController::class);
        Route::apiResource('consultas-previas', ConsultaPreviaController::class);
    });
    
    // Veedurías
    Route::apiResource('veedurias', VeeduriaController::class);
    Route::get('/veedurias/{id}/seguimientos', [VeeduriaController::class, 'seguimientos']);
    Route::post('/veedurias/{id}/seguimientos', [VeeduriaController::class, 'crearSeguimiento']);
    
    // Donaciones
    Route::apiResource('donaciones', DonacionController::class);
    
    // Gestión de Permisos
    Route::prefix('permisos')->group(function () {
        // Listar permisos de un usuario
        Route::get('/usuario/{userId}', [PermisosController::class, 'listarPermisosUsuario']);
        
        // Verificar permiso específico
        Route::get('/verificar/{userId}/{tipoPermiso}', [PermisosController::class, 'verificarPermiso']);
        
        // Permisos por rol
        Route::get('/rol/{rol}', [PermisosController::class, 'permisosRol']);
        
        // CRUD de permisos (Solo Administradores)
        Route::middleware(['can:gestionar_permisos'])->group(function () {
            Route::post('/otorgar', [PermisosController::class, 'otorgarPermiso']);
            Route::put('/{permisoId}', [PermisosController::class, 'actualizarPermiso']);
            Route::post('/{permisoId}/vetar', [PermisosController::class, 'vetarPermiso']);
            Route::post('/{permisoId}/activar', [PermisosController::class, 'activarPermiso']);
            Route::delete('/{permisoId}', [PermisosController::class, 'eliminarPermiso']);
        });
        
        // Historial
        Route::get('/{permisoId}/historial', [PermisosController::class, 'historialPermiso']);
    });
    
    // Dashboard simplificado (sin estadísticas)
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'message' => 'Dashboard cargado correctamente'
            ]
        ]);
    });
});

