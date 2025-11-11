@extends('layouts.base')

@section('title', 'Reporte de Personal')

@section('content')

<h2 class="text-xl font-bold mb-4">📄 Reporte de Personal</h2>

<form method="POST" action="{{ route('admin.reportes.personal.export') }}" class="space-y-4">
    @csrf

    {{-- FILTRO: ROL --}}
    <div>
        <label class="block mb-2 font-semibold">Rol:</label>
        <select name="rol_id" class="border rounded px-3 py-2 w-full">
            <option value="">Todos</option>
            @foreach($roles as $rol)
                <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
            @endforeach
        </select>
    </div>

    {{-- FILTRO: FECHA DE CONTRATACIÓN --}}
    <div>
        <label class="block mb-2 font-semibold">Fecha de contratación (desde):</label>
        <input type="date" name="fecha_desde" class="border rounded px-3 py-2 w-full">

        <label class="block mt-2 mb-2 font-semibold">Fecha de contratación (hasta):</label>
        <input type="date" name="fecha_hasta" class="border rounded px-3 py-2 w-full">
    </div>

    {{-- TIPO DE REPORTE --}}
    <div>
        <label class="block mb-2 font-semibold">Exportar como:</label>
        <select name="tipo" class="border rounded px-3 py-2 w-full" required>
            <option value="">Seleccione...</option>
            <option value="pdf">PDF</option>
            <option value="excel">Excel</option>
        </select>
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Generar reporte
    </button>

</form>

@endsection