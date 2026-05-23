{{--
    @file edit.blade.php
    @description Formulario para editar un grupo/aula
    @version 1.0.0
--}}
@extends('layouts.app')
@section('title', 'Editar Aula')
@section('content')

{{-- Título --}}
<div class="mb-6">
    <h1 class="text-2xl font-heading font-semibold text-omg-nile">Editar Aula</h1>
    <p class="text-sm font-body text-omg-kashmir mt-1">
        Modifica la información de {{ $grupo->nombre }} — {{ $grupo->materia }}
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

    <form method="POST"
          action="{{ route('ca.grupos.update', $grupo->id_grupo) }}"
          class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Institución --}}
        <div>
            <label class="block text-sm font-body text-omg-dark mb-1">
                Institución
            </label>
            <select
                name="id_institucion"
                required
                class="w-full px-4 py-2.5 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-kashmir focus:border-transparent">
                @foreach ($instituciones as $institucion)
                    <option value="{{ $institucion->id_institucion }}"
                        {{ $grupo->id_institucion == $institucion->id_institucion ? 'selected' : '' }}>
                        {{ $institucion->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nombre --}}
        <div>
            <label class="block text-sm font-body text-omg-dark mb-1">
                Nombre del grupo
            </label>
            <input
                type="text"
                name="nombre"
                value="{{ old('nombre', $grupo->nombre) }}"
                required
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
                value="{{ old('materia', $grupo->materia) }}"
                required
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
                value="{{ old('periodo', $grupo->periodo) }}"
                required
                class="w-full px-4 py-2.5 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-kashmir focus:border-transparent @error('periodo') border-red-400 @enderror"
            />
            @error('periodo')
                <p class="text-xs text-red-500 mt-1 italic font-body">{{ $message }}</p>
            @enderror
        </div>


        {{-- Horario --}}
        <div>
            <label class="block text-sm font-body text-omg-dark mb-2">
                Horario <span class="text-omg-kashmir font-normal">(opcional)</span>
            </label>
            <p class="text-xs font-body text-omg-kashmir mb-2">Días de clase</p>
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach (['L'=>'Lun','M'=>'Mar','X'=>'Mié','J'=>'Jue','V'=>'Vie','S'=>'Sáb','D'=>'Dom'] as $val => $label)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="dias[]" value="{{ $val }}"
                               class="peer hidden"
                               {{ in_array($val, old('dias', $grupo->dias ? str_split($grupo->dias) : [])) ? 'checked' : '' }}>
                        <span class="peer-checked:bg-omg-nile peer-checked:text-white px-3 py-1.5 rounded-lg border border-omg-kashmir text-xs font-body text-omg-kashmir hover:border-omg-nile transition-colors select-none">
                            {{ $label }}
                        </span>
                    </label>
                @endforeach
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-body text-omg-kashmir mb-1">Hora inicio</label>
                    <input type="time" name="hora_inicio" value="{{ old('hora_inicio', $grupo->hora_inicio) }}"
                           class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-kashmir"/>
                </div>
                <div>
                    <label class="block text-xs font-body text-omg-kashmir mb-1">Hora fin</label>
                    <input type="time" name="hora_fin" value="{{ old('hora_fin', $grupo->hora_fin) }}"
                           class="w-full px-3 py-2 bg-white border border-omg-kashmir rounded-lg text-sm font-body text-omg-dark focus:outline-none focus:ring-2 focus:ring-omg-kashmir"/>
                </div>
            </div>
        </div>

        {{-- No. Alumnos --}}
        <div>
            <label class="block text-sm font-body text-omg-dark mb-1">
                Número de alumnos
            </label>
            <input
                type="number"
                name="no_alumnos"
                value="{{ old('no_alumnos', $grupo->no_alumnos) }}"
                required
                min="1"
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
                Actualizar
            </button>
        </div>

    </form>
</div>

@endsection