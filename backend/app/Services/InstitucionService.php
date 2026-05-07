<?php

namespace App\Services;

use App\Models\Institucion;
use App\Models\Usuario;
use App\Repositories\Contracts\InstitucionRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InstitucionService
{
    public function __construct(
        private readonly InstitucionRepositoryInterface $instituciones
    ) {}

    public function listar(Usuario $docente): array
    {
        return $this->instituciones->porDocente($docente->id_usuario)
            ->map(fn(Institucion $i) => $this->serializar($i))
            ->values()
            ->all();
    }

    public function obtener(Institucion $institucion): array
    {
        return $this->serializar($institucion);
    }

    public function crear(Usuario $docente, array $entrada): array
    {
        $datos = $this->validarCreacion($entrada);
        $institucion = $this->instituciones->crear([
            'id_docente' => $docente->id_usuario,
            'nombre'     => $datos['nombre'],
            'logo'       => $datos['logo'] ?? null,
        ]);
        return $this->serializar($institucion);
    }

    public function actualizar(Institucion $institucion, array $entrada): array
    {
        $datos = $this->validarActualizacion($entrada);
        $this->instituciones->actualizar($institucion, $datos);
        return $this->serializar($institucion->fresh());
    }

    public function eliminar(Institucion $institucion): void
    {
        $this->instituciones->eliminar($institucion);
    }

    private function serializar(Institucion $institucion): array
    {
        return [
            'id_institucion' => $institucion->id_institucion,
            'nombre'         => $institucion->nombre,
            'logo'           => $institucion->logo,
        ];
    }

    private function validarCreacion(array $entrada): array
    {
        $validator = Validator::make($entrada, [
            'nombre' => ['required', 'string', 'max:150'],
            'logo'   => ['nullable', 'string', 'max:500'],
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }

    private function validarActualizacion(array $entrada): array
    {
        $validator = Validator::make($entrada, [
            'nombre' => ['sometimes', 'required', 'string', 'max:150'],
            'logo'   => ['nullable', 'string', 'max:500'],
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
}