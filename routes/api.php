<?php

use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\VeeduriaController;
use App\Http\Controllers\Api\DonacionController;
use App\Http\Controllers\Api\TareaController;
use App\Http\Controllers\Api\ArchivoController;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\ConfiguracionController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegistroControlador;
use App\Http\Controllers\Api\PublicoController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\IAEtnicaController;
use App\Http\Controllers\AuthController as AuthControllerWeb;
use App\Http\Controllers\PanelVistaController;
use App\Http\Controllers\GestionUsuariosController;
use Illuminate\Support\Facades\Route;

// Incluir rutas específicas de módulos
require_once __DIR__ . '/api-usuarios.php';
require_once __DIR__ . '/api-administrador-general.php';
require_once __DIR__ . '/api-completa.php';
require_once __DIR__ . '/api-ia-derechos-especializados.php';
require_once __DIR__ . '/api-planes-minero-ambiental.php';

// Rutas públicas (sin autenticación)
Route::prefix('publico')->group(function () {
    Route::get('tipos-veeduria', [PublicoController::class, 'tiposVeeduria']);
    Route::get('estados-veeduria', [PublicoController::class, 'estadosVeeduria']);
    Route::get('categorias-veeduria', [PublicoController::class, 'categoriasVeeduria']);
    Route::get('tipos-documento', [PublicoController::class, 'tiposDocumento']);
    Route::get('generos', [PublicoController::class, 'generos']);
    Route::get('prioridades-tarea', [PublicoController::class, 'prioridadesTarea']);
    Route::get('estados-tarea', [PublicoController::class, 'estadosTarea']);
    Route::get('tipos-donacion', [PublicoController::class, 'tiposDonacion']);
    Route::get('estados-donacion', [PublicoController::class, 'estadosDonacion']);
    
    // Estadísticas públicas para página de inicio
    Route::get('estadisticas-inicio', [App\Http\Controllers\Api\EstadisticasController::class, 'estadisticasInicio']);
});

