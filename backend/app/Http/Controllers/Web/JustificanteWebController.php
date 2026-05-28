<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Repositories\Contracts\AsistenciaRepositoryInterface;
use App\Repositories\Contracts\GrupoRepositoryInterface;
use App\Services\AsistenciaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class JustificanteWebController extends Controller
{
    public function __construct(
        private readonly AsistenciaRepositoryInterface $asistencias,
        private readonly GrupoRepositoryInterface      $grupos,
        private readonly AsistenciaService             $asistenciaService,
    ) {}

    public function index()
    {
        $institucionId = session('institucion_id');

        // Requerir institución activa
        if (!$institucionId) {
            return redirect()->route('ca.instituciones.index')
                ->with('info', 'Selecciona una institución para ver sus justificantes');
        }

        $grupos = $this->grupos
            ->todosPorInstitucion($institucionId, Auth::user()->id_usuario)
            ->load([
                'sesiones' => fn($q) => $q->where('est_sesion', 0)->orderByDesc('fec_sesion'),
                'sesiones.asistencias' => fn($q) => $q->whereIn('est_asistencia', [2, 3]),
                'sesiones.asistencias.alumno',
            ]);

        return view('modules.justificantes.index', compact('grupos'));
    }

    public function justificar(Asistencia $asistencia)
    {
        try {
            $this->asistenciaService->editarEstado($asistencia, [
                'est_asistencia' => 3,
            ]);
            return redirect()->route('ca.justificantes.index')
                ->with('success', 'La información se actualizó correctamente');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    public function marcarAusente(Asistencia $asistencia)
    {
        try {
            $this->asistenciaService->editarEstado($asistencia, [
                'est_asistencia' => 2,
            ]);
            return redirect()->route('ca.justificantes.index')
                ->with('success', 'La información se actualizó correctamente');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}
