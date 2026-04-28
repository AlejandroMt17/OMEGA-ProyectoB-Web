<?php

namespace App\Services;

use App\Models\Grupo;
use App\Models\Sesion;
use App\Models\Usuario;
use App\Repositories\Contracts\GrupoRepositoryInterface;
use App\Repositories\Contracts\SesionRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SesionService
{
    public function __construct(
        private readonly SesionRepositoryInterface $sesiones,
        private readonly GrupoRepositoryInterface  $grupos,
    ) {}

    public function listar(int $idGrupo, Usuario $docente): array
    {
        $grupo = $this->grupos->buscarPorId($idGrupo);
        $this->verificarPropietarioGrupo($grupo, $docente);

        return $this->sesiones->todasPorGrupo($idGrupo)
            ->map(fn(Sesion $s) => $this->serializar($s))
            ->values()
            ->all();
    }

    public function abrir(int $idGrupo, array $entrada, Usuario $docente): array
    {
        $grupo = $this->grupos->buscarPorId($idGrupo);
        $this->verificarPropietarioGrupo($grupo, $docente);

        // Verificar que no haya sesión activa
        $sesionActiva = $this->sesiones->buscarActivaPorGrupo($idGrupo);
        if ($sesionActiva) {
            throw ValidationException::withMessages([
                'sesion' => ['Ya existe una sesión activa para este grupo.'],
            ]);
        }

        $validator = Validator::make($entrada, [
            'fec_sesion' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $clave = strtoupper(Str::random(6));

        $sesion = $this->sesiones->crear([
            'id_grupo'      => $idGrupo,
            'clave'         => $clave,
            'est_sesion'    => 1,
            'fec_sesion'    => $entrada['fec_sesion'],
            'hora_apertura' => now(),
        ]);

        return $this->serializar($sesion);
    }

    public function cerrar(Sesion $sesion, Usuario $docente): array
    {
        $grupo = $this->grupos->buscarPorId($sesion->id_grupo);
        $this->verificarPropietarioGrupo($grupo, $docente);

        if ($sesion->est_sesion === 0) {
            throw ValidationException::withMessages([
                'sesion' => ['La sesión ya está cerrada.'],
            ]);
        }

        $this->sesiones->guardar($sesion, [
            'est_sesion'  => 0,
            'hora_cierre' => now(),
        ]);

        return $this->serializar($sesion->fresh());
    }

    public function obtener(Sesion $sesion, Usuario $docente): array
    {
        $grupo = $this->grupos->buscarPorId($sesion->id_grupo);
        $this->verificarPropietarioGrupo($grupo, $docente);
        return $this->serializar($sesion);
    }

    private function verificarPropietarioGrupo(?Grupo $grupo, Usuario $docente): void
    {
        if (!$grupo || $grupo->id_docente !== $docente->id_usuario) {
            throw new AuthorizationException('No tienes permiso para acceder a este grupo.');
        }
    }

    private function serializar(Sesion $sesion): array
    {
        return [
            'id_sesion'     => $sesion->id_sesion,
            'id_grupo'      => $sesion->id_grupo,
            'clave'         => $sesion->est_sesion === 1 ? $sesion->clave : null,
            'est_sesion'    => $sesion->est_sesion,
            'fec_sesion'    => $sesion->fec_sesion?->toDateString(),
            'hora_apertura' => $sesion->hora_apertura?->toIso8601String(),
            'hora_cierre'   => $sesion->hora_cierre?->toIso8601String(),
        ];
    }
}