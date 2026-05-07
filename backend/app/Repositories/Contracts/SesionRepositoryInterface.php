<?php

namespace App\Repositories\Contracts;

use App\Models\Sesion;
use Illuminate\Database\Eloquent\Collection;

interface SesionRepositoryInterface
{
    public function porGrupo(int $grupoId): Collection;
    public function buscarPorId(int $id): ?Sesion;
    public function sesionActiva(int $grupoId): ?Sesion;
    public function crear(array $datos): Sesion;
    public function actualizar(Sesion $sesion, array $datos): bool;
}