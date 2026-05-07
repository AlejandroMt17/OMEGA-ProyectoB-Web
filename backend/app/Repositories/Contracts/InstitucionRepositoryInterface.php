<?php

namespace App\Repositories\Contracts;

use App\Models\Institucion;
use Illuminate\Database\Eloquent\Collection;

interface InstitucionRepositoryInterface
{
    public function porDocente(int $docenteId): Collection;
    public function buscarPorId(int $id): ?Institucion;
    public function crear(array $datos): Institucion;
    public function actualizar(Institucion $institucion, array $datos): bool;
    public function eliminar(Institucion $institucion): bool;
}