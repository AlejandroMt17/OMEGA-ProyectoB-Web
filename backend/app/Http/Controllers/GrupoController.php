<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Services\GrupoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function __construct(
        private readonly GrupoService $grupos
    ) {}

    public function index(Request $request, int $institucion): JsonResponse
    {
        $data = $this->grupos->listarPorId($institucion);
        return response()->json(['data' => $data]);
    }

    public function store(Request $request, int $institucion): JsonResponse
    {
        $data = $this->grupos->crearEnInstitucion($institucion, $request->all());
        return response()->json(['data' => $data], 201);
    }

    public function show(int $grupo): JsonResponse
    {
        $data = $this->grupos->obtenerPorId($grupo);
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, int $grupo): JsonResponse
    {
        $data = $this->grupos->actualizarPorId($grupo, $request->all());
        return response()->json(['data' => $data]);
    }

    public function destroy(int $grupo): JsonResponse
    {
        $this->grupos->eliminarPorId($grupo);
        return response()->noContent();
    }
}