<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    UsuarioController,
    VeeduriaController,
    DonacionController,
    TareaController,
    ArchivoController,
    RolController,
    PermisoController,
    ConfiguracionController,
    LogController,
    DashboardController,
    AuthController,
    EstadisticasController
};

/*
|--------------------------------------------------------------------------
| API Routes - CSDT Sistema Mejorado
|--------------------------------------------------------------------------
|
| Rutas API optimizadas con funcionalidades CRUD completas
| Implementa nomenclatura estándar y mejores prácticas
|
*/

// ========================================
// RUTAS PÚBLICAS
// ========================================
Route::prefix('v2')->group(function () {
    
    // Verificación de salud
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'API CSDT v2.0 funcionando correctamente',
            'timestamp' => now()->toISOString(),
            'version' => '2.0.0',
            'status' => 'operational'
        ]);
    });

    // Autenticación pública
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    });

    // Datos de referencia públicos
    Route::prefix('publico')->group(function () {
        Route::get('/tipos-veeduria', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'pet' => 'Petición',
                    'que' => 'Queja',
                    'rec' => 'Reclamo',
                    'sug' => 'Sugerencia',
                    'fel' => 'Felicitación',
                    'den' => 'Denuncia',
                ]
            ]);
        });

        Route::get('/estados-veeduria', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'pen' => 'Pendiente',
                    'pro' => 'En Proceso',
                    'rad' => 'Radicada',
                    'cer' => 'Cerrada',
                    'can' => 'Cancelada',
                ]
            ]);
        });

        Route::get('/prioridades-tarea', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'baj' => 'Baja',
                    'med' => 'Media',
                    'alt' => 'Alta',
                    'urg' => 'Urgente',
                ]
            ]);
        });

        Route::get('/categorias-veeduria', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'inf' => 'Infraestructura',
                    'ser' => 'Servicios Públicos',
                    'seg' => 'Seguridad',
                    'edu' => 'Educación',
                    'sal' => 'Salud',
                    'tra' => 'Transporte',
                    'amb' => 'Medio Ambiente',
                    'otr' => 'Otros',
                ]
            ]);
        });

        Route::get('/tipos-documento', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'cc' => 'Cédula de Ciudadanía',
                    'ce' => 'Cédula de Extranjería',
                    'ti' => 'Tarjeta de Identidad',
                    'pp' => 'Pasaporte',
                    'nit' => 'NIT',
                ]
            ]);
        });

        Route::get('/generos', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'm' => 'Masculino',
                    'f' => 'Femenino',
                    'o' => 'Otro',
                    'n' => 'No Especificado',
                ]
            ]);
        });
    });
});

