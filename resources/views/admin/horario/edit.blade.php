@extends('layouts.base')

@section('title', 'Editar Horario')

@section('content')
<div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-2xl mx-auto">
    <h2 class="text-3xl font-bold mb-6 text-gray-800 text-center">Editar Horario</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.horario.update', $horario->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="dia" class="block text-gray-700 font-semibold mb-1">Día:</label>
            <select name="dia" id="dia" class="border border-gray-300 rounded-lg w-full p-2" required>
                @php
                    $dias = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
                @endphp
                @foreach($dias as $dia)
                    <option value="{{ $dia }}" {{ $horario->dia == $dia ? 'selected' : '' }}>{{ $dia }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="hora_inicio" class="block text-gray-700 font-semibold mb-1">Hora de inicio:</label>
            <input type="time" name="hora_inicio" id="hora_inicio" value="{{ $horario->hora_inicio }}" class="border border-gray-300 rounded-lg w-full p-2" required>
        </div>

        <div>
            <label for="hora_fin" class="block text-gray-700 font-semibold mb-1">Hora de fin:</label>
            <input type="time" name="hora_fin" id="hora_fin" value="{{ $horario->hora_fin }}" class="border border-gray-300 rounded-lg w-full p-2" required>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('admin.horario.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Cancelar
            </a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg">
                Actualizar
            </button>
        </div>
    </form>
</div>
@endsection