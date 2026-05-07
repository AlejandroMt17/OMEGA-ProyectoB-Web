<?php

namespace App\Repositories\Contracts;

use App\Models\Asistencia;
use Illuminate\Database\Eloquent\Collection;

interface AsistenciaRepositoryInterface
{
    public function porSesion(int $sesionId): Collection;
    public function buscarPorSesionYAlumno(int $sesionId, int $alumnoId): ?Asistencia;
    public function crearParaAlumnos(int $sesionId, array $alumnoIds): void;
    public function actualizar(Asistencia $asistencia, array $datos): bool;
}