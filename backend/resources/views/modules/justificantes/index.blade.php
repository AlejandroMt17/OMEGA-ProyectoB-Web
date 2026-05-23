@extends('layouts.app')
@section('title', 'Justificantes')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-heading font-semibold text-omg-nile">Justificantes</h1>
    <p class="text-sm font-body text-omg-kashmir mt-1">
        Gestiona las ausencias y justificantes de tus alumnos
    </p>
</div>

@forelse ($grupos as $grupo)
    {{-- Acordeón por Grupo --}}
    <div class="bg-white rounded-xl border border-omg-kashmir-dark mb-4 overflow-hidden"
         x-data="{ abierto: {{ $loop->first ? 'true' : 'false' }} }">

        {{-- Header del grupo --}}
        <button @click="abierto = !abierto"
                class="w-full flex items-center justify-between px-5 py-4 hover:bg-omg-chardon transition-colors">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-chalkboard-user text-omg-nile"></i>
                <div class="text-left">
                    <p class="text-sm font-heading font-semibold text-omg-nile">
                        {{ $grupo->nombre }} — {{ $grupo->materia }}
                    </p>
                    <p class="text-xs font-body text-omg-kashmir">{{ $grupo->periodo }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @php
                    $pendientes = $grupo->sesiones->flatMap->asistencias->where('est_asistencia', 2)->count();
                @endphp
                @if ($pendientes > 0)
                    <span class="bg-red-100 text-red-600 text-xs font-body px-2 py-1 rounded-full">
                        {{ $pendientes }} ausente(s)
                    </span>
                @endif
                <i class="fa-solid fa-chevron-down text-omg-kashmir transition-transform"
                   :class="abierto ? 'rotate-180' : ''"></i>
            </div>
        </button>

        {{-- Sesiones del grupo --}}
        <div x-show="abierto" x-collapse>
            @forelse ($grupo->sesiones->sortByDesc('fec_sesion') as $sesion)
                @php
                    $asistencias = $sesion->asistencias->whereIn('est_asistencia', [2, 3])
                                         ->sortBy(fn($a) => $a->alumno?->ap_pat);
                @endphp
                @if ($asistencias->count() > 0)
                    <div class="border-t border-omg-kashmir-dark">
                        {{-- Header sesión --}}
                        <div class="bg-omg-chardon px-5 py-2 flex items-center gap-2">
                            <i class="fa-regular fa-calendar text-omg-kashmir text-xs"></i>
                            <p class="text-xs font-body text-omg-kashmir">
                                Sesión del {{ $sesion->fec_sesion->format('d/m/Y') }}
                                · Apertura {{ $sesion->hora_apertura->format('H:i') }}
                                @if ($sesion->hora_cierre)
                                    · Cierre {{ $sesion->hora_cierre->format('H:i') }}
                                @endif
                            </p>
                        </div>

                        {{-- Tabla de alumnos de esa sesión --}}
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-omg-kashmir-dark">
                                    <th class="text-left px-5 py-2 text-xs font-heading font-semibold text-omg-kashmir uppercase tracking-wide">Alumno</th>
                                    <th class="text-left px-5 py-2 text-xs font-heading font-semibold text-omg-kashmir uppercase tracking-wide">Correo</th>
                                    <th class="text-center px-5 py-2 text-xs font-heading font-semibold text-omg-kashmir uppercase tracking-wide">Estado</th>
                                    <th class="text-right px-5 py-2 text-xs font-heading font-semibold text-omg-kashmir uppercase tracking-wide">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-omg-kashmir-dark">
                                @foreach ($asistencias as $asistencia)
                                    <tr class="hover:bg-omg-chardon transition-colors">
                                        <td class="px-5 py-3">
                                            <p class="text-sm font-body font-semibold text-omg-dark">
                                                {{ $asistencia->alumno?->ap_pat }}
                                                {{ $asistencia->alumno?->ap_mat }},
                                                {{ $asistencia->alumno?->nombre }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-3">
                                            <p class="text-xs font-body text-omg-kashmir">
                                                {{ $asistencia->alumno?->email }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            @if ($asistencia->est_asistencia === 2)
                                                <span class="bg-red-100 text-red-600 text-xs font-body px-2 py-1 rounded-full">
                                                    Ausente
                                                </span>
                                            @else
                                                <span class="bg-omg-pastel text-omg-nile text-xs font-body px-2 py-1 rounded-full">
                                                    Justificada
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            @if ($asistencia->est_asistencia === 2)
                                                <form method="POST"
                                                      action="{{ route('ca.justificantes.justificar', $asistencia->id_asistencia) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-nile hover:bg-omg-nile-dark text-white rounded-lg text-xs font-body transition-colors ml-auto">
                                                        <i class="fa-solid fa-file-circle-check"></i>
                                                        Justificar
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST"
                                                      action="{{ route('ca.justificantes.ausente', $asistencia->id_asistencia) }}"
                                                      onsubmit="return confirm('¿Revertir a ausente?')">
                                                    @csrf
                                                    <button type="submit"
                                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-500 hover:text-white text-red-500 rounded-lg text-xs font-body transition-colors ml-auto">
                                                        <i class="fa-solid fa-circle-xmark"></i>
                                                        Revertir
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @empty
                <div class="border-t border-omg-kashmir-dark px-5 py-8 text-center">
                    <p class="text-sm font-body text-omg-kashmir">Sin sesiones registradas</p>
                </div>
            @endforelse

            @if ($grupo->sesiones->flatMap->asistencias->whereIn('est_asistencia', [2,3])->count() === 0)
                <div class="border-t border-omg-kashmir-dark px-5 py-6 text-center">
                    <i class="fa-solid fa-circle-check text-green-400 fa-lg mb-2"></i>
                    <p class="text-sm font-body text-omg-kashmir">Sin ausencias en este grupo</p>
                </div>
            @endif
        </div>
    </div>
@empty
    <div class="bg-white rounded-xl border border-omg-kashmir-dark px-5 py-12 text-center">
        <i class="fa-solid fa-file-circle-check text-omg-kashmir fa-2x mb-3"></i>
        <p class="text-sm font-body text-omg-kashmir">No tienes grupos registrados</p>
    </div>
@endforelse

@endsection
