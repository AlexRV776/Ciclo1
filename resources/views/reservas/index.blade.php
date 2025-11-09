@extends('layouts.base')

@section('title', 'Reservar Aula')

@section('content')

<h2 class="text-xl font-semibold mb-4">Solicitar Reserva de Aula</h2>

<form action="{{ route('reservas.disponibles') }}" method="POST" class="space-y-4">
    @csrf

    <div>
        <label>Fecha:</label>
        <input type="date" name="fecha" class="border p-2 rounded w-full">
    </div>

    <div>
        <label>Hora inicio:</label>
        <input type="time" name="hora_inicio" class="border p-2 rounded w-full">
    </div>

    <div>
        <label>Hora fin:</label>
        <input type="time" name="hora_fin" class="border p-2 rounded w-full">
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Buscar aulas disponibles
    </button>
</form>

@endsection