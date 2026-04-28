<?php

namespace App\Services;

use App\Models\Asistencia;
use App\Models\Sesion;
use App\Models\Usuario;
use App\Repositories\Contracts\AsistenciaRepositoryInterface;
use App\Repositories\Contracts\SesionRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AsistenciaService
{
    public function __construct(
        private readonly AsistenciaRepositoryInterface $asistencias,
        private readonly SesionRepositoryInterface     $sesiones,
    ) {}

    public function listarPorSesion(int $idSesion): array
    {
        return $this->asistencias->todasPorSesion($idSesion)
            ->map(fn(Asistencia $a) => $this->serializar($a))
            ->values()
            ->all();
    }

    public function registrar(array $entrada, Usuario $alumno): array
    {
        $validator = Validator::make($entrada, [
            'clave'    => ['required', 'string'],
            'id_grupo' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Buscar sesión activa del grupo
        $sesion = $this->sesiones->buscarActivaPorGrupo($entrada['id_grupo']);

        if (!$sesion) {
            throw ValidationException::withMessages([
                'sesion' => ['No hay una sesión activa para este grupo.'],
            ]);
        }

        // Validar clave
        if ($sesion->clave !== strtoupper($entrada['clave'])) {
            throw ValidationException::withMessages([
                'clave' => ['La clave de asistencia es incorrecta.'],
            ]);
        }

        // Validar duplicado
        $existe = $this->asistencias->buscarPorSesionYAlumno($sesion->id_sesion, $alumno->id_usuario);
        if ($existe) {
            throw ValidationException::withMessages([
                'asistencia' => ['Ya registraste tu asistencia en esta sesión.'],
            ]);
        }

        $asistencia = $this->asistencias->crear([
            'id_sesion'      => $sesion->id_sesion,
            'id_alumno'      => $alumno->id_usuario,
            'est_asistencia' => 1, // Presente
            'hora_registro'  => now(),
        ]);

        return $this->serializar($asistencia);
    }

    public function editarEstado(Asistencia $asistencia, array $entrada): array
    {
        $validator = Validator::make($entrada, [
            'est_asistencia' => ['required', 'integer', 'in:1,2,3'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $this->asistencias->guardar($asistencia, [
            'est_asistencia' => $entrada['est_asistencia'],
        ]);

        return $this->serializar($asistencia->fresh());
    }

    private function serializar(Asistencia $asistencia): array
    {
        return [
            'id_asistencia'  => $asistencia->id_asistencia,
            'id_sesion'      => $asistencia->id_sesion,
            'id_alumno'      => $asistencia->id_alumno,
            'est_asistencia' => $asistencia->est_asistencia,
            'hora_registro'  => $asistencia->hora_registro?->toIso8601String(),
        ];
    }
}