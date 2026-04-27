<?php

use App\Http\Controllers\AuthController;
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

    Route::apiResource('usuarios', UsuarioController::class);
});