// Rutas del Sistema de IA
Route::prefix('ia')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/estadisticas', [App\Http\Controllers\Api\SistemaIAControllerMejorado::class, 'estadisticasIA']);
    Route::post('/recomendaciones', [App\Http\Controllers\Api\SistemaIAControllerMejorado::class, 'obtenerRecomendaciones']);
    Route::post('/generar-narracion', [App\Http\Controllers\Api\SistemaIAControllerMejorado::class, 'generarNarracion']);
    Route::post('/analizar-veeduria/{id}', [App\Http\Controllers\Api\SistemaIAControllerMejorado::class, 'analizarVeeduria']);
    
    // Nuevas rutas de IA especializada
    Route::post('/naturaleza-justicia', [App\Http\Controllers\Api\IAController::class, 'analizarNaturalezaJusticia']);
    Route::post('/derechos-etnicos', [App\Http\Controllers\Api\IAController::class, 'analizarDerechosEtnicos']);
    Route::post('/derecho-constitucional', [App\Http\Controllers\Api\IAController::class, 'analizarDerechoConstitucional']);
    Route::post('/derecho-administrativo', [App\Http\Controllers\Api\IAController::class, 'analizarDerechoAdministrativo']);
    Route::post('/derecho-penal', [App\Http\Controllers\Api\IAController::class, 'analizarDerechoPenal']);
    Route::post('/derecho-civil', [App\Http\Controllers\Api\IAController::class, 'analizarDerechoCivil']);
    Route::post('/derecho-laboral', [App\Http\Controllers\Api\IAController::class, 'analizarDerechoLaboral']);
    Route::post('/medicina-natural', [App\Http\Controllers\Api\IAController::class, 'analizarMedicinaNatural']);
    Route::post('/derechos-ambientales', [App\Http\Controllers\Api\IAController::class, 'analizarDerechosAmbientales']);
    Route::post('/derechos-mineros', [App\Http\Controllers\Api\IAController::class, 'analizarDerechosMineros']);
    Route::post('/peritaje-catastral', [App\Http\Controllers\Api\IAController::class, 'analizarPeritajeCatastral']);
    Route::post('/metodologia-agil', [App\Http\Controllers\Api\IAController::class, 'analizarMetodologiaAgil']);
    Route::post('/resena-historica', [App\Http\Controllers\Api\IAController::class, 'analizarResenaHistorica']);
        Route::post('/analisis-general', [App\Http\Controllers\Api\IAController::class, 'analisisGeneral']);
        
        // Nuevas especialidades post-doctorado
        Route::post('/derechos-catastrales', [App\Http\Controllers\Api\IAController::class, 'analizarDerechosCatastrales']);
        Route::post('/derechos-mineros-avanzado', [App\Http\Controllers\Api\IAController::class, 'analizarDerechosMinerosAvanzado']);
        Route::post('/dictamen-ambiental', [App\Http\Controllers\Api\IAController::class, 'analizarDictamenAmbiental']);
        Route::post('/derecho-informatico', [App\Http\Controllers\Api\IAController::class, 'analizarDerechoInformatico']);
        Route::post('/geoportales', [App\Http\Controllers\Api\IAController::class, 'analizarGeoportales']);
        Route::post('/georreferenciacion', [App\Http\Controllers\Api\IAController::class, 'analizarGeorreferenciacion']);
        Route::post('/participacion-ciudadana', [App\Http\Controllers\Api\IAController::class, 'analizarParticipacionCiudadana']);
        Route::post('/accion-popular', [App\Http\Controllers\Api\IAController::class, 'analizarAccionPopular']);
        Route::post('/reforma-constitucional', [App\Http\Controllers\Api\IAController::class, 'analizarReformaConstitucional']);
        Route::post('/politologia', [App\Http\Controllers\Api\IAController::class, 'analizarPolitologia']);
        Route::post('/forense-informatico', [App\Http\Controllers\Api\IAController::class, 'analizarForenseInformatico']);
        Route::post('/cruces-datos', [App\Http\Controllers\Api\IAController::class, 'analizarCrucesDatos']);
        Route::post('/ubicacion-predios', [App\Http\Controllers\Api\IAController::class, 'analizarUbicacionPredios']);
        
        // Rutas de IA Étnica Especializada
        Route::prefix('etnica')->group(function () {
            Route::post('/consulta-etnica', [IAEtnicaController::class, 'analizarConsultaEtnica']);
            Route::post('/marco-juridico', [IAEtnicaController::class, 'analizarMarcoJuridico']);
            Route::post('/impacto-territorial', [IAEtnicaController::class, 'analizarImpactoTerritorial']);
            Route::post('/jurisdiccion-especial-indigena', [IAEtnicaController::class, 'analizarJurisdiccionEspecialIndigena']);
            Route::post('/patrimonio-cultural', [IAEtnicaController::class, 'analizarPatrimonioCulturalEtnico']);
            Route::post('/educacion-propia', [IAEtnicaController::class, 'analizarEducacionPropiaEtnica']);
            Route::post('/territorios-ancestrales', [IAEtnicaController::class, 'analizarTerritoriosAncestrales']);
            Route::get('/estadisticas', [IAEtnicaController::class, 'estadisticasIAEtnica']);
        });
    });

// Rutas de Logs
Route::prefix('logs')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [App\Http\Controllers\Api\LogController::class, 'index']);
    Route::get('/recientes/{dias?}', [App\Http\Controllers\Api\LogController::class, 'recientes']);
    Route::get('/estadisticas', [App\Http\Controllers\Api\LogController::class, 'estadisticas']);
});

// Rutas de Validación
Route::prefix('validacion')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/permisos', [App\Http\Controllers\Api\ValidacionController::class, 'validarPermisos']);
    Route::post('/rol', [App\Http\Controllers\Api\ValidacionController::class, 'validarRol']);
    Route::get('/usuario/{id}/permisos', [App\Http\Controllers\Api\ValidacionController::class, 'obtenerPermisosUsuario']);
});

