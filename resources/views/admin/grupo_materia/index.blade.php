@extends('layouts.base')

@section('content')
<h2 class="text-3xl font-bold mb-4">Asignaciones Grupo–Materia–Docente</h2>

<a href="{{ route('admin.grupo_materia.create') }}" 
    class="bg-blue-600 text-white px-4 py-2 rounded-lg mb-4 inline-block">
    Nueva Asignación
</a>

<table class="w-full bg-white shadow rounded">
    <thead class="bg-gray-200">
        <tr>
            <th class="p-2">Grupo</th>
            <th class="p-2">Materia</th>
            <th class="p-2">Docente</th>
        </tr>
    </thead>

    <tbody>
        @foreach($relaciones as $rel)
        <tr class="border-b">
            <td class="p-2">{{ $rel->grupo->nombre }}</td>
            <td class="p-2">{{ $rel->materia->nombre }}</td>
            <td class="p-2">{{ $rel->docente->usuario->nombre }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection