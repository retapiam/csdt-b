<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IADerechosEspecializadosController;

/*
|--------------------------------------------------------------------------
| API Routes - IA Derechos Especializados
|--------------------------------------------------------------------------
|
| Aquí se definen las rutas para los servicios de IA especializados
| en diferentes áreas de derechos.
|
*/

Route::prefix('ia-derechos')->group(function () {
    
    // Análisis de Derechos Mineros
    Route::post('/mineros', [IADerechosEspecializadosController::class, 'analizarDerechosMineros'])
        ->name('ia.derechos.mineros');
    
    // Análisis de Derechos Catastrales
    Route::post('/catastrales', [IADerechosEspecializadosController::class, 'analizarDerechosCatastrales'])
        ->name('ia.derechos.catastrales');
    
    // Análisis de Desarrollo Territorial
    Route::post('/desarrollo-territorial', [IADerechosEspecializadosController::class, 'analizarDesarrolloTerritorial'])
        ->name('ia.derechos.desarrollo.territorial');
    
    // Análisis de Planes de Desarrollo y Gobierno
    Route::post('/planes-desarrollo-gobierno', [IADerechosEspecializadosController::class, 'analizarPlanesDesarrolloGobierno'])
        ->name('ia.derechos.planes.desarrollo.gobierno');
    
    // Análisis de Derechos Internacionales
    Route::post('/internacionales', [IADerechosEspecializadosController::class, 'analizarDerechosInternacionales'])
        ->name('ia.derechos.internacionales');
    
    // Análisis de Derechos CAN e INCA
    Route::post('/can-inca', [IADerechosEspecializadosController::class, 'analizarDerechosCanInca'])
        ->name('ia.derechos.can.inca');
    
    // Análisis de Derechos Latinoamericanos
    Route::post('/latinoamericanos', [IADerechosEspecializadosController::class, 'analizarDerechosLatinoamericanos'])
        ->name('ia.derechos.latinoamericanos');
    
    // Análisis de Derechos de Propiedad
    Route::post('/propiedad', [IADerechosEspecializadosController::class, 'analizarDerechosPropiedad'])
        ->name('ia.derechos.propiedad');
    
    // Análisis de Derechos en Comunidades Étnicas
    Route::post('/comunidades-etnicas', [IADerechosEspecializadosController::class, 'analizarDerechosComunidadesEtnicas'])
        ->name('ia.derechos.comunidades.etnicas');
    
    // Obtener especialidades disponibles
    Route::get('/especialidades', [IADerechosEspecializadosController::class, 'obtenerEspecialidades'])
        ->name('ia.derechos.especialidades');
});

// Rutas adicionales para análisis combinados
Route::prefix('ia-derechos-combinados')->group(function () {
    
    // Análisis combinado de derechos mineros y étnicos
    Route::post('/mineros-etnicos', function (Request $request) {
        $iaDerechos = app(IADerechosEspecializados::class);
        
        $resultadoMinero = $iaDerechos->analizarDerechosMineros(
            $request->datos_mineros,
            $request->tipo_mineria ?? 'general'
        );
        
        $resultadoEtnico = $iaDerechos->analizarDerechosComunidadesEtnicas(
            $request->datos_etnicos,
            $request->tipo_comunidad ?? 'indigena'
        );
        
        return response()->json([
            'success' => true,
            'analisis_combinado' => [
                'derechos_mineros' => $resultadoMinero,
                'derechos_etnicos' => $resultadoEtnico,
                'recomendaciones_integradas' => 'Análisis combinado de derechos mineros y étnicos para casos de minería en territorios indígenas'
            ]
        ]);
    })->name('ia.derechos.combinados.mineros.etnicos');
    
    // Análisis combinado de derechos catastrales y territoriales
    Route::post('/catastrales-territoriales', function (Request $request) {
        $iaDerechos = app(IADerechosEspecializados::class);
        
        $resultadoCatastral = $iaDerechos->analizarDerechosCatastrales(
            $request->datos_catastrales,
            $request->tipo_predio ?? 'general'
        );
        
        $resultadoTerritorial = $iaDerechos->analizarDesarrolloTerritorial(
            $request->datos_territoriales,
            $request->nivel_gobierno ?? 'municipal'
        );
        
        return response()->json([
            'success' => true,
            'analisis_combinado' => [
                'derechos_catastrales' => $resultadoCatastral,
                'desarrollo_territorial' => $resultadoTerritorial,
                'recomendaciones_integradas' => 'Análisis combinado de derechos catastrales y desarrollo territorial para casos de planificación urbana'
            ]
        ]);
    })->name('ia.derechos.combinados.catastrales.territoriales');
    
    // Análisis integral de derechos
    Route::post('/integral', function (Request $request) {
        $iaDerechos = app(IADerechosEspecializados::class);
        
        $analisis = [];
        
        if ($request->has('datos_mineros')) {
            $analisis['derechos_mineros'] = $iaDerechos->analizarDerechosMineros(
                $request->datos_mineros,
                $request->tipo_mineria ?? 'general'
            );
        }
        
        if ($request->has('datos_catastrales')) {
            $analisis['derechos_catastrales'] = $iaDerechos->analizarDerechosCatastrales(
                $request->datos_catastrales,
                $request->tipo_predio ?? 'general'
            );
        }
        
        if ($request->has('datos_territoriales')) {
            $analisis['desarrollo_territorial'] = $iaDerechos->analizarDesarrolloTerritorial(
                $request->datos_territoriales,
                $request->nivel_gobierno ?? 'municipal'
            );
        }
        
        if ($request->has('datos_etnicos')) {
            $analisis['derechos_comunidades_etnicas'] = $iaDerechos->analizarDerechosComunidadesEtnicas(
                $request->datos_etnicos,
                $request->tipo_comunidad ?? 'indigena'
            );
        }
        
        return response()->json([
            'success' => true,
            'analisis_integral' => $analisis,
            'recomendaciones_generales' => 'Análisis integral de múltiples áreas de derechos para casos complejos'
        ]);
    })->name('ia.derechos.integral');
});
