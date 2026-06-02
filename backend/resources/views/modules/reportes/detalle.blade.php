@extends('layouts.app')
@section('title', 'Reporte — ' . $grupo->nombre)
@section('content')

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <div class="flex items-center gap-2 mb-1 text-sm font-body text-omg-kashmir">
            <a href="{{ route('ca.reportes.index') }}" class="hover:text-omg-nile">Reportes</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span>{{ $grupo->nombre }}</span>
        </div>
        <h1 class="text-2xl font-heading font-semibold text-omg-nile">
            {{ $grupo->nombre }} — {{ $grupo->materia }}
        </h1>
        <p class="text-sm font-body text-omg-kashmir mt-1">{{ $grupo->periodo }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('ca.dashboard.index') }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-omg-chardon hover:bg-omg-pastel text-omg-nile font-heading font-semibold rounded-lg transition-colors text-sm">
            <i class="fa-solid fa-house"></i> Inicio
        </a>
        <a href="{{ route('ca.reportes.index') }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-omg-chardon hover:bg-omg-pastel text-omg-nile font-heading font-semibold rounded-lg transition-colors text-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

{{-- Exportar --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('ca.reportes.excel', $grupo->id_grupo) }}"
       class="flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-heading font-semibold rounded-lg transition-colors text-sm">
        <i class="fa-solid fa-file-excel"></i> Exportar Excel
    </a>
    <a href="{{ route('ca.reportes.pdf', $grupo->id_grupo) }}"
       class="flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-heading font-semibold rounded-lg transition-colors text-sm">
        <i class="fa-solid fa-file-pdf"></i> Exportar PDF
    </a>
</div>

{{-- Sección sesiones (colapsable) --}}
<div class="bg-white rounded-xl border border-omg-kashmir-dark overflow-hidden mb-6"
     x-data="{ abierto: false }">

    {{-- Header acordeón --}}
    <button @click="abierto = !abierto"
            class="w-full flex items-center justify-between px-5 py-4 hover:bg-omg-chardon transition-colors">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-calendar-days text-omg-nile"></i>
            <div class="text-left">
                <p class="text-base font-heading font-semibold text-omg-nile">Total de sesiones</p>
                <p class="text-xs font-body text-omg-kashmir">{{ $sesiones->count() }} sesión(es) registradas</p>
            </div>
        </div>
        <i class="fa-solid fa-chevron-down text-omg-kashmir transition-transform duration-200"
           :class="abierto ? 'rotate-180' : ''"></i>
    </button>

    <div x-show="abierto" x-collapse>
        <table class="w-full">
            <thead>
                <tr class="border-t border-b border-omg-kashmir-dark bg-omg-chardon">
                    <th class="text-left px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">#</th>
                    <th class="text-left px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Fecha de la sesión</th>
                    <th class="text-center px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Asistencias</th>
                    <th class="text-center px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Faltas</th>
                    <th class="text-center px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Justificaciones</th>
                    <th class="text-right px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-omg-kashmir-dark">
                @forelse ($sesiones as $i => $item)
                    <tr class="hover:bg-omg-chardon transition-colors">
                        <td class="px-5 py-3 text-sm font-body text-omg-kashmir">{{ $i + 1 }}</td>
                        <td class="px-5 py-3">
                            <p class="text-sm font-body text-omg-dark">{{ $item['sesion']->fec_sesion->format('d/m/Y') }}</p>
                            <p class="text-xs font-body text-omg-kashmir">
                                {{ $item['sesion']->hora_apertura->format('H:i') }}
                                @if($item['sesion']->hora_cierre) — {{ $item['sesion']->hora_cierre->format('H:i') }} @endif
                            </p>
                        </td>
                        <td class="px-5 py-3 text-center text-sm font-heading font-semibold text-green-600">{{ $item['presentes'] }}</td>
                        <td class="px-5 py-3 text-center text-sm font-heading font-semibold text-red-500">{{ $item['ausentes'] }}</td>
                        <td class="px-5 py-3 text-center text-sm font-heading font-semibold text-omg-nile">{{ $item['justif'] }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('ca.sesiones.asistencias', $item['sesion']->id_sesion) }}"
                               class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-pastel hover:bg-omg-nile hover:text-white text-omg-nile rounded-lg text-xs font-body transition-colors ml-auto w-fit">
                                <i class="fa-solid fa-ellipsis"></i> Detalles
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-sm font-body text-omg-kashmir">
                            No hay sesiones registradas para este grupo
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Sección alumnos (colapsable) --}}
<div class="bg-white rounded-xl border border-omg-kashmir-dark overflow-hidden"
     x-data="{ abierto: false }">

    {{-- Header acordeón --}}
    <button @click="abierto = !abierto"
            class="w-full flex items-center justify-between px-5 py-4 hover:bg-omg-chardon transition-colors">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-users text-omg-nile"></i>
            <div class="text-left">
                <p class="text-base font-heading font-semibold text-omg-nile">Detalle por alumno</p>
                <p class="text-xs font-body text-omg-kashmir">Haz clic para ver el historial sesión a sesión de cada alumno</p>
            </div>
        </div>
        <i class="fa-solid fa-chevron-down text-omg-kashmir transition-transform duration-200"
           :class="abierto ? 'rotate-180' : ''"></i>
    </button>

    <div x-show="abierto" x-collapse>
        @php
            use App\Models\RubroEvaluacion;
            $rubros = RubroEvaluacion::where('id_institucion', $grupo->id_institucion)
                ->orderBy('porcentaje_minimo', 'desc')->get();
        @endphp
        <table class="w-full">
            <thead>
                <tr class="border-t border-b border-omg-kashmir-dark bg-omg-chardon">
                    <th class="text-left px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Alumno</th>
                    <th class="text-center px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Asistencias</th>
                    <th class="text-center px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Faltas</th>
                    <th class="text-center px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Justificaciones</th>
                    <th class="text-center px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">% Asistencia</th>
                    @foreach ($rubros as $rubro)
                        <th class="text-center px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">
                            {{ $rubro->nombre }}
                            <span class="block text-omg-kashmir font-normal normal-case">mín. {{ $rubro->porcentaje_minimo }}%</span>
                        </th>
                    @endforeach
                    <th class="text-right px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-omg-kashmir-dark">
                @php
                    use App\Models\GrupoAlumno;
                    use App\Models\Asistencia;
                    use App\Models\Sesion;
                    $sesionesIds = Sesion::where('id_grupo', $grupo->id_grupo)->where('est_sesion', 0)->pluck('id_sesion');
                    $alumnosGrupo = GrupoAlumno::where('id_grupo', $grupo->id_grupo)->with('alumno')->get()->sortBy('alumno.ap_pat');
                @endphp
                @forelse ($alumnosGrupo as $ga)
                    @php
                        $al  = $ga->alumno;
                        $p   = Asistencia::whereIn('id_sesion', $sesionesIds)->where('id_alumno', $al->id_usuario)->where('est_asistencia', 1)->count();
                        $a   = Asistencia::whereIn('id_sesion', $sesionesIds)->where('id_alumno', $al->id_usuario)->where('est_asistencia', 2)->count();
                        $j   = Asistencia::whereIn('id_sesion', $sesionesIds)->where('id_alumno', $al->id_usuario)->where('est_asistencia', 3)->count();
                        $t   = $sesionesIds->count();
                        $pct = $t > 0 ? round((($p + $j) / $t) * 100, 1) : 0;
                    @endphp
                    <tr class="hover:bg-omg-chardon transition-colors">
                        <td class="px-5 py-3">
                            <p class="text-sm font-body font-semibold text-omg-dark">{{ $al->ap_pat }} {{ $al->ap_mat }}, {{ $al->nombre }}</p>
                            <p class="text-xs font-body text-omg-kashmir">{{ $al->email }}</p>
                        </td>
                        <td class="px-5 py-3 text-center text-sm font-heading font-semibold text-green-600">{{ $p }}</td>
                        <td class="px-5 py-3 text-center text-sm font-heading font-semibold text-red-500">{{ $a }}</td>
                        <td class="px-5 py-3 text-center text-sm font-heading font-semibold text-omg-nile">{{ $j }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="text-sm font-heading font-bold {{ $pct >= 80 ? 'text-green-600' : ($pct >= 60 ? 'text-yellow-500' : 'text-red-500') }}">
                                {{ $pct }}%
                            </span>
                        </td>
                        @foreach ($rubros as $rubro)
                            <td class="px-5 py-3 text-center">
                                @if ($pct >= $rubro->porcentaje_minimo)
                                    <i class="fa-solid fa-circle-check text-green-500 fa-lg" title="Cumple {{ $rubro->nombre }}"></i>
                                @else
                                    <i class="fa-solid fa-circle-xmark text-red-500 fa-lg" title="No cumple {{ $rubro->nombre }}"></i>
                                @endif
                            </td>
                        @endforeach
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('ca.reportes.alumno', [$grupo->id_grupo, $al->id_usuario]) }}"
                               class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-pastel hover:bg-omg-nile hover:text-white text-omg-nile rounded-lg text-xs font-body transition-colors ml-auto w-fit">
                                <i class="fa-solid fa-chart-line"></i> Ver historial
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 6 + $rubros->count() }}" class="px-5 py-8 text-center text-sm font-body text-omg-kashmir">
                            Sin alumnos inscritos
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
