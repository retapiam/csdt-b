<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    UsuarioControllerCompleto,
    VeeduriaControllerCompleto,
    RolController,
    PermisoController,
    DonacionController,
    TareaController,
    ArchivoController,
    ConfiguracionController,
    LogController,
    PaginaController,
    PQRSFDController,
    AnalisisIAController,
    NarracionIAController,
    EstadisticasController
};

/*
|--------------------------------------------------------------------------
| API Routes - CSDT Sistema Completo
|--------------------------------------------------------------------------
|
| Rutas API completas implementando nomenclatura estándar según CONTROL.md
| Todas las rutas están protegidas con autenticación Sanctum
|
*/

// Rutas públicas
Route::prefix('v1')->group(function () {
    
    // Autenticación
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    // Rutas públicas para información general
    Route::get('configuracion/publica', [ConfiguracionController::class, 'configuracionPublica']);
    Route::get('estadisticas/publicas', [EstadisticasController::class, 'estadisticasPublicas']);
    Route::get('paginas/activas', [PaginaController::class, 'paginasActivas']);
});

// Rutas protegidas
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Autenticación protegida
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
    });

    // ========================================
    // GESTIÓN DE USUARIOS
    // ========================================
    Route::prefix('usuarios')->group(function () {
        // CRUD básico
        Route::get('/', [UsuarioControllerCompleto::class, 'index']);
        Route::post('/', [UsuarioControllerCompleto::class, 'store']);
        Route::get('/{id}', [UsuarioControllerCompleto::class, 'show']);
        Route::put('/{id}', [UsuarioControllerCompleto::class, 'update']);
        Route::patch('/{id}', [UsuarioControllerCompleto::class, 'update']);
        Route::delete('/{id}', [UsuarioControllerCompleto::class, 'destroy']);
        
        // Operaciones adicionales
        Route::get('/buscar', [UsuarioControllerCompleto::class, 'buscar']);
        Route::post('/validar', [UsuarioControllerCompleto::class, 'validar']);
        Route::post('/{id}/restaurar', [UsuarioControllerCompleto::class, 'restaurar']);
        Route::post('/{id}/activar', [UsuarioControllerCompleto::class, 'activar']);
        Route::post('/{id}/desactivar', [UsuarioControllerCompleto::class, 'desactivar']);
        Route::post('/{id}/verificar-correo', [UsuarioControllerCompleto::class, 'verificarCorreo']);
        
        // Estadísticas y reportes
        Route::get('/estadisticas/generales', [UsuarioControllerCompleto::class, 'estadisticas']);
        Route::get('/activos', [UsuarioControllerCompleto::class, 'usuariosActivos']);
        Route::get('/exportar', [UsuarioControllerCompleto::class, 'exportar']);
    });

    // ========================================
    // GESTIÓN DE ROLES
    // ========================================
    Route::prefix('roles')->group(function () {
        // CRUD básico
        Route::get('/', [RolController::class, 'index']);
        Route::post('/', [RolController::class, 'store']);
        Route::get('/{id}', [RolController::class, 'show']);
        Route::put('/{id}', [RolController::class, 'update']);
        Route::patch('/{id}', [RolController::class, 'update']);
        Route::delete('/{id}', [RolController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::get('/buscar', [RolController::class, 'buscar']);
        Route::post('/{id}/activar', [RolController::class, 'activar']);
        Route::post('/{id}/desactivar', [RolController::class, 'desactivar']);
        Route::post('/{id}/restaurar', [RolController::class, 'restaurar']);
        
        // Gestión de permisos
        Route::get('/{id}/permisos', [RolController::class, 'permisos']);
        Route::post('/{id}/permisos', [RolController::class, 'asignarPermisos']);
        Route::delete('/{id}/permisos/{permiso_id}', [RolController::class, 'quitarPermiso']);
        
        // Gestión de usuarios
        Route::get('/{id}/usuarios', [RolController::class, 'usuarios']);
        Route::post('/{id}/usuarios/{usuario_id}', [RolController::class, 'asignarUsuario']);
        Route::delete('/{id}/usuarios/{usuario_id}', [RolController::class, 'quitarUsuario']);
    });

    // ========================================
    // GESTIÓN DE PERMISOS
    // ========================================
    Route::prefix('permisos')->group(function () {
        // CRUD básico
        Route::get('/', [PermisoController::class, 'index']);
        Route::post('/', [PermisoController::class, 'store']);
        Route::get('/{id}', [PermisoController::class, 'show']);
        Route::put('/{id}', [PermisoController::class, 'update']);
        Route::patch('/{id}', [PermisoController::class, 'update']);
        Route::delete('/{id}', [PermisoController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::get('/buscar', [PermisoController::class, 'buscar']);
        Route::get('/por-modulo/{modulo}', [PermisoController::class, 'porModulo']);
        Route::get('/por-recurso/{recurso}', [PermisoController::class, 'porRecurso']);
        Route::post('/{id}/activar', [PermisoController::class, 'activar']);
        Route::post('/{id}/desactivar', [PermisoController::class, 'desactivar']);
        
        // Generación automática
        Route::post('/generar-crud', [PermisoController::class, 'generarPermisosCRUD']);
        Route::post('/generar-especiales', [PermisoController::class, 'generarPermisosEspeciales']);
    });

    // ========================================
    // GESTIÓN DE VEEDURÍAS
    // ========================================
    Route::prefix('veedurias')->group(function () {
        // CRUD básico
        Route::get('/', [VeeduriaControllerCompleto::class, 'index']);
        Route::post('/', [VeeduriaControllerCompleto::class, 'store']);
        Route::get('/{id}', [VeeduriaControllerCompleto::class, 'show']);
        Route::put('/{id}', [VeeduriaControllerCompleto::class, 'update']);
        Route::patch('/{id}', [VeeduriaControllerCompleto::class, 'update']);
        Route::delete('/{id}', [VeeduriaControllerCompleto::class, 'destroy']);
        
        // Operaciones adicionales
        Route::get('/buscar', [VeeduriaControllerCompleto::class, 'buscar']);
        Route::post('/{id}/restaurar', [VeeduriaControllerCompleto::class, 'restaurar']);
        
        // Gestión de estados
        Route::post('/{id}/radicar', [VeeduriaControllerCompleto::class, 'radicar']);
        Route::post('/{id}/cerrar', [VeeduriaControllerCompleto::class, 'cerrar']);
        Route::post('/{id}/cancelar', [VeeduriaControllerCompleto::class, 'cancelar']);
        
        // Gestión de operadores
        Route::post('/{id}/asignar-operador', [VeeduriaControllerCompleto::class, 'asignarOperador']);
        
        // IA y análisis
        Route::post('/{id}/recomendacion-ia', [VeeduriaControllerCompleto::class, 'agregarRecomendacionIA']);
        
        // Estadísticas y reportes
        Route::get('/estadisticas/generales', [VeeduriaControllerCompleto::class, 'estadisticas']);
        Route::get('/{id}/estadisticas', [VeeduriaControllerCompleto::class, 'estadisticasVeeduria']);
        Route::get('/exportar', [VeeduriaControllerCompleto::class, 'exportar']);
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
        Route::patch('/{id}', [TareaController::class, 'update']);
        Route::delete('/{id}', [TareaController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::get('/buscar', [TareaController::class, 'buscar']);
        Route::post('/{id}/restaurar', [TareaController::class, 'restaurar']);
        
        // Gestión de estados
        Route::post('/{id}/iniciar', [TareaController::class, 'iniciar']);
        Route::post('/{id}/completar', [TareaController::class, 'completar']);
        Route::post('/{id}/cancelar', [TareaController::class, 'cancelar']);
        Route::post('/{id}/suspender', [TareaController::class, 'suspender']);
        Route::post('/{id}/reanudar', [TareaController::class, 'reanudar']);
        
        // Gestión de asignaciones
        Route::post('/{id}/asignar', [TareaController::class, 'asignar']);
        
        // Consultas especiales
        Route::get('/vencidas', [TareaController::class, 'tareasVencidas']);
        Route::get('/por-vencer', [TareaController::class, 'tareasPorVencer']);
        Route::get('/por-usuario/{usuario_id}', [TareaController::class, 'tareasPorUsuario']);
        Route::get('/por-veeduria/{veeduria_id}', [TareaController::class, 'tareasPorVeeduria']);
        
        // Estadísticas
        Route::get('/estadisticas', [TareaController::class, 'estadisticas']);
        Route::get('/exportar', [TareaController::class, 'exportar']);
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
        Route::patch('/{id}', [DonacionController::class, 'update']);
        Route::delete('/{id}', [DonacionController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::get('/buscar', [DonacionController::class, 'buscar']);
        Route::post('/{id}/restaurar', [DonacionController::class, 'restaurar']);
        
        // Gestión de estados
        Route::post('/{id}/confirmar', [DonacionController::class, 'confirmar']);
        Route::post('/{id}/rechazar', [DonacionController::class, 'rechazar']);
        Route::post('/{id}/cancelar', [DonacionController::class, 'cancelar']);
        Route::post('/{id}/procesar', [DonacionController::class, 'procesar']);
        
        // Consultas especiales
        Route::get('/por-usuario/{usuario_id}', [DonacionController::class, 'donacionesPorUsuario']);
        Route::get('/por-veeduria/{veeduria_id}', [DonacionController::class, 'donacionesPorVeeduria']);
        Route::get('/top-donadores', [DonacionController::class, 'topDonadores']);
        Route::get('/por-mes', [DonacionController::class, 'donacionesPorMes']);
        
        // Estadísticas
        Route::get('/estadisticas', [DonacionController::class, 'estadisticas']);
        Route::get('/exportar', [DonacionController::class, 'exportar']);
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
        Route::patch('/{id}', [ArchivoController::class, 'update']);
        Route::delete('/{id}', [ArchivoController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::get('/buscar', [ArchivoController::class, 'buscar']);
        Route::post('/{id}/restaurar', [ArchivoController::class, 'restaurar']);
        Route::post('/{id}/activar', [ArchivoController::class, 'activar']);
        Route::post('/{id}/desactivar', [ArchivoController::class, 'desactivar']);
        
        // Gestión de archivos
        Route::post('/subir', [ArchivoController::class, 'subir']);
        Route::get('/{id}/descargar', [ArchivoController::class, 'descargar']);
        Route::get('/{id}/vista-previa', [ArchivoController::class, 'vistaPrevia']);
        Route::post('/{id}/mover', [ArchivoController::class, 'mover']);
        Route::post('/{id}/copiar', [ArchivoController::class, 'copiar']);
        
        // Verificación de integridad
        Route::post('/{id}/verificar-integridad', [ArchivoController::class, 'verificarIntegridad']);
        Route::post('/{id}/generar-hash', [ArchivoController::class, 'generarHash']);
        Route::post('/verificar-todos', [ArchivoController::class, 'verificarIntegridadTodos']);
        
        // Consultas especiales
        Route::get('/por-usuario/{usuario_id}', [ArchivoController::class, 'archivosPorUsuario']);
        Route::get('/por-veeduria/{veeduria_id}', [ArchivoController::class, 'archivosPorVeeduria']);
        Route::get('/por-tarea/{tarea_id}', [ArchivoController::class, 'archivosPorTarea']);
        Route::get('/imagenes', [ArchivoController::class, 'imagenes']);
        Route::get('/documentos', [ArchivoController::class, 'documentos']);
        
        // Mantenimiento
        Route::post('/limpiar-eliminados', [ArchivoController::class, 'limpiarEliminados']);
        Route::post('/generar-hashes-faltantes', [ArchivoController::class, 'generarHashesFaltantes']);
        
        // Estadísticas
        Route::get('/estadisticas', [ArchivoController::class, 'estadisticas']);
        Route::get('/exportar', [ArchivoController::class, 'exportar']);
    });

    // ========================================
    // CONFIGURACIÓN DEL SISTEMA
    // ========================================
    Route::prefix('configuracion')->group(function () {
        // CRUD básico
        Route::get('/', [ConfiguracionController::class, 'index']);
        Route::post('/', [ConfiguracionController::class, 'store']);
        Route::get('/{id}', [ConfiguracionController::class, 'show']);
        Route::put('/{id}', [ConfiguracionController::class, 'update']);
        Route::patch('/{id}', [ConfiguracionController::class, 'update']);
        Route::delete('/{id}', [ConfiguracionController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::get('/buscar', [ConfiguracionController::class, 'buscar']);
        Route::get('/por-categoria/{categoria}', [ConfiguracionController::class, 'porCategoria']);
        Route::post('/{id}/activar', [ConfiguracionController::class, 'activar']);
        Route::post('/{id}/desactivar', [ConfiguracionController::class, 'desactivar']);
        
        // Gestión de configuraciones
        Route::get('/clave/{clave}', [ConfiguracionController::class, 'porClave']);
        Route::post('/clave/{clave}', [ConfiguracionController::class, 'actualizarPorClave']);
        Route::post('/masiva', [ConfiguracionController::class, 'actualizacionMasiva']);
        
        // Backup y restauración
        Route::post('/backup', [ConfiguracionController::class, 'crearBackup']);
        Route::post('/restaurar', [ConfiguracionController::class, 'restaurarBackup']);
        
        // Configuraciones públicas
        Route::get('/publicas', [ConfiguracionController::class, 'configuracionPublica']);
    });

    // ========================================
    // LOGS Y AUDITORÍA
    // ========================================
    Route::prefix('logs')->group(function () {
        // CRUD básico
        Route::get('/', [LogController::class, 'index']);
        Route::get('/{id}', [LogController::class, 'show']);
        
        // Operaciones adicionales
        Route::get('/buscar', [LogController::class, 'buscar']);
        Route::get('/por-usuario/{usuario_id}', [LogController::class, 'logsPorUsuario']);
        Route::get('/por-tabla/{tabla}', [LogController::class, 'logsPorTabla']);
        Route::get('/por-accion/{accion}', [LogController::class, 'logsPorAccion']);
        
        // Consultas especiales
        Route::get('/recientes', [LogController::class, 'logsRecientes']);
        Route::get('/por-fecha', [LogController::class, 'logsPorFecha']);
        Route::get('/actividad-usuario/{usuario_id}', [LogController::class, 'actividadUsuario']);
        
        // Estadísticas
        Route::get('/estadisticas', [LogController::class, 'estadisticas']);
        Route::get('/exportar', [LogController::class, 'exportar']);
        
        // Mantenimiento
        Route::post('/limpiar', [LogController::class, 'limpiarLogs']);
    });

    // ========================================
    // GESTIÓN DE PÁGINAS
    // ========================================
    Route::prefix('paginas')->group(function () {
        // CRUD básico
        Route::get('/', [PaginaController::class, 'index']);
        Route::post('/', [PaginaController::class, 'store']);
        Route::get('/{id}', [PaginaController::class, 'show']);
        Route::put('/{id}', [PaginaController::class, 'update']);
        Route::patch('/{id}', [PaginaController::class, 'update']);
        Route::delete('/{id}', [PaginaController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::get('/buscar', [PaginaController::class, 'buscar']);
        Route::post('/{id}/activar', [PaginaController::class, 'activar']);
        Route::post('/{id}/desactivar', [PaginaController::class, 'desactivar']);
        
        // Gestión de permisos
        Route::get('/{id}/permisos', [PaginaController::class, 'permisos']);
        Route::post('/{id}/permisos', [PaginaController::class, 'asignarPermisos']);
        Route::delete('/{id}/permisos/{permiso_id}', [PaginaController::class, 'quitarPermiso']);
        
        // Gestión de roles
        Route::get('/{id}/roles', [PaginaController::class, 'roles']);
        Route::post('/{id}/roles', [PaginaController::class, 'asignarRoles']);
        Route::delete('/{id}/roles/{rol_id}', [PaginaController::class, 'quitarRol']);
        
        // Páginas públicas
        Route::get('/publicas/activas', [PaginaController::class, 'paginasActivas']);
    });

    // ========================================
    // GESTIÓN DE PQRSFD
    // ========================================
    Route::prefix('pqrsfd')->group(function () {
        // CRUD básico
        Route::get('/', [PQRSFDController::class, 'index']);
        Route::post('/', [PQRSFDController::class, 'store']);
        Route::get('/{id}', [PQRSFDController::class, 'show']);
        Route::put('/{id}', [PQRSFDController::class, 'update']);
        Route::patch('/{id}', [PQRSFDController::class, 'update']);
        Route::delete('/{id}', [PQRSFDController::class, 'destroy']);
        
        // Operaciones adicionales
        Route::get('/buscar', [PQRSFDController::class, 'buscar']);
        Route::post('/{id}/restaurar', [PQRSFDController::class, 'restaurar']);
        
        // Gestión de estados
        Route::post('/{id}/radicar', [PQRSFDController::class, 'radicar']);
        Route::post('/{id}/responder', [PQRSFDController::class, 'responder']);
        Route::post('/{id}/cerrar', [PQRSFDController::class, 'cerrar']);
        
        // Consultas especiales
        Route::get('/por-usuario/{usuario_id}', [PQRSFDController::class, 'pqrsfdPorUsuario']);
        Route::get('/por-tipo/{tipo}', [PQRSFDController::class, 'pqrsfdPorTipo']);
        Route::get('/pendientes', [PQRSFDController::class, 'pqrsfdPendientes']);
        Route::get('/vencidas', [PQRSFDController::class, 'pqrsfdVencidas']);
        
        // Estadísticas
        Route::get('/estadisticas', [PQRSFDController::class, 'estadisticas']);
        Route::get('/exportar', [PQRSFDController::class, 'exportar']);
    });

    // ========================================
    // INTELIGENCIA ARTIFICIAL
    // ========================================
    Route::prefix('ia')->group(function () {
        
        // Análisis de IA
        Route::prefix('analisis')->group(function () {
            Route::get('/', [AnalisisIAController::class, 'index']);
            Route::post('/', [AnalisisIAController::class, 'store']);
            Route::get('/{id}', [AnalisisIAController::class, 'show']);
            Route::put('/{id}', [AnalisisIAController::class, 'update']);
            Route::patch('/{id}', [AnalisisIAController::class, 'update']);
            Route::delete('/{id}', [AnalisisIAController::class, 'destroy']);
            
            // Operaciones específicas
            Route::post('/analizar', [AnalisisIAController::class, 'analizar']);
            Route::get('/buscar', [AnalisisIAController::class, 'buscar']);
            Route::get('/por-usuario/{usuario_id}', [AnalisisIAController::class, 'analisisPorUsuario']);
            Route::get('/por-veeduria/{veeduria_id}', [AnalisisIAController::class, 'analisisPorVeeduria']);
            Route::get('/estadisticas', [AnalisisIAController::class, 'estadisticas']);
        });
        
        // Narraciones de IA
        Route::prefix('narraciones')->group(function () {
            Route::get('/', [NarracionIAController::class, 'index']);
            Route::post('/', [NarracionIAController::class, 'store']);
            Route::get('/{id}', [NarracionIAController::class, 'show']);
            Route::put('/{id}', [NarracionIAController::class, 'update']);
            Route::patch('/{id}', [NarracionIAController::class, 'update']);
            Route::delete('/{id}', [NarracionIAController::class, 'destroy']);
            
            // Operaciones específicas
            Route::post('/generar', [NarracionIAController::class, 'generar']);
            Route::get('/buscar', [NarracionIAController::class, 'buscar']);
            Route::get('/por-codigo/{codigo}', [NarracionIAController::class, 'porCodigo']);
            Route::get('/por-usuario/{usuario_id}', [NarracionIAController::class, 'narracionesPorUsuario']);
            Route::get('/estadisticas', [NarracionIAController::class, 'estadisticas']);
        });
        
        // Estadísticas generales de IA
        Route::get('/estadisticas', [EstadisticasController::class, 'estadisticasIA']);
        Route::get('/metricas', [EstadisticasController::class, 'metricasIA']);
    });

    // ========================================
    // ESTADÍSTICAS GENERALES
    // ========================================
    Route::prefix('estadisticas')->group(function () {
        Route::get('/dashboard', [EstadisticasController::class, 'dashboard']);
        Route::get('/generales', [EstadisticasController::class, 'generales']);
        Route::get('/por-modulo/{modulo}', [EstadisticasController::class, 'porModulo']);
        Route::get('/tendencias', [EstadisticasController::class, 'tendencias']);
        Route::get('/comparativas', [EstadisticasController::class, 'comparativas']);
        Route::get('/exportar', [EstadisticasController::class, 'exportar']);
    });

    // ========================================
    // RUTAS ESPECIALES Y UTILIDADES
    // ========================================
    Route::prefix('utilidades')->group(function () {
        // Búsqueda global
        Route::get('/buscar-global', function (Request $request) {
            // Implementar búsqueda global en todas las tablas
        });
        
        // Validaciones generales
        Route::post('/validar-email', function (Request $request) {
            // Validar disponibilidad de email
        });
        
        Route::post('/validar-documento', function (Request $request) {
            // Validar disponibilidad de documento
        });
        
        // Generación de códigos
        Route::get('/generar-codigo/{tipo}', function (Request $request, $tipo) {
            // Generar códigos únicos según el tipo
        });
        
        // Limpieza de datos
        Route::post('/limpiar-cache', function () {
            // Limpiar cache del sistema
        });
        
        Route::post('/optimizar-base-datos', function () {
            // Optimizar base de datos
        });
    });
});

// ========================================
// RUTAS DE DESARROLLO (solo en desarrollo)
// ========================================
if (config('app.debug')) {
    Route::prefix('v1/dev')->middleware('auth:sanctum')->group(function () {
        
        // Generación de datos de prueba
        Route::post('/generar-datos-prueba', function () {
            // Generar datos de prueba para desarrollo
        });
        
        // Limpieza de base de datos
        Route::post('/limpiar-datos', function () {
            // Limpiar datos de desarrollo
        });
        
        // Información del sistema
        Route::get('/info-sistema', function () {
            // Información detallada del sistema
        });
        
        // Logs de debug
        Route::get('/debug-logs', [LogController::class, 'debugLogs']);
    });
}
