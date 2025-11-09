@extends('layouts.base')

@section('title', 'Marcar Asistencia')

@section('content')

<div class="max-w-lg mx-auto bg-white p-6 rounded-xl shadow text-center">

    <h2 class="text-2xl font-bold mb-4">Marcar asistencia</h2>

    <p class="mb-2">
        <strong>Materia:</strong> {{ $hm->grupoMateria->materia->nombre }}
    </p>
    <p class="mb-2">
        <strong>Grupo:</strong> {{ $hm->grupoMateria->grupo->nombre }}
    </p>
    <p class="mb-2">
        <strong>Horario:</strong> {{ $hm->horario->hora_inicio }} - {{ $hm->horario->hora_fin }}
    </p>

    <form action="{{ route('asistencia.guardar', $hm->id) }}" method="POST" class="mt-6">
        @csrf

        <p class="mb-4">Selecciona la modalidad:</p>

        <div class="flex justify-center gap-4 mb-6">
            <label>
                <input type="radio" name="modalidad" value="virtual" required> Virtual
            </label>
            <label>
                <input type="radio" name="modalidad" value="presencial" required> Presencial
            </label>
        </div>

        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Confirmar asistencia
        </button>
    </form>

</div>

@endsection