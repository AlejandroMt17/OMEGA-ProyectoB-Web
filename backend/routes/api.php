<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
 * Rutas API REST — Sistema de Control de Asistencias
 * Prefijo automático: /api
 */

// Rutas públicas
Route::post('auth/registro', [AuthController::class, 'registro']);
Route::post('auth/login',    [AuthController::class, 'login']);

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me',      [AuthController::class, 'me']);

    // Usuarios
    Route::get('usuarios',           [UsuarioController::class, 'index']);
    Route::post('usuarios',          [UsuarioController::class, 'store']);
    Route::get('usuarios/{usuario}', [UsuarioController::class, 'show']);
    Route::put('usuarios/{usuario}', [UsuarioController::class, 'update']);
    Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy']);

    // Instituciones
    Route::get('instituciones',                    [InstitucionController::class, 'index']);
    Route::post('instituciones',                   [InstitucionController::class, 'store']);
    Route::get('instituciones/{institucion}',      [InstitucionController::class, 'show']);
    Route::put('instituciones/{institucion}',      [InstitucionController::class, 'update']);
    Route::delete('instituciones/{institucion}',   [InstitucionController::class, 'destroy']);
});