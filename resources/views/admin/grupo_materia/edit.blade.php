@extends('layouts.base')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-lg max-w-xl mx-auto">

    <h2 class="text-2xl font-bold mb-4">Editar Asignación</h2>

    <form action="{{ route('admin.grupo_materia.update', $relacion->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Grupo -->
        <label class="block mb-2 font-semibold">Grupo</label>
        <select name="grupo_id" class="w-full px-3 py-2 border rounded mb-4" required>
            @foreach($grupos as $grupo)
                <option value="{{ $grupo->id }}" 
                    {{ $relacion->grupo_id == $grupo->id ? 'selected' : '' }}>
                    {{ $grupo->nombre }}
                </option>
            @endforeach
        </select>

        <!-- Materia -->
        <label class="block mb-2 font-semibold">Materia</label>
        <select name="materia_sigla" class="w-full px-3 py-2 border rounded mb-4" required>
            @foreach($materias as $materia)
                <option value="{{ $materia->sigla }}" 
                    {{ $relacion->materia_sigla == $materia->sigla ? 'selected' : '' }}>
                    {{ $materia->sigla }} — {{ $materia->nombre }}
                </option>
            @endforeach
        </select>

        <!-- Docente -->
        <label class="block mb-2 font-semibold">Docente</label>
        <select name="docente_registro" class="w-full px-3 py-2 border rounded mb-4" required>
            @foreach($docentes as $doc)
                <option value="{{ $doc->registro }}" 
                    {{ $relacion->docente_registro == $doc->registro ? 'selected' : '' }}>
                    {{ $doc->registro }} — {{ $doc->usuario->nombre }}
                </option>
            @endforeach
        </select>

        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Actualizar
        </button>
    </form>
</div>
@endsection
