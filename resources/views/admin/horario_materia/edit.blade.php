@extends('layouts.base')

@section('title', 'Editar Horario Asignado')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-2xl">

<h2 class="text-xl font-bold mb-4">Editar Asignación de Horario</h2>

<form action="{{ route('admin.horario_materia.update', $horario_materium->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label class="font-semibold">Grupo - Materia:</label>
    <select name="grupo_materia_id" class="w-full p-2 border rounded mb-3" required>
        @foreach ($grupoMaterias as $gm)
            <option value="{{ $gm->id }}" {{ $gm->id == $horario_materium->grupo_materia_id ? 'selected' : '' }}>
                {{ $gm->grupo->nombre }} - {{ $gm->materia->nombre }}
            </option>
        @endforeach
    </select>

    <label class="font-semibold">Horario:</label>
    <select name="horario_id" class="w-full p-2 border rounded mb-3" required>
        @foreach ($horarios as $h)
            <option value="{{ $h->id }}" {{ $h->id == $horario_materium->horario_id ? 'selected' : '' }}>
                {{ $h->dia }} ({{ $h->hora_inicio }} - {{ $h->hora_fin }})
            </option>
        @endforeach
    </select>

    <label class="font-semibold">Aula:</label>
    <select name="nro" class="w-full p-2 border rounded mb-3" required>
        @foreach ($aulas as $a)
            <option value="{{ $a->nro }}" {{ $a->nro == $horario_materium->nro ? 'selected' : '' }}>
                {{ $a->nro }} — Cap: {{ $a->capacidad }}
            </option>
        @endforeach
    </select>

    <button class="bg-blue-600 text-white px-4 py-2 rounded">Actualizar</button>
</form>

</div>
@endsection