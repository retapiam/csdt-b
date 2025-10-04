<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IAPMPController;
use App\Http\Controllers\Api\IAIPMAController;
use App\Http\Controllers\Api\IAPMIController;

/*
|--------------------------------------------------------------------------
| API Routes - IAs de Ética y Gestión de Proyectos
|--------------------------------------------------------------------------
|
| Rutas para las IAs especializadas en ética y gestión de proyectos:
| - PMP (Project Management Professional)
| - IPMA (International Project Management Association)
| - PMI (Project Management Institute)
|
*/

// Rutas para PMP (Project Management Professional)
Route::prefix('ia-pmp')->group(function () {
    Route::post('/analisis', [IAPMPController::class, 'analizarPMP']);
    Route::post('/analisis-integral', [IAPMPController::class, 'analisisIntegralPMP']);
    Route::get('/estadisticas', [IAPMPController::class, 'estadisticasPMP']);
});

// Rutas para IPMA (International Project Management Association)
Route::prefix('ia-ipma')->group(function () {
    Route::post('/competencias', [IAIPMAController::class, 'analizarCompetenciasIPMA']);
    Route::post('/codigo-etica', [IAIPMAController::class, 'analizarCodigoEticaIPMA']);
    Route::post('/analisis-integral', [IAIPMAController::class, 'analisisIntegralIPMA']);
    Route::get('/estadisticas', [IAIPMAController::class, 'estadisticasIPMA']);
});

// Rutas para PMI (Project Management Institute)
Route::prefix('ia-pmi')->group(function () {
    Route::post('/codigo-etica', [IAPMIController::class, 'analizarCodigoEticaPMI']);
    Route::post('/estandares', [IAPMIController::class, 'analizarEstandaresPMI']);
    Route::post('/analisis-integral', [IAPMIController::class, 'analisisIntegralPMI']);
    Route::get('/estadisticas', [IAPMIController::class, 'estadisticasPMI']);
});

// Rutas consolidadas para todas las IAs de ética
Route::prefix('ias-etica')->group(function () {
    // Análisis integral de todas las IAs de ética
    Route::post('/analisis-integral', function (Request $request) {
        $tiposAnalisis = $request->input('tipos_analisis', ['pmp', 'ipma', 'pmi']);
        $resultados = [];
        
        foreach ($tiposAnalisis as $tipo) {
            switch ($tipo) {
                case 'pmp':
                    $controller = app(IAPMPController::class);
                    $resultados['pmp'] = $controller->analisisIntegralPMP($request);
                    break;
                case 'ipma':
                    $controller = app(IAIPMAController::class);
                    $resultados['ipma'] = $controller->analisisIntegralIPMA($request);
                    break;
                case 'pmi':
                    $controller = app(IAPMIController::class);
                    $resultados['pmi'] = $controller->analisisIntegralPMI($request);
                    break;
            }
        }
        
        return response()->json([
            'exito' => true,
            'datos' => $resultados
        ]);
    });
    
    // Estadísticas consolidadas
    Route::get('/estadisticas-consolidadas', function () {
        $estadisticas = [
            'pmp' => app(IAPMPController::class)->estadisticasPMP(),
            'ipma' => app(IAIPMAController::class)->estadisticasIPMA(),
            'pmi' => app(IAPMIController::class)->estadisticasPMI()
        ];
        
        return response()->json([
            'exito' => true,
            'datos' => $estadisticas
        ]);
    });
});
