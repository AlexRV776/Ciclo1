@extends('layouts.base')

@section('title', 'Asignar Horario')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-2xl">

<h2 class="text-xl font-bold mb-4">Asignar Horario</h2>

{{-- Mostrar mensaje de error de conflicto --}}
@if(session('error'))
    <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

{{-- Validaciones de Laravel --}}
@if($errors->any())
    <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.horario_materia.store') }}" method="POST">
    @csrf

    <label class="font-semibold">Grupo - Materia:</label>
    <select name="grupo_materia_id" class="w-full p-2 border rounded mb-3" required>
        @foreach ($grupoMaterias as $gm)
            <option value="{{ $gm->id }}">
                {{ $gm->grupo->nombre }} - {{ $gm->materia->nombre }}
            </option>
        @endforeach
    </select>

    <label class="font-semibold">Horario:</label>
    <select name="horario_id" class="w-full p-2 border rounded mb-3" required>
        @foreach ($horarios as $h)
            <option value="{{ $h->id }}">
                {{ ucfirst($h->dia) }} ({{ $h->hora_inicio }} - {{ $h->hora_fin }})
            </option>
        @endforeach
    </select>

    <label class="font-semibold">Aula:</label>
    <select name="nro" class="w-full p-2 border rounded mb-3" required>
        @foreach ($aulas as $a)
            <option value="{{ $a->nro }}">{{ $a->nro }} — Cap: {{ $a->capacidad }}</option>
        @endforeach
    </select>

    <button class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
</form>

</div>
@endsection
