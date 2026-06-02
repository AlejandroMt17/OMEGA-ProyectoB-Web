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
    <a href="#alumnos-riesgo"
       class="bg-white rounded-xl border {{ $alumnosEnRiesgo->count() > 0 ? 'border-orange-300' : 'border-omg-kashmir-dark' }} p-5 flex items-center gap-4 hover:shadow-sm transition-shadow">
        <div class="w-12 h-12 {{ $alumnosEnRiesgo->count() > 0 ? 'bg-orange-50' : 'bg-omg-chardon' }} rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-triangle-exclamation {{ $alumnosEnRiesgo->count() > 0 ? 'text-orange-500' : 'text-omg-coral' }} fa-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-heading font-semibold {{ $alumnosEnRiesgo->count() > 0 ? 'text-orange-500' : 'text-omg-nile' }}">
                {{ $alumnosEnRiesgo->count() }}
            </p>
            <p class="text-xs font-body text-omg-kashmir">En riesgo</p>
        </div>
    </a>
    <a href="{{ route('ca.justificantes.index') }}"
       class="bg-white rounded-xl border border-omg-kashmir-dark p-5 flex items-center gap-4 hover:shadow-sm transition-shadow">
        <div class="w-12 h-12 bg-omg-chardon rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-file-circle-check text-omg-nile fa-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-heading font-semibold text-omg-nile">{{ $justificantesPend }}</p>
            <p class="text-xs font-body text-omg-kashmir">Justificantes</p>
        </div>
    </a>
</div>

{{-- Sesiones de hoy --}}
@if ($sesionesHoy->count() > 0)
<div class="bg-white rounded-xl border border-omg-kashmir-dark p-5 mb-6">
    <h2 class="text-base font-heading font-semibold text-omg-nile mb-4">
        <i class="fa-solid fa-circle text-green-400 text-xs mr-1 animate-pulse"></i>
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
                    <span><i class="fa-solid fa-users mr-1"></i>{{ $item['presentes'] }}/{{ $item['total'] }}</span>
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

@if ($alumnosEnRiesgo->count() > 0)
<div class="bg-white rounded-xl border border-orange-200 overflow-hidden mb-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 px-5 py-4 bg-orange-50 border-b border-orange-200">
        <i class="fa-solid fa-triangle-exclamation text-orange-500"></i>
        <h2 class="text-base font-heading font-semibold text-orange-700">
            Alumnos en Riesgo ({{ $alumnosEnRiesgo->count() }})
        </h2>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('ca.dashboard.index') }}#alumnos-riesgo"
          class="flex items-center gap-3 flex-wrap px-5 py-3 bg-orange-50 border-b border-orange-200">

        <select name="inst" onchange="this.form.submit()"
                class="px-3 py-1.5 bg-white border border-orange-200 rounded-lg text-xs font-body text-omg-dark focus:outline-none">
            <option value="">Todas las instituciones</option>
            @foreach ($instSelect as $instItem)
                <option value="{{ $instItem['id'] }}" {{ $filtroInst == $instItem['id'] ? 'selected' : '' }}>
                    {{ $instItem['nombre'] }}
                </option>
            @endforeach
        </select>

        <select name="grupo" onchange="this.form.submit()"
                {{ !$filtroInst ? 'disabled' : '' }}
                class="px-3 py-1.5 bg-white border border-orange-200 rounded-lg text-xs font-body text-omg-dark focus:outline-none {{ !$filtroInst ? 'opacity-40' : '' }}">
            <option value="">Todos los grupos</option>
            @foreach ($gruposSelect as $grupoItem)
                <option value="{{ $grupoItem['id'] }}" {{ $filtroGrupo == $grupoItem['id'] ? 'selected' : '' }}>
                    {{ $grupoItem['nombre'] }}
                </option>
            @endforeach
        </select>

        <select name="estado" onchange="this.form.submit()"
                {{ !$filtroInst ? 'disabled' : '' }}
                class="px-3 py-1.5 bg-white border border-orange-200 rounded-lg text-xs font-body text-omg-dark focus:outline-none {{ !$filtroInst ? 'opacity-40' : '' }}">
            <option value="">Todos los estados</option>
            <option value="riesgo"   {{ $filtroEstado === 'riesgo'   ? 'selected' : '' }}>En riesgo</option>
            <option value="excedido" {{ $filtroEstado === 'excedido' ? 'selected' : '' }}>Límite excedido</option>
        </select>

        @if ($filtroInst || $filtroGrupo || $filtroEstado)
            <a href="{{ route('ca.dashboard.index') }}#alumnos-riesgo"
               class="px-3 py-1.5 bg-white border border-orange-200 text-orange-600 rounded-lg text-xs font-body hover:bg-orange-100 transition-colors">
                <i class="fa-solid fa-xmark mr-1"></i> Limpiar
            </a>
        @endif
    </form>

    {{-- Leyenda de colores (solo si hay institución seleccionada) --}}
    @if ($filtroInst)
    <div class="px-5 py-3 bg-white border-b border-orange-100 space-y-1.5">
        <div class="flex items-start gap-2">
            <span class="mt-0.5 inline-block w-2.5 h-2.5 rounded-full bg-green-400 flex-shrink-0"></span>
            <span class="text-xs font-body text-omg-dark">Dentro del margen de riesgo (menos del 5% sobre Curso Primera oportunidad)</span>
        </div>
        <div class="flex items-start gap-2">
            <span class="mt-0.5 inline-block w-2.5 h-2.5 rounded-full bg-yellow-400 flex-shrink-0"></span>
            <span class="text-xs font-body text-omg-dark">Perdió el derecho a la primera evaluación</span>
        </div>
        <div class="flex items-start gap-2">
            <span class="mt-0.5 inline-block w-2.5 h-2.5 rounded-full bg-red-400 flex-shrink-0"></span>
            <span class="text-xs font-body text-omg-dark">Perdió el derecho a la segunda evaluación</span>
        </div>
    </div>
    @endif

    {{-- Resultados (se actualiza por fetch) --}}
    @include('modules.dashboard.partials.riesgo')

