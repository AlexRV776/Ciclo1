@extends('layouts.base')

@section('content')
<h2 class="text-xl font-bold mb-4">Buscar aulas disponibles</h2>

<form action="{{ route('reservas.disponibles') }}" method="GET" class="space-y-4">

    <label class="block font-semibold">Fecha:</label>
    <input type="date" name="fecha" class="border rounded p-2" required>

    <label class="block font-semibold">Hora inicio:</label>
    <input type="time" name="hora_inicio" class="border rounded p-2" required>

    <label class="block font-semibold">Hora fin:</label>
    <input type="time" name="hora_fin" class="border rounded p-2" required>

    <button class="bg-blue-600 text-white p-2 rounded-lg">
        Buscar aulas libres
    </button>
</form>
@endsection