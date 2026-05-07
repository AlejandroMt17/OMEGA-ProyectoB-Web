<?php

namespace App\Services;

use App\Models\Usuario;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly AuthRepositoryInterface $auth
    ) {}

    public function login(array $entrada): array
    {
        $datos = $this->validarLogin($entrada);

        $usuario = $this->auth->buscarPorEmail($datos['email']);

        if (!$usuario || !Hash::check($datos['password'], $usuario->contrasenia)) {
            throw ValidationException::withMessages([
                'email' => ['Correo o contraseña incorrectos.'],
            ]);
        }

        $usuario->tokens()->delete();
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return [
            'usuario' => $this->serializar($usuario),
            'token'   => $token,
        ];
    }

    public function register(array $entrada): array
    {
        $datos = $this->validarRegistro($entrada);

        $usuario = $this->auth->crear([
            'nombre'     => $datos['nombre'],
            'ap_pat'     => $datos['ap_pat'],
            'ap_mat'     => $datos['ap_mat'] ?? null,
            'email'      => $datos['email'],
            'contrasenia' => $datos['password'],
            'rol'        => $datos['rol'],
        ]);

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return [
            'usuario' => $this->serializar($usuario),
            'token'   => $token,
        ];
    }

    public function logout(Usuario $usuario): void
    {
        $usuario->tokens()->delete();
    }

    public function me(Usuario $usuario): array
    {
        return $this->serializar($usuario);
    }

    private function serializar(Usuario $usuario): array
    {
        return [
            'id_usuario' => $usuario->id_usuario,
            'nombre'     => $usuario->nombre,
            'ap_pat'     => $usuario->ap_pat,
            'ap_mat'     => $usuario->ap_mat,
            'email'      => $usuario->email,
            'rol'        => $usuario->rol,
        ];
    }

    private function validarLogin(array $entrada): array
    {
        $validator = Validator::make($entrada, [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    private function validarRegistro(array $entrada): array
    {
        $validator = Validator::make($entrada, [
            'nombre'   => ['required', 'string', 'max:100'],
            'ap_pat'   => ['required', 'string', 'max:100'],
            'ap_mat'   => ['nullable', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:200', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:6'],
            'rol'      => ['required', 'integer', 'in:1,2'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}