<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\RubroController;
use App\Http\Controllers\SesionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnoController;  

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

    Route::get('grupos/{grupo}/sesiones',         [SesionController::class, 'index']);
    Route::post('grupos/{grupo}/sesiones/abrir',  [SesionController::class, 'abrir']);
    Route::get('sesiones/{sesion}',               [SesionController::class, 'show']);
    Route::post('sesiones/{sesion}/cerrar',       [SesionController::class, 'cerrar']);
    Route::patch('sesiones/{sesion}/alumnos/{alumno}/asistencia', [SesionController::class, 'actualizarAsistencia']);

    Route::get('instituciones/{institucion}/rubros',    [RubroController::class, 'index']);
    Route::post('instituciones/{institucion}/rubros',   [RubroController::class, 'store']);
    Route::get('rubros/{rubro}',                        [RubroController::class, 'show']);
    Route::put('rubros/{rubro}',                        [RubroController::class, 'update']);
    Route::delete('rubros/{rubro}',                     [RubroController::class, 'destroy']);
    Route::get('alumno/grupos', [AlumnoController::class, 'grupos']);
    Route::get('grupos/{grupo}/sesiones/historial', [SesionController::class, 'historial']);
    Route::get('grupos/{grupo}/alumnos', [GrupoController::class, 'alumnos']);
    Route::delete('grupos/{grupo}/alumnos/{alumno}', [GrupoController::class, 'eliminarAlumno']);
});