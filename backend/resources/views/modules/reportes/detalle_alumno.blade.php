@extends('layouts.app')
@section('title', 'Reporte — ' . $alumno->nombre)
@section('content')

{{-- Breadcrumb --}}
<div class="mb-6">
    <div class="flex items-center gap-2 mb-1 text-sm font-body text-omg-kashmir">
        <a href="{{ route('ca.reportes.index') }}" class="hover:text-omg-nile">Reportes</a>
        <i class="fa-solid fa-chevron-right text-xs"></i>
        <a href="{{ route('ca.reportes.detalle', $grupo->id_grupo) }}" class="hover:text-omg-nile">
            {{ $grupo->nombre }}
        </a>
        <i class="fa-solid fa-chevron-right text-xs"></i>
        <span>{{ $alumno->nombre }} {{ $alumno->ap_pat }}</span>
    </div>
    <h1 class="text-2xl font-heading font-semibold text-omg-nile">
        {{ $alumno->ap_pat }} {{ $alumno->ap_mat }}, {{ $alumno->nombre }}
    </h1>
    <p class="text-sm font-body text-omg-kashmir mt-1">
        {{ $grupo->materia }} · {{ $grupo->nombre }} · {{ $grupo->periodo }}
    </p>
</div>

{{-- Resumen --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-4 text-center">
        <p class="text-2xl font-heading font-bold {{ $porcentaje >= 80 ? 'text-green-600' : ($porcentaje >= 60 ? 'text-yellow-500' : 'text-red-500') }}">
            {{ $porcentaje }}%
        </p>
        <p class="text-xs font-body text-omg-kashmir mt-1">Asistencia</p>
    </div>
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-4 text-center">
        <p class="text-2xl font-heading font-bold text-green-600">{{ $presentes }}</p>
        <p class="text-xs font-body text-omg-kashmir mt-1">Asistencias</p>
    </div>
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-4 text-center">
        <p class="text-2xl font-heading font-bold text-red-500">{{ $ausentes }}</p>
        <p class="text-xs font-body text-omg-kashmir mt-1">Faltas</p>
    </div>
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-4 text-center">
        <p class="text-2xl font-heading font-bold text-omg-nile">{{ $justificadas }}</p>
        <p class="text-xs font-body text-omg-kashmir mt-1">Justificaciones</p>
    </div>
</div>

{{-- Tabla sesión a sesión --}}
<div class="bg-white rounded-xl border border-omg-kashmir-dark overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-omg-kashmir-dark bg-omg-chardon">
                <th class="text-left px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">#</th>
                <th class="text-left px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Fecha</th>
                <th class="text-left px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Apertura</th>
                <th class="text-center px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Estado</th>
                <th class="text-center px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Hora registro</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-omg-kashmir-dark">
            @forelse ($sesiones as $i => $item)
                <tr class="hover:bg-omg-chardon transition-colors">
                    <td class="px-5 py-3 text-sm font-body text-omg-kashmir">{{ $i + 1 }}</td>
                    <td class="px-5 py-3">
                        <p class="text-sm font-body text-omg-dark">
                            {{ $item['sesion']->fec_sesion->format('d/m/Y') }}
                        </p>
                    </td>
                    <td class="px-5 py-3">
                        <p class="text-sm font-body text-omg-kashmir">
                            {{ $item['sesion']->hora_apertura->format('H:i') }}
                        </p>
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if ($item['estado'] === 1)
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-body px-2 py-1 rounded-full">
                                <i class="fa-solid fa-circle-check text-xs"></i> Presente
                            </span>
                        @elseif ($item['estado'] === 2)
                            <span class="inline-flex items-center gap-1 bg-red-100 text-red-600 text-xs font-body px-2 py-1 rounded-full">
                                <i class="fa-solid fa-circle-xmark text-xs"></i> Ausente
                            </span>
                        @elseif ($item['estado'] === 3)
                            <span class="inline-flex items-center gap-1 bg-omg-pastel text-omg-nile text-xs font-body px-2 py-1 rounded-full">
                                <i class="fa-solid fa-file-circle-check text-xs"></i> Justificada
                            </span>
                        @else
                            <span class="text-xs font-body text-omg-kashmir">Sin registro</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        <p class="text-xs font-body text-omg-kashmir">{{ $item['hora'] }}</p>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center">
                        <p class="text-sm font-body text-omg-kashmir">Sin sesiones registradas</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
