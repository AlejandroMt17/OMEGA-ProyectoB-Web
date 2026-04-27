<?php

use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
 * Rutas API REST — Sistema de Control de Asistencias
 * Prefijo automático: /api
 */

Route::apiResource('usuarios', UsuarioController::class);