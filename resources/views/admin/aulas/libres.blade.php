@extends('layouts.base')

@section('title', 'Aulas Libres')

@section('content')
<div class="container mx-auto">
    <h2 class="text-2xl font-semibold mb-4">🏫 Aulas Libres</h2>

    <form method="GET" action="{{ route('admin.aulas.libres') }}" class="mb-6 bg-gray-50 p-4 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Día:</label>
                <select name="dia" class="w-full mt-1 border-gray-300 rounded-lg">
                    @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] as $d)
                        <option value="{{ $d }}" {{ $dia == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Hora inicio:</label>
                <input type="time" name="hora_inicio" value="{{ $hora_inicio }}" class="w-full mt-1 border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Hora fin:</label>
                <input type="time" name="hora_fin" value="{{ $hora_fin }}" class="w-full mt-1 border-gray-300 rounded-lg">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                🔍 Buscar aulas libres
            </button>
        </div>
    </form>

    <div class="bg-white shadow rounded-lg p-4">
        @if($aulasLibres->isEmpty())
            <p class="text-gray-600">❌ No hay aulas disponibles en ese horario.</p>
        @else
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left border-b">Nro Aula</th>
                        <th class="px-4 py-2 text-left border-b">Capacidad</th>
                        <th class="px-4 py-2 text-left border-b">Ubicación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($aulasLibres as $aula)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border-b">{{ $aula->nro }}</td>
                            <td class="px-4 py-2 border-b">{{ $aula->capacidad ?? '—' }}</td>
                            <td class="px-4 py-2 border-b">{{ $aula->ubicacion ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection