<?php

namespace App\Http\Controllers;

use App\Services\SuscripcionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuscripcionController extends Controller
{
    public function __construct(
        private readonly SuscripcionService $suscripciones
    ) {}

    // GET /suscripcion
    public function show(Request $request): JsonResponse
    {
        $data = $this->suscripciones->obtener($request->user());
        return response()->json(['data' => $data]);
    }
}
