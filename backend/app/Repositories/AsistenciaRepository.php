<?php

namespace App\Repositories;

use App\Models\Asistencia;
use App\Repositories\Contracts\AsistenciaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AsistenciaRepository implements AsistenciaRepositoryInterface
{
    public function porSesion(int $sesionId): Collection
    {
        return Asistencia::query()
            ->with('alumno')
            ->where('id_sesion', $sesionId)
            ->get();
    }

    public function buscarPorSesionYAlumno(int $sesionId, int $alumnoId): ?Asistencia
    {
        return Asistencia::query()
            ->where('id_sesion', $sesionId)
            ->where('id_alumno', $alumnoId)
            ->first();
    }

    public function crearParaAlumnos(int $sesionId, array $alumnoIds): void
    {
        $registros = array_map(fn($id) => [
            'id_sesion'      => $sesionId,
            'id_alumno'      => $id,
            'est_asistencia' => 2,
            'hora_registro'  => null,
        ], $alumnoIds);

        Asistencia::insert($registros);
    }

    public function actualizar(Asistencia $asistencia, array $datos): bool
    {
        return $asistencia->update($datos);
    }
}