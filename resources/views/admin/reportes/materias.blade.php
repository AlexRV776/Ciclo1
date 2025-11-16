@extends('layouts.base')

@section('title', 'Reporte de Materias')

@section('content')
<h2 class="text-xl font-bold mb-4">📘 Reporte de Materias</h2>

<form method="POST" action="{{ route('admin.reportes.materias.export') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block mb-2 font-semibold">Semestre:</label>
        <select name="semestre" class="border rounded px-3 py-2 w-full">
            <option value="">Todos</option>
            @foreach($semestres as $s)
                <option value="{{ $s->semestre }}">{{ $s->semestre }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block mb-2 font-semibold">Exportar como:</label>
        <select name="tipo" class="border rounded px-3 py-2 w-full" required>
            <option value="">Seleccione...</option>
            <option value="pdf">PDF</option>
            <option value="excel">Excel</option>
        </select>
    </div>

    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Generar reporte
    </button>
</form>
@endsection
