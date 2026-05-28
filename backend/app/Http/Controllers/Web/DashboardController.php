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
            $rubros = RubroEvaluacion::whereHas('institucion.grupos', fn($q) => $q->where('id_grupo', $idGrupo))
                ->orderByDesc('porcentaje_minimo')
                ->get();

            // Primer rubro (mayor %) = ordinario o equivalente
            $rubroPrincipal    = $rubros->first();
            $pctPrincipal      = $rubroPrincipal?->porcentaje_minimo ?? 80;
            $nombrePrincipal   = $rubroPrincipal?->nombre ?? 'Ordinario';

            // Incluir sesión activa en el total proyectado
            $sesionesActivas = Sesion::where('id_grupo', $idGrupo)->where('est_sesion', 1)->get();
            $sesiones        = Sesion::where('id_grupo', $idGrupo)->where('est_sesion', 0)->get();
            $totalSes        = $sesiones->count();
            // Total proyectado = sesiones cerradas + 1 si hay sesión activa
            $totalProyectado = $totalSes + $sesionesActivas->count();
            if ($totalSes === 0 && $totalProyectado === 0) continue;

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

                // Porcentaje actual (sobre sesiones cerradas)
                $pct = $totalSes > 0 ? round(($asistidas / $totalSes) * 100, 1) : 100.0;

                // Proyección: si se abre una sesión más y el alumno no asiste
                // ¿cuántas faltas tendría en total?
                $ausentesProyectados = $ausentes + $sesionesActivas->count(); // peor caso
                $faltasPermitidas    = (int) floor($totalProyectado * (1 - $pctPrincipal / 100));
                $faltasRestantes     = max(0, $faltasPermitidas - $ausentes);

                // perdio = ya superó las faltas permitidas con las sesiones cerradas
                $perdio = $totalSes > 0 && $ausentes > $faltasPermitidas;

                // En riesgo proyectado = con la sesión activa podría perder el rubro
                $enRiesgoProyectado = !$perdio && ($ausentesProyectados > $faltasPermitidas);

                // Solo agregar si realmente está en riesgo o ya perdió
                if ($perdio || $enRiesgoProyectado || ($faltasRestantes <= 2 && $totalSes > 0)) {
                    $alumnosEnRiesgo->push([
                        'alumno'           => $ga->alumno,
                        'grupo'            => $ga->grupo,
                        'porcentaje'       => $pct,
                        'total_faltas'     => $ausentes,
                        'faltas_restantes' => $faltasRestantes,
                        'perdio'           => $perdio,
                        'rubro_principal'  => $nombrePrincipal,
                        'pct_principal'    => $pctPrincipal,
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
