<?php

use App\Http\Controllers\Web\AuthWebController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
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
        Route::get('instituciones', function () {
            return view('modules.instituciones.index');
        })->name('ca.instituciones.index');

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