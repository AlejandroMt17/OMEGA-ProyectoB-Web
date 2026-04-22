<?php

/**
 * Rutas API REST (prefijo /api).
 * Definir aquí los recursos expuestos al cliente (p. ej. la app Flutter).
 */

use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::apiResource('usuarios', UsuarioController::class);
