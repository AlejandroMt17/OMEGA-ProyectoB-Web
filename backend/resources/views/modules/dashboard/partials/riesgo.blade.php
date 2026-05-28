{{-- RF-76: Alumnos en riesgo --}}
@if ($alumnosEnRiesgo->count() > 0)
<div id="alumnos-riesgo" class="bg-white rounded-xl border border-orange-200 overflow-hidden mb-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 px-5 py-4 bg-orange-50 border-b border-orange-200">
        <i class="fa-solid fa-triangle-exclamation text-orange-500"></i>
        <h2 class="text-base font-heading font-semibold text-orange-700">
            Alumnos en Riesgo ({{ $alumnosEnRiesgo->count() }})
        </h2>
    </div>

    {{-- Filtros en tiempo real --}}
    <div class="flex items-center gap-3 flex-wrap px-5 py-3 bg-orange-50 border-b border-orange-200">

        {{-- Institución --}}
        <select name="inst" onchange="actualizarRiesgo()"
                class="px-3 py-1.5 bg-white border border-orange-200 rounded-lg text-xs font-body text-omg-dark focus:outline-none">
            <option value="">Todas las instituciones</option>
            @foreach ($instSelect as $inst)
                <option value="{{ $inst['id'] }}" {{ $filtroInst == $inst['id'] ? 'selected' : '' }}>
                    {{ $inst['nombre'] }}
                </option>
            @endforeach
        </select>

        {{-- Grupo (solo si hay institución) --}}
        <select name="grupo" {{ !$filtroInst ? 'disabled' : '' }} onchange="actualizarRiesgo()"
                class="px-3 py-1.5 bg-white border border-orange-200 rounded-lg text-xs font-body text-omg-dark focus:outline-none {{ !$filtroInst ? 'opacity-40 cursor-not-allowed' : '' }}">
            <option value="">Todos los grupos</option>
            @foreach ($gruposSelect as $grupo)
                <option value="{{ $grupo['id'] }}" {{ $filtroGrupo == $grupo['id'] ? 'selected' : '' }}>
                    {{ $grupo['nombre'] }}
                </option>
            @endforeach
        </select>

        {{-- Estado (solo si hay institución) --}}
        <select name="estado" {{ !$filtroInst ? 'disabled' : '' }} onchange="actualizarRiesgo()"
                class="px-3 py-1.5 bg-white border border-orange-200 rounded-lg text-xs font-body text-omg-dark focus:outline-none {{ !$filtroInst ? 'opacity-40 cursor-not-allowed' : '' }}">
            <option value="">Todos los estados</option>
            <option value="riesgo" {{ $filtroEstado === 'riesgo' ? 'selected' : '' }}>En riesgo</option>
            <option value="excedido" {{ $filtroEstado === 'excedido' ? 'selected' : '' }}>Límite excedido</option>
        </select>

        @if ($filtroInst || $filtroGrupo || $filtroEstado)
        <button onclick="limpiarRiesgo()"
                class="px-3 py-1.5 bg-white border border-orange-200 text-orange-600 rounded-lg text-xs font-body hover:bg-orange-100 transition-colors">
            <i class="fa-solid fa-xmark mr-1"></i> Limpiar
        </button>
        @endif
        <span id="riesgo-cargando" class="hidden text-orange-400 text-xs">
            <i class="fa-solid fa-spinner fa-spin"></i> Cargando...
        </span>
    </div>

    {{-- Resultados --}}
    <div id="riesgo-resultados">
    {{-- Placeholder --}}
    @if (!$filtroInst)
        <div class="px-5 py-8 text-center">
            <i class="fa-solid fa-building-columns text-omg-nile fa-2x mb-3 opacity-40"></i>
            <p class="text-sm font-body text-omg-nile font-semibold">Selecciona una institución para ver los grupos en riesgo</p>
            <p class="text-xs font-body text-omg-kashmir mt-1">Usa el filtro de arriba para comenzar</p>
        </div>
    @else
        {{-- Grupos en riesgo (acordeón) --}}
        @forelse ($riesgoPorGrupo as $grupoId => $items)
            @php $grupo = $items->first()['grupo']; @endphp
            <div class="border-b border-omg-kashmir-dark last:border-b-0" x-data="{ abierto: false }">

                {{-- Header grupo — clickeable --}}
                <button @click="abierto = !abierto"
                        class="w-full flex items-center justify-between px-5 py-3 bg-omg-chardon hover:bg-orange-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-chalkboard-user text-omg-nile text-sm"></i>
                        <div class="text-left">
                            <p class="text-sm font-heading font-semibold text-omg-nile">
                                {{ $grupo->nombre }} — {{ $grupo->materia }}
                            </p>
                            <p class="text-xs font-body text-omg-kashmir">{{ $grupo->periodo }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @php
                            $perdidos = $items->where('perdio', true)->count();
                            $enRiesgo = $items->where('perdio', false)->count();
                        @endphp
                        @if ($perdidos > 0)
                            <span class="bg-red-100 text-red-600 text-xs font-body px-2 py-0.5 rounded-full">{{ $perdidos }} excedido(s)</span>
                        @endif
                        @if ($enRiesgo > 0)
                            <span class="bg-orange-100 text-orange-600 text-xs font-body px-2 py-0.5 rounded-full">{{ $enRiesgo }} en riesgo</span>
                        @endif
                        <i class="fa-solid fa-chevron-down text-omg-kashmir text-xs transition-transform duration-200"
                           :class="abierto ? 'rotate-180' : ''"></i>
                    </div>
                </button>

                {{-- Alumnos (colapsado por defecto) --}}
                <div x-show="abierto" x-collapse>
                <table class="w-full">
                    <thead>
                        <tr class="bg-white border-t border-omg-kashmir-dark">
                            <th class="text-left px-5 py-2 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Alumno</th>
                            <th class="text-center px-5 py-2 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">% Asistencia</th>
                            <th class="text-center px-5 py-2 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Total faltas</th>
                            <th class="text-center px-5 py-2 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-omg-kashmir-dark">
                        @foreach ($items->sortBy('porcentaje') as $item)
                            <tr class="hover:bg-omg-chardon transition-colors">
                                <td class="px-5 py-3">
                                    <p class="text-sm font-body font-semibold text-omg-dark">
                                        {{ $item['alumno']->ap_pat }} {{ $item['alumno']->ap_mat }}, {{ $item['alumno']->nombre }}
                                    </p>
                                    <p class="text-xs font-body text-omg-kashmir">{{ $item['alumno']->email }}</p>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="text-sm font-heading font-bold {{ $item['perdio'] ? 'text-red-500' : 'text-orange-500' }}">
                                        {{ $item['porcentaje'] }}%
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="text-sm font-heading font-bold {{ $item['total_faltas'] >= 3 ? 'text-red-500' : 'text-orange-500' }}">
                                        {{ $item['total_faltas'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if ($item['perdio'])
                                        <span class="bg-red-100 text-red-600 text-xs font-body px-2 py-1 rounded-full">Límite excedido</span>
                                    @else
                                        <span class="bg-orange-100 text-orange-600 text-xs font-body px-2 py-1 rounded-full">En riesgo</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>{{-- fin x-collapse --}}
            </div>
        @empty
            <div class="px-5 py-8 text-center">
                <p class="text-sm font-body text-omg-kashmir">No hay alumnos en riesgo con los filtros seleccionados</p>
            </div>
        @endforelse
    @endif
</div>
    </div>{{-- #riesgo-resultados --}}
@endif
@push('scripts')
<script>
function actualizarRiesgo() {
    const inst   = document.getElementById('filtro-inst')?.value ?? '';
    const grupo  = document.getElementById('filtro-grupo')?.value ?? '';
    const estado = document.getElementById('filtro-estado')?.value ?? '';

    // Bloquear/desbloquear combos
    const selGrupo  = document.getElementById('filtro-grupo');
    const selEstado = document.getElementById('filtro-estado');
    if (selGrupo)  { selGrupo.disabled  = !inst; selGrupo.classList.toggle('opacity-40', !inst); }
    if (selEstado) { selEstado.disabled = !inst; selEstado.classList.toggle('opacity-40', !inst); }

    document.getElementById('riesgo-cargando')?.classList.remove('hidden');

    fetch(`{{ route('ca.dashboard.riesgo') }}?inst=${inst}&grupo=${grupo}&estado=${estado}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        const nuevo = tmp.querySelector('#riesgo-resultados');
        const nuevoGrupo = tmp.querySelector('#filtro-grupo');
        if (nuevo)      document.getElementById('riesgo-resultados').replaceWith(nuevo);
        if (nuevoGrupo) document.getElementById('filtro-grupo').innerHTML = nuevoGrupo.innerHTML;
        document.getElementById('riesgo-cargando')?.classList.add('hidden');
    })
    .catch(() => document.getElementById('riesgo-cargando')?.classList.add('hidden'));
}

function limpiarRiesgo() {
    const s = ['filtro-inst','filtro-grupo','filtro-estado'];
    s.forEach(id => { const el = document.getElementById(id); if(el) el.value = ''; });
    actualizarRiesgo();
}
</script>
@endpush