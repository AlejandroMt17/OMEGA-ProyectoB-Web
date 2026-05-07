<?php

namespace App\Repositories\Contracts;

use App\Models\Grupo;
use Illuminate\Database\Eloquent\Collection;

interface GrupoRepositoryInterface
{
    public function porInstitucion(int $institucionId): Collection;
    public function buscarPorId(int $id): ?Grupo;
    public function buscarPorCodigo(string $codigo): ?Grupo;
    public function crear(array $datos): Grupo;
    public function actualizar(Grupo $grupo, array $datos): bool;
    public function eliminar(Grupo $grupo): bool;
}