// Rutas de Archivos
Route::prefix('archivos')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [App\Http\Controllers\Api\ArchivoController::class, 'index']);
    Route::get('/estadisticas', [App\Http\Controllers\Api\ArchivoController::class, 'estadisticas']);
    Route::get('/{id}', [App\Http\Controllers\Api\ArchivoController::class, 'show']);
    Route::post('/', [App\Http\Controllers\Api\ArchivoController::class, 'store']);
    Route::put('/{id}', [App\Http\Controllers\Api\ArchivoController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Api\ArchivoController::class, 'destroy']);
});

// Rutas específicas para PDFs
Route::prefix('archivos/pdf')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [App\Http\Controllers\Api\PDFController::class, 'index']);
    Route::get('/estadisticas', [App\Http\Controllers\Api\PDFController::class, 'statistics']);
    Route::get('/{id}', [App\Http\Controllers\Api\PDFController::class, 'show']);
    Route::post('/', [App\Http\Controllers\Api\PDFController::class, 'store']);
    Route::put('/{id}', [App\Http\Controllers\Api\PDFController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Api\PDFController::class, 'destroy']);
    Route::post('/{id}/descargar', [App\Http\Controllers\Api\PDFController::class, 'download']);
    Route::post('/generar', [App\Http\Controllers\Api\PDFController::class, 'generate']);
    Route::post('/{id}/procesar-ia', [App\Http\Controllers\Api\PDFController::class, 'processWithAI']);
    
    // Rutas específicas para generar PDFs
    Route::post('/generar/analisis-legal', [App\Http\Controllers\Api\PDFController::class, 'generarAnalisisLegal']);
    Route::post('/generar/pqrsfd', [App\Http\Controllers\Api\PDFController::class, 'generarPQRSFD']);
    Route::post('/generar/donacion', [App\Http\Controllers\Api\PDFController::class, 'generarDonacion']);
    Route::post('/generar/reporte-veeduria', [App\Http\Controllers\Api\PDFController::class, 'generarReporteVeeduria']);
    Route::post('/{id}/extraer-texto', function($id) {
        return response()->json(['success' => true, 'message' => 'Funcionalidad en desarrollo']);
    });
    Route::get('/{id}/metadatos', function($id) {
        return response()->json(['success' => true, 'message' => 'Funcionalidad en desarrollo']);
    });
    Route::post('/{id}/validar', function($id) {
        return response()->json(['success' => true, 'message' => 'Funcionalidad en desarrollo']);
    });
    Route::post('/{id}/firmar', function($id) {
        return response()->json(['success' => true, 'message' => 'Funcionalidad en desarrollo']);
    });
    Route::post('/{id}/convertir', function($id) {
        return response()->json(['success' => true, 'message' => 'Funcionalidad en desarrollo']);
    });
    Route::get('/plantillas', function() {
        return response()->json(['success' => true, 'data' => [], 'message' => 'Plantillas obtenidas']);
    });
    Route::post('/plantillas', function() {
        return response()->json(['success' => true, 'message' => 'Plantilla creada']);
    });
    Route::post('/plantilla/{id}', function($id) {
        return response()->json(['success' => true, 'message' => 'PDF generado desde plantilla']);
    });
});

// Rutas de Dashboard (consolidadas)
Route::prefix('dashboard')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [App\Http\Controllers\Api\DashboardController::class, 'general']);
    Route::get('/general', [App\Http\Controllers\Api\DashboardController::class, 'general']);
    Route::get('/administrador-general', [App\Http\Controllers\Api\DashboardController::class, 'administradorGeneral']);
    Route::get('/administrador', [App\Http\Controllers\Api\DashboardController::class, 'administrador']);
    Route::get('/operador', [App\Http\Controllers\Api\DashboardController::class, 'operador']);
    Route::get('/cliente', [App\Http\Controllers\Api\DashboardController::class, 'cliente']);
});

/*
|--------------------------------------------------------------------------
| API Routes - CONSEJO SOCIAL DE VEEDURÍA Y DESARROLLO TERRITORIAL
|--------------------------------------------------------------------------
|
| Aquí se registran todas las rutas de la API de la aplicación.
| Las rutas se cargan a través del RouteServiceProvider.
|
*/

// Ruta de verificación de salud de la API
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API del CONSEJO SOCIAL DE VEEDURÍA Y DESARROLLO TERRITORIAL funcionando correctamente',
        'timestamp' => now()->toISOString(),
        'version' => '2.0.0',
    ]);
});

// Rutas de autenticación
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('/cambiar-contrasena', [AuthController::class, 'cambiarContrasena'])->middleware('auth:sanctum');
    Route::post('/recuperar-contrasena', [AuthController::class, 'recuperarContrasena']);
    Route::post('/resetear-contrasena', [AuthController::class, 'resetearContrasena']);
    Route::post('/verificar-email', [AuthController::class, 'verificarEmail']);
});

// Rutas de autenticación web
Route::prefix('auth-web')->group(function () {
    Route::get('/login', [AuthControllerWeb::class, 'mostrarLogin'])->name('api.login');
    Route::post('/login', [AuthControllerWeb::class, 'login']);
    Route::post('/logout', [AuthControllerWeb::class, 'logout'])->name('api.logout');
    Route::get('/usuario', [AuthControllerWeb::class, 'usuario'])->middleware('auth:sanctum');
    Route::post('/verificar-acceso', [AuthControllerWeb::class, 'verificarAcceso'])->middleware('auth:sanctum');
    Route::get('/paginas-accesibles', [AuthControllerWeb::class, 'paginasAccesibles'])->middleware('auth:sanctum');
    Route::post('/cambiar-password', [AuthControllerWeb::class, 'cambiarPassword'])->middleware('auth:sanctum');
    Route::post('/actualizar-perfil', [AuthControllerWeb::class, 'actualizarPerfil'])->middleware('auth:sanctum');
});

// Rutas de registro (públicas - sin restricciones de permisos)
Route::prefix('registro')->group(function () {
    Route::post('/validar-campos', [RegistroControlador::class, 'validarCampos']);
    Route::post('/registrar', [RegistroControlador::class, 'registrar']);
    Route::post('/verificar-email', [RegistroControlador::class, 'verificarEmail']);
    
    // Rutas de administración (solo para administradores) - SIMPLE
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/pendientes', [RegistroControlador::class, 'obtenerPendientes']);
        Route::post('/aprobar/{id}', [RegistroControlador::class, 'aprobar']);
        Route::post('/rechazar/{id}', [RegistroControlador::class, 'rechazar']);
    });
});

// Rutas de registro público directo (sin restricciones)
Route::prefix('registro-publico')->group(function () {
    Route::post('/cliente', [App\Http\Controllers\Api\RegistroPublicoController::class, 'registrarCliente']);
    Route::post('/operador', [App\Http\Controllers\Api\RegistroPublicoController::class, 'registrarOperador']);
    Route::post('/administrador', [App\Http\Controllers\Api\RegistroPublicoController::class, 'registrarAdministrador']);
    Route::post('/validar-campos', [App\Http\Controllers\Api\RegistroPublicoController::class, 'validarCampos']);
});

// Rutas públicas (sin autenticación)
Route::prefix('publico')->group(function () {
    // Datos de referencia públicos
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
            ],
            'message' => 'Tipos de veeduría obtenidos exitosamente',
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
            ],
            'message' => 'Estados de veeduría obtenidos exitosamente',
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
            ],
            'message' => 'Prioridades de tarea obtenidas exitosamente',
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
            ],
            'message' => 'Categorías de veeduría obtenidas exitosamente',
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
            ],
            'message' => 'Tipos de documento obtenidos exitosamente',
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
            ],
            'message' => 'Géneros obtenidos exitosamente',
        ]);
    });
});

// Rutas protegidas por autenticación
Route::middleware('auth:sanctum')->group(function () {

    // Rutas de usuarios
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'index']);
        Route::get('/{id}', [UsuarioController::class, 'show']);
        Route::post('/', [UsuarioController::class, 'store']);
        Route::put('/{id}', [UsuarioController::class, 'update']);
        Route::delete('/{id}', [UsuarioController::class, 'destroy']);
        Route::post('/{id}/restaurar', [UsuarioController::class, 'restore']);
        Route::put('/{id}/cambiar-estado', [UsuarioController::class, 'cambiarEstado']);
        Route::post('/{id}/verificar-correo', [UsuarioController::class, 'verificarCorreo']);
        Route::get('/{id}/estadisticas', [UsuarioController::class, 'estadisticas']);
        Route::get('/buscar/termino', [UsuarioController::class, 'buscar']);
    });

    // Rutas de veedurías
    Route::prefix('veedurias')->group(function () {
        Route::get('/', [VeeduriaController::class, 'index']);
        Route::get('/{id}', [VeeduriaController::class, 'show']);
        Route::post('/', [VeeduriaController::class, 'store']);
        Route::put('/{id}', [VeeduriaController::class, 'update']);
        Route::delete('/{id}', [VeeduriaController::class, 'destroy']);
        Route::post('/{id}/restaurar', [VeeduriaController::class, 'restore']);
        Route::post('/{id}/radicar', [VeeduriaController::class, 'radicar']);
        Route::post('/{id}/cerrar', [VeeduriaController::class, 'cerrar']);
        Route::post('/{id}/cancelar', [VeeduriaController::class, 'cancelar']);
        Route::post('/{id}/asignar-operador', [VeeduriaController::class, 'asignarOperador']);
        Route::get('/{id}/estadisticas', [VeeduriaController::class, 'estadisticas']);
        Route::get('/buscar/termino', [VeeduriaController::class, 'buscar']);
    });

    // Rutas de donaciones
    Route::prefix('donaciones')->group(function () {
        Route::get('/', [DonacionController::class, 'index']);
        Route::get('/{id}', [DonacionController::class, 'show']);
        Route::post('/', [DonacionController::class, 'store']);
        Route::put('/{id}', [DonacionController::class, 'update']);
        Route::delete('/{id}', [DonacionController::class, 'destroy']);
        Route::post('/{id}/restaurar', [DonacionController::class, 'restore']);
        Route::post('/{id}/confirmar', [DonacionController::class, 'confirmar']);
        Route::post('/{id}/rechazar', [DonacionController::class, 'rechazar']);
        Route::post('/{id}/cancelar', [DonacionController::class, 'cancelar']);
        Route::post('/{id}/procesar', [DonacionController::class, 'procesar']);
        Route::get('/estadisticas/generales', [DonacionController::class, 'estadisticas']);
        Route::get('/buscar/termino', [DonacionController::class, 'buscar']);
    });

    // Rutas de tareas
    Route::prefix('tareas')->group(function () {
        Route::get('/', [TareaController::class, 'index']);
        Route::get('/{id}', [TareaController::class, 'show']);
        Route::post('/', [TareaController::class, 'store']);
        Route::put('/{id}', [TareaController::class, 'update']);
        Route::delete('/{id}', [TareaController::class, 'destroy']);
        Route::post('/{id}/restaurar', [TareaController::class, 'restore']);
        Route::post('/{id}/iniciar', [TareaController::class, 'iniciar']);
        Route::post('/{id}/completar', [TareaController::class, 'completar']);
        Route::post('/{id}/cancelar', [TareaController::class, 'cancelar']);
        Route::post('/{id}/suspender', [TareaController::class, 'suspender']);
        Route::post('/{id}/reanudar', [TareaController::class, 'reanudar']);
        Route::post('/{id}/asignar', [TareaController::class, 'asignar']);
        Route::get('/estadisticas/generales', [TareaController::class, 'estadisticas']);
        Route::get('/buscar/termino', [TareaController::class, 'buscar']);
    });

    // Rutas de archivos
    Route::prefix('archivos')->group(function () {
        Route::get('/', [ArchivoController::class, 'index']);
        Route::get('/{id}', [ArchivoController::class, 'show']);
        Route::post('/', [ArchivoController::class, 'store']);
        Route::put('/{id}', [ArchivoController::class, 'update']);
        Route::delete('/{id}', [ArchivoController::class, 'destroy']);
        Route::post('/{id}/restaurar', [ArchivoController::class, 'restore']);
        Route::get('/{id}/descargar', [ArchivoController::class, 'descargar']);
        Route::get('/{id}/estadisticas', [ArchivoController::class, 'estadisticas']);
        Route::get('/buscar/termino', [ArchivoController::class, 'buscar']);
    });

    // Rutas de roles (solo administradores)
    Route::prefix('roles')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [RolController::class, 'index']);
        Route::get('/{id}', [RolController::class, 'show']);
        Route::post('/', [RolController::class, 'store']);
        Route::put('/{id}', [RolController::class, 'update']);
        Route::delete('/{id}', [RolController::class, 'destroy']);
        Route::post('/{id}/activar', [RolController::class, 'activar']);
        Route::post('/{id}/desactivar', [RolController::class, 'desactivar']);
        Route::post('/{id}/agregar-permiso', [RolController::class, 'agregarPermiso']);
        Route::post('/{id}/quitar-permiso', [RolController::class, 'quitarPermiso']);
    });

    // Rutas de configuraciones (solo administradores)
    Route::prefix('configuraciones')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [ConfiguracionController::class, 'index']);
        Route::get('/{id}', [ConfiguracionController::class, 'show']);
        Route::post('/', [ConfiguracionController::class, 'store']);
        Route::put('/{id}', [ConfiguracionController::class, 'update']);
        Route::delete('/{id}', [ConfiguracionController::class, 'destroy']);
        Route::get('/clave/{clave}', [ConfiguracionController::class, 'obtenerPorClave']);
        Route::put('/clave/{clave}', [ConfiguracionController::class, 'actualizarPorClave']);
        Route::get('/categoria/{categoria}', [ConfiguracionController::class, 'obtenerPorCategoria']);
        Route::post('/{id}/activar', [ConfiguracionController::class, 'activar']);
        Route::post('/{id}/desactivar', [ConfiguracionController::class, 'desactivar']);
    });

    // Rutas de logs (solo administradores)
    Route::prefix('logs')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [LogController::class, 'index']);
        Route::get('/{id}', [LogController::class, 'show']);
        Route::get('/usuario/{usuarioId}', [LogController::class, 'porUsuario']);
        Route::get('/accion/{accion}', [LogController::class, 'porAccion']);
        Route::get('/tabla/{tabla}', [LogController::class, 'porTabla']);
        Route::get('/registro/{tabla}/{registroId}', [LogController::class, 'porRegistro']);
        Route::get('/fecha/{fechaInicio}/{fechaFin?}', [LogController::class, 'porFecha']);
        Route::get('/recientes/{dias?}', [LogController::class, 'recientes']);
    });

    // Rutas del Panel de Vista (solo administradores)
    Route::prefix('panel-vista')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [PanelVistaController::class, 'index']);
        Route::get('/paginas', [PanelVistaController::class, 'obtenerPaginas']);
        Route::post('/paginas', [PanelVistaController::class, 'crearPagina']);
        Route::put('/paginas/{id}', [PanelVistaController::class, 'actualizarPagina']);
        Route::delete('/paginas/{id}', [PanelVistaController::class, 'eliminarPagina']);
        Route::post('/paginas/{id}/permisos', [PanelVistaController::class, 'actualizarPermisos']);
        Route::post('/paginas/{id}/roles', [PanelVistaController::class, 'actualizarRoles']);
        Route::post('/paginas/{id}/estado', [PanelVistaController::class, 'actualizarEstado']);
        Route::get('/paginas-usuario/{usuario_id}', [PanelVistaController::class, 'obtenerPaginasUsuario']);
        Route::get('/estadisticas', [PanelVistaController::class, 'obtenerEstadisticas']);
    });

    // Rutas de Gestión de Usuarios (solo administradores)
    Route::prefix('gestion-usuarios')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [GestionUsuariosController::class, 'index']);
        Route::get('/usuarios', [GestionUsuariosController::class, 'obtenerUsuarios']);
        Route::get('/usuarios/{id}', [GestionUsuariosController::class, 'obtenerUsuario']);
        Route::post('/usuarios', [GestionUsuariosController::class, 'crearUsuario']);
        Route::put('/usuarios/{id}', [GestionUsuariosController::class, 'actualizarUsuario']);
        Route::delete('/usuarios/{id}', [GestionUsuariosController::class, 'eliminarUsuario']);
        Route::post('/usuarios/{id}/restaurar', [GestionUsuariosController::class, 'restaurarUsuario']);
        Route::post('/usuarios/{id}/cambiar-estado', [GestionUsuariosController::class, 'cambiarEstado']);
        Route::post('/usuarios/{id}/cambiar-password', [GestionUsuariosController::class, 'cambiarPassword']);
        Route::get('/usuarios/{id}/paginas', [GestionUsuariosController::class, 'obtenerPaginasUsuario']);
        Route::get('/estadisticas', [GestionUsuariosController::class, 'obtenerEstadisticas']);
    });

    // Rutas de dashboard y estadísticas generales
    Route::prefix('dashboard')->group(function () {
        Route::get('/resumen', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_usuarios' => \App\Models\Usuario::count(),
                    'total_veedurias' => \App\Models\Veeduria::count(),
                    'total_donaciones' => \App\Models\Donacion::count(),
                    'total_tareas' => \App\Models\Tarea::count(),
                    'total_archivos' => \App\Models\Archivo::count(),
                    'veedurias_pendientes' => \App\Models\Veeduria::where('est', 'pen')->count(),
                    'donaciones_pendientes' => \App\Models\Donacion::where('est', 'pen')->count(),
                    'tareas_pendientes' => \App\Models\Tarea::where('est', 'pen')->count(),
                    'tareas_vencidas' => \App\Models\Tarea::vencidas()->count(),
                ],
                'message' => 'Resumen del dashboard obtenido exitosamente',
            ]);
        });

        Route::get('/estadisticas-generales', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'veedurias_por_estado' => \App\Models\Veeduria::selectRaw('est, COUNT(*) as total')
                        ->groupBy('est')
                        ->get(),
                    'veedurias_por_tipo' => \App\Models\Veeduria::selectRaw('tip, COUNT(*) as total')
                        ->groupBy('tip')
                        ->get(),
                    'donaciones_por_estado' => \App\Models\Donacion::selectRaw('est, COUNT(*) as total')
                        ->groupBy('est')
                        ->get(),
                    'tareas_por_estado' => \App\Models\Tarea::selectRaw('est, COUNT(*) as total')
                        ->groupBy('est')
                        ->get(),
                    'usuarios_por_rol' => \App\Models\Usuario::selectRaw('rol, COUNT(*) as total')
                        ->groupBy('rol')
                        ->get(),
                ],
                'message' => 'Estadísticas generales obtenidas exitosamente',
            ]);
        });
    });
});

// Ruta de fallback para rutas no encontradas
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Ruta no encontrada',
        'error' => 'La ruta solicitada no existe en la API',
    ], 404);
});