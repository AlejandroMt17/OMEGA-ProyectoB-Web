@extends('layouts.app')
@section('title', 'Inicio')
@section('content')

{{-- Encabezado --}}
<div class="mb-6">
    <h1 class="text-2xl font-heading font-semibold text-omg-nile">
        Bienvenido, {{ auth()->user()->nombre }} 👋
    </h1>
    <p class="text-sm font-body text-omg-kashmir mt-1">
        {{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
    </p>
</div>

{{-- Tarjetas resumen --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-omg-chardon rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-chalkboard-user text-omg-nile fa-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-heading font-semibold text-omg-nile">{{ $aulasActivas }}</p>
            <p class="text-xs font-body text-omg-kashmir">Aulas activas</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-omg-chardon rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-calendar-day text-omg-nile fa-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-heading font-semibold text-omg-nile">{{ $sesionesHoy->count() }}</p>
            <p class="text-xs font-body text-omg-kashmir">Sesiones hoy</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-omg-chardon rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-triangle-exclamation text-omg-coral fa-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-heading font-semibold text-omg-nile">0</p>
            <p class="text-xs font-body text-omg-kashmir">En riesgo</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-omg-chardon rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-file-circle-check text-omg-nile fa-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-heading font-semibold text-omg-nile">{{ $justificantesPend }}</p>
            <p class="text-xs font-body text-omg-kashmir">Justificantes</p>
        </div>
    </div>
</div>

{{-- Sesiones de hoy --}}
@if ($sesionesHoy->count() > 0)
<div class="bg-white rounded-xl border border-omg-kashmir-dark p-5 mb-6">
    <h2 class="text-base font-heading font-semibold text-omg-nile mb-4">
        <i class="fa-solid fa-circle text-green-400 text-xs mr-1"></i>
        Sesiones de Hoy
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach ($sesionesHoy as $item)
            @php $s = $item['sesion']; @endphp
            <div class="border border-omg-kashmir-dark rounded-lg p-4 {{ $s->est_sesion === 1 ? 'border-l-4 border-l-green-400' : '' }}">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-heading font-semibold text-omg-nile">
                        {{ $s->grupo->nombre }} — {{ $s->grupo->materia }}
                    </p>
                    @if ($s->est_sesion === 1)
                        <span class="bg-green-100 text-green-600 text-xs font-body px-2 py-0.5 rounded-full">Activa</span>
                    @else
                        <span class="bg-omg-chardon text-omg-kashmir text-xs font-body px-2 py-0.5 rounded-full">Cerrada</span>
                    @endif
                </div>
                <div class="flex items-center gap-4 text-xs font-body text-omg-kashmir">
                    <span><i class="fa-regular fa-clock mr-1"></i>{{ $s->hora_apertura->format('H:i') }}</span>
                    <span><i class="fa-solid fa-users mr-1"></i>{{ $item['presentes'] }}/{{ $item['total'] }} presentes</span>
                </div>
                @if ($s->est_sesion === 1)
                    <div class="mt-2 bg-omg-chardon rounded px-3 py-1.5 text-center">
                        <p class="text-xs font-body text-omg-kashmir">Clave activa</p>
                        <p class="text-lg font-heading font-bold text-omg-nile tracking-widest">{{ $s->clave }}</p>
                    </div>
                @endif
                <a href="{{ route('ca.grupos.sesiones', $s->grupo) }}"
                   class="mt-3 flex items-center justify-center gap-1.5 w-full px-3 py-1.5 bg-omg-pastel hover:bg-omg-nile hover:text-white text-omg-nile rounded-lg text-xs font-body transition-colors">
                    <i class="fa-solid fa-arrow-right"></i> Ver sesión
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Mis Instituciones y Aulas --}}
@forelse ($instituciones as $item)
    @php $inst = $item['institucion']; $grupos = $item['grupos']; @endphp
    <div class="bg-white rounded-xl border border-omg-kashmir-dark mb-5 overflow-hidden">

        {{-- Header institución --}}
        <div class="flex items-center gap-4 px-5 py-4 border-b border-omg-kashmir-dark bg-omg-chardon">
            {{-- Logo --}}
            <div class="w-10 h-10 rounded-lg border border-omg-kashmir-dark bg-white flex items-center justify-center overflow-hidden flex-shrink-0">
                @if ($inst->logo)
                    <img src="{{ $inst->logo }}" alt="{{ $inst->nombre }}"
                         class="h-8 w-auto object-contain"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div style="display:none" class="items-center justify-center w-full h-full">
                        <i class="fa-solid fa-building-columns text-omg-kashmir text-sm"></i>
                    </div>
                @else
                    <i class="fa-solid fa-building-columns text-omg-kashmir text-sm"></i>
                @endif
            </div>
            <div class="flex-1">
                <p class="text-sm font-heading font-semibold text-omg-nile">{{ $inst->nombre }}</p>
                <p class="text-xs font-body text-omg-kashmir">{{ $grupos->count() }} grupo(s)</p>
            </div>
            <a href="{{ route('ca.instituciones.seleccionar', $inst->id_institucion) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-coral hover:bg-omg-coral-dark text-white rounded-lg text-xs font-body transition-colors">
                <i class="fa-solid fa-check"></i> Seleccionar
            </a>
        </div>

        {{-- Grupos --}}
        @if ($grupos->count() > 0)
            <div class="divide-y divide-omg-kashmir-dark">
                @foreach ($grupos as $g)
                    @php $grupo = $g['grupo']; @endphp
                    <div class="flex items-center px-5 py-3 hover:bg-omg-chardon transition-colors">
                        <div class="flex-1">
                            <p class="text-sm font-body font-semibold text-omg-dark">
                                {{ $grupo->nombre }} — {{ $grupo->materia }}
                            </p>
                            <p class="text-xs font-body text-omg-kashmir">
                                {{ $grupo->periodo }} · {{ $g['totalAlumnos'] }} alumno(s)
                            </p>
                        </div>
                        @if ($g['sesionActiva'])
                            <span class="bg-green-100 text-green-600 text-xs font-body px-2 py-0.5 rounded-full mr-3">
                                Sesión activa
                            </span>
                        @endif
                        <div class="flex items-center gap-2">
                            <a href="{{ route('ca.grupos.sesiones', $grupo) }}"
                               class="flex items-center gap-1 px-3 py-1.5 bg-omg-nile hover:bg-omg-nile-dark text-white rounded-lg text-xs font-body transition-colors">
                                <i class="fa-solid fa-calendar-check"></i> Sesiones
                            </a>
                            <a href="{{ route('ca.grupos.alumnos', $grupo) }}"
                               class="flex items-center gap-1 px-3 py-1.5 bg-omg-pastel hover:bg-omg-nile hover:text-white text-omg-nile rounded-lg text-xs font-body transition-colors">
                                <i class="fa-solid fa-users"></i> Alumnos
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-5 py-6 text-center">
                <p class="text-sm font-body text-omg-kashmir">Sin grupos en esta institución</p>
                <a href="{{ route('ca.grupos.create') }}"
                   class="inline-flex items-center gap-1.5 mt-2 text-xs font-body text-omg-nile hover:underline">
                    <i class="fa-solid fa-plus"></i> Crear grupo
                </a>
            </div>
        @endif
    </div>
@empty
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-12 text-center">
        <i class="fa-solid fa-building-columns text-omg-kashmir fa-2x mb-3"></i>
        <p class="text-sm font-body text-omg-kashmir">No tienes instituciones registradas</p>
        <a href="{{ route('ca.instituciones.create') }}"
           class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 bg-omg-coral text-white rounded-lg text-sm font-body">
            <i class="fa-solid fa-plus"></i> Nueva institución
        </a>
    </div>
@endforelse

@endsection
