@extends('layouts.base')

@section('title', 'Asignar Horarios')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-5xl">

    <h2 class="text-2xl font-bold mb-4">Horarios Asignados</h2>

    <a href="{{ route('admin.horario_materia.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-lg">
       ➕ Asignar Horario
    </a>

    <table class="w-full mt-6 table-auto border">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2">Grupo</th>
                <th class="p-2">Materia</th>
                <th class="p-2">Día</th>
                <th class="p-2">Hora</th>
                <th class="p-2">Aula</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($asignaciones as $h)
            <tr class="border">
                <td class="p-2">{{ $h->grupoMateria->grupo->nombre }}</td>
                <td class="p-2">{{ $h->grupoMateria->materia->nombre }}</td>
                <td class="p-2">{{ $h->horario->dia }}</td>
                <td class="p-2">{{ $h->horario->hora_inicio }} - {{ $h->horario->hora_fin }}</td>
                <td class="p-2">{{ $h->aula->nro }}</td>

                <td class="p-2 flex gap-2">
                    <a href="{{ route('admin.horario_materia.edit', $h->id) }}" 
                        class="bg-yellow-500 text-white px-3 py-1 rounded">Editar</a>

                    <form action="{{ route('admin.horario_materia.destroy', $h->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="bg-red-600 text-white px-3 py-1 rounded"
                                onclick="return confirm('¿Eliminar?')">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection