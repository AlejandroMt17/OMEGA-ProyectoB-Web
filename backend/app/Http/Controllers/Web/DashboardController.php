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
        $alumnosEnRiesgo = $this->calcularAlumnosEnRiesgo($gruposIds);

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

        // Instituciones para el select: TODAS las del docente
        $instSelect = $instituciones->map(fn($i) => [
            'id'     => $i['institucion']->id_institucion,
            'nombre' => $i['institucion']->nombre,
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

    /**
     * Partial de alumnos en riesgo para AJAX (sin recargar la página).
     */
    public function riesgoPartial(\Illuminate\Http\Request $request)
    {
        $docente   = \Illuminate\Support\Facades\Auth::user();
        $gruposIds = $this->grupos->todosPorDocente($docente->id_usuario)->pluck('id_grupo');
        $instituciones = $this->instituciones->todasPorDocente($docente->id_usuario);

        $alumnosEnRiesgo = $this->calcularAlumnosEnRiesgo($gruposIds);

        $filtroInst   = $request->query('inst', '');
        $filtroGrupo  = $request->query('grupo', '');
        $filtroEstado = $request->query('estado', '');

        $alumnosFiltrados = $alumnosEnRiesgo;
        if ($filtroInst)   $alumnosFiltrados = $alumnosFiltrados->filter(fn($i) => $i['id_institucion'] == $filtroInst);
        if ($filtroGrupo)  $alumnosFiltrados = $alumnosFiltrados->filter(fn($i) => $i['grupo']->id_grupo == $filtroGrupo);
        if ($filtroEstado === 'excedido') $alumnosFiltrados = $alumnosFiltrados->filter(fn($i) => $i['perdio']);
        if ($filtroEstado === 'riesgo')   $alumnosFiltrados = $alumnosFiltrados->filter(fn($i) => !$i['perdio']);

        $riesgoPorGrupo = $alumnosFiltrados->groupBy(fn($i) => $i['grupo']->id_grupo);

        $instSelect = $instituciones->map(fn($i) => [
            'id'     => $i['institucion']->id_institucion,
            'nombre' => $i['institucion']->nombre,
        ])->values();

        $gruposSelect = $alumnosEnRiesgo
            ->when($filtroInst, fn($c) => $c->filter(fn($i) => $i['id_institucion'] == $filtroInst))
            ->groupBy(fn($i) => $i['grupo']->id_grupo)
            ->map(fn($items, $grupoId) => [
                'id'     => $grupoId,
                'nombre' => $items->first()['grupo']->nombre . ' — ' . $items->first()['grupo']->materia,
            ])->values();

        return view('modules.dashboard.partials.riesgo', compact(
            'alumnosEnRiesgo', 'riesgoPorGrupo',
            'instSelect', 'gruposSelect',
            'filtroInst', 'filtroGrupo', 'filtroEstado'
        ));
    }

    /**
     * Calcula los alumnos en riesgo para un conjunto de grupos.
     */
    private function calcularAlumnosEnRiesgo($gruposIds): \Illuminate\Support\Collection
    {
        $alumnosEnRiesgo = collect();
        foreach ($gruposIds as $idGrupo) {
            $rubros = RubroEvaluacion::whereHas('institucion.grupos', fn($q) => $q->where('id_grupo', $idGrupo))
                ->orderByDesc('porcentaje_minimo')->get();

            $rubroPrincipal  = $rubros->first();
            $pctPrincipal    = $rubroPrincipal?->porcentaje_minimo ?? 80;
            $nombrePrincipal = $rubroPrincipal?->nombre ?? 'Ordinario';

            $sesiones   = Sesion::where('id_grupo', $idGrupo)->where('est_sesion', 0)->get();
            $hayActiva  = Sesion::where('id_grupo', $idGrupo)->where('est_sesion', 1)->exists();
            $totalSes   = $sesiones->count();
            if ($totalSes === 0) continue;

            $faltasPermitidas = (int) floor($totalSes * (1 - $pctPrincipal / 100));

            $alumnos = GrupoAlumno::where('id_grupo', $idGrupo)->with(['alumno', 'grupo'])->get();
            foreach ($alumnos as $ga) {
                $sesIds       = $sesiones->pluck('id_sesion');
                $presentes    = Asistencia::whereIn('id_sesion', $sesIds)->where('id_alumno', $ga->id_alumno)->where('est_asistencia', 1)->count();
                $justificadas = Asistencia::whereIn('id_sesion', $sesIds)->where('id_alumno', $ga->id_alumno)->where('est_asistencia', 3)->count();
                $ausentes     = Asistencia::whereIn('id_sesion', $sesIds)->where('id_alumno', $ga->id_alumno)->where('est_asistencia', 2)->count();

                $asistidas       = $presentes + $justificadas;
                $pct             = round(($asistidas / $totalSes) * 100, 1);
                $perdio          = $ausentes > $faltasPermitidas;
                $faltasRestantes = max(0, $faltasPermitidas - $ausentes);

                $enRiesgoProyectado = false;
                if (!$perdio && $hayActiva) {
                    $totalProyectado    = $totalSes + 1;
                    $faltasPermProyect  = (int) floor($totalProyectado * (1 - $pctPrincipal / 100));
                    $enRiesgoProyectado = ($ausentes + 1) > $faltasPermProyect;
                }

                if (!$perdio && $pct > 90.0 && !$enRiesgoProyectado) continue;

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
        return $alumnosEnRiesgo;
    }
}