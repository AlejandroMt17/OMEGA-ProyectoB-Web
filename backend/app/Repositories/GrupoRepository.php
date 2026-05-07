<?php

namespace App\Repositories;

use App\Models\Grupo;
use App\Repositories\Contracts\GrupoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GrupoRepository implements GrupoRepositoryInterface
{
    public function porInstitucion(int $institucionId): Collection
    {
        return Grupo::query()
            ->where('id_institucion', $institucionId)
            ->orderBy('id_grupo')
            ->get();
    }

    public function buscarPorId(int $id): ?Grupo
    {
        return Grupo::query()->find($id);
    }

    public function buscarPorCodigo(string $codigo): ?Grupo
    {
        return Grupo::query()->where('codigo_inv', $codigo)->first();
    }

    public function crear(array $datos): Grupo
    {
        return Grupo::query()->create($datos);
    }

    public function actualizar(Grupo $grupo, array $datos): bool
    {
        return $grupo->update($datos);
    }

    public function eliminar(Grupo $grupo): bool
    {
        return (bool) $grupo->delete();
    }
}