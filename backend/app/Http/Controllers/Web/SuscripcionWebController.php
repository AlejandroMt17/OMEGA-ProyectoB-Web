<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SuscripcionService;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador Web — Vista de suscripción del Docente.
 * @version 1.0.0
 */
class SuscripcionWebController extends Controller
{
    public function __construct(
        private readonly SuscripcionService $suscripciones,
    ) {}

    public function index()
    {
        $suscripcion = $this->suscripciones->obtener(Auth::user());
        return view('modules.suscripcion.index', compact('suscripcion'));
    }
}