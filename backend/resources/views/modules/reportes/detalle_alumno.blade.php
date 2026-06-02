@extends('layouts.app')
@section('title', 'Historial — ' . $alumno->nombre)
@section('content')

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <div class="flex items-center gap-2 mb-1 text-sm font-body text-omg-kashmir">
            <a href="{{ route('ca.reportes.index') }}" class="hover:text-omg-nile">Reportes</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <a href="{{ route('ca.reportes.detalle', $grupo->id_grupo) }}" class="hover:text-omg-nile">{{ $grupo->nombre }}</a>
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
    <div class="flex items-center gap-2">
        <a href="{{ route('ca.dashboard.index') }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-omg-chardon hover:bg-omg-pastel text-omg-nile font-heading font-semibold rounded-lg transition-colors text-sm">
            <i class="fa-solid fa-house"></i> Inicio
        </a>
        <a href="{{ route('ca.reportes.detalle', $grupo->id_grupo) }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-omg-chardon hover:bg-omg-pastel text-omg-nile font-heading font-semibold rounded-lg transition-colors text-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>
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

{{-- Filtros --}}
<form method="GET" action="{{ route('ca.reportes.alumno', [$grupo->id_grupo, $alumno->id_usuario]) }}"
      id="form-filtros-alumno"
      class="bg-white rounded-xl border border-omg-kashmir-dark p-4 mb-6"
      x-data="{
          desde: '{{ $filtroDesde }}',
          hasta: '{{ $filtroHasta }}',
          errorFecha: false,
          validar() {
              if (this.desde && this.hasta && this.desde > this.hasta) {
                  this.errorFecha = true; return false;
              }
              this.errorFecha = false; return true;
          }
      }"
      @submit.prevent="if(validar()) $el.submit()">

    <div class="flex flex-wrap items-end gap-3">
        <div class="w-40">
            <label class="block text-xs font-body text-omg-kashmir mb-1">Desde</label>
            <input type="date" name="desde" x-model="desde" @change="validar()"
                   class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body focus:outline-none focus:ring-2 focus:ring-omg-nile"
                   :class="errorFecha ? 'border-red-400' : ''"/>
        </div>
        <div class="w-40">
            <label class="block text-xs font-body text-omg-kashmir mb-1">Hasta</label>
            <input type="date" name="hasta" x-model="hasta" @change="validar()"
                   class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body focus:outline-none focus:ring-2 focus:ring-omg-nile"
                   :class="errorFecha ? 'border-red-400' : ''"/>
        </div>
        <div class="w-44">
            <label class="block text-xs font-body text-omg-kashmir mb-1">Estado</label>
            <select name="estado" onchange="document.getElementById('form-filtros-alumno').submit()"
                    class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body focus:outline-none focus:ring-2 focus:ring-omg-nile">
                <option value="">Todos</option>
                <option value="1" {{ $filtroEstado === '1' ? 'selected' : '' }}>Solo asistencias</option>
                <option value="2" {{ $filtroEstado === '2' ? 'selected' : '' }}>Solo faltas</option>
                <option value="3" {{ $filtroEstado === '3' ? 'selected' : '' }}>Solo justificaciones</option>
            </select>
        </div>
        <button type="submit" :disabled="errorFecha"
                :class="errorFecha ? 'opacity-50 cursor-not-allowed' : 'hover:bg-omg-nile-dark'"
                class="flex items-center gap-2 px-4 py-2 bg-omg-nile text-white font-heading font-semibold rounded-lg text-sm transition-colors">
            <i class="fa-solid fa-filter"></i> Filtrar
        </button>
        @if ($filtroDesde || $filtroHasta || $filtroEstado !== '')
            <a href="{{ route('ca.reportes.alumno', [$grupo->id_grupo, $alumno->id_usuario]) }}"
               class="flex items-center gap-2 px-4 py-2 bg-omg-chardon text-omg-nile font-heading font-semibold rounded-lg text-sm hover:bg-omg-pastel transition-colors">
                <i class="fa-solid fa-xmark"></i> Limpiar
            </a>
        @endif
    </div>

    <p x-show="errorFecha" class="mt-2 text-xs text-red-500 font-body">
        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
        La fecha inicial no puede ser posterior a la fecha final.
    </p>
    @if ($errorFecha)
        <p class="mt-2 text-xs text-red-500 font-body">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $errorFecha }}
        </p>
    @endif
</form>

{{-- Tabla sesión a sesión --}}
<div class="bg-white rounded-xl border border-omg-kashmir-dark overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-omg-kashmir-dark bg-omg-chardon">
                <th class="text-left px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">#</th>
                <th class="text-left px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Fecha de la sesión</th>
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
                        <p class="text-sm font-body text-omg-dark">{{ $item['sesion']->fec_sesion->format('d/m/Y') }}</p>
                    </td>
                    <td class="px-5 py-3">
                        <p class="text-sm font-body text-omg-kashmir">{{ $item['sesion']->hora_apertura->format('H:i') }}</p>
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
                    <td colspan="5" class="px-5 py-10 text-center text-sm font-body text-omg-kashmir">
                        No hay sesiones con los filtros seleccionados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
