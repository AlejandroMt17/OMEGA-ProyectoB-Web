@extends('layouts.app')
@section('title', 'Reportes')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-heading font-semibold text-omg-nile">Reportes</h1>
    <p class="text-sm font-body text-omg-kashmir mt-1">Visualiza el resumen de asistencias por grupo</p>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('ca.reportes.index') }}"
      class="bg-white rounded-xl border border-omg-kashmir-dark p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        {{-- Búsqueda --}}
        <div>
            <label class="block text-xs font-body text-omg-kashmir mb-1">Buscar grupo o materia</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-omg-kashmir text-xs"></i>
                <input type="text" name="busqueda" value="{{ $busqueda }}"
                       placeholder="Ej: Auditoria, 216000..."
                       class="w-full pl-8 pr-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-nile"/>
            </div>
        </div>

        {{-- Periodo --}}
        <div>
            <label class="block text-xs font-body text-omg-kashmir mb-1">Periodo</label>
            <select name="periodo"
                    class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-nile">
                <option value="">Todos los periodos</option>
                @foreach ($periodos as $p)
                    <option value="{{ $p }}" {{ $periodo === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>

        {{-- % Mínimo --}}
        <div>
            <label class="block text-xs font-body text-omg-kashmir mb-1">% Asistencia mínimo</label>
            <input type="number" name="min_pct" value="{{ $minPct }}" min="0" max="100"
                   placeholder="Ej: 60"
                   class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-nile"/>
        </div>

        {{-- % Máximo --}}
        <div>
            <label class="block text-xs font-body text-omg-kashmir mb-1">% Asistencia máximo</label>
            <input type="number" name="max_pct" value="{{ $maxPct }}" min="0" max="100"
                   placeholder="Ej: 80"
                   class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-nile"/>
        </div>
    </div>

    <div class="flex items-center justify-between mt-3">
        <p class="text-xs font-body text-omg-kashmir">
            {{ $reportes->count() }} grupo(s) encontrado(s)
        </p>
        <div class="flex gap-2">
            @if ($busqueda || $periodo || $minPct !== '' || $maxPct !== '')
                <a href="{{ route('ca.reportes.index') }}"
                   class="px-3 py-1.5 bg-omg-chardon text-omg-kashmir hover:text-omg-nile rounded-lg text-xs font-body transition-colors">
                    <i class="fa-solid fa-xmark mr-1"></i> Limpiar
                </a>
            @endif
            <button type="submit"
                class="px-4 py-1.5 bg-omg-nile text-white rounded-lg text-xs font-body hover:bg-omg-nile-dark transition-colors">
                <i class="fa-solid fa-filter mr-1"></i> Filtrar
            </button>
        </div>
    </div>
</form>

{{-- Tarjetas por grupo --}}
@forelse ($reportes as $reporte)
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-5 mb-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-heading font-semibold text-omg-nile">
                    {{ $reporte['grupo']->nombre }} — {{ $reporte['grupo']->materia }}
                </h2>
                <p class="text-xs font-body text-omg-kashmir mt-0.5">
                    {{ $reporte['grupo']->periodo }} · {{ $reporte['total_sesiones'] }} sesión(es)
                </p>
            </div>
            <a href="{{ route('ca.reportes.detalle', $reporte['grupo']->id_grupo) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-pastel hover:bg-omg-nile hover:text-white text-omg-nile rounded-lg text-xs font-body transition-colors">
                <i class="fa-solid fa-ellipsis"></i>
                Detalles
            </a>
        </div>

        <div class="mb-3">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-body text-omg-kashmir">Porcentaje de asistencias</span>
                <span class="text-sm font-heading font-semibold text-omg-nile">{{ $reporte['porcentaje'] }}%</span>
            </div>
            <div class="w-full bg-omg-pastel rounded-full h-2">
                <div class="h-2 rounded-full transition-all
                    {{ $reporte['porcentaje'] >= 80 ? 'bg-green-500' : ($reporte['porcentaje'] >= 60 ? 'bg-yellow-400' : 'bg-red-500') }}"
                    style="width: {{ $reporte['porcentaje'] }}%">
                </div>
            </div>
        </div>

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
@empty
    <div class="bg-white rounded-xl border border-omg-kashmir-dark p-12 text-center">
        <i class="fa-solid fa-chart-bar text-omg-kashmir fa-2x mb-3"></i>
        <p class="text-sm font-body text-omg-kashmir">
            {{ $busqueda || $periodo || $minPct !== '' || $maxPct !== '' ? 'No hay grupos que coincidan con los filtros' : 'No se encontraron registros' }}
        </p>
        @if ($busqueda || $periodo || $minPct !== '' || $maxPct !== '')
            <a href="{{ route('ca.reportes.index') }}" class="text-xs font-body text-omg-nile hover:underline mt-2 inline-block">
                Limpiar filtros
            </a>
        @endif
    </div>
@endforelse

@endsection
