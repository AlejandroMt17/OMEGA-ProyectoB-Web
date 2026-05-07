<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\SesionController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me',      [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('instituciones', InstitucionController::class);
    Route::apiResource('instituciones.grupos', GrupoController::class)->shallow();

    Route::get('grupos/{grupo}/sesiones',          [SesionController::class, 'index']);
    Route::post('grupos/{grupo}/sesiones/abrir',   [SesionController::class, 'abrir']);
    Route::get('sesiones/{sesion}',                [SesionController::class, 'show']);
    Route::post('sesiones/{sesion}/cerrar',        [SesionController::class, 'cerrar']);
    Route::patch('sesiones/{sesion}/alumnos/{alumno}/asistencia', [SesionController::class, 'actualizarAsistencia']);
});