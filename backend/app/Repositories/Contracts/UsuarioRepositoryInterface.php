<?php

namespace App\Repositories\Contracts;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contrato del repositorio: abstrae el acceso a datos (Eloquent / futuro cache).
 */
interface UsuarioRepositoryInterface
{
    public function todos(): Collection;

    public function buscarPorId(int $id): ?Usuario;

    public function crear(array $datos): Usuario;

    public function guardar(Usuario $usuario, array $datos): bool;

    public function eliminar(Usuario $usuario): bool;
}
