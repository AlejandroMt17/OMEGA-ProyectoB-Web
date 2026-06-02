@extends('layouts.app')
@section('title', 'Periodos — ' . $institucion->nombre)
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm font-body text-omg-kashmir mb-1">
            <a href="{{ route('ca.instituciones.index') }}" class="hover:text-omg-nile">Mis Instituciones</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span>{{ $institucion->nombre }}</span>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span>Periodos</span>
        </div>
        <h1 class="text-2xl font-heading font-semibold text-omg-nile">Periodos Académicos</h1>
        <p class="text-sm font-body text-omg-kashmir mt-1">
            Define los periodos de <strong>{{ $institucion->nombre }}</strong>. Al crear un aula podrás seleccionar uno de estos periodos.
        </p>
    </div>
    <a href="{{ route('ca.instituciones.index') }}"
       class="flex items-center gap-2 px-4 py-2.5 bg-omg-chardon hover:bg-omg-pastel text-omg-nile font-heading font-semibold rounded-lg transition-colors text-sm">
        <i class="fa-solid fa-arrow-left"></i> Volver
    </a>
</div>

@if (session('success'))
    <div class="flex items-center gap-3 bg-white border border-green-200 rounded-lg px-4 py-3 mb-6">
        <i class="fa-solid fa-circle-check text-green-500"></i>
        <p class="text-sm font-body text-omg-dark">{{ session('success') }}</p>
    </div>
@endif
@if (session('error'))
    <div class="flex items-center gap-3 bg-white border border-orange-200 rounded-lg px-4 py-3 mb-6">
        <i class="fa-solid fa-triangle-exclamation text-orange-500"></i>
        <p class="text-sm font-body text-omg-dark">{{ session('error') }}</p>
    </div>
@endif

{{-- Agregar periodo --}}
<div class="bg-white rounded-xl border border-omg-kashmir-dark p-5 mb-6"
     x-data="{
        mostrarPersonalizado: false,
        personalizado: '',
        get opciones() {
            const anio = new Date().getFullYear();
            return [
                'Ene-Jun ' + anio,
                'Ago-Dic ' + anio,
                'Ene-Jun ' + (anio + 1),
                'Ago-Dic ' + (anio + 1),
            ];
        }
     }">
    <h2 class="text-sm font-heading font-semibold text-omg-nile mb-3">Agregar periodo</h2>

    {{-- Opciones rápidas --}}
    @php
        $periodosExistentes = $periodos->pluck('nombre')->map(fn($n) => strtolower(trim($n)))->toArray();
    @endphp
    <div class="flex flex-wrap gap-2 mb-4"
         x-data="{ existentes: @json($periodosExistentes) }">
        <template x-for="op in opciones" :key="op">
            <span>
                <form x-show="!existentes.includes(op.toLowerCase())"
                      method="POST" action="{{ route('ca.periodos.store', $institucion->id_institucion) }}">
                    @csrf
                    <input type="hidden" name="nombre" :value="op">
                    <button type="submit"
                            class="px-3 py-1.5 border rounded-lg text-xs font-body transition-colors bg-white text-omg-nile border-omg-kashmir hover:border-omg-nile hover:bg-omg-chardon"
                            x-text="op">
                    </button>
                </form>
                <span x-show="existentes.includes(op.toLowerCase())"
                      class="px-3 py-1.5 border border-green-300 rounded-lg text-xs font-body bg-green-50 text-green-600 flex items-center gap-1">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span x-text="op"></span>
                </span>
            </span>
        </template>
    </div>

    {{-- Periodo personalizado --}}
    <button type="button" @click="mostrarPersonalizado = !mostrarPersonalizado"
            class="text-xs font-body text-omg-nile hover:underline flex items-center gap-1 mb-3">
        <i class="fa-solid fa-plus text-xs"></i>
        Agregar periodo personalizado
    </button>

    <div x-show="mostrarPersonalizado" x-transition>
        <form method="POST" action="{{ route('ca.periodos.store', $institucion->id_institucion) }}"
              class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <input type="text" name="nombre" x-model="personalizado" required
                       placeholder="Ej: Feb-Jul 2026"
                       class="w-full px-3 py-2.5 bg-white border border-omg-kashmir rounded-lg text-sm font-body focus:outline-none focus:ring-2 focus:ring-omg-nile @error('nombre') border-red-400 @enderror"/>
                @error('nombre')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="flex items-center gap-1.5 px-4 py-2.5 bg-omg-coral hover:bg-omg-coral-dark text-white rounded-lg text-sm font-body transition-colors">
                <i class="fa-solid fa-plus"></i> Agregar
            </button>
        </form>
    </div>
</div>

{{-- Lista de periodos --}}
<div class="bg-white rounded-xl border border-omg-kashmir-dark overflow-hidden">

    {{-- Encabezado --}}
    <div class="flex items-center px-5 py-3 bg-omg-chardon border-b border-omg-kashmir-dark">
        <span class="flex-1 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Periodo</span>
        <span class="text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Acciones</span>
    </div>

    <div class="divide-y divide-omg-kashmir-dark">
        @forelse ($periodos as $periodo)
            <div class="px-5 py-3 hover:bg-omg-chardon transition-colors"
                 x-data="{ editando: false, nombre: '{{ addslashes($periodo->nombre) }}' }">

                {{-- Vista normal --}}
                <div x-show="!editando" class="flex items-center gap-3">
                    <span class="flex-1 text-sm font-body font-semibold text-omg-dark">{{ $periodo->nombre }}</span>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="editando = true"
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-omg-pastel hover:bg-omg-nile hover:text-white text-omg-nile rounded-lg text-xs font-body transition-colors">
                            <i class="fa-regular fa-pen-to-square"></i> Editar
                        </button>
                        <div x-data="{ open: false }">
                            <button type="button" @click="open = true"
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-500 hover:text-white text-red-500 rounded-lg text-xs font-body transition-colors">
                                <i class="fa-solid fa-trash"></i> Eliminar
                            </button>
                            <div x-show="open" x-transition class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
                                <div class="bg-white rounded-2xl p-6 max-w-xs w-full mx-4 shadow-xl">
                                    <p class="text-sm font-heading font-semibold text-omg-nile mb-2">¿Eliminar periodo?</p>
                                    <p class="text-xs font-body text-omg-kashmir mb-4">
                                        Se eliminará <strong>{{ $periodo->nombre }}</strong>. Las aulas que ya usan este periodo no se verán afectadas.
                                    </p>
                                    <div class="flex gap-3">
                                        <button @click="open = false" class="flex-1 py-2 bg-omg-chardon text-omg-nile font-heading font-semibold rounded-lg text-sm">Cancelar</button>
                                        <form method="POST" action="{{ route('ca.periodos.destroy', [$institucion->id_institucion, $periodo->id_periodo]) }}" class="flex-1">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-full py-2 bg-red-500 text-white font-heading font-semibold rounded-lg text-sm">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Vista edición --}}
                <div x-show="editando">
                    <form method="POST" action="{{ route('ca.periodos.update', [$institucion->id_institucion, $periodo->id_periodo]) }}">
                        @csrf @method('PATCH')
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="text" name="nombre" x-model="nombre" required
                                   class="flex-1 min-w-0 px-3 py-1.5 border border-omg-nile rounded-lg text-sm font-body focus:outline-none focus:ring-2 focus:ring-omg-nile"/>
                            <button type="button" @click="editando = false; nombre = '{{ addslashes($periodo->nombre) }}'"
                                class="flex items-center gap-1 px-2.5 py-1.5 bg-omg-chardon text-omg-nile rounded-lg text-xs font-body hover:bg-omg-pastel transition-colors whitespace-nowrap">
                                <i class="fa-solid fa-xmark"></i> Cancelar
                            </button>
                            <button type="submit"
                                class="flex items-center gap-1 px-2.5 py-1.5 bg-omg-coral text-white rounded-lg text-xs font-body hover:bg-omg-coral-dark transition-colors whitespace-nowrap">
                                <i class="fa-solid fa-check"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        @empty
            <div class="px-5 py-10 text-center">
                <i class="fa-solid fa-calendar text-omg-kashmir fa-2x mb-3"></i>
                <p class="text-sm font-body text-omg-kashmir">Sin periodos configurados</p>
                <p class="text-xs font-body text-omg-kashmir mt-1">Agrega el primero con el formulario de arriba</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
