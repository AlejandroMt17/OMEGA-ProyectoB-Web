@extends('layouts.app')
@section('title', 'Periodos — ' . $institucion->nombre)
@section('content')

<div class="mb-6">
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

@if (session('success'))
    <div class="flex items-center gap-3 bg-white border border-green-200 rounded-lg px-4 py-3 mb-6">
        <i class="fa-solid fa-circle-check text-green-500"></i>
        <p class="text-sm font-body text-omg-dark">{{ session('success') }}</p>
    </div>
@endif

{{-- Formulario para agregar --}}
<div class="bg-white rounded-xl border border-omg-kashmir-dark p-5 mb-6">
    <h2 class="text-sm font-heading font-semibold text-omg-nile mb-3">Agregar periodo</h2>
    <form method="POST" action="{{ route('ca.periodos.store', $institucion->id_institucion) }}"
          class="flex items-end gap-3">
        @csrf
        <div class="flex-1">
            <label class="block text-xs font-body text-omg-kashmir mb-1">Nombre del periodo</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" required
                   placeholder="Ej: Enero Junio 2026, Agosto Diciembre 2026..."
                   class="w-full px-3 py-2.5 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-nile @error('nombre') border-red-400 @enderror"/>
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

{{-- Lista de periodos --}}
<div class="bg-white rounded-xl border border-omg-kashmir-dark overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-omg-kashmir-dark bg-omg-chardon">
                <th class="text-left px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Periodo</th>
                <th class="text-right px-5 py-3 text-xs font-heading font-semibold text-omg-nile uppercase tracking-wide">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-omg-kashmir-dark">
            @forelse ($periodos as $periodo)
                <tr class="hover:bg-omg-chardon transition-colors">
                    <td class="px-5 py-3">
                        <p class="text-sm font-body font-semibold text-omg-dark">{{ $periodo->nombre }}</p>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div x-data="{ open: false }">
                            <button type="button" @click="open = true"
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-500 hover:text-white text-red-500 rounded-lg text-xs font-body transition-colors ml-auto">
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
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-5 py-10 text-center">
                        <i class="fa-solid fa-calendar text-omg-kashmir fa-2x mb-3"></i>
                        <p class="text-sm font-body text-omg-kashmir">Sin periodos configurados</p>
                        <p class="text-xs font-body text-omg-kashmir mt-1">Agrega el primero con el formulario de arriba</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
