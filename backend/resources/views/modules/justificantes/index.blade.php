@extends('layouts.app')
@section('title', 'Justificantes')
@section('content')

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-heading font-semibold text-omg-nile">Justificantes</h1>
        <p class="text-sm font-body text-omg-kashmir mt-1">Gestiona las ausencias y justificantes de tus alumnos</p>
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

{{-- Filtros --}}
<form method="GET" action="{{ route('ca.justificantes.index') }}"
      class="bg-white rounded-xl border border-omg-kashmir-dark p-5 mb-6"
      x-data="{
          desde: '{{ $filtroDesde }}',
          hasta: '{{ $filtroHasta }}',
          errorFecha: false,
          validar() {
              if (this.desde && this.hasta && this.desde > this.hasta) {
                  this.errorFecha = true;
                  return false;
              }
              this.errorFecha = false;
              return true;
          }
      }"
      @submit.prevent="if(validar()) $el.submit()">

    <div class="flex flex-wrap items-end gap-3">
        {{-- Periodo --}}
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-body text-omg-kashmir mb-1">Periodo</label>
            <select name="periodo"
                    class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body focus:outline-none focus:ring-2 focus:ring-omg-nile">
                <option value="">Todos los periodos</option>
                @foreach ($periodos as $p)
                    <option value="{{ $p }}" {{ $filtroPeriodo === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>

        {{-- Desde --}}
        <div class="w-40">
            <label class="block text-xs font-body text-omg-kashmir mb-1">Desde</label>
            <input type="date" name="desde" x-model="desde" @change="validar()"
                   class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body focus:outline-none focus:ring-2 focus:ring-omg-nile"
                   :class="errorFecha ? 'border-red-400' : ''"/>
        </div>

        {{-- Hasta --}}
        <div class="w-40">
            <label class="block text-xs font-body text-omg-kashmir mb-1">Hasta</label>
            <input type="date" name="hasta" x-model="hasta" @change="validar()"
                   class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body focus:outline-none focus:ring-2 focus:ring-omg-nile"
                   :class="errorFecha ? 'border-red-400' : ''"/>
        </div>

        <button type="submit"
                :disabled="errorFecha"
                :class="errorFecha ? 'opacity-50 cursor-not-allowed' : 'hover:bg-omg-nile-dark'"
                class="flex items-center gap-2 px-4 py-2 bg-omg-nile text-white font-heading font-semibold rounded-lg text-sm transition-colors">
            <i class="fa-solid fa-filter"></i> Filtrar
        </button>

        @if ($filtroPeriodo || $filtroDesde || $filtroHasta)
            <a href="{{ route('ca.justificantes.index') }}"
               class="flex items-center gap-2 px-4 py-2 bg-omg-chardon text-omg-nile font-heading font-semibold rounded-lg text-sm hover:bg-omg-pastel transition-colors">
                <i class="fa-solid fa-xmark"></i> Limpiar
            </a>
        @endif
    </div>

    {{-- Error fechas --}}
    <p x-show="errorFecha" class="mt-2 text-xs text-red-500 font-body">
        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
        La fecha inicial no puede ser posterior a la fecha final.
    </p>

    @if ($errorFecha)
        <p class="mt-2 text-xs text-red-500 font-body">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
            {{ $errorFecha }}
        </p>
    @endif
</form>

{{-- Lista de grupos --}}
@forelse ($grupos as $grupo)
    <div class="bg-white rounded-xl border border-omg-kashmir-dark mb-4 overflow-hidden"
         x-data="{ abierto: false }">

        {{-- Header grupo --}}
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
                <i class="fa-solid fa-chevron-down text-omg-kashmir transition-transform duration-200"
                   :class="abierto ? 'rotate-180' : ''"></i>
            </div>
        </button>

        {{-- Sesiones --}}
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

                        <button @click="sesAbierta = !sesAbierta"
                                class="w-full flex items-center justify-between px-5 py-3 bg-omg-chardon hover:bg-omg-pastel transition-colors">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-calendar text-omg-kashmir text-xs"></i>
                                <p class="text-sm font-body font-semibold text-omg-dark">
                                    {{ ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'][$sesion->fec_sesion->dayOfWeek === 0 ? 6 : $sesion->fec_sesion->dayOfWeek - 1] }}
                                    {{ $sesion->fec_sesion->day }} de
                                    {{ ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'][$sesion->fec_sesion->month - 1] }}
                                    de {{ $sesion->fec_sesion->year }}
                                </p>
                                <span class="text-xs font-body text-omg-kashmir">
                                    · {{ $sesion->hora_apertura->format('H:i') }}
                                    @if ($sesion->hora_cierre) – {{ $sesion->hora_cierre->format('H:i') }} @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                    $ausSesion  = $asistencias->where('est_asistencia', 2)->count();
                                    $justSesion = $asistencias->where('est_asistencia', 3)->count();
                                @endphp
                                @if ($ausSesion > 0)
                                    <span class="bg-red-100 text-red-600 text-xs font-body px-2 py-0.5 rounded-full">
                                        {{ $ausSesion }} ausente(s)
                                    </span>
                                @endif
                                @if ($justSesion > 0)
                                    <span class="bg-green-100 text-green-600 text-xs font-body px-2 py-0.5 rounded-full">
                                        {{ $justSesion }} justificada(s)
                                    </span>
                                @endif
                                <i class="fa-solid fa-chevron-down text-omg-kashmir text-xs transition-transform duration-200"
                                   :class="sesAbierta ? 'rotate-180' : ''"></i>
                            </div>
                        </button>

                        {{-- Alumnos --}}
                        <div x-show="sesAbierta" x-collapse>
                            <div class="divide-y divide-omg-kashmir-dark">
                                @foreach ($asistencias as $asistencia)
                                    <div class="flex items-center gap-3 px-5 py-3 hover:bg-omg-chardon transition-colors"
                                         x-data="{ estado: {{ $asistencia->est_asistencia }}, cargando: false }"
                                         id="asistencia-{{ $asistencia->id_asistencia }}">

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-body font-semibold text-omg-dark truncate">
                                                {{ $asistencia->alumno?->ap_pat }} {{ $asistencia->alumno?->ap_mat }},
                                                {{ $asistencia->alumno?->nombre }}
                                            </p>
                                            <p class="text-xs font-body text-omg-kashmir">{{ $asistencia->alumno?->email }}</p>
                                        </div>

                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <span x-show="estado === 2"
                                                  class="bg-red-100 text-red-600 text-xs font-body px-2 py-1 rounded-full">Ausente</span>
                                            <span x-show="estado === 3"
                                                  class="bg-green-100 text-green-600 text-xs font-body px-2 py-1 rounded-full">Justificada</span>

                                            <button x-show="estado === 2" :disabled="cargando"
                                                    @click="
                                                        cargando = true;
                                                        fetch('{{ route('ca.justificantes.justificar', $asistencia->id_asistencia) }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                                'X-Requested-With': 'XMLHttpRequest',
                                                            }
                                                        })
                                                        .then(r => r.json())
                                                        .then(d => { if(d.ok) estado = 3; })
                                                        .finally(() => cargando = false);
                                                    "
                                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-nile hover:bg-omg-nile-dark text-white rounded-lg text-xs font-body transition-colors">
                                                <i class="fa-solid fa-file-circle-check" x-show="!cargando"></i>
                                                <i class="fa-solid fa-spinner fa-spin" x-show="cargando"></i>
                                                Justificar
                                            </button>

                                            <button x-show="estado === 3" :disabled="cargando"
                                                    @click="
                                                        cargando = true;
                                                        fetch('{{ route('ca.justificantes.ausente', $asistencia->id_asistencia) }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                                'X-Requested-With': 'XMLHttpRequest',
                                                            }
                                                        })
                                                        .then(r => r.json())
                                                        .then(d => { if(d.ok) estado = 2; })
                                                        .finally(() => cargando = false);
                                                    "
                                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-chardon hover:bg-red-500 hover:text-white text-omg-kashmir rounded-lg text-xs font-body transition-colors">
                                                <i class="fa-solid fa-rotate-left" x-show="!cargando"></i>
                                                <i class="fa-solid fa-spinner fa-spin" x-show="cargando"></i>
                                                Revertir
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="px-5 py-6 text-center border-t border-omg-kashmir-dark">
                    <p class="text-sm font-body text-omg-kashmir">Sin ausencias ni justificantes en este rango</p>
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