</div>
@endif


{{-- Mis Instituciones (acordeón — grupos ocultos por defecto) --}}
<div class="mb-2">
    <h2 class="text-base font-heading font-semibold text-omg-nile mb-3">Mis Instituciones</h2>
</div>
@forelse ($instituciones as $item)
    @php $inst = $item['institucion']; $grupos = $item['grupos']; @endphp
    <div class="bg-white rounded-xl border border-omg-kashmir-dark mb-4 overflow-hidden"
         x-data="{ abierto: false }">

        {{-- Header institución (siempre visible) --}}
        <div class="flex items-center gap-4 px-5 py-4 bg-omg-chardon">
            {{-- Logo --}}
            <div class="w-10 h-10 rounded-lg border border-omg-kashmir-dark bg-white flex items-center justify-center overflow-hidden flex-shrink-0">
                @if ($inst->logo)
                    <img src="{{ $inst->logo }}" alt="{{ $inst->nombre }}" class="h-8 w-auto object-contain"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div style="display:none" class="items-center justify-center w-full h-full">
                        <i class="fa-solid fa-building-columns text-omg-kashmir text-sm"></i>
                    </div>
                @else
                    <i class="fa-solid fa-building-columns text-omg-kashmir text-sm"></i>
                @endif
            </div>
            {{-- Nombre y contador --}}
            <div class="flex-1">
                <p class="text-sm font-heading font-semibold text-omg-nile">{{ $inst->nombre }}</p>
                <p class="text-xs font-body text-omg-kashmir">{{ $grupos->count() }} grupo(s)</p>
            </div>
            {{-- Acciones --}}
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('ca.instituciones.seleccionar', $inst->id_institucion) }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-coral hover:bg-omg-coral-dark text-white rounded-lg text-xs font-body transition-colors">
                        <i class="fa-solid fa-check"></i> Seleccionar
                    </button>
                </form>
                {{-- Botón desplegar grupos --}}
                <button @click="abierto = !abierto"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-pastel hover:bg-omg-nile hover:text-white text-omg-nile rounded-lg text-xs font-body transition-colors">
                    <i class="fa-solid fa-layer-group"></i>
                    <span x-text="abierto ? 'Ocultar grupos' : 'Ver grupos'"></span>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                       :class="abierto ? 'rotate-180' : ''"></i>
                </button>
            </div>
        </div>

        {{-- Grupos (colapsados por defecto) --}}
        <div x-show="abierto" x-collapse>
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
                                <span class="bg-green-100 text-green-600 text-xs font-body px-2 py-0.5 rounded-full mr-3 animate-pulse">
                                    EN VIVO
                                </span>
                            @endif
                            <div class="flex items-center gap-2">
                                <a href="{{ route('ca.instituciones.ir', [$inst->id_institucion, 'destino' => route('ca.grupos.sesiones', $grupo)]) }}"
                                   class="flex items-center gap-1 px-3 py-1.5 bg-omg-nile hover:bg-omg-nile-dark text-white rounded-lg text-xs font-body transition-colors">
                                    <i class="fa-solid fa-calendar-check"></i> Sesiones
                                </a>
                                <a href="{{ route('ca.instituciones.ir', [$inst->id_institucion, 'destino' => route('ca.grupos.alumnos', $grupo)]) }}"
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
                </div>
            @endif
        </div>
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
