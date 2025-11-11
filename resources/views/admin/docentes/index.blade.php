@extends('layouts.base')

@section('title', 'Gestión de Docentes')

@section('content')
<div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-5xl mx-auto">

    <div class="flex justify-between items-center mb-8 border-b pb-4">
        <h2 class="text-3xl font-bold text-gray-800">📋 Gestión de Docentes</h2>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden text-center">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-3">Registro</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Especialidad</th>
                <th>Sueldo</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($docentes as $d)
                <tr class="border-t">
                    <td class="p-3">{{ $d->registro }}</td>
                    <td>{{ $d->usuario->nombre ?? '-' }}</td>
                    <td>{{ $d->usuario->correo ?? '-' }}</td>
                    <td>{{ $d->especialidad }}</td>
                    <td>{{ $d->sueldo }}</td>
                    <td>
                        <a href="{{ route('admin.docentes.edit', $d) }}" 
                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                           Editar
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-4 text-gray-500">No hay docentes registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-8 text-center">
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline font-semibold">
            ← Volver al panel
        </a>
    </div>
</div>
@endsection