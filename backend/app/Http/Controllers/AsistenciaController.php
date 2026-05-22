<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Services\AsistenciaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador HTTP — Registro y gestión de asistencias.
 * Sin lógica de negocio, solo delega al AsistenciaService.
 */
class AsistenciaController extends Controller
{
    public function __construct(
        private readonly AsistenciaService $asistencias
    ) {}

    public function porSesion(int $idSesion): JsonResponse
    {
        return response()->json([
            'data' => $this->asistencias->listarPorSesion($idSesion),
        ]);
    }

    public function registrar(Request $request): JsonResponse
    {
        $asistencia = $this->asistencias->registrar($request->all(), $request->user());
        return response()->json(['data' => $asistencia], 201);
    }

    public function editarEstado(Request $request, Asistencia $asistencia): JsonResponse
    {
        $actualizada = $this->asistencias->editarEstado($asistencia, $request->all());
        return response()->json(['data' => $actualizada]);
    }
}