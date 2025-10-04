<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IADerechoConstitucionalController;
use App\Http\Controllers\Api\IADerechoInternacionalController;
use App\Http\Controllers\Api\IADerechoTributarioController;
use App\Http\Controllers\Api\IADerechoLaboralController;
use App\Http\Controllers\Api\IADerechoPoliticoController;
use App\Http\Controllers\Api\IAConsejoMunicipalController;
use App\Http\Controllers\Api\IADiputadosGobernadoresController;

Route::middleware('auth:sanctum')->prefix('ia-especializadas-futuras')->group(function () {
    
    // Rutas para IA de Derecho Constitucional
    Route::prefix('derecho-constitucional')->group(function () {
        Route::post('analisis-integral', [IADerechoConstitucionalController::class, 'analisisIntegral']);
        Route::post('derechos-fundamentales', [IADerechoConstitucionalController::class, 'analizarDerechosFundamentales']);
        Route::post('control-constitucional', [IADerechoConstitucionalController::class, 'analizarControlConstitucional']);
        Route::post('acciones-constitucionales', [IADerechoConstitucionalController::class, 'analizarAccionesConstitucionales']);
        Route::post('estructura-estado', [IADerechoConstitucionalController::class, 'analizarEstructuraEstado']);
        Route::post('reforma-constitucional', [IADerechoConstitucionalController::class, 'analizarReformaConstitucional']);
        Route::get('estadisticas', [IADerechoConstitucionalController::class, 'estadisticas']);
    });

    // Rutas para IA de Derecho Internacional
    Route::prefix('derecho-internacional')->group(function () {
        Route::post('analisis-integral', [IADerechoInternacionalController::class, 'analisisIntegral']);
        Route::post('derecho-internacional-publico', [IADerechoInternacionalController::class, 'analizarDerechoInternacionalPublico']);
        Route::post('derecho-internacional-privado', [IADerechoInternacionalController::class, 'analizarDerechoInternacionalPrivado']);
        Route::post('derecho-internacional-humanitario', [IADerechoInternacionalController::class, 'analizarDerechoInternacionalHumanitario']);
        Route::post('derecho-internacional-derechos-humanos', [IADerechoInternacionalController::class, 'analizarDerechoInternacionalDerechosHumanos']);
        Route::post('derecho-internacional-economico', [IADerechoInternacionalController::class, 'analizarDerechoInternacionalEconomico']);
        Route::get('estadisticas', [IADerechoInternacionalController::class, 'estadisticas']);
    });

    // Rutas para IA de Derecho Tributario
    Route::prefix('derecho-tributario')->group(function () {
        Route::post('analisis-integral', [IADerechoTributarioController::class, 'analisisIntegral']);
        Route::post('impuestos-directos', [IADerechoTributarioController::class, 'analizarImpuestosDirectos']);
        Route::post('impuestos-indirectos', [IADerechoTributarioController::class, 'analizarImpuestosIndirectos']);
        Route::post('procedimiento-tributario', [IADerechoTributarioController::class, 'analizarProcedimientoTributario']);
        Route::post('contencioso-tributario', [IADerechoTributarioController::class, 'analizarContenciosoTributario']);
        Route::post('planeacion-tributaria', [IADerechoTributarioController::class, 'analizarPlaneacionTributaria']);
        Route::get('estadisticas', [IADerechoTributarioController::class, 'estadisticas']);
    });

    // Rutas para IA de Derecho Laboral
    Route::prefix('derecho-laboral')->group(function () {
        Route::post('analisis-integral', [IADerechoLaboralController::class, 'analisisIntegral']);
        Route::post('contrato-trabajo', [IADerechoLaboralController::class, 'analizarContratoTrabajo']);
        Route::post('derechos-laborales', [IADerechoLaboralController::class, 'analizarDerechosLaborales']);
        Route::post('seguridad-social', [IADerechoLaboralController::class, 'analizarSeguridadSocial']);
        Route::post('procedimiento-laboral', [IADerechoLaboralController::class, 'analizarProcedimientoLaboral']);
        Route::post('contencioso-laboral', [IADerechoLaboralController::class, 'analizarContenciosoLaboral']);
        Route::get('estadisticas', [IADerechoLaboralController::class, 'estadisticas']);
    });

    // Rutas para IA de Derecho Político
    Route::prefix('derecho-politico')->group(function () {
        Route::post('analisis-integral', [IADerechoPoliticoController::class, 'analisisIntegral']);
        Route::post('derecho-electoral', [IADerechoPoliticoController::class, 'analizarDerechoElectoral']);
        Route::post('derecho-parlamentario', [IADerechoPoliticoController::class, 'analizarDerechoParlamentario']);
        Route::post('derecho-administrativo', [IADerechoPoliticoController::class, 'analizarDerechoAdministrativo']);
        Route::post('participacion-ciudadana', [IADerechoPoliticoController::class, 'analizarParticipacionCiudadana']);
        Route::post('control-politico', [IADerechoPoliticoController::class, 'analizarControlPolitico']);
        Route::get('estadisticas', [IADerechoPoliticoController::class, 'estadisticas']);
    });

    // Rutas para IA de Consejo Municipal
    Route::prefix('consejo-municipal')->group(function () {
        Route::post('analisis-integral', [IAConsejoMunicipalController::class, 'analisisIntegral']);
        Route::post('gobierno-municipal', [IAConsejoMunicipalController::class, 'analizarGobiernoMunicipal']);
        Route::post('planeacion-municipal', [IAConsejoMunicipalController::class, 'analizarPlaneacionMunicipal']);
        Route::post('presupuesto-municipal', [IAConsejoMunicipalController::class, 'analizarPresupuestoMunicipal']);
        Route::post('servicios-publicos', [IAConsejoMunicipalController::class, 'analizarServiciosPublicos']);
        Route::post('participacion-ciudadana-municipal', [IAConsejoMunicipalController::class, 'analizarParticipacionCiudadanaMunicipal']);
        Route::get('estadisticas', [IAConsejoMunicipalController::class, 'estadisticas']);
    });

    // Rutas para IA de Diputados y Gobernadores
    Route::prefix('diputados-gobernadores')->group(function () {
        Route::post('analisis-integral', [IADiputadosGobernadoresController::class, 'analisisIntegral']);
        Route::post('gobierno-departamental', [IADiputadosGobernadoresController::class, 'analizarGobiernoDepartamental']);
        Route::post('asamblea-departamental', [IADiputadosGobernadoresController::class, 'analizarAsambleaDepartamental']);
        Route::post('planeacion-departamental', [IADiputadosGobernadoresController::class, 'analizarPlaneacionDepartamental']);
        Route::post('presupuesto-departamental', [IADiputadosGobernadoresController::class, 'analizarPresupuestoDepartamental']);
        Route::post('competencias-departamentales', [IADiputadosGobernadoresController::class, 'analizarCompetenciasDepartamentales']);
        Route::get('estadisticas', [IADiputadosGobernadoresController::class, 'estadisticas']);
    });
});

