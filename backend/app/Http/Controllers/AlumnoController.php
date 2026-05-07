<?php

namespace App\Http\Controllers;

use App\Models\GrupoAlumno;
use App\Models\Asistencia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function grupos(Request $request): JsonResponse
    {
        $alumnoId = $request->user()->id_usuario;

        $grupos = GrupoAlumno::query()
            ->with(['grupo.institucion'])
            ->where('id_alumno', $alumnoId)
            ->get()
            ->map(function ($ga) use ($alumnoId) {
                $grupo = $ga->grupo;

                $asistencias = Asistencia::query()
                    ->whereHas('sesion', fn($q) => $q->where('id_grupo', $grupo->id_grupo))
                    ->where('id_alumno', $alumnoId)
                    ->get();

                $total       = $asistencias->count();
                $presentes   = $asistencias->where('est_asistencia', 1)->count();
                $faltas      = $asistencias->where('est_asistencia', 2)->count();
                $justificadas = $asistencias->where('est_asistencia', 3)->count();

                return [
                    'id_grupo'        => $grupo->id_grupo,
                    'nombre'          => $grupo->nombre,
                    'materia'         => $grupo->materia,
                    'periodo'         => $grupo->periodo,
                    'codigo_inv'      => $grupo->codigo_inv,
                    'id_institucion'  => $grupo->id_institucion,
                    'nombre_institucion' => $grupo->institucion?->nombre,
                    'total_sesiones'  => $total,
                    'presentes'       => $presentes,
                    'faltas'          => $faltas,
                    'justificadas'    => $justificadas,
                ];
            });

        return response()->json(['data' => $grupos]);
    }
}