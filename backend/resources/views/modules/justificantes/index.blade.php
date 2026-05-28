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
    {{-- Acordeón nivel 1: Grupo/Materia — cerrado por defecto --}}
    <div class="bg-white rounded-xl border border-omg-kashmir-dark mb-4 overflow-hidden"
         x-data="{ abierto: false }">

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
                    $totalAusentes    = $grupo->sesiones->flatMap->asistencias->where('est_asistencia', 2)->count();
                    $totalJustificadas = $grupo->sesiones->flatMap->asistencias->where('est_asistencia', 3)->count();
                    $totalSesiones    = $grupo->sesiones->count();
                @endphp
                @if ($totalAusentes > 0)
                    <span class="bg-red-100 text-red-600 text-xs font-body px-2 py-1 rounded-full">
                        {{ $totalAusentes }} ausente(s)
                    </span>
                @endif
                @if ($totalJustificadas > 0)
                    <span class="bg-omg-pastel text-omg-nile text-xs font-body px-2 py-1 rounded-full">
                        {{ $totalJustificadas }} justificada(s)
                    </span>
                @endif
                <span class="text-omg-kashmir text-xs font-body">{{ $totalSesiones }} sesión(es)</span>
                <i class="fa-solid fa-chevron-down text-omg-kashmir transition-transform duration-200"
                   :class="abierto ? 'rotate-180' : ''"></i>
            </div>
        </button>

        {{-- Nivel 2: Sesiones — acordeón anidado --}}
        <div x-show="abierto" x-collapse>
            @forelse ($grupo->sesiones->sortByDesc('fec_sesion') as $sesion)
                @php
                    $asistencias = $sesion->asistencias
                        ->whereIn('est_asistencia', [2, 3])
                        ->sortBy(fn($a) => $a->alumno?->ap_pat);
                @endphp
                @if ($asistencias->count() > 0)
                    <div class="border-t border-omg-kashmir-dark"
                         x-data="{ sesAbierta: false }">

                        {{-- Header sesión --}}
                        <button @click="sesAbierta = !sesAbierta"
                                class="w-full flex items-center justify-between px-5 py-3 bg-omg-chardon hover:bg-omg-pastel transition-colors">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-calendar text-omg-kashmir text-xs"></i>
                                <p class="text-sm font-body font-semibold text-omg-dark">
                                    {{ $sesion->fec_sesion->isoFormat('dddd D [de] MMMM [de] YYYY') }}
                                </p>
                                <span class="text-xs font-body text-omg-kashmir">
                                    · {{ $sesion->hora_apertura->format('H:i') }}
                                    @if ($sesion->hora_cierre) – {{ $sesion->hora_cierre->format('H:i') }} @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                    $ausSesSion    = $asistencias->where('est_asistencia', 2)->count();
                                    $justSesSion   = $asistencias->where('est_asistencia', 3)->count();
                                @endphp
                                @if ($ausSesSion > 0)
                                    <span class="bg-red-100 text-red-600 text-xs font-body px-2 py-0.5 rounded-full">
                                        {{ $ausSesSion }} ausente(s)
                                    </span>
                                @endif
                                @if ($justSesSion > 0)
                                    <span class="bg-green-100 text-green-600 text-xs font-body px-2 py-0.5 rounded-full">
                                        {{ $justSesSion }} justificada(s)
                                    </span>
                                @endif
                                <i class="fa-solid fa-chevron-down text-omg-kashmir text-xs transition-transform duration-200"
                                   :class="sesAbierta ? 'rotate-180' : ''"></i>
                            </div>
                        </button>

                        {{-- Nivel 3: Alumnos de esa sesión --}}
                        <div x-show="sesAbierta" x-collapse>
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-omg-kashmir-dark">
                                        <th class="text-left px-5 py-2 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Alumno</th>
                                        <th class="text-left px-5 py-2 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Correo</th>
                                        <th class="text-center px-5 py-2 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Estado</th>
                                        <th class="text-right px-5 py-2 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-omg-kashmir-dark">
                                    @foreach ($asistencias as $asistencia)
                                        <tr class="hover:bg-omg-chardon transition-colors">
                                            <td class="px-5 py-3">
                                                <p class="text-sm font-body font-semibold text-omg-dark">
                                                    {{ $asistencia->alumno?->ap_pat }} {{ $asistencia->alumno?->ap_mat }},
                                                    {{ $asistencia->alumno?->nombre }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-3">
                                                <p class="text-xs font-body text-omg-kashmir">{{ $asistencia->alumno?->email }}</p>
                                            </td>
                                            <td class="px-5 py-3 text-center">
                                                @if ($asistencia->est_asistencia === 2)
                                                    <span class="bg-red-100 text-red-600 text-xs font-body px-2 py-1 rounded-full">Ausente</span>
                                                @else
                                                    <span class="bg-green-100 text-green-600 text-xs font-body px-2 py-1 rounded-full">Justificada</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3">
                                                <div class="flex items-center justify-end gap-2">
                                                    @if ($asistencia->est_asistencia === 2)
                                                        <form method="POST"
                                                              action="{{ route('ca.justificantes.justificar', $asistencia->id_asistencia) }}">
                                                            @csrf
                                                            <button type="submit"
                                                                class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-nile hover:bg-omg-nile-dark text-white rounded-lg text-xs font-body transition-colors">
                                                                <i class="fa-solid fa-file-circle-check"></i>
                                                                Justificar
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST"
                                                              action="{{ route('ca.justificantes.marcar-ausente', $asistencia->id_asistencia) }}">
                                                            @csrf
                                                            <button type="submit"
                                                                class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-chardon hover:bg-red-500 hover:text-white text-omg-kashmir rounded-lg text-xs font-body transition-colors">
                                                                <i class="fa-solid fa-rotate-left"></i>
                                                                Revertir
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @empty
                <div class="px-5 py-6 text-center border-t border-omg-kashmir-dark">
                    <p class="text-sm font-body text-omg-kashmir">Sin ausencias ni justificantes registrados</p>
                </div>
            @endforelse
        </div>
    </div>
@empty
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-12 text-center">
        <i class="fa-solid fa-file-circle-check text-omg-kashmir fa-2x mb-3"></i>
        <p class="text-sm font-body text-omg-kashmir">No hay grupos con ausencias registradas</p>
    </div>
@endforelse

@endsection
