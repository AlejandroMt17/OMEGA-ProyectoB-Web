@extends('layouts.app')
@section('title', 'Reportes')
@section('content')

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-heading font-semibold text-omg-nile">Reportes</h1>
        <p class="text-sm font-body text-omg-kashmir mt-1">Visualiza el resumen de asistencias por grupo</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('ca.dashboard.index') }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-omg-chardon hover:bg-omg-pastel text-omg-nile font-heading font-semibold rounded-lg transition-colors text-sm">
            <i class="fa-solid fa-house"></i> Inicio
        </a>
        <a href="javascript:history.back()"
           class="flex items-center gap-2 px-4 py-2.5 bg-omg-chardon hover:bg-omg-pastel text-omg-nile font-heading font-semibold rounded-lg transition-colors text-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

{{-- Filtros con auto-submit --}}
<form method="GET" action="{{ route('ca.reportes.index') }}"
      id="form-filtros"
      class="bg-white rounded-xl border border-omg-kashmir-dark p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-body text-omg-kashmir mb-1">Buscar grupo o materia</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-omg-kashmir text-xs"></i>
                <input type="text" name="busqueda" value="{{ $busqueda }}"
                       placeholder="Ej: Auditoria..."
                       oninput="clearTimeout(window._debounce); window._debounce = setTimeout(() => document.getElementById('form-filtros').submit(), 500)"
                       class="w-full pl-8 pr-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body focus:outline-none focus:ring-2 focus:ring-omg-nile"/>
            </div>
        </div>
        <div>
            <label class="block text-xs font-body text-omg-kashmir mb-1">Periodo</label>
            <select name="periodo" onchange="document.getElementById('form-filtros').submit()"
                    class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body focus:outline-none focus:ring-2 focus:ring-omg-nile">
                <option value="">Todos los periodos</option>
                @foreach ($periodos as $p)
                    <option value="{{ $p }}" {{ $periodo === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-body text-omg-kashmir mb-1">% Asistencia mínimo</label>
            <input type="number" name="min_pct" value="{{ $minPct }}" min="0" max="100"
                   placeholder="Ej: 60"
                   onchange="document.getElementById('form-filtros').submit()"
                   class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body focus:outline-none focus:ring-2 focus:ring-omg-nile"/>
        </div>
        <div>
            <label class="block text-xs font-body text-omg-kashmir mb-1">% Asistencia máximo</label>
            <input type="number" name="max_pct" value="{{ $maxPct }}" min="0" max="100"
                   placeholder="Ej: 80"
                   onchange="document.getElementById('form-filtros').submit()"
                   class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body focus:outline-none focus:ring-2 focus:ring-omg-nile"/>
        </div>
    </div>
    <div class="flex items-center justify-between mt-3">
        <p class="text-xs font-body text-omg-kashmir">
            {{ $reportesPorInstitucion->sum(fn($i) => $i['reportes']->count()) }} grupo(s) encontrado(s)
        </p>
        @if ($busqueda || $periodo || $minPct !== '' || $maxPct !== '')
            <a href="{{ route('ca.reportes.index') }}"
               class="px-3 py-1.5 bg-omg-chardon text-omg-kashmir hover:text-omg-nile rounded-lg text-xs font-body transition-colors">
                <i class="fa-solid fa-xmark mr-1"></i> Limpiar filtros
            </a>
        @endif
    </div>
</form>

{{-- Grupos por institución --}}
@forelse ($reportesPorInstitucion as $bloque)
    <div class="mb-8">
        {{-- Cabecera institución --}}
        <div class="flex items-center gap-3 mb-4">
            @if ($bloque['institucion']?->logo)
                <img src="{{ $bloque['institucion']->logo }}" alt="Logo"
                     class="w-8 h-8 object-contain rounded"/>
            @endif
            <h2 class="text-base font-heading font-semibold text-omg-nile">
                {{ $bloque['institucion']?->nombre ?? 'Institución' }}
            </h2>
            <span class="text-xs font-body text-omg-kashmir bg-omg-chardon px-2 py-0.5 rounded-full">
                {{ $bloque['reportes']->count() }} aula(s)
            </span>
        </div>

        <div class="space-y-4">
            @foreach ($bloque['reportes'] as $reporte)
                @php
                    $pct   = $reporte['porcentaje'];
                    $color = $pct >= 80 ? 'green' : ($pct >= 60 ? 'yellow' : 'red');
                    $descripcion = match(true) {
                        $pct >= 80 => 'Los alumnos asisten con regularidad. El grupo mantiene un buen ritmo.',
                        $pct >= 60 => 'Asistencia moderada. Algunos alumnos podrían estar en riesgo.',
                        default    => 'Asistencia baja. Se recomienda revisar el estado del grupo.',
                    };
                    $iconColor = match($color) {
                        'green'  => 'text-green-600',
                        'yellow' => 'text-yellow-500',
                        default  => 'text-red-500',
                    };
                @endphp
                <div class="bg-white rounded-xl border border-omg-kashmir-dark p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-heading font-semibold text-omg-nile truncate">
                                {{ $reporte['grupo']->nombre }} — {{ $reporte['grupo']->materia }}
                            </h3>
                            <p class="text-xs font-body text-omg-kashmir mt-0.5">
                                {{ $reporte['grupo']->periodo }} · {{ $reporte['total_sesiones'] }} sesión(es)
                            </p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                            {{-- Porcentaje destacado junto al título --}}
                            <span class="text-xl font-heading font-bold {{ $iconColor }}">
                                {{ $pct }}%
                            </span>
                            <a href="{{ route('ca.reportes.detalle', $reporte['grupo']->id_grupo) }}"
                               class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-pastel hover:bg-omg-nile hover:text-white text-omg-nile rounded-lg text-xs font-body transition-colors">
                                <i class="fa-solid fa-ellipsis"></i> Detalles
                            </a>
                        </div>
                    </div>

                    {{-- Barra de progreso --}}
                    <div class="w-full bg-omg-chardon rounded-full h-2 mb-3">
                        <div class="h-2 rounded-full transition-all
                            {{ $color === 'green' ? 'bg-green-500' : ($color === 'yellow' ? 'bg-yellow-400' : 'bg-red-500') }}"
                            style="width: {{ $pct }}%">
                        </div>
                    </div>

                    {{-- Descripción del estado --}}
                    <p class="text-xs font-body {{ $iconColor }} mb-3">
                        <i class="fa-solid {{ $color === 'green' ? 'fa-circle-check' : ($color === 'yellow' ? 'fa-triangle-exclamation' : 'fa-circle-xmark') }} mr-1"></i>
                        {{ $descripcion }}
                    </p>

                    {{-- Contadores --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-green-50 rounded-lg p-3 text-center">
                            <p class="text-lg font-heading font-semibold text-green-600">{{ $reporte['total_presentes'] }}</p>
                            <p class="text-xs font-body text-omg-kashmir">Asistencias</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-3 text-center">
                            <p class="text-lg font-heading font-semibold text-red-500">{{ $reporte['total_ausentes'] }}</p>
                            <p class="text-xs font-body text-omg-kashmir">Faltas</p>
                        </div>
                        <div class="bg-omg-chardon rounded-lg p-3 text-center">
                            <p class="text-lg font-heading font-semibold text-omg-nile">{{ $reporte['total_justif'] }}</p>
                            <p class="text-xs font-body text-omg-kashmir">Justificaciones</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-12 text-center">
        <i class="fa-solid fa-chart-bar text-omg-kashmir fa-2x mb-3"></i>
        <p class="text-sm font-body text-omg-kashmir">No hay grupos que coincidan con los filtros</p>
        @if ($busqueda || $periodo || $minPct !== '' || $maxPct !== '')
            <a href="{{ route('ca.reportes.index') }}" class="text-xs font-body text-omg-nile hover:underline mt-2 inline-block">
                Limpiar filtros
            </a>
        @endif
    </div>
@endforelse

@endsection
