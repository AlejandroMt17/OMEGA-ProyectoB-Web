<?php

namespace App\Http\Controllers;

use App\Models\Sesion;
use App\Services\SesionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador HTTP — Gestión de sesiones de asistencia.
 * Sin lógica de negocio, solo delega al SesionService.
 */
class SesionController extends Controller
{
    public function __construct(
        private readonly SesionService $sesiones
    ) {}

    public function index(Request $request, int $idGrupo): JsonResponse
    {
        return response()->json([
            'data' => $this->sesiones->listar($idGrupo, $request->user()),
        ]);
    }

    public function abrir(Request $request, int $idGrupo): JsonResponse
    {
        $sesion = $this->sesiones->abrir($idGrupo, $request->all(), $request->user());
        return response()->json(['data' => $sesion], 201);
    }

    public function cerrar(Request $request, Sesion $sesion): JsonResponse
    {
        $actualizada = $this->sesiones->cerrar($sesion, $request->user());
        return response()->json(['data' => $actualizada]);
    }

    public function show(Request $request, Sesion $sesion): JsonResponse
    {
        return response()->json([
            'data' => $this->sesiones->obtener($sesion, $request->user()),
        ]);
    }
}