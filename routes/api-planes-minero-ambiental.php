<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanesTrabajoMineroAmbientalController;

/*
|--------------------------------------------------------------------------
| API Routes - Planes de Trabajo Minero y Ambiental
|--------------------------------------------------------------------------
|
| Rutas para el sistema de generación de planes de trabajo mineros y
| ambientales con IA de nivel POST-DOCTORADO.
|
*/

Route::middleware('auth:sanctum')->group(function () {
    
    /**
     * GENERACIÓN DE PLANES DE TRABAJO
     */
    
    // Generar Plan de Trabajo Minero
    Route::post('/planes-trabajo/minero', [PlanesTrabajoMineroAmbientalController::class, 'generarPlanMinero'])
        ->name('api.planes.minero');
    
    // Generar Plan de Trabajo Ambiental
    Route::post('/planes-trabajo/ambiental', [PlanesTrabajoMineroAmbientalController::class, 'generarPlanAmbiental'])
        ->name('api.planes.ambiental');
    
    // Generar Plan Integrado Minero-Ambiental
    Route::post('/planes-trabajo/integrado', [PlanesTrabajoMineroAmbientalController::class, 'generarPlanIntegrado'])
        ->name('api.planes.integrado');
    
    /**
     * ANÁLISIS ESPECIALIZADOS
     */
    
    // Analizar Impacto Ambiental Minero
    Route::post('/planes-trabajo/analisis/impacto-ambiental', [PlanesTrabajoMineroAmbientalController::class, 'analizarImpactoAmbiental'])
        ->name('api.planes.impacto-ambiental');
    
    // Analizar Derechos Étnicos en Minería
    Route::post('/planes-trabajo/analisis/derechos-etnicos', [PlanesTrabajoMineroAmbientalController::class, 'analizarDerechosEtnicos'])
        ->name('api.planes.derechos-etnicos');
    
    // Generar Cronograma de Trabajo Minero
    Route::post('/planes-trabajo/cronograma', [PlanesTrabajoMineroAmbientalController::class, 'generarCronograma'])
        ->name('api.planes.cronograma');
    
    // Analizar Riesgos Minero-Ambientales
    Route::post('/planes-trabajo/analisis/riesgos', [PlanesTrabajoMineroAmbientalController::class, 'analizarRiesgos'])
        ->name('api.planes.riesgos');
    
    // Generar Medidas de Mitigación
    Route::post('/planes-trabajo/mitigacion', [PlanesTrabajoMineroAmbientalController::class, 'generarMedidasMitigacion'])
        ->name('api.planes.mitigacion');
    
    /**
     * GESTIÓN DE PLANES
     */
    
    // Listar Planes del Usuario
    Route::get('/planes-trabajo', [PlanesTrabajoMineroAmbientalController::class, 'listarPlanes'])
        ->name('api.planes.listar');
    
    // Obtener Plan Específico
    Route::get('/planes-trabajo/{id}', [PlanesTrabajoMineroAmbientalController::class, 'obtenerPlan'])
        ->name('api.planes.obtener');
    
    // Actualizar Estado del Plan
    Route::patch('/planes-trabajo/{id}/estado', [PlanesTrabajoMineroAmbientalController::class, 'actualizarEstado'])
        ->name('api.planes.actualizar-estado');
    
    // Eliminar Plan
    Route::delete('/planes-trabajo/{id}', [PlanesTrabajoMineroAmbientalController::class, 'eliminarPlan'])
        ->name('api.planes.eliminar');
    
    /**
     * ESTADÍSTICAS Y REPORTES
     */
    
    // Obtener Estadísticas de Planes
    Route::get('/planes-trabajo/estadisticas/resumen', [PlanesTrabajoMineroAmbientalController::class, 'obtenerEstadisticas'])
        ->name('api.planes.estadisticas');
});

