<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Sesion;
use App\Repositories\Contracts\GrupoRepositoryInterface;
use App\Repositories\Contracts\InstitucionRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GrupoRepositoryInterface      $grupos,
        private readonly InstitucionRepositoryInterface $instituciones,
    ) {}

    public function index()
    {
        $docente = Auth::user();

        // Instituciones con sus grupos
        $instituciones = $this->instituciones->todasPorDocente($docente->id_usuario)
            ->map(function ($inst) {
                $grupos = $this->grupos->todosPorInstitucion($inst->id_institucion)
                    ->map(function ($grupo) {
                        $sesionActiva = Sesion::where('id_grupo', $grupo->id_grupo)
                            ->where('est_sesion', 1)
                            ->first();

                        $totalAlumnos = $grupo->grupoAlumnos()->count();

                        return [
                            'grupo'        => $grupo,
                            'sesionActiva' => $sesionActiva,
                            'totalAlumnos' => $totalAlumnos,
                        ];
                    });

                return [
                    'institucion' => $inst,
                    'grupos'      => $grupos,
                ];
            });

        // Sesiones de hoy
        $gruposIds   = $this->grupos->todosPorDocente($docente->id_usuario)->pluck('id_grupo');
        $sesionesHoy = Sesion::whereIn('id_grupo', $gruposIds)
            ->whereDate('fec_sesion', today())
            ->with('grupo')
            ->orderByDesc('hora_apertura')
            ->get()
            ->map(function ($sesion) {
                $presentes = Asistencia::where('id_sesion', $sesion->id_sesion)
                    ->where('est_asistencia', 1)->count();
                $total = $sesion->grupo->grupoAlumnos()->count();
                return [
                    'sesion'    => $sesion,
                    'presentes' => $presentes,
                    'total'     => $total,
                ];
            });

        // Contadores
        $aulasActivas         = $gruposIds->count();
        $justificantesPend    = Asistencia::whereHas('sesion', fn($q) => $q->whereIn('id_grupo', $gruposIds))
            ->where('est_asistencia', 2)->count();

        return view('modules.dashboard.index', compact(
            'instituciones',
            'sesionesHoy',
            'aulasActivas',
            'justificantesPend',
        ));
    }
}
