<?php

namespace App\Repositories;

use App\Models\Sesion;
use App\Repositories\Contracts\SesionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SesionRepository implements SesionRepositoryInterface
{
    public function porGrupo(int $grupoId): Collection
    {
        return Sesion::query()
            ->where('id_grupo', $grupoId)
            ->orderByDesc('fec_sesion')
            ->get();
    }

    public function buscarPorId(int $id): ?Sesion
    {
        return Sesion::query()->find($id);
    }

    public function sesionActiva(int $grupoId): ?Sesion
    {
        return Sesion::query()
            ->where('id_grupo', $grupoId)
            ->where('est_sesion', 1)
            ->first();
    }

    public function crear(array $datos): Sesion
    {
        return Sesion::query()->create($datos);
    }

    public function actualizar(Sesion $sesion, array $datos): bool
    {
        return $sesion->update($datos);
    }
}