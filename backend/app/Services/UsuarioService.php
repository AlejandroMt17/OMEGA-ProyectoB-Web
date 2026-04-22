<?php

namespace App\Services;

use App\Models\Usuario;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

/**
 * Servicios: lógica de negocio, validaciones y orquestación (MVC + capa de dominio).
 * Los controladores solo delegan aquí.
 */
class UsuarioService
{
    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarios
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listar(): array
    {
        return $this->usuarios->todos()
            ->map(fn (Usuario $u) => $this->serializar($u))
            ->values()
            ->all();
    }

    public function obtener(Usuario $usuario): array
    {
        return $this->serializar($usuario);
    }

    /**
     * @param  array<string, mixed>  $entrada
     * @return array<string, mixed>
     */
    public function crear(array $entrada): array
    {
        $datos = $this->validarCreacion($entrada);
        $usuario = $this->usuarios->crear($datos);

        return $this->serializar($usuario);
    }

    /**
     * @param  array<string, mixed>  $entrada
     * @return array<string, mixed>
     */
    public function actualizar(Usuario $usuario, array $entrada): array
    {
        $datos = $this->validarActualizacion($entrada, $usuario->id);
        $this->usuarios->guardar($usuario, $datos);

        return $this->serializar($usuario->fresh());
    }

    public function eliminar(Usuario $usuario): void
    {
        $this->usuarios->eliminar($usuario);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializar(Usuario $usuario): array
    {
        return [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'created_at' => $usuario->created_at?->toIso8601String(),
            'updated_at' => $usuario->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @param  array<string, mixed>  $entrada
     * @return array<string, mixed>
     */
    private function validarCreacion(array $entrada): array
    {
        $validator = Validator::make($entrada, [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:usuarios,email'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * @param  array<string, mixed>  $entrada
     * @return array<string, mixed>
     */
    private function validarActualizacion(array $entrada, int $id): array
    {
        $validator = Validator::make($entrada, [
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:usuarios,email,'.$id],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
