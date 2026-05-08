<?php

namespace App\Services;

use App\Models\Sesion;
use App\Repositories\Contracts\AsistenciaRepositoryInterface;
use App\Repositories\Contracts\GrupoRepositoryInterface;
use App\Repositories\Contracts\SesionRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SesionService
{
    public function __construct(
        private readonly SesionRepositoryInterface     $sesiones,
        private readonly AsistenciaRepositoryInterface $asistencias,
        private readonly GrupoRepositoryInterface      $grupos,
    ) {}

    public function listar(int $grupoId): array
    {
        return $this->sesiones->porGrupo($grupoId)
            ->map(fn(Sesion $s) => $this->serializar($s))
            ->values()
            ->all();
    }

    public function obtener(int $id): array
    {
        $sesion = $this->sesiones->buscarPorId($id);
        abort_if(!$sesion, 404, 'Sesion no encontrada.');
        return $this->serializarDetalle($sesion);
    }

    public function abrir(int $grupoId): array
    {
        $activa = $this->sesiones->sesionActiva($grupoId);
        if ($activa) {
            throw ValidationException::withMessages([
                'grupo' => ['Ya existe una sesion activa para este grupo.'],
            ]);
        }

        $sesion = $this->sesiones->crear([
            'id_grupo'      => $grupoId,
            'est_sesion'    => 1,
            'fec_sesion'    => Carbon::today(),
            'hora_apertura' => Carbon::now(),
            'hora_cierre'   => null,
            'clave'         => strtoupper(Str::random(6)),
        ]);

        $grupo     = $this->grupos->buscarPorId($grupoId);
        $alumnoIds = $grupo->alumnos->pluck('id_usuario')->toArray();

        if (!empty($alumnoIds)) {
            $this->asistencias->crearParaAlumnos($sesion->id_sesion, $alumnoIds);
        }

        return $this->serializar($sesion);
    }

    public function cerrar(int $id): array
    {
        $sesion = $this->sesiones->buscarPorId($id);
        abort_if(!$sesion, 404, 'Sesion no encontrada.');
        abort_if($sesion->est_sesion === 2, 422, 'La sesion ya esta cerrada.');

        $this->sesiones->actualizar($sesion, [
            'est_sesion'  => 2,
            'hora_cierre' => Carbon::now(),
            'clave'       => null,
        ]);

        return $this->serializar($sesion->fresh());
    }

    public function registrarAsistenciaAlumno(int $sesionId, int $alumnoId, string $clave): array
    {
        $sesion = $this->sesiones->buscarPorId($sesionId);
        abort_if(!$sesion,                  404, 'Sesion no encontrada.');
        abort_if($sesion->est_sesion !== 1, 422, 'La sesion no esta activa.');
        abort_if(
            strtoupper(trim($clave)) !== $sesion->clave,
            422,
            'Clave incorrecta.'
        );

        $asistencia = $this->asistencias->buscarPorSesionYAlumno($sesionId, $alumnoId);
        abort_if(!$asistencia, 404, 'No estas inscrito en este grupo.');
        abort_if($asistencia->est_asistencia === 1, 422, 'Ya registraste tu asistencia.');

        $this->asistencias->actualizar($asistencia, [
            'est_asistencia' => 1,
            'hora_registro'  => Carbon::now(),
        ]);

        return [
            'id_asistencia'  => $asistencia->id_asistencia,
            'est_asistencia' => 1,
            'hora_registro'  => Carbon::now()->toDateTimeString(),
        ];
    }

    public function actualizarAsistencia(int $sesionId, int $alumnoId, array $entrada): array
    {
        $validator = Validator::make($entrada, [
            'est_asistencia' => ['required', 'integer', 'in:1,2,3'],
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $asistencia = $this->asistencias->buscarPorSesionYAlumno($sesionId, $alumnoId);
        abort_if(!$asistencia, 404, 'Registro de asistencia no encontrado.');

        $datos = ['est_asistencia' => $entrada['est_asistencia']];
        if ($entrada['est_asistencia'] === 1) {
            $datos['hora_registro'] = Carbon::now();
        }

        $this->asistencias->actualizar($asistencia, $datos);

        return [
            'id_asistencia'  => $asistencia->id_asistencia,
            'id_sesion'      => $asistencia->id_sesion,
            'id_alumno'      => $asistencia->id_alumno,
            'est_asistencia' => $asistencia->fresh()->est_asistencia,
            'hora_registro'  => $asistencia->fresh()->hora_registro,
        ];
    }

    public function historial(int $grupoId): array
    {
        return $this->sesiones->porGrupo($grupoId)
            ->where('est_sesion', 2)
            ->map(function (Sesion $s) {
                $asistencias  = $this->asistencias->porSesion($s->id_sesion);
                $total        = $asistencias->count();
                $presentes    = $asistencias->where('est_asistencia', 1)->count();
                $faltas       = $asistencias->where('est_asistencia', 2)->count();
                $justificadas = $asistencias->where('est_asistencia', 3)->count();

                return [
                    'id_sesion'     => $s->id_sesion,
                    'fec_sesion'    => $s->fec_sesion?->toDateString(),
                    'hora_apertura' => $s->hora_apertura?->toTimeString(),
                    'hora_cierre'   => $s->hora_cierre?->toTimeString(),
                    'total_alumnos' => $total,
                    'presentes'     => $presentes,
                    'faltas'        => $faltas,
                    'justificadas'  => $justificadas,
                ];
            })
            ->values()
            ->all();
    }

    private function serializar(Sesion $sesion): array
    {
        return [
            'id_sesion'     => $sesion->id_sesion,
            'id_grupo'      => $sesion->id_grupo,
            'est_sesion'    => $sesion->est_sesion,
            'fec_sesion'    => $sesion->fec_sesion?->toDateString(),
            'hora_apertura' => $sesion->hora_apertura?->toDateTimeString(),
            'hora_cierre'   => $sesion->hora_cierre?->toDateTimeString(),
            'clave'         => $sesion->clave,
        ];
    }

    private function serializarDetalle(Sesion $sesion): array
    {
        $asistencias = $this->asistencias->porSesion($sesion->id_sesion);

        return array_merge($this->serializar($sesion), [
            'asistencias' => $asistencias->map(fn($a) => [
                'id_asistencia'  => $a->id_asistencia,
                'id_alumno'      => $a->id_alumno,
                'nombre_alumno'  => $a->alumno
                    ? "{$a->alumno->nombre} {$a->alumno->ap_pat}"
                    : null,
                'est_asistencia' => $a->est_asistencia,
                'hora_registro'  => $a->hora_registro?->toDateTimeString(),
            ])->values()->all(),
        ]);
    }
}