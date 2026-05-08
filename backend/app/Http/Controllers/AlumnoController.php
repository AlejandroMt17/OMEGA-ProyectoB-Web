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
            ->with(['grupo.institucion.rubros', 'grupo.sesionActiva'])
            ->where('id_alumno', $alumnoId)
            ->get()
            ->map(function ($ga) use ($alumnoId) {
                $grupo = $ga->grupo;

                $asistencias = Asistencia::query()
                    ->whereHas('sesion', fn($q) => $q->where('id_grupo', $grupo->id_grupo))
                    ->where('id_alumno', $alumnoId)
                    ->get();

                $total        = $asistencias->count();
                $presentes    = $asistencias->where('est_asistencia', 1)->count();
                $faltas       = $asistencias->where('est_asistencia', 2)->count();
                $justificadas = $asistencias->where('est_asistencia', 3)->count();

                $sesionActiva = $grupo->sesionActiva;

                $rubros = $grupo->institucion?->rubros
                    ->sortByDesc('porcentaje_minimo')
                    ->values()
                    ->map(fn($r) => [
                        'nombre'            => $r->nombre,
                        'porcentaje_minimo' => (float) $r->porcentaje_minimo,
                    ])->all() ?? [];

                return [
                    'id_grupo'           => $grupo->id_grupo,
                    'nombre'             => $grupo->nombre,
                    'materia'            => $grupo->materia,
                    'periodo'            => $grupo->periodo,
                    'codigo_inv'         => $grupo->codigo_inv,
                    'id_institucion'     => $grupo->id_institucion,
                    'nombre_institucion' => $grupo->institucion?->nombre,
                    'total_sesiones'     => $total,
                    'presentes'          => $presentes,
                    'faltas'             => $faltas,
                    'justificadas'       => $justificadas,
                    'rubros'             => $rubros,
                    'sesion_activa'      => $sesionActiva ? [
                        'id_sesion'     => $sesionActiva->id_sesion,
                        'hora_apertura' => $sesionActiva->hora_apertura?->toDateTimeString(),
                        'clave'         => $sesionActiva->clave,
                    ] : null,
                ];
            });

        return response()->json(['data' => $grupos]);
    }

    public function unirseGrupo(Request $request): JsonResponse
    {
        $codigo   = $request->input('codigo');
        $alumnoId = $request->user()->id_usuario;

        $grupo = \App\Models\Grupo::query()
            ->where('codigo_inv', strtoupper($codigo))
            ->first();

        if (!$grupo) {
            return response()->json(['message' => 'Codigo invalido o grupo no encontrado.'], 404);
        }

        $yaInscrito = $grupo->alumnos()->where('id_alumno', $alumnoId)->exists();

        if ($yaInscrito) {
            return response()->json(['message' => 'Ya estas inscrito en este grupo.'], 422);
        }

        $grupo->alumnos()->attach($alumnoId, [
            'fec_inscripcion' => now()->toDateString(),
        ]);

        return response()->json(['data' => [
            'id_grupo'   => $grupo->id_grupo,
            'nombre'     => $grupo->nombre,
            'materia'    => $grupo->materia,
            'codigo_inv' => $grupo->codigo_inv,
        ]], 201);
    }
}