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

    public function index(\Illuminate\Http\Request $request)
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

            $sesiones   = Sesion::where('id_grupo', $idGrupo)->where('est_sesion', 0)->get();
            $hayActiva  = Sesion::where('id_grupo', $idGrupo)->where('est_sesion', 1)->exists();
            $totalSes   = $sesiones->count();
            if ($totalSes === 0) continue;

            // Faltas máximas permitidas basadas en sesiones CERRADAS
            // $pctPrincipal = 80 → con 10 sesiones: floor(10 * 0.20) = 2 faltas permitidas
            $faltasPermitidas = (int) floor($totalSes * (1 - $pctPrincipal / 100));

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

                // Ya perdió: superó las faltas permitidas en sesiones cerradas
                $perdio = $ausentes > $faltasPermitidas;

                // Faltas restantes antes de perder el rubro
                $faltasRestantes = max(0, $faltasPermitidas - $ausentes);

                // En riesgo con proyección de sesión activa:
                // Si hay sesión activa, proyectar que el alumno no asiste (+1 falta)
                // Con eso, ¿supera el límite calculado sobre totalSes+1?
                $enRiesgoProyectado = false;
                if (!$perdio && $hayActiva) {
                    $totalProyectado     = $totalSes + 1;
                    $faltasPermProyect   = (int) floor($totalProyectado * (1 - $pctPrincipal / 100));
                    $ausentesProyectados = $ausentes + 1; // peor caso: no asiste
                    $enRiesgoProyectado  = $ausentesProyectados > $faltasPermProyect;
                }

                // Solo agregar si ya perdió o está genuinamente en riesgo
                // (tiene <= 2 faltas restantes O la proyección indica riesgo)
                if ($perdio || ($faltasRestantes <= 2 && $faltasPermitidas > 0) || $enRiesgoProyectado) {
                    $alumnosEnRiesgo->push([
                        'alumno'           => $ga->alumno,
                        'grupo'            => $ga->grupo,
                        'porcentaje'       => $pct,
                        'total_faltas'     => $ausentes,
                        'faltas_restantes' => $faltasRestantes,
                        'perdio'           => $perdio,
                        'rubro_principal'  => $nombrePrincipal,
                        'pct_principal'    => $pctPrincipal,
                        'id_institucion'   => $ga->grupo->id_institucion,
                    ]);
                }
            }
        }

        // Contadores
        $aulasActivas      = $gruposIds->count();
        $justificantesPend = Asistencia::whereHas('sesion', fn($q) => $q->whereIn('id_grupo', $gruposIds))
            ->where('est_asistencia', 2)->count();

        // Filtros GET
        $filtroInst   = $request->query('inst', '');
        $filtroGrupo  = $request->query('grupo', '');
        $filtroEstado = $request->query('estado', '');

        $alumnosFiltrados = $alumnosEnRiesgo;
        if ($filtroInst)   $alumnosFiltrados = $alumnosFiltrados->filter(fn($i) => $i['id_institucion'] == $filtroInst);
        if ($filtroGrupo)  $alumnosFiltrados = $alumnosFiltrados->filter(fn($i) => $i['grupo']->id_grupo == $filtroGrupo);
        if ($filtroEstado === 'excedido') $alumnosFiltrados = $alumnosFiltrados->filter(fn($i) => $i['perdio']);
        if ($filtroEstado === 'riesgo')   $alumnosFiltrados = $alumnosFiltrados->filter(fn($i) => !$i['perdio']);

        $riesgoPorGrupo = $alumnosFiltrados->groupBy(fn($i) => $i['grupo']->id_grupo);

        // Instituciones para el select (solo las que tienen alumnos en riesgo)
        $instSelect = $alumnosEnRiesgo
            ->groupBy(fn($i) => $i['id_institucion'])
            ->map(fn($items, $instId) => [
                'id'     => $instId,
                'nombre' => $instituciones->firstWhere('institucion.id_institucion', $instId)['institucion']->nombre ?? 'Institución',
            ])->values();

        // Grupos para el select (filtrados por institución)
        $gruposSelect = $alumnosEnRiesgo
            ->when($filtroInst, fn($c) => $c->filter(fn($i) => $i['id_institucion'] == $filtroInst))
            ->groupBy(fn($i) => $i['grupo']->id_grupo)
            ->map(fn($items, $grupoId) => [
                'id'     => $grupoId,
                'nombre' => $items->first()['grupo']->nombre . ' — ' . $items->first()['grupo']->materia,
            ])->values();

        return view('modules.dashboard.index', compact(
            'instituciones', 'sesionesHoy', 'aulasActivas',
            'justificantesPend', 'alumnosEnRiesgo',
            'riesgoPorGrupo', 'instSelect', 'gruposSelect',
            'filtroInst', 'filtroGrupo', 'filtroEstado'
        ));
    }
}
