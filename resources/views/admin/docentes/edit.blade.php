@extends('layouts.base')

@section('title', 'Editar Datos Docente')

@section('content')
<div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md mx-auto">
    <h2 class="text-2xl font-bold text-blue-700 mb-4">
        ✏️ Editar Datos del Docente </h2>

    <form action="{{ route('admin.docentes.update', $docente) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="especialidad" class="block font-semibold text-gray-700">📘 Especialidad</label>
            <input type="text" name="especialidad" id="especialidad"
                   value="{{ old('especialidad', $docente->especialidad) }}"
                   class="w-full border rounded-lg p-2" required>
        </div>

        <div class="mb-4">
            <label for="sueldo" class="block font-semibold text-gray-700">💰 Sueldo</label>
            <input type="number" name="sueldo" id="sueldo"
                   value="{{ old('sueldo', $docente->sueldo) }}"
                   class="w-full border rounded-lg p-2" step="0.01" required>
        </div>

        <div class="text-center">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                💾 Guardar Cambios
            </button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ route('admin.docentes.index') }}" class="text-blue-600 hover:underline">
            ← Volver a la lista
        </a>
    </div>
</div>
@endsection