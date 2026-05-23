<?php

namespace App\Exports;

use App\Models\Asistencia;
use App\Models\Grupo;
use App\Models\GrupoAlumno;
use App\Models\Sesion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ReporteGrupoExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly Grupo $grupo) {}

    public function collection(): Collection
    {
        $sesiones   = Sesion::where('id_grupo', $this->grupo->id_grupo)
                            ->where('est_sesion', 0)
                            ->orderBy('fec_sesion')
                            ->get();

        $alumnos    = GrupoAlumno::where('id_grupo', $this->grupo->id_grupo)
                            ->with('alumno')
                            ->get();

        $rows = collect();

        foreach ($alumnos as $ga) {
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

            $total      = $sesiones->count();
            $porcentaje = $total > 0 ? round((($presentes + $justif) / $total) * 100, 1) : 0;

            $rows->push([
                'Apellido Paterno' => $alumno->ap_pat,
                'Apellido Materno' => $alumno->ap_mat,
                'Nombre'          => $alumno->nombre,
                'Correo'          => $alumno->email,
                'Presentes'       => $presentes,
                'Ausentes'        => $ausentes,
                'Justificadas'    => $justif,
                'Total Sesiones'  => $total,
                '% Asistencia'    => $porcentaje . '%',
            ]);
        }

        return $rows->sortBy('Apellido Paterno')->values();
    }

    public function headings(): array
    {
        return [
            'Apellido Paterno',
            'Apellido Materno',
            'Nombre',
            'Correo',
            'Presentes',
            'Ausentes',
            'Justificadas',
            'Total Sesiones',
            '% Asistencia',
        ];
    }

    public function title(): string
    {
        return substr($this->grupo->nombre . ' - ' . $this->grupo->materia, 0, 31);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF2C3E6B']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
