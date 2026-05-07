<?php

namespace App\Repositories\Contracts;

use App\Models\Usuario;

interface AuthRepositoryInterface
{
    public function buscarPorEmail(string $email): ?Usuario;
    public function crear(array $datos): Usuario;
}