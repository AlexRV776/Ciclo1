@extends('layouts.base')

@section('title', 'Asistencias Pendientes')

@section('content')

<div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-2xl font-bold mb-6">Asistencias pendientes por confirmar</h2>

    @if(session('success'))
        <script>
            Swal.fire('Éxito', '{{ session('success') }}', 'success');
        </script>
    @endif

    <table class="min-w-full border rounded-lg overflow-hidden shadow">
        <thead>
            <tr class="bg-gray-200">
                <th class="px-4 py-2">Docente</th>
                <th class="px-4 py-2">Materia</th>
                <th class="px-4 py-2">Fecha</th>
                <th class="px-4 py-2">Modalidad</th>
                <th class="px-4 py-2">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($items as $a)
            <tr class="border-b">
                <td class="px-4 py-2">{{ $a->docente->usuario->nombre }}</td>
                <td class="px-4 py-2">{{ $a->horarioMateria->grupoMateria->materia->nombre }}</td>
                <td class="px-4 py-2">{{ $a->fecha }}</td>
                <td class="px-4 py-2">{{ $a->modalidad }}</td>
                <td class="px-4 py-2">
                    <form action="{{ route('asistencia.confirmar', $a->id) }}" method="POST">
                        @csrf
                        <button class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">
                            Confirmar
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>

</div>

@endsection