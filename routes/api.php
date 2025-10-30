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
use App\Http\Controllers\Api\AlertasController;
use App\Http\Controllers\Api\InstitucionController;
use App\Http\Controllers\Api\MenuPAEController;
use App\Http\Controllers\Api\EntregaPAEController;
use App\Http\Controllers\Api\IncidenciaPAEController;
use App\Http\Controllers\Api\CAEComiteController;
use App\Http\Controllers\Api\CAEActaController;
use App\Http\Controllers\Api\CAESeguimientoController;

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

    // ==================== ADMIN - ESTADO DEL SISTEMA ====================
    Route::prefix('admin/sistema')->group(function () {
        Route::get('/estado', function () {
            try {
                $queueDefault = config('queue.default');
                $hasAI = !empty(config('services.openai.api_key')) || !empty(config('services.anthropic.api_key'));
                $storageOk = is_dir(storage_path()) && is_writable(storage_path());

                return response()->json([
                    'success' => true,
                    'data' => [
                        'pdf' => true, // cliente puede generar PDF; backend opcional
                        'ia' => (bool) $hasAI,
                        'storage' => (bool) $storageOk,
                        'cola' => (bool) $queueDefault,
                        'worker' => false, // requiere verificación con cache/heartbeat si se habilita
                        'version' => app()->version(),
                        'queue_default' => $queueDefault,
                    ]
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'No fue posible obtener el estado del sistema',
                    'error' => $e->getMessage()
                ], 500);
            }
        });

        Route::post('/probar-cola', function () {
            try {
                dispatch(function () {
                    \Log::info('[CSDT] Job de prueba ejecutado correctamente');
                })->onQueue(config('queue.connections.database.queue', 'default'));

                return response()->json([
                    'success' => true,
                    'message' => 'Job de prueba encolado'
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error encolando job de prueba',
                    'error' => $e->getMessage()
                ], 500);
            }
        });

        // Disparar alertas tempranas manualmente
        Route::post('/generar-alertas', function () {
            try {
                dispatch(new \App\Jobs\GenerarAlertasTempranasJob());
                return response()->json([
                    'success' => true,
                    'message' => 'Job de alertas tempranas encolado'
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error encolando job de alertas',
                    'error' => $e->getMessage()
                ], 500);
            }
        });

        // Configuración runtime de alertas (cache)
        Route::post('/alerts-email', function (Request $request) {
            try {
                $enabled = (bool) $request->get('enabled', false);
                \Cache::put('alerts_email_enabled', $enabled, 86400);
                return response()->json(['success' => true, 'enabled' => $enabled]);
            } catch (\Throwable $e) {
                return response()->json(['success' => false, 'message' => 'Error actualizando configuración', 'error' => $e->getMessage()], 500);
            }
        });
    });

    // ==================== PDF BÁSICO (SERVER-SIDE) ====================
    Route::prefix('pdf')->group(function () {
        Route::get('/proyecto/{id}', function ($id) {
            $html = '<html><body><h1>Proyecto #'.htmlspecialchars($id).'</h1><p>Documento generado por CSDT.</p></body></html>';
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="proyecto_'.$id.'.pdf"'
            ]);
        });

        Route::get('/actividad/{id}', function ($id) {
            $html = '<html><body><h1>Dependencia #'.htmlspecialchars($id).'</h1><p>Documento generado por CSDT.</p></body></html>';
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="dependencia_'.$id.'.pdf"'
            ]);
        });

        Route::get('/tarea/{id}', function ($id) {
            $html = '<html><body><h1>Cola #'.htmlspecialchars($id).'</h1><p>Documento generado por CSDT.</p></body></html>';
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="cola_'.$id.'.pdf"'
            ]);
        });

        // Plantillas específicas
        Route::get('/acta-cae/{id}', function ($id) {
            $html = '<html><body><h1>Acta CAE #'.htmlspecialchars($id).'</h1><p>Acta del Comité de Alimentación Escolar.</p></body></html>';
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="acta_cae_'.$id.'.pdf"'
            ]);
        });

        Route::get('/denuncia/{id}', function ($id) {
            $html = '<html><body><h1>Denuncia/Corrección #'.htmlspecialchars($id).'</h1><p>Detalle de denuncia o corrección PAE.</p></body></html>';
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="denuncia_'.$id.'.pdf"'
            ]);
        });

        Route::get('/hallazgos/{id}', function ($id) {
            $html = '<html><body><h1>Hallazgos #'.htmlspecialchars($id).'</h1><p>Informe de veeduría y hallazgos.</p></body></html>';
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="hallazgos_'.$id.'.pdf"'
            ]);
        });
    });

    // ==================== ALERTAS PAE ====================
    Route::prefix('alertas')->group(function () {
        Route::get('/', [AlertasController::class, 'index']);
        Route::get('/{id}', [AlertasController::class, 'show']);
        Route::put('/{id}', [AlertasController::class, 'update']);
    });

    // ==================== PAE ====================
    Route::prefix('pae')->group(function () {
        Route::apiResource('instituciones', InstitucionController::class);
        Route::apiResource('menus', MenuPAEController::class);
        Route::apiResource('entregas', EntregaPAEController::class);
        Route::apiResource('incidencias', IncidenciaPAEController::class);
    });

    // ==================== CAE ====================
    Route::prefix('cae')->group(function () {
        Route::apiResource('comites', CAEComiteController::class);
        Route::apiResource('actas', CAEActaController::class);
        Route::apiResource('seguimientos', CAESeguimientoController::class);
    });
});

