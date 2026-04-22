<?php

namespace App\Repositories;

use App\Models\Usuario;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repositorios: consultas y persistencia (sin reglas de negocio complejas).
 * Un repositorio por agregado / entidad facilita tests y sustitución de fuente de datos.
 */
class UsuarioRepository implements UsuarioRepositoryInterface
{
    public function todos(): Collection
    {
        return Usuario::query()->orderBy('id')->get();
    }

    public function buscarPorId(int $id): ?Usuario
    {
        return Usuario::query()->find($id);
    }

    public function crear(array $datos): Usuario
    {
        return Usuario::query()->create($datos);
    }

    public function guardar(Usuario $usuario, array $datos): bool
    {
        return $usuario->update($datos);
    }

    public function eliminar(Usuario $usuario): bool
    {
        return (bool) $usuario->delete();
    }
}
