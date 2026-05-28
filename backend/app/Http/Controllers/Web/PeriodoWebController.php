<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador Web — Gestión de periodos académicos por institución.
 */
class PeriodoWebController extends Controller
{
    public function index(int $idInstitucion)
    {
        $institucion = Institucion::findOrFail($idInstitucion);
        abort_if($institucion->id_docente !== Auth::user()->id_usuario, 403);

        $periodos = Periodo::where('id_institucion', $idInstitucion)
            ->orderByDesc('created_at')->get();

        return view('modules.periodos.index', compact('institucion', 'periodos'));
    }

    public function store(Request $request, int $idInstitucion)
    {
        $institucion = Institucion::findOrFail($idInstitucion);
        abort_if($institucion->id_docente !== Auth::user()->id_usuario, 403);

        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
        ], [
            'nombre.required' => 'El nombre del periodo es obligatorio',
        ]);

        Periodo::create([
            'id_institucion' => $idInstitucion,
            'nombre'         => $request->nombre,
            'activo'         => true,
        ]);

        return redirect()->route('ca.periodos.index', $idInstitucion)
            ->with('success', 'Periodo agregado correctamente');
    }

    public function update(Request $request, int $idInstitucion, Periodo $periodo)
    {
        abort_if($periodo->id_institucion !== $idInstitucion, 403);

        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
        ], [
            'nombre.required' => 'El nombre del periodo es obligatorio',
        ]);

        $periodo->update(['nombre' => $request->nombre]);

        return redirect()->route('ca.periodos.index', $idInstitucion)
            ->with('success', 'Periodo actualizado correctamente');
    }

    public function destroy(int $idInstitucion, Periodo $periodo)
    {
        abort_if($periodo->id_institucion !== $idInstitucion, 403);
        $periodo->delete();

        return redirect()->route('ca.periodos.index', $idInstitucion)
            ->with('success', 'Periodo eliminado correctamente');
    }
}
