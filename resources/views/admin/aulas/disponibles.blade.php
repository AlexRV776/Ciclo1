@extends('layouts.base')

@section('title', 'Aulas disponibles')

@section('content')
<h2 class="text-xl font-semibold mb-4">🏫 Aulas disponibles</h2>

<form method="GET" class="flex gap-4 mb-6">
    <select name="dia" class="border p-2 rounded">
        @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] as $d)
            <option value="{{ $d }}" {{ $dia == $d ? 'selected' : '' }}>{{ $d }}</option>
        @endforeach
    </select>
    <input type="time" name="hora_inicio" value="{{ $hora_inicio }}" class="border p-2 rounded">
    <input type="time" name="hora_fin" value="{{ $hora_fin }}" class="border p-2 rounded">
    <button class="bg-blue-600 text-white px-4 rounded">Buscar</button>
</form>

@if($aulasLibres->isEmpty())
    <p class="text-red-500">No hay aulas disponibles en ese horario 😢</p>
@else
    <table class="w-full text-left border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">N° Aula</th>
                <th class="p-2">Capacidad</th>
                <th class="p-2">Piso</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aulasLibres as $aula)
                <tr class="border-t">
                    <td class="p-2">{{ $aula->nro }}</td>
                    <td class="p-2">{{ $aula->capacidad }}</td>
                    <td class="p-2">{{ $aula->piso }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection