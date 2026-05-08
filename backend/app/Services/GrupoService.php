<?php

namespace App\Services;

use App\Models\Grupo;
use App\Repositories\Contracts\GrupoRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GrupoService
{
    public function __construct(
        private readonly GrupoRepositoryInterface $grupos
    ) {}

    public function listarPorId(int $institucionId): array
    {
        return $this->grupos->porInstitucion($institucionId)
            ->map(fn(Grupo $g) => $this->serializar($g))
            ->values()
            ->all();
    }

    public function obtenerPorId(int $id): array
    {
        $grupo = $this->grupos->buscarPorId($id);
        abort_if(!$grupo, 404, 'Grupo no encontrado.');
        return $this->serializar($grupo);
    }

    public function crearEnInstitucion(int $institucionId, array $entrada): array
    {
        $datos = $this->validarCreacion($entrada);
        $grupo = $this->grupos->crear([
            'id_institucion' => $institucionId,
            'nombre'         => $datos['nombre'],
            'materia'        => $datos['materia'],
            'periodo'        => $datos['periodo'] ?? null,
            'no_alumnos'     => 0,
            'codigo_inv'     => $this->generarCodigo($datos['materia']),
        ]);
        return $this->serializar($grupo);
    }

    public function actualizarPorId(int $id, array $entrada): array
    {
        $grupo = $this->grupos->buscarPorId($id);
        abort_if(!$grupo, 404, 'Grupo no encontrado.');
        $datos = $this->validarActualizacion($entrada);
        $this->grupos->actualizar($grupo, $datos);
        return $this->serializar($grupo->fresh());
    }

    public function eliminarPorId(int $id): void
    {
        $grupo = $this->grupos->buscarPorId($id);
        abort_if(!$grupo, 404, 'Grupo no encontrado.');
        $this->grupos->eliminar($grupo);
    }

    private function generarCodigo(string $materia): string
    {
        $prefijo = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $materia), 0, 3));
        do {
            $codigo = $prefijo . '-' . strtoupper(Str::random(5));
        } while ($this->grupos->buscarPorCodigo($codigo));
        return $codigo;
    }

    private function serializar(Grupo $grupo): array
    {
        return [
            'id_grupo'       => $grupo->id_grupo,
            'id_institucion' => $grupo->id_institucion,
            'nombre'         => $grupo->nombre,
            'materia'        => $grupo->materia,
            'periodo'        => $grupo->periodo,
            'no_alumnos'     => $grupo->no_alumnos,
            'codigo_inv'     => $grupo->codigo_inv,
        ];
    }

    private function validarCreacion(array $entrada): array
    {
        $validator = Validator::make($entrada, [
            'nombre'  => ['required', 'string', 'max:100'],
            'materia' => ['required', 'string', 'max:150'],
            'periodo' => ['nullable', 'string', 'max:50'],
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }

    private function validarActualizacion(array $entrada): array
    {
        $validator = Validator::make($entrada, [
            'nombre'  => ['sometimes', 'required', 'string', 'max:100'],
            'materia' => ['sometimes', 'required', 'string', 'max:150'],
            'periodo' => ['nullable', 'string', 'max:50'],
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
    public function alumnos(int $grupoId): array
    {
        $grupo = $this->grupos->buscarPorId($grupoId);
        abort_if(!$grupo, 404, 'Grupo no encontrado.');

        return $grupo->alumnos->map(function ($alumno) use ($grupoId) {
            $asistencias = \App\Models\Asistencia::query()
                ->whereHas('sesion', fn($q) => $q
                    ->where('id_grupo', $grupoId)
                    ->where('est_sesion', 2))
                ->where('id_alumno', $alumno->id_usuario)
                ->get();

            $total     = $asistencias->count();
            $asistidas = $asistencias->where('est_asistencia', 1)->count()
                    + $asistencias->where('est_asistencia', 3)->count();

            return [
                'alumno_id'          => $alumno->id_usuario,
                'nombre'             => $alumno->nombre,
                'ap_pat'             => $alumno->ap_pat,
                'ap_mat'             => $alumno->ap_mat ?? '',
                'email'              => $alumno->email,
                'total_sesiones'     => $total,
                'sesiones_asistidas' => $asistidas,
                'fecha_inscripcion'  => $alumno->pivot->fec_inscripcion ?? '',
            ];
        })->values()->all();
    }
    public function eliminarAlumno(int $grupoId, int $alumnoId): void
    {
        $grupo = $this->grupos->buscarPorId($grupoId);
        abort_if(!$grupo, 404, 'Grupo no encontrado.');

        $grupo->alumnos()->detach($alumnoId);
    }
}