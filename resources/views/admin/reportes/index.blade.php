@extends('layouts.base')

@section('title', 'Reportes')

@section('content')

<div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-2xl font-bold mb-6">📊 Panel de Reportes</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Botón: Reporte de Personal --}}
        <a href="{{ route('admin.reportes.personal') }}" 
           class="block p-6 bg-blue-600 text-white font-bold text-center rounded-xl shadow hover:bg-blue-700 transition">
            📄 Reporte de Personal
        </a>

        {{-- Botón: Reporte de Asistencia --}}
        <a href="{{ route('admin.reportes.asistencia') }}" 
           class="block p-6 bg-green-600 text-white font-bold text-center rounded-xl shadow hover:bg-green-700 transition">
            🕓 Reporte de Asistencia
        </a>

    </div>

</div>

@endsection

