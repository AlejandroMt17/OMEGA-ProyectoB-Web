<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Services\InstitucionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstitucionController extends Controller
{
    public function __construct(
        private readonly InstitucionService $instituciones
    ) {}

    public function index(Request $request): JsonResponse
    {
        $data = $this->instituciones->listar($request->user());
        return response()->json(['data' => $data]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->instituciones->crear($request->user(), $request->all());
        return response()->json(['data' => $data], 201);
    }

    public function show(Institucion $institucion): JsonResponse
    {
        $data = $this->instituciones->obtener($institucion);
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, Institucion $institucion): JsonResponse
    {
        $data = $this->instituciones->actualizar($institucion, $request->all());
        return response()->json(['data' => $data]);
    }

    public function destroy(Institucion $institucion): JsonResponse
    {
        $this->instituciones->eliminar($institucion);
        return response()->noContent();
    }
}