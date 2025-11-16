@extends('layouts.base')

@section('title', 'Reporte de Grupos')

@section('content')

<h2 class="text-xl font-bold mb-4">📚 Reporte de Grupos</h2>

<form method="POST" action="{{ route('admin.reportes.grupos.export') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block mb-2 font-semibold">Exportar como:</label>
        <select name="tipo" class="border rounded px-3 py-2 w-full" required>
            <option value="">Seleccione...</option>
            <option value="pdf">PDF</option>
            <option value="excel">Excel</option>
        </select>
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Generar Reporte
    </button>
</form>

@endsection