// ========================================
// RUTAS PROTEGIDAS
// ========================================
Route::prefix('v2')->middleware('auth:sanctum')->group(function () {
    
    // Autenticación protegida
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    });

    // ========================================
    // GESTIÓN DE USUARIOS
    // ========================================
    Route::prefix('usuarios')->group(function () {
        // CRUD básico
        Route::get('/', [UsuarioController::class, 'index']);
        Route::post('/', [UsuarioController::class, 'store']);
        Route::get('/{id}', [UsuarioController::class, 'show']);
        Route::put('/{id}', [UsuarioController::class, 'update']);
        Route::delete('/{id}', [UsuarioController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::post('/{id}/restaurar', [UsuarioController::class, 'restore']);
        Route::put('/{id}/cambiar-estado', [UsuarioController::class, 'cambiarEstado']);
        Route::post('/{id}/verificar-correo', [UsuarioController::class, 'verificarCorreo']);
        Route::put('/{id}/cambiar-contrasena', [UsuarioController::class, 'cambiarContrasena']);
        
        // Búsqueda y validación
        Route::get('/buscar', [UsuarioController::class, 'buscar']);
        Route::post('/validar', [UsuarioController::class, 'validar']);
        
        // Estadísticas
        Route::get('/estadisticas/generales', [UsuarioController::class, 'estadisticas']);
        Route::get('/{id}/estadisticas', [UsuarioController::class, 'estadisticas']);
        
        // Exportación
        Route::get('/exportar', [UsuarioController::class, 'exportar']);
        
        // Filtros específicos
        Route::get('/activos', [UsuarioController::class, 'index'])->where('est', 'act');
        Route::get('/inactivos', [UsuarioController::class, 'index'])->where('est', 'ina');
        Route::get('/pendientes', [UsuarioController::class, 'index'])->where('est', 'pen');
        Route::get('/suspendidos', [UsuarioController::class, 'index'])->where('est', 'sus');
        Route::get('/clientes', [UsuarioController::class, 'index'])->where('rol', 'cli');
        Route::get('/operadores', [UsuarioController::class, 'index'])->where('rol', 'ope');
        Route::get('/administradores', [UsuarioController::class, 'index'])->where('rol', 'adm');
    });

    // ========================================
    // GESTIÓN DE VEEDURÍAS
    // ========================================
    Route::prefix('veedurias')->group(function () {
        // CRUD básico
        Route::get('/', [VeeduriaController::class, 'index']);
        Route::post('/', [VeeduriaController::class, 'store']);
        Route::get('/{id}', [VeeduriaController::class, 'show']);
        Route::put('/{id}', [VeeduriaController::class, 'update']);
        Route::delete('/{id}', [VeeduriaController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::post('/{id}/restaurar', [VeeduriaController::class, 'restaurar']);
        Route::post('/{id}/radicar', [VeeduriaController::class, 'radicar']);
        Route::post('/{id}/cerrar', [VeeduriaController::class, 'cerrar']);
        Route::post('/{id}/cancelar', [VeeduriaController::class, 'cancelar']);
        Route::post('/{id}/asignar-operador', [VeeduriaController::class, 'asignarOperador']);
        Route::put('/{id}/cambiar-prioridad', [VeeduriaController::class, 'cambiarPrioridad']);
        Route::put('/{id}/actualizar-presupuesto', [VeeduriaController::class, 'actualizarPresupuesto']);
        
        // Búsqueda y validación
        Route::get('/buscar', [VeeduriaController::class, 'buscar']);
        Route::post('/validar', [VeeduriaController::class, 'validar']);
        
        // Estadísticas
        Route::get('/estadisticas/generales', [VeeduriaController::class, 'estadisticas']);
        Route::get('/{id}/estadisticas', [VeeduriaController::class, 'estadisticasVeeduria']);
        
        // Exportación
        Route::get('/exportar', [VeeduriaController::class, 'exportar']);
        
        // Filtros específicos
        Route::get('/pendientes', [VeeduriaController::class, 'index'])->where('est', 'pen');
        Route::get('/en-proceso', [VeeduriaController::class, 'index'])->where('est', 'pro');
        Route::get('/radicadas', [VeeduriaController::class, 'index'])->where('est', 'rad');
        Route::get('/cerradas', [VeeduriaController::class, 'index'])->where('est', 'cer');
        Route::get('/canceladas', [VeeduriaController::class, 'index'])->where('est', 'can');
        Route::get('/vencidas', [VeeduriaController::class, 'vencidas']);
        Route::get('/por-vencer', [VeeduriaController::class, 'porVencer']);
        Route::get('/con-presupuesto', [VeeduriaController::class, 'conPresupuesto']);
        Route::get('/con-ubicacion', [VeeduriaController::class, 'conUbicacion']);
    });

    // ========================================
    // GESTIÓN DE DONACIONES
    // ========================================
    Route::prefix('donaciones')->group(function () {
        // CRUD básico
        Route::get('/', [DonacionController::class, 'index']);
        Route::post('/', [DonacionController::class, 'store']);
        Route::get('/{id}', [DonacionController::class, 'show']);
        Route::put('/{id}', [DonacionController::class, 'update']);
        Route::delete('/{id}', [DonacionController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::post('/{id}/restaurar', [DonacionController::class, 'restaurar']);
        Route::post('/{id}/confirmar', [DonacionController::class, 'confirmar']);
        Route::post('/{id}/rechazar', [DonacionController::class, 'rechazar']);
        Route::post('/{id}/cancelar', [DonacionController::class, 'cancelar']);
        Route::post('/{id}/procesar', [DonacionController::class, 'procesar']);
        
        // Búsqueda y validación
        Route::get('/buscar', [DonacionController::class, 'buscar']);
        Route::post('/validar', [DonacionController::class, 'validar']);
        
        // Estadísticas
        Route::get('/estadisticas/generales', [DonacionController::class, 'estadisticas']);
        Route::get('/{id}/estadisticas', [DonacionController::class, 'estadisticasDonacion']);
        
        // Exportación
        Route::get('/exportar', [DonacionController::class, 'exportar']);
        
        // Filtros específicos
        Route::get('/pendientes', [DonacionController::class, 'index'])->where('est', 'pen');
        Route::get('/confirmadas', [DonacionController::class, 'index'])->where('est', 'con');
        Route::get('/rechazadas', [DonacionController::class, 'index'])->where('est', 'rec');
        Route::get('/canceladas', [DonacionController::class, 'index'])->where('est', 'can');
        Route::get('/por-usuario/{usuario_id}', [DonacionController::class, 'porUsuario']);
        Route::get('/por-veeduria/{veeduria_id}', [DonacionController::class, 'porVeeduria']);
        Route::get('/top-donadores', [DonacionController::class, 'topDonadores']);
        Route::get('/por-mes', [DonacionController::class, 'porMes']);
    });

    // ========================================
    // GESTIÓN DE TAREAS
    // ========================================
    Route::prefix('tareas')->group(function () {
        // CRUD básico
        Route::get('/', [TareaController::class, 'index']);
        Route::post('/', [TareaController::class, 'store']);
        Route::get('/{id}', [TareaController::class, 'show']);
        Route::put('/{id}', [TareaController::class, 'update']);
        Route::delete('/{id}', [TareaController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::post('/{id}/restaurar', [TareaController::class, 'restaurar']);
        Route::post('/{id}/iniciar', [TareaController::class, 'iniciar']);
        Route::post('/{id}/completar', [TareaController::class, 'completar']);
        Route::post('/{id}/cancelar', [TareaController::class, 'cancelar']);
        Route::post('/{id}/suspender', [TareaController::class, 'suspender']);
        Route::post('/{id}/reanudar', [TareaController::class, 'reanudar']);
        Route::post('/{id}/asignar', [TareaController::class, 'asignar']);
        Route::put('/{id}/actualizar-progreso', [TareaController::class, 'actualizarProgreso']);
        
        // Búsqueda y validación
        Route::get('/buscar', [TareaController::class, 'buscar']);
        Route::post('/validar', [TareaController::class, 'validar']);
        
        // Estadísticas
        Route::get('/estadisticas/generales', [TareaController::class, 'estadisticas']);
        Route::get('/{id}/estadisticas', [TareaController::class, 'estadisticasTarea']);
        
        // Exportación
        Route::get('/exportar', [TareaController::class, 'exportar']);
        
        // Filtros específicos
        Route::get('/pendientes', [TareaController::class, 'index'])->where('est', 'pen');
        Route::get('/en-proceso', [TareaController::class, 'index'])->where('est', 'pro');
        Route::get('/completadas', [TareaController::class, 'index'])->where('est', 'com');
        Route::get('/canceladas', [TareaController::class, 'index'])->where('est', 'can');
        Route::get('/suspendidas', [TareaController::class, 'index'])->where('est', 'sus');
        Route::get('/vencidas', [TareaController::class, 'vencidas']);
        Route::get('/por-vencer', [TareaController::class, 'porVencer']);
        Route::get('/por-usuario/{usuario_id}', [TareaController::class, 'porUsuario']);
        Route::get('/por-veeduria/{veeduria_id}', [TareaController::class, 'porVeeduria']);
    });

    // ========================================
    // GESTIÓN DE ARCHIVOS
    // ========================================
    Route::prefix('archivos')->group(function () {
        // CRUD básico
        Route::get('/', [ArchivoController::class, 'index']);
        Route::post('/', [ArchivoController::class, 'store']);
        Route::get('/{id}', [ArchivoController::class, 'show']);
        Route::put('/{id}', [ArchivoController::class, 'update']);
        Route::delete('/{id}', [ArchivoController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::post('/{id}/restaurar', [ArchivoController::class, 'restaurar']);
        Route::post('/{id}/activar', [ArchivoController::class, 'activar']);
        Route::post('/{id}/desactivar', [ArchivoController::class, 'desactivar']);
        Route::get('/{id}/descargar', [ArchivoController::class, 'descargar']);
        Route::get('/{id}/vista-previa', [ArchivoController::class, 'vistaPrevia']);
        Route::post('/{id}/mover', [ArchivoController::class, 'mover']);
        Route::post('/{id}/copiar', [ArchivoController::class, 'copiar']);
        Route::post('/{id}/verificar-integridad', [ArchivoController::class, 'verificarIntegridad']);
        
        // Búsqueda y validación
        Route::get('/buscar', [ArchivoController::class, 'buscar']);
        Route::post('/validar', [ArchivoController::class, 'validar']);
        
        // Estadísticas
        Route::get('/estadisticas/generales', [ArchivoController::class, 'estadisticas']);
        Route::get('/{id}/estadisticas', [ArchivoController::class, 'estadisticasArchivo']);
        
        // Exportación
        Route::get('/exportar', [ArchivoController::class, 'exportar']);
        
        // Filtros específicos
        Route::get('/activos', [ArchivoController::class, 'index'])->where('est', 'act');
        Route::get('/inactivos', [ArchivoController::class, 'index'])->where('est', 'ina');
        Route::get('/eliminados', [ArchivoController::class, 'index'])->where('est', 'eli');
        Route::get('/publicos', [ArchivoController::class, 'publicos']);
        Route::get('/privados', [ArchivoController::class, 'privados']);
        Route::get('/por-usuario/{usuario_id}', [ArchivoController::class, 'porUsuario']);
        Route::get('/por-veeduria/{veeduria_id}', [ArchivoController::class, 'porVeeduria']);
        Route::get('/por-tarea/{tarea_id}', [ArchivoController::class, 'porTarea']);
        Route::get('/imagenes', [ArchivoController::class, 'imagenes']);
        Route::get('/documentos', [ArchivoController::class, 'documentos']);
    });

    // ========================================
    // DASHBOARD Y ESTADÍSTICAS
    // ========================================
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'general']);
        Route::get('/administrador-general', [DashboardController::class, 'administradorGeneral']);
        Route::get('/administrador', [DashboardController::class, 'administrador']);
        Route::get('/operador', [DashboardController::class, 'operador']);
        Route::get('/cliente', [DashboardController::class, 'cliente']);
    });

    Route::prefix('estadisticas')->group(function () {
        Route::get('/generales', [EstadisticasController::class, 'generales']);
        Route::get('/por-modulo/{modulo}', [EstadisticasController::class, 'porModulo']);
        Route::get('/tendencias', [EstadisticasController::class, 'tendencias']);
        Route::get('/comparativas', [EstadisticasController::class, 'comparativas']);
    });

    // ========================================
    // EXPORTACIÓN GENERAL
    // ========================================
    Route::prefix('exportar')->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'exportar']);
        Route::get('/veedurias', [VeeduriaController::class, 'exportar']);
        Route::get('/donaciones', [DonacionController::class, 'exportar']);
        Route::get('/tareas', [TareaController::class, 'exportar']);
        Route::get('/archivos', [ArchivoController::class, 'exportar']);
        Route::get('/reporte-completo', [EstadisticasController::class, 'exportar']);
    });

    // ========================================
    // LOGS Y AUDITORÍA
    // ========================================
    Route::prefix('logs')->group(function () {
        Route::get('/', [LogController::class, 'index']);
        Route::get('/{id}', [LogController::class, 'show']);
        Route::get('/buscar', [LogController::class, 'buscar']);
        Route::get('/por-usuario/{usuario_id}', [LogController::class, 'porUsuario']);
        Route::get('/por-tabla/{tabla}', [LogController::class, 'porTabla']);
        Route::get('/por-accion/{accion}', [LogController::class, 'porAccion']);
        Route::get('/recientes', [LogController::class, 'recientes']);
        Route::get('/por-fecha', [LogController::class, 'porFecha']);
        Route::get('/estadisticas', [LogController::class, 'estadisticas']);
        Route::get('/exportar', [LogController::class, 'exportar']);
    });
});

// ========================================
// RUTAS DE FALLBACK
// ========================================
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Ruta no encontrada',
        'error' => 'La ruta solicitada no existe en la API v2.0',
        'version' => '2.0.0',
        'documentation' => '/api/v2/health'
    ], 404);
});
