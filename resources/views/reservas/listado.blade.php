@extends('layouts.base')

@section('title', 'Listado de Reservas')

@section('content')

<h2 class="text-xl font-semibold mb-4">📋 Reservas de Aulas</h2>

    <a href="{{ route('reservas.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg mb-4 inline-block">
        + Reservar aula
    </a>

    @if($reservas->isEmpty())
        <p class="text-gray-500">No hay reservas registradas.</p>
    @else
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 border-b text-left">Aula</th>
                    <th class="py-2 px-4 border-b text-left">Fecha</th>
                    <th class="py-2 px-4 border-b text-left">Día</th>
                    <th class="py-2 px-4 border-b text-left">Hora Inicio</th>
                    <th class="py-2 px-4 border-b text-left">Hora Fin</th>
                    <th class="py-2 px-4 border-b text-left">Reservado por</th>
                    <th class="py-2 px-4 border-b text-left">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservas as $reserva)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border-b">{{ $reserva->aula->nro }}</td>
                        <td class="py-2 px-4 border-b">{{ $reserva->fecha }}</td>
                        <td class="py-2 px-4 border-b">{{ $reserva->dia }}</td>
                        <td class="py-2 px-4 border-b">{{ $reserva->hora_inicio }}</td>
                        <td class="py-2 px-4 border-b">{{ $reserva->hora_fin }}</td>
                        <td class="py-2 px-4 border-b">
                            {{ $reserva->usuario->nombre ?? '—' }}
                            {{ $reserva->usuario->apellido ?? '' }}
                        </td>
                        <td class="py-2 px-4 border-b">
                            <span class="px-2 py-1 rounded text-sm
                                @if($reserva->estado == 'pendiente') bg-yellow-200 text-yellow-800
                                @elseif($reserva->estado == 'aprobada') bg-green-200 text-green-800
                                @elseif($reserva->estado == 'rechazada') bg-red-200 text-red-800
                                @endif">
                                {{ ucfirst($reserva->estado) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
@endif

@endsection
