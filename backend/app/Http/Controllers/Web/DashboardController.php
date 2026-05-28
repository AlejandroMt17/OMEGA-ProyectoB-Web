<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\GrupoAlumno;
use App\Models\RubroEvaluacion;
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
        $docente    = Auth::user();
        $gruposIds  = $this->grupos->todosPorDocente($docente->id_usuario)->pluck('id_grupo');

        // Instituciones con grupos
        $instituciones = $this->instituciones->todasPorDocente($docente->id_usuario)
            ->map(function ($inst) {
                $grupos = $this->grupos->todosPorInstitucion($inst->id_institucion)
                    ->map(function ($grupo) {
                        $sesionActiva = Sesion::where('id_grupo', $grupo->id_grupo)
                            ->where('est_sesion', 1)->first();
                        return [
                            'grupo'        => $grupo,
                            'sesionActiva' => $sesionActiva,
                            'totalAlumnos' => $grupo->grupoAlumnos()->count(),
                        ];
                    });
                return ['institucion' => $inst, 'grupos' => $grupos];
            });

        // Sesiones de hoy
        $sesionesHoy = Sesion::whereIn('id_grupo', $gruposIds)
            ->whereDate('fec_sesion', today())
            ->with('grupo')
            ->orderByDesc('hora_apertura')
            ->get()
            ->map(function ($sesion) {
                $presentes = Asistencia::where('id_sesion', $sesion->id_sesion)
                    ->where('est_asistencia', 1)->count();
                return [
                    'sesion'    => $sesion,
                    'presentes' => $presentes,
                    'total'     => $sesion->grupo->grupoAlumnos()->count(),
                ];
            });

        // RF-76: Alumnos en riesgo
        $alumnosEnRiesgo = collect();
        foreach ($gruposIds as $idGrupo) {
            $rubros     = RubroEvaluacion::whereHas('institucion.grupos', fn($q) => $q->where('id_grupo', $idGrupo))->get();
            $minPct     = $rubros->min('porcentaje_minimo') ?? 80;
            $sesiones   = Sesion::where('id_grupo', $idGrupo)->where('est_sesion', 0)->get();
            $totalSes   = $sesiones->count();
            if ($totalSes === 0) continue;

            $alumnos = GrupoAlumno::where('id_grupo', $idGrupo)->with(['alumno', 'grupo'])->get();
            foreach ($alumnos as $ga) {
                $sesIds   = $sesiones->pluck('id_sesion');
                $presentes = Asistencia::whereIn('id_sesion', $sesIds)
                    ->where('id_alumno', $ga->id_alumno)
                    ->where('est_asistencia', 1)->count();
                $justificadas = Asistencia::whereIn('id_sesion', $sesIds)
                    ->where('id_alumno', $ga->id_alumno)
                    ->where('est_asistencia', 3)->count();
                $ausentes  = Asistencia::whereIn('id_sesion', $sesIds)
                    ->where('id_alumno', $ga->id_alumno)
                    ->where('est_asistencia', 2)->count();

                $asistidas = $presentes + $justificadas;
                $pct       = round(($asistidas / $totalSes) * 100, 1);

                // Faltas permitidas = cuántas puede tener antes de perder el ordinario
                $faltasPermitidas = (int) floor($totalSes * (1 - $minPct / 100));
                $faltasRestantes  = max(0, $faltasPermitidas - $ausentes);
                $perdio           = $pct < $minPct;

                // En riesgo: ya perdió O le quedan <= 2 faltas
                if ($perdio || $faltasRestantes <= 2) {
                    $alumnosEnRiesgo->push([
                        'alumno'          => $ga->alumno,
                        'grupo'           => $ga->grupo,
                        'porcentaje'      => $pct,
                        'total_faltas'    => $ausentes,
                        'faltas_restantes'=> $faltasRestantes,
                        'perdio'          => $perdio,
                    ]);
                }
            }
        }

        // Contadores
        $aulasActivas      = $gruposIds->count();
        $justificantesPend = Asistencia::whereHas('sesion', fn($q) => $q->whereIn('id_grupo', $gruposIds))
            ->where('est_asistencia', 2)->count();

        return view('modules.dashboard.index', compact(
            'instituciones', 'sesionesHoy', 'aulasActivas',
            'justificantesPend', 'alumnosEnRiesgo'
        ));
    }
}
