@extends('layouts.base')

@section('title', 'Confirmar Reserva')

@section('content')

<h2 class="text-xl font-bold mb-4">Confirmar Reserva de Aula</h2>

<div class="border p-4 rounded-lg bg-gray-50">

    <p><strong>Aula:</strong> {{ $aula->nro }}</p>
    <p><strong>Capacidad:</strong> {{ $aula->capacidad }}</p>
    <p><strong>Piso:</strong> {{ $aula->piso }}</p>

    <hr class="my-3">

    <p><strong>Docente:</strong> {{ $docente->nombre }} {{ $docente->apellido ?? '' }}</p>
    <p><strong>Fecha:</strong> {{ $fecha }}</p>
    <p><strong>Hora:</strong> {{ $inicio }} — {{ $fin }}</p>

    <form method="POST" action="{{ route('reservas.crear') }}" class="mt-4">
        @csrf

        <input type="hidden" name="aula_id" value="{{ $aula->nro }}">
        <input type="hidden" name="fecha" value="{{ $fecha }}">
        <input type="hidden" name="hora_inicio" value="{{ $inicio }}">
        <input type="hidden" name="hora_fin" value="{{ $fin }}">

        <button class="bg-green-600 text-white px-4 py-2 rounded">
            ✅ Confirmar Reserva
        </button>
    </form>

</div>

@endsection