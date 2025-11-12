@extends('layouts.base')

@section('title', 'Calendario Docente')

@section('content')
<div class="flex flex-col space-y-6">

    {{-- ✅ Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            📅 Mi calendario semanal
        </h2>
        <p class="text-gray-500 text-sm mt-1 sm:mt-0">
            {{ now()->format('d/m/Y') }}
        </p>
    </div>

    {{-- ✅ Contenedor general del calendario --}}
    <div class="grid gap-6 fade-in">
        @forelse ($porDia as $dia => $horarios)
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden transition hover:shadow-lg">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-500 text-white px-5 py-3 flex justify-between items-center">
                    <h3 class="font-semibold text-lg tracking-wide">{{ strtoupper($dia) }}</h3>
                    <span class="text-sm text-blue-100">{{ count($horarios) }} clase{{ count($horarios) > 1 ? 's' : '' }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-gray-700">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                            <tr>
                                <th class="py-3 px-4 text-left">Hora inicio</th>
                                <th class="py-3 px-4 text-left">Hora fin</th>
                                <th class="py-3 px-4 text-left">Materia</th>
                                <th class="py-3 px-4 text-left">Grupo</th>
                                <th class="py-3 px-4 text-left">Aula</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($horarios as $h)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="py-3 px-4">{{ $h->horario->hora_inicio }}</td>
                                    <td class="py-3 px-4">{{ $h->horario->hora_fin }}</td>
                                    <td class="py-3 px-4 font-medium text-gray-900">{{ $h->grupoMateria->materia->nombre ?? '—' }}</td>
                                    <td class="py-3 px-4">{{ $h->grupoMateria->grupo->nombre ?? '—' }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                            {{ $h->aula->nro ?? '—' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                <p class="text-gray-500 text-lg">No tienes horarios asignados aún.</p>
                <p class="text-sm text-gray-400 mt-1">Cuando se te asignen materias, aparecerán aquí.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection