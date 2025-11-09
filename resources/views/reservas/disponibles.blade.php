@extends('layouts.base')

@section('title', 'Aulas Disponibles')

@section('content')

<h2 class="text-xl font-semibold mb-4">Aulas disponibles</h2>

@if($aulas->isEmpty())
    <p class="text-gray-500">No hay aulas libres en ese horario.</p>
@else
    @foreach($aulas as $aula)
        <form method="POST" action="{{ route('reservas.confirmar') }}" class="p-3 border rounded mb-2">
            @csrf
            
            <p class="font-bold text-lg">Aula {{ $aula->nro }}</p>
            <p class="text-gray-600">Capacidad: {{ $aula->capacidad }}</p>
            <p class="text-gray-600">Piso: {{ $aula->piso }}</p>

            <input type="hidden" name="aula_id" value="{{ $aula->nro }}">
            <input type="hidden" name="fecha" value="{{ $fecha }}">
            <input type="hidden" name="hora_inicio" value="{{ $inicio }}">
            <input type="hidden" name="hora_fin" value="{{ $fin }}">

            <button class="bg-blue-600 text-white px-3 py-2 rounded mt-3">
                Reservar esta aula
            </button>
        </form>
    @endforeach
@endif

@endsection