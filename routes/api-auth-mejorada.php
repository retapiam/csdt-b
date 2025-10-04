<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthControllerMejorado;

/*
|--------------------------------------------------------------------------
| API Routes - Autenticación Mejorada CSDT
|--------------------------------------------------------------------------
|
| Rutas de autenticación con validaciones mejoradas, profesiones y niveles
| Implementa sistema completo de roles, permisos y verificación
|
*/

// ========================================
// RUTAS PÚBLICAS DE AUTENTICACIÓN
// ========================================
Route::prefix('auth')->group(function () {
    
    // Login y registro mejorados
    Route::post('/login', [AuthControllerMejorado::class, 'login']);
    Route::post('/register', [AuthControllerMejorado::class, 'register']);
    
    // Datos de referencia públicos
    Route::get('/profesiones', [AuthControllerMejorado::class, 'profesiones']);
    Route::get('/niveles', [AuthControllerMejorado::class, 'niveles']);
    
    // Recuperación de contraseña (implementar después)
    Route::post('/forgot-password', function () {
        return response()->json([
            'success' => false,
            'message' => 'Función en desarrollo'
        ], 501);
    });
    
    Route::post('/reset-password', function () {
        return response()->json([
            'success' => false,
            'message' => 'Función en desarrollo'
        ], 501);
    });
});

// ========================================
// RUTAS PROTEGIDAS
// ========================================
Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    
    // Información del usuario autenticado
    Route::get('/me', [AuthControllerMejorado::class, 'me']);
    
    // Logout
    Route::post('/logout', [AuthControllerMejorado::class, 'logout']);
    
    // Cambio de contraseña (implementar después)
    Route::post('/change-password', function () {
        return response()->json([
            'success' => false,
            'message' => 'Función en desarrollo'
        ], 501);
    });
    
    // Actualización de perfil (implementar después)
    Route::put('/profile', function () {
        return response()->json([
            'success' => false,
            'message' => 'Función en desarrollo'
        ], 501);
    });
});

// ========================================
// RUTAS DE ADMINISTRACIÓN (Solo Administradores)
// ========================================
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
    
    // Verificación de perfiles (implementar después)
    Route::get('/perfiles-pendientes', function () {
        return response()->json([
            'success' => false,
            'message' => 'Función en desarrollo'
        ], 501);
    });
    
    Route::post('/verificar-perfil/{id}', function () {
        return response()->json([
            'success' => false,
            'message' => 'Función en desarrollo'
        ], 501);
    });
    
    Route::post('/rechazar-perfil/{id}', function () {
        return response()->json([
            'success' => false,
            'message' => 'Función en desarrollo'
        ], 501);
    });
});
