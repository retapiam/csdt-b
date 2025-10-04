<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IASpecializadaController;
use App\Http\Controllers\Api\IAPlanesGobiernoController;

/*
|--------------------------------------------------------------------------
| API Routes - IA Especializada Post-Doctorado
|--------------------------------------------------------------------------
|
| Rutas para servicios de IA especializada de nivel post-doctorado
| Incluye análisis jurídicos especializados y planes de gobierno
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    
    // ========================================
    // RUTAS DE IA ESPECIALIZADA
    // ========================================
    
    // Derecho Minero
    Route::post('/ia-derecho-minero', [IASpecializadaController::class, 'analizarDerechoMinero']);
    
    // Derecho Ambiental
    Route::post('/ia-derecho-ambiental', [IASpecializadaController::class, 'analizarDerechoAmbiental']);
    
    // Derecho Étnico
    Route::post('/ia-derecho-etnico', [IASpecializadaController::class, 'analizarDerechoEtnico']);
    
    // Derecho Catastral
    Route::post('/ia-derecho-catastral', [IASpecializadaController::class, 'analizarDerechoCatastral']);
    
    // Derecho Anti-Corrupción
    Route::post('/ia-derecho-anticorrupcion', [IASpecializadaController::class, 'analizarDerechoAnticorrupcion']);
    
    // Derecho Médico
    Route::post('/ia-derecho-medico', [IASpecializadaController::class, 'analizarDerechoMedico']);
    
    // Derecho Penal Avanzado
    Route::post('/ia-derecho-penal-avanzado', [IASpecializadaController::class, 'analizarDerechoPenalAvanzado']);
    
    // Derecho Disciplinario
    Route::post('/ia-derecho-disciplinario', [IASpecializadaController::class, 'analizarDerechoDisciplinario']);
    
    // Análisis Integral Multi-Especialidad
    Route::post('/ia-analisis-integral', [IASpecializadaController::class, 'analisisIntegralMultiEspecialidad']);
    
    // Obtener especialidades disponibles
    Route::get('/ia-especialidades', [IASpecializadaController::class, 'obtenerEspecialidadesDisponibles']);
    
    // Obtener estadísticas de análisis
    Route::get('/ia-estadisticas', [IASpecializadaController::class, 'obtenerEstadisticasAnalisis']);
    
    // ========================================
    // RUTAS DE PLANES DE GOBIERNO
    // ========================================
    
    // Plan de Desarrollo Municipal
    Route::post('/ia-plan-desarrollo-municipal', [IAPlanesGobiernoController::class, 'analizarPlanDesarrolloMunicipal']);
    
    // Plan de Desarrollo Departamental
    Route::post('/ia-plan-desarrollo-departamental', [IAPlanesGobiernoController::class, 'analizarPlanDesarrolloDepartamental']);
    
    // Plan de Ordenamiento Territorial
    Route::post('/ia-plan-ordenamiento-territorial', [IAPlanesGobiernoController::class, 'analizarPlanOrdenamientoTerritorial']);
    
    // Plan de Gobierno Étnico
    Route::post('/ia-plan-gobierno-etnico', [IAPlanesGobiernoController::class, 'analizarPlanGobiernoEtnico']);
    
    // Plan de Vida Comunitario
    Route::post('/ia-plan-vida-comunitario', [IAPlanesGobiernoController::class, 'analizarPlanVidaComunitario']);
    
    // Plan de Etnodesarrollo
    Route::post('/ia-plan-etnodesarrollo', [IAPlanesGobiernoController::class, 'analizarPlanEtnodesarrollo']);
    
    // Plan Anti-Corrupción
    Route::post('/ia-plan-anticorrupcion', [IAPlanesGobiernoController::class, 'analizarPlanAnticorrupcion']);
    
    // Plan de Ética y Transparencia
    Route::post('/ia-plan-etica-transparencia', [IAPlanesGobiernoController::class, 'analizarPlanEticaTransparencia']);
    
    // Análisis Integral de Planes de Gobierno
    Route::post('/ia-planes-integral', [IAPlanesGobiernoController::class, 'analisisIntegralPlanesGobierno']);
    
    // Obtener estadísticas de planes
    Route::get('/ia-estadisticas-planes', [IAPlanesGobiernoController::class, 'obtenerEstadisticasPlanes']);
    
    // ========================================
    // RUTAS DE CONSULTA RÁPIDA
    // ========================================
    
    // Consulta rápida por especialidad
    Route::post('/ia-consulta-rapida/{especialidad}', function ($especialidad, Request $request) {
        $controller = app(IASpecializadaController::class);
        $metodo = 'analizar' . str_replace('_', '', ucwords($especialidad, '_'));
        
        if (method_exists($controller, $metodo)) {
            return $controller->$metodo($request);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Especialidad no encontrada'
        ], 404);
    });
    
    // Consulta rápida por tipo de plan
    Route::post('/ia-consulta-plan/{tipoPlan}', function ($tipoPlan, Request $request) {
        $controller = app(IAPlanesGobiernoController::class);
        $metodo = 'analizar' . str_replace('_', '', ucwords($tipoPlan, '_'));
        
        if (method_exists($controller, $metodo)) {
            return $controller->$metodo($request);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Tipo de plan no encontrado'
        ], 404);
    });
    
    // ========================================
    // RUTAS DE CONSEJO TERRITORIAL
    // ========================================
    
    // Consultar todas las IAs especializadas
    Route::post('/consejo-territorial/consultar-todas-ias', function (Request $request) {
        $controller = app(IASpecializadaController::class);
        return $controller->analisisIntegralMultiEspecialidad($request);
    });
    
    // Consultar todos los planes de gobierno
    Route::post('/consejo-territorial/consultar-todos-planes', function (Request $request) {
        $controller = app(IAPlanesGobiernoController::class);
        return $controller->analisisIntegralPlanesGobierno($request);
    });
    
    // Consulta integral completa (IAs + Planes)
    Route::post('/consejo-territorial/consulta-integral-completa', function (Request $request) {
        $iaController = app(IASpecializadaController::class);
        $planesController = app(IAPlanesGobiernoController::class);
        
        $datos = $request->all();
        
        // Ejecutar análisis de IAs especializadas
        $resultadoIAs = $iaController->analisisIntegralMultiEspecialidad($request);
        $resultadoPlanes = $planesController->analisisIntegralPlanesGobierno($request);
        
        return response()->json([
            'success' => true,
            'analisis_completo' => [
                'ias_especializadas' => $resultadoIAs->getData(),
                'planes_gobierno' => $resultadoPlanes->getData(),
                'timestamp' => now()->toISOString(),
                'nivel' => 'post_doctorado'
            ]
        ]);
    });
    
    // Obtener estadísticas del consejo territorial
    Route::get('/consejo-territorial/estadisticas', function () {
        $iaController = app(IASpecializadaController::class);
        $planesController = app(IAPlanesGobiernoController::class);
        
        $estadisticasIAs = $iaController->obtenerEstadisticasAnalisis();
        $estadisticasPlanes = $planesController->obtenerEstadisticasPlanes();
        
        return response()->json([
            'success' => true,
            'estadisticas' => [
                'ias_especializadas' => $estadisticasIAs->getData(),
                'planes_gobierno' => $estadisticasPlanes->getData(),
                'total_analisis' => $estadisticasIAs->getData()->estadisticas->total_analisis + 
                                   $estadisticasPlanes->getData()->estadisticas->total_planes_analizados
            ]
        ]);
    });
    
});
