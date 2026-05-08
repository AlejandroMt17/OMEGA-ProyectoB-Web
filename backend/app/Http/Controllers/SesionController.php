<?php

namespace App\Http\Controllers;

use App\Services\SesionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SesionController extends Controller
{
    public function __construct(
        private readonly SesionService $sesiones
    ) {}

    public function index(int $grupo): JsonResponse
    {
        $data = $this->sesiones->listar($grupo);
        return response()->json(['data' => $data]);
    }

    public function show(int $sesion): JsonResponse
    {
        $data = $this->sesiones->obtener($sesion);
        return response()->json(['data' => $data]);
    }

    public function abrir(int $grupo): JsonResponse
    {
        $data = $this->sesiones->abrir($grupo);
        return response()->json(['data' => $data], 201);
    }

    public function cerrar(int $sesion): JsonResponse
    {
        $data = $this->sesiones->cerrar($sesion);
        return response()->json(['data' => $data]);
    }

    public function actualizarAsistencia(Request $request, int $sesion, int $alumno): JsonResponse
    {
        $data = $this->sesiones->actualizarAsistencia($sesion, $alumno, $request->all());
        return response()->json(['data' => $data]);
    }
    public function historial(int $grupo): JsonResponse
    {
        $data = $this->sesiones->historial($grupo);
        return response()->json(['data' => $data]);
    }
    public function registrarAsistenciaAlumno(Request $request, int $sesion): JsonResponse
    {
        $alumnoId = $request->user()->id_usuario;
        $clave    = $request->input('clave', '');

        $data = $this->sesiones->registrarAsistenciaAlumno($sesion, $alumnoId, $clave);

        return response()->json(['data' => $data]);
    }
}