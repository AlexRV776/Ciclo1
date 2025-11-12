@extends('layouts.base')

@section('title', 'Gestionar Asistencia')

@section('content')

<div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-2xl font-bold mb-6">Gestionar Asistencias</h2>

    {{-- Formulario para filtrar por fecha --}}
    <form action="{{ route('asistencia.filtrar') }}" method="POST" class="mb-6 flex items-center gap-4">
    @csrf
    <label class="font-semibold">Fecha:</label>
    <input type="date" name="fecha" value="{{ $fecha ?? '' }}" class="border p-2 rounded">

    <label class="font-semibold">Materia:</label>
    <select name="materia_id" class="border p-2 rounded">
        <option value="">Todas</option>
        @foreach($materias as $m)
            <option value="{{ $m->id }}" @if(isset($materia_id) && $materia_id == $m->id) selected @endif>
                {{ $m->nombre }}
            </option>
        @endforeach
    </select>

    <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded">Filtrar</button>
</form>

    {{-- Tabla de resultados --}}
    @if(isset($asistencias) && $asistencias->count() > 0)
        <table class="min-w-full border rounded-lg overflow-hidden shadow">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Docente</th>
                    <th class="px-4 py-2">Materia</th>
                    <th class="px-4 py-2">Grupo</th>
                    <th class="px-4 py-2">Estado</th>
                    <th class="px-4 py-2">Modalidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asistencias as $a)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $a->docente->usuario->nombre }} {{ $a->docente->usuario->apellido ?? '' }}</td>
                        <td class="px-4 py-2">{{ $a->horarioMateria->grupoMateria->materia->nombre }}</td>
                        <td class="px-4 py-2">{{ $a->horarioMateria->grupoMateria->grupo->nombre }}</td>
                        <td class="px-4 py-2">
                            @if($a->estado == 'pendiente')
                                <span class="text-yellow-600 font-semibold">{{ $a->estado }}</span>
                            @else
                                <span class="text-green-600 font-semibold">{{ $a->estado }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $a->modalidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif(isset($asistencias))
        <p class="text-gray-600">No hay asistencias registradas en esta fecha.</p>
    @endif

</div>

@endsection
