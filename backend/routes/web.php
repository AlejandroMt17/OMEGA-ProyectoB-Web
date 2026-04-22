<?php

/**
 * Rutas web (HTML / Blade). La API REST vive en routes/api.php.
 */

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
