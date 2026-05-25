<?php

namespace App\Http\Controllers\Web;

use App\Exports\ReporteGrupoExport;
use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\GrupoAlumno;
use App\Models\Sesion;
use App\Repositories\Contracts\GrupoRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Controlador Web — Reportes de asistencia por grupo.
 * RF-06 — Exportar Excel y PDF (plan mensual).
 * @version 1.1.0
 */
class ReporteWebController extends Controller
{
    public function __construct(
        private readonly GrupoRepositoryInterface $grupos,
    ) {}

    public function index()
    {
        $grupos  = $this->grupos->todosPorDocente(Auth::user()->id_usuario);

        $reportes = $grupos->map(function ($grupo) {
            $sesiones  = Sesion::where('id_grupo', $grupo->id_grupo)->get();
            $sesionIds = $sesiones->pluck('id_sesion');

            $totalSesiones  = $sesiones->count();
            $totalPresentes = Asistencia::whereIn('id_sesion', $sesionIds)->where('est_asistencia', 1)->count();
            $totalAusentes  = Asistencia::whereIn('id_sesion', $sesionIds)->where('est_asistencia', 2)->count();
            $totalJustif    = Asistencia::whereIn('id_sesion', $sesionIds)->where('est_asistencia', 3)->count();
            $totalAsist     = $totalPresentes + $totalAusentes + $totalJustif;

            return [
                'grupo'           => $grupo,
                'total_sesiones'  => $totalSesiones,
                'total_presentes' => $totalPresentes,
                'total_ausentes'  => $totalAusentes,
                'total_justif'    => $totalJustif,
                'porcentaje'      => $totalAsist > 0
                    ? round(($totalPresentes / $totalAsist) * 100, 1)
                    : 0,
            ];
        });

        return view('modules.reportes.index', compact('reportes'));
    }

    public function detalle(int $idGrupo)
    {
        $grupo = $this->grupos->buscarPorId($idGrupo);
        abort_if(!$grupo || $grupo->id_docente !== Auth::user()->id_usuario, 403);

        $sesiones = Sesion::where('id_grupo', $idGrupo)
            ->orderByDesc('fec_sesion')
            ->get()
            ->map(function ($sesion) {
                return [
                    'sesion'    => $sesion,
                    'presentes' => Asistencia::where('id_sesion', $sesion->id_sesion)->where('est_asistencia', 1)->count(),
                    'ausentes'  => Asistencia::where('id_sesion', $sesion->id_sesion)->where('est_asistencia', 2)->count(),
                    'justif'    => Asistencia::where('id_sesion', $sesion->id_sesion)->where('est_asistencia', 3)->count(),
                ];
            });

        return view('modules.reportes.detalle', compact('grupo', 'sesiones'));
    }

    /**
     * RF-06 — Exportar reporte de asistencia por alumno en Excel.
     */
    public function exportarExcel(int $idGrupo)
    {
        $grupo = $this->grupos->buscarPorId($idGrupo);
        abort_if(!$grupo || $grupo->id_docente !== Auth::user()->id_usuario, 403);

        $nombre = 'reporte-' . $grupo->nombre . '-' . $grupo->materia . '.xlsx';
        return Excel::download(new ReporteGrupoExport($grupo), $nombre);
    }

    /**
     * RF-06 — Exportar reporte de asistencia por alumno en PDF.
     */
    public function exportarPdf(int $idGrupo)
    {
        $grupo = $this->grupos->buscarPorId($idGrupo);
        abort_if(!$grupo || $grupo->id_docente !== Auth::user()->id_usuario, 403);

        $sesiones = Sesion::where('id_grupo', $idGrupo)
            ->where('est_sesion', 0)
            ->orderBy('fec_sesion')
            ->get();

        $alumnos = GrupoAlumno::where('id_grupo', $idGrupo)
            ->with('alumno')
            ->get()
            ->map(function ($ga) use ($sesiones) {
                $alumno    = $ga->alumno;
                $presentes = 0;
                $ausentes  = 0;
                $justif    = 0;

                foreach ($sesiones as $sesion) {
                    $asistencia = Asistencia::where('id_sesion', $sesion->id_sesion)
                        ->where('id_alumno', $alumno->id_usuario)
                        ->first();
                    if ($asistencia) {
                        match ($asistencia->est_asistencia) {
                            1 => $presentes++,
                            2 => $ausentes++,
                            3 => $justif++,
                            default => null,
                        };
                    }
                }

                $total = $sesiones->count();
                return [
                    'alumno'     => $alumno,
                    'presentes'  => $presentes,
                    'ausentes'   => $ausentes,
                    'justif'     => $justif,
                    'total'      => $total,
                    'porcentaje' => $total > 0 ? round((($presentes + $justif) / $total) * 100, 1) : 0,
                ];
            })
            ->sortBy('alumno.ap_pat')
            ->values();

        $pdf = Pdf::loadView('modules.reportes.pdf', compact('grupo', 'sesiones', 'alumnos'))
                  ->setPaper('letter', 'landscape');

        return $pdf->download('reporte-' . $grupo->nombre . '-' . $grupo->materia . '.pdf');
    }
    /**
     * Detalle de asistencia sesión a sesión de un alumno en un grupo.
     */
    public function detalleAlumno(int $idGrupo, int $idAlumno)
    {
        $grupo = $this->grupos->buscarPorId($idGrupo);
        abort_if(!$grupo || $grupo->id_docente !== Auth::user()->id_usuario, 403);

        $alumno = \App\Models\Usuario::findOrFail($idAlumno);

        $sesiones = \App\Models\Sesion::where('id_grupo', $idGrupo)
            ->orderBy('fec_sesion')
            ->get()
            ->map(function ($sesion) use ($idAlumno) {
                $asistencia = \App\Models\Asistencia::where('id_sesion', $sesion->id_sesion)
                    ->where('id_alumno', $idAlumno)
                    ->first();

                return [
                    'sesion'      => $sesion,
                    'asistencia'  => $asistencia,
                    'estado'      => $asistencia?->est_asistencia ?? null,
                    'hora'        => $asistencia?->hora_registro?->format('H:i:s') ?? '—',
                ];
            });

        $presentes    = $sesiones->where('estado', 1)->count();
        $ausentes     = $sesiones->where('estado', 2)->count();
        $justificadas = $sesiones->where('estado', 3)->count();
        $total        = $sesiones->count();
        $porcentaje   = $total > 0 ? round((($presentes + $justificadas) / $total) * 100, 1) : 0;

        return view('modules.reportes.detalle_alumno', compact(
            'grupo', 'alumno', 'sesiones',
            'presentes', 'ausentes', 'justificadas', 'total', 'porcentaje'
        ));
    }
}