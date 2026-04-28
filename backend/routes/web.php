<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\InstitucionWebController;
use Illuminate\Support\Facades\Route;

/*
 * Rutas Web — Sistema de Control de Asistencias
 * Prefijo: /p/ca
 */

// Ruta raíz
Route::get('/', function () {
    return redirect()->route('ca.login');
});

// Rutas públicas
Route::prefix('p/ca')->group(function () {

    // Autenticación
    Route::get('login',    [AuthWebController::class, 'showLogin'])->name('ca.login');
    Route::post('login',   [AuthWebController::class, 'login'])->name('ca.login.post');
    Route::get('registro', [AuthWebController::class, 'showRegistro'])->name('ca.registro');
    Route::post('registro',[AuthWebController::class, 'registro'])->name('ca.registro.post');

    // Rutas protegidas
    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthWebController::class, 'logout'])->name('ca.logout');

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('ca.dashboard.index');

        // Instituciones
        Route::get('instituciones',                        [InstitucionWebController::class, 'index'])->name('ca.instituciones.index');
        Route::get('instituciones/crear',                  [InstitucionWebController::class, 'create'])->name('ca.instituciones.create');
        Route::post('instituciones',                       [InstitucionWebController::class, 'store'])->name('ca.instituciones.store');
        Route::get('instituciones/{institucion}/editar',   [InstitucionWebController::class, 'edit'])->name('ca.instituciones.edit');
        Route::put('instituciones/{institucion}',          [InstitucionWebController::class, 'update'])->name('ca.instituciones.update');
        Route::delete('instituciones/{institucion}',       [InstitucionWebController::class, 'destroy'])->name('ca.instituciones.destroy');

        // Grupos
        Route::get('grupos', function () {
            return view('modules.grupos.index');
        })->name('ca.grupos.index');

        // Justificantes
        Route::get('justificantes', function () {
            return view('modules.justificantes.index');
        })->name('ca.justificantes.index');

        // Reportes
        Route::get('reportes', function () {
            return view('modules.reportes.index');
        })->name('ca.reportes.index');

        // Suscripción
        Route::get('suscripcion', function () {
            return view('modules.suscripcion.index');
        })->name('ca.suscripcion.index');
    });
});