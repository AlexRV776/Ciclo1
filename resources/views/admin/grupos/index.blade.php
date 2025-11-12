@extends('layouts.base')

@section('title', 'Gestión de Grupos')

@section('content')
<div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-5xl mx-auto">

    {{-- MENÚ SUPERIOR --}}
    <div class="flex justify-between items-center mb-8 border-b pb-4">
        <h2 class="text-3xl font-bold text-gray-800">📋 Gestión de Grupos</h2>
        <div class="space-x-3">
            <a href="{{ route('admin.grupos.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-300">
                + Crear Grupo
            </a>
            <a href="{{ route('admin.grupo_materia.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-300">
                📚 Asignar Materias
            </a>
        </div>
    </div>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabla de grupos --}}
    <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden text-center">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-3">#</th>
                <th>Nombre del Grupo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($grupos as $g)
                <tr class="border-t">
                    <td class="p-3">{{ $g->id }}</td>
                    <td>{{ $g->nombre }}</td>
                    <td class="space-x-2">
                        <a href="{{ route('admin.grupos.edit', $g) }}" 
                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">Editar</a>
                        <form action="{{ route('admin.grupos.destroy', $g) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded"
                                    onclick="return confirm('¿Seguro que deseas eliminar este grupo?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-4 text-gray-500">No hay grupos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Volver --}}
    <div class="mt-8 text-center">
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline font-semibold">
            ← Volver al panel
        </a>
    </div>
</div>
@endsection