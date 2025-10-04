<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IAVeeduriaAnticorrupcionController;
use App\Http\Controllers\Api\IADerechoInformaticoController;
use App\Http\Controllers\Api\IAFiscalizacionController;
use App\Http\Controllers\Api\IAInvestigacionForenseController;

/*
|--------------------------------------------------------------------------
| API Routes - IAs Especializadas Avanzadas
|--------------------------------------------------------------------------
|
| Rutas para las IAs especializadas de nivel post-doctorado:
| - Veeduría y Anti-Corrupción
| - Derecho Informático
| - Fiscalización
| - Investigación Forense
|
*/

Route::middleware(['auth:sanctum'])->prefix('api/ia-especializadas-avanzadas')->group(function () {
    
    // ========================================
    // VEEDURÍA Y ANTI-CORRUPCIÓN
    // ========================================
    Route::prefix('veeduria-anticorrupcion')->group(function () {
        Route::post('/veeduria-ciudadana', [IAVeeduriaAnticorrupcionController::class, 'analizarVeeduriaCiudadana']);
        Route::post('/control-social', [IAVeeduriaAnticorrupcionController::class, 'analizarControlSocial']);
        Route::post('/transparencia-publica', [IAVeeduriaAnticorrupcionController::class, 'analizarTransparenciaPublica']);
        Route::post('/rendicion-cuentas', [IAVeeduriaAnticorrupcionController::class, 'analizarRendicionCuentas']);
        Route::post('/participacion-ciudadana', [IAVeeduriaAnticorrupcionController::class, 'analizarParticipacionCiudadana']);
        Route::post('/contratacion-publica', [IAVeeduriaAnticorrupcionController::class, 'analizarContratacionPublica']);
        Route::post('/gestion-riesgos', [IAVeeduriaAnticorrupcionController::class, 'analizarGestionRiesgos']);
        Route::post('/analisis-integral', [IAVeeduriaAnticorrupcionController::class, 'analisisIntegralVeeduriaAnticorrupcion']);
        Route::get('/estadisticas', [IAVeeduriaAnticorrupcionController::class, 'obtenerEstadisticasVeeduria']);
    });

    // ========================================
    // DERECHO INFORMÁTICO
    // ========================================
    Route::prefix('derecho-informatico')->group(function () {
        Route::post('/ciberseguridad', [IADerechoInformaticoController::class, 'analizarCiberseguridad']);
        Route::post('/proteccion-datos', [IADerechoInformaticoController::class, 'analizarProteccionDatos']);
        Route::post('/delitos-informaticos', [IADerechoInformaticoController::class, 'analizarDelitosInformaticos']);
        Route::post('/evidencia-digital', [IADerechoInformaticoController::class, 'analizarEvidenciaDigital']);
        Route::post('/comercio-electronico', [IADerechoInformaticoController::class, 'analizarComercioElectronico']);
        Route::post('/ia-derecho', [IADerechoInformaticoController::class, 'analizarIAyDerecho']);
        Route::post('/analisis-integral', [IADerechoInformaticoController::class, 'analisisIntegralDerechoInformatico']);
        Route::get('/estadisticas', [IADerechoInformaticoController::class, 'obtenerEstadisticasDerechoInformatico']);
    });

    // ========================================
    // FISCALIZACIÓN
    // ========================================
    Route::prefix('fiscalizacion')->group(function () {
        Route::post('/control-fiscal', [IAFiscalizacionController::class, 'analizarControlFiscal']);
        Route::post('/auditoria-publica', [IAFiscalizacionController::class, 'analizarAuditoriaPublica']);
        Route::post('/supervision-financiera', [IAFiscalizacionController::class, 'analizarSupervisionFinanciera']);
        Route::post('/gestion-riesgos-fiscal', [IAFiscalizacionController::class, 'analizarGestionRiesgosFiscal']);
        Route::post('/cumplimiento-normativo', [IAFiscalizacionController::class, 'analizarCumplimientoNormativo']);
        Route::post('/rendicion-cuentas-fiscal', [IAFiscalizacionController::class, 'analizarRendicionCuentasFiscal']);
        Route::post('/control-interno', [IAFiscalizacionController::class, 'analizarControlInterno']);
        Route::post('/analisis-integral', [IAFiscalizacionController::class, 'analisisIntegralFiscalizacion']);
        Route::get('/estadisticas', [IAFiscalizacionController::class, 'obtenerEstadisticasFiscalizacion']);
    });

    // ========================================
    // INVESTIGACIÓN FORENSE
    // ========================================
    Route::prefix('investigacion-forense')->group(function () {
        Route::post('/forense-digital', [IAInvestigacionForenseController::class, 'analizarForenseDigital']);
        Route::post('/evidencia-digital', [IAInvestigacionForenseController::class, 'analizarEvidenciaDigital']);
        Route::post('/cibercrimen', [IAInvestigacionForenseController::class, 'analizarCibercrimen']);
        Route::post('/perfilacion-criminal', [IAInvestigacionForenseController::class, 'analizarPerfilacionCriminal']);
        Route::post('/criminologia', [IAInvestigacionForenseController::class, 'analizarCriminologia']);
        Route::post('/victimologia', [IAInvestigacionForenseController::class, 'analizarVictimologia']);
        Route::post('/psicologia-forense', [IAInvestigacionForenseController::class, 'analizarPsicologiaForense']);
        Route::post('/analisis-integral', [IAInvestigacionForenseController::class, 'analisisIntegralInvestigacionForense']);
        Route::get('/estadisticas', [IAInvestigacionForenseController::class, 'obtenerEstadisticasInvestigacionForense']);
    });

    // ========================================
    // CONSULTA INTEGRAL DE TODAS LAS IAs
    // ========================================
    Route::prefix('consulta-integral')->group(function () {
        Route::post('/todas-las-ias', function (Request $request) {
            $datos = $request->all();
            $resultados = [];
            
            // Veeduría y Anti-Corrupción
            $veeduriaController = new IAVeeduriaAnticorrupcionController(new \App\Services\IAVeeduriaAnticorrupcion());
            $resultados['veeduria_anticorrupcion'] = $veeduriaController->analisisIntegralVeeduriaAnticorrupcion($request);
            
            // Derecho Informático
            $derechoController = new IADerechoInformaticoController(new \App\Services\IADerechoInformatico());
            $resultados['derecho_informatico'] = $derechoController->analisisIntegralDerechoInformatico($request);
            
            // Fiscalización
            $fiscalizacionController = new IAFiscalizacionController(new \App\Services\IAFiscalizacion());
            $resultados['fiscalizacion'] = $fiscalizacionController->analisisIntegralFiscalizacion($request);
            
            // Investigación Forense
            $forenseController = new IAInvestigacionForenseController(new \App\Services\IAInvestigacionForense());
            $resultados['investigacion_forense'] = $forenseController->analisisIntegralInvestigacionForense($request);
            
            return response()->json([
                'exito' => true,
                'mensaje' => 'Consulta integral de todas las IAs completada exitosamente',
                'datos' => $resultados
            ], 200);
        });
        
        Route::get('/estadisticas-generales', function () {
            $estadisticas = [];
            
            // Veeduría y Anti-Corrupción
            $veeduriaService = new \App\Services\IAVeeduriaAnticorrupcion();
            $estadisticas['veeduria_anticorrupcion'] = $veeduriaService->obtenerEstadisticasVeeduria();
            
            // Derecho Informático
            $derechoService = new \App\Services\IADerechoInformatico();
            $estadisticas['derecho_informatico'] = $derechoService->obtenerEstadisticasDerechoInformatico();
            
            // Fiscalización
            $fiscalizacionService = new \App\Services\IAFiscalizacion();
            $estadisticas['fiscalizacion'] = $fiscalizacionService->obtenerEstadisticasFiscalizacion();
            
            // Investigación Forense
            $forenseService = new \App\Services\IAInvestigacionForense();
            $estadisticas['investigacion_forense'] = $forenseService->obtenerEstadisticasInvestigacionForense();
            
            return response()->json([
                'exito' => true,
                'mensaje' => 'Estadísticas generales obtenidas exitosamente',
                'datos' => $estadisticas
            ], 200);
        });
    });
});
