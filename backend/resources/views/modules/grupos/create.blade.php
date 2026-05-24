{{--
    @file create.blade.php
    @description Formulario para crear un nuevo grupo/aula
    @version 1.0.0
--}}
@extends('layouts.app')
@section('title', 'Nueva Aula')
@section('content')

{{-- Título --}}
<div class="mb-6">
    <h1 class="text-2xl font-heading font-semibold text-omg-nile">Nueva Aula</h1>
    <p class="text-sm font-body text-omg-kashmir mt-1">
        Registra un nuevo grupo para controlar asistencias
    </p>
</div>

{{-- Formulario --}}
<div class="bg-white rounded-xl border border-omg-kashmir-dark p-6 max-w-lg">

    @if ($errors->any())
        <div class="flex items-start gap-3 bg-white border border-red-200 rounded-lg px-4 py-3 mb-6">
            <i class="fa-solid fa-circle-xmark text-red-500 mt-0.5"></i>
            <ul class="text-sm text-omg-dark space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('ca.grupos.store') }}" class="space-y-5">
        @csrf

        {{-- Institución --}}
        <div>
            <label class="block text-sm font-body text-omg-dark mb-1">
                Institución
            </label>
            <select
                name="id_institucion"
                required
                class="w-full px-4 py-2.5 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-kashmir focus:border-transparent @error('id_institucion') border-red-400 @enderror">
                <option value="">Selecciona una institución</option>
                @foreach ($instituciones as $institucion)
                    <option value="{{ $institucion->id_institucion }}"
                        {{ old('id_institucion') == $institucion->id_institucion ? 'selected' : '' }}>
                        {{ $institucion->nombre }}
                    </option>
                @endforeach
            </select>
            @error('id_institucion')
                <p class="text-xs text-red-500 mt-1 italic font-body">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nombre --}}
        <div>
            <label class="block text-sm font-body text-omg-dark mb-1">
                Nombre del grupo
            </label>
            <input
                type="text"
                name="nombre"
                value="{{ old('nombre') }}"
                required
                placeholder="Ej: 3A"
                class="w-full px-4 py-2.5 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-kashmir focus:border-transparent @error('nombre') border-red-400 @enderror"
            />
            @error('nombre')
                <p class="text-xs text-red-500 mt-1 italic font-body">{{ $message }}</p>
            @enderror
        </div>

        {{-- Materia --}}
        <div>
            <label class="block text-sm font-body text-omg-dark mb-1">
                Materia
            </label>
            <input
                type="text"
                name="materia"
                value="{{ old('materia') }}"
                required
                placeholder="Ej: Cálculo Diferencial"
                class="w-full px-4 py-2.5 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-kashmir focus:border-transparent @error('materia') border-red-400 @enderror"
            />
            @error('materia')
                <p class="text-xs text-red-500 mt-1 italic font-body">{{ $message }}</p>
            @enderror
        </div>

        {{-- Periodo --}}
        <div>
            <label class="block text-sm font-body text-omg-dark mb-1">
                Periodo
            </label>
            <input
                type="text"
                name="periodo"
                value="{{ old('periodo') }}"
                required
                placeholder="Ej: Ene-Jun 2026"
                class="w-full px-4 py-2.5 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-kashmir focus:border-transparent @error('periodo') border-red-400 @enderror"
            />
            @error('periodo')
                <p class="text-xs text-red-500 mt-1 italic font-body">{{ $message }}</p>
            @enderror
        </div>



        {{-- Horario por día --}}
        <div x-data="{
            filas: [],
            init() {
                const dias = @json(old('horario_dias', []));
                const ini  = @json(old('horario_inicio', []));
                const fin  = @json(old('horario_fin', []));
                if (dias.length) {
                    this.filas = dias.map((d,i) => ({ dia: d, inicio: ini[i]||'', fin: fin[i]||'' }));
                }
            },
            agregar() { this.filas.push({ dia: '', inicio: '', fin: '' }); },
            eliminar(i) { this.filas.splice(i, 1); }
        }" x-init="init()">
            <div class="flex items-center justify-between mb-2">
                <label class="text-sm font-body text-omg-dark">
                    Horario <span class="text-omg-kashmir font-normal">(opcional)</span>
                </label>
                <button type="button" @click="agregar()"
                    class="flex items-center gap-1.5 px-3 py-1 bg-omg-chardon hover:bg-omg-nile hover:text-white text-omg-nile rounded-lg text-xs font-body transition-colors">
                    <i class="fa-solid fa-plus"></i> Agregar día
                </button>
            </div>

            <div class="space-y-2">
                <template x-for="(fila, i) in filas" :key="i">
                    <div class="flex items-center gap-2 bg-omg-chardon rounded-lg px-3 py-2">
                        {{-- Día --}}
                        <select :name="'horario_dias[' + i + ']'" x-model="fila.dia"
                                class="px-2 py-1.5 bg-white border border-omg-kashmir rounded-lg text-xs font-body text-omg-dark focus:outline-none w-24">
                            <option value="">Día</option>
                            <option value="L">Lunes</option>
                            <option value="M">Martes</option>
                            <option value="X">Miércoles</option>
                            <option value="J">Jueves</option>
                            <option value="V">Viernes</option>
                            <option value="S">Sábado</option>
                            <option value="D">Domingo</option>
                        </select>
                        {{-- Hora inicio --}}
                        <div class="flex items-center gap-1 flex-1">
                            <input type="time" :name="'horario_inicio[' + i + ']'" x-model="fila.inicio"
                                   class="flex-1 px-2 py-1.5 bg-white border border-omg-kashmir rounded-lg text-xs font-body text-omg-dark focus:outline-none"/>
                            <span class="text-xs text-omg-kashmir">—</span>
                            <input type="time" :name="'horario_fin[' + i + ']'" x-model="fila.fin"
                                   class="flex-1 px-2 py-1.5 bg-white border border-omg-kashmir rounded-lg text-xs font-body text-omg-dark focus:outline-none"/>
                        </div>
                        {{-- Eliminar --}}
                        <button type="button" @click="eliminar(i)"
                                class="w-7 h-7 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors flex-shrink-0">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                </template>
            </div>

            <p x-show="filas.length === 0" class="text-xs font-body text-omg-kashmir italic mt-1">
                Sin horario definido — presiona "Agregar día" para comenzar
            </p>
        </div>


        {{-- No. Alumnos --}}
        <div>
            <label class="block text-sm font-body text-omg-dark mb-1">
                Número de alumnos
            </label>
            <input
                type="number"
                name="no_alumnos"
                value="{{ old('no_alumnos') }}"
                required
                min="1"
                placeholder="Ej: 30"
                class="w-full px-4 py-2.5 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-kashmir focus:border-transparent @error('no_alumnos') border-red-400 @enderror"
            />
            @error('no_alumnos')
                <p class="text-xs text-red-500 mt-1 italic font-body">{{ $message }}</p>
            @enderror
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('ca.grupos.index') }}"
               class="flex items-center gap-2 px-4 py-2.5 bg-omg-pastel hover:bg-omg-nile hover:text-white text-omg-nile font-heading font-semibold rounded-lg transition-colors text-sm">
                <i class="fa-solid fa-ban"></i>
                Cancelar
            </a>
            <button
                type="submit"
                class="flex items-center gap-2 px-4 py-2.5 bg-omg-coral hover:bg-omg-coral-dark text-white font-heading font-semibold rounded-lg transition-colors text-sm">
                <i class="fa-solid fa-check"></i>
                Guardar
            </button>
        </div>

    </form>
</div>

@endsection