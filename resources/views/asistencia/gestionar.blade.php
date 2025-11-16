@extends('layouts.base')

@section('title', 'Gestionar Asistencia')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-2xl font-bold mb-6">Gestionar Asistencias</h2>

    {{-- Formulario GET para filtrar --}}
    <form action="{{ route('asistencia.gestionar') }}" method="GET" class="mb-6 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
        <div>
            <label class="block text-sm font-semibold">Fecha desde</label>
            <input type="date" name="fecha_desde" value="{{ $fecha_desde ?? '' }}" class="mt-1 block w-full border rounded p-2" />
        </div>

        <div>
            <label class="block text-sm font-semibold">Fecha hasta</label>
            <input type="date" name="fecha_hasta" value="{{ $fecha_hasta ?? '' }}" class="mt-1 block w-full border rounded p-2" />
        </div>


        <div>
            <label class="block text-sm font-semibold">Registro docente</label>
            <input type="text" name="registro" value="{{ $registro ?? '' }}" placeholder="Ej: 6666" class="mt-1 block w-full border rounded p-2" />
        </div>

        <div>
            <label class="block text-sm font-semibold">Materia</label>
            <select name="materia_sigla" class="mt-1 block w-full border rounded p-2">
                <option value="">Todas</option>
                @foreach($materias as $m)
                    <option value="{{ $m->sigla }}" @if(isset($materia_sigla) && $materia_sigla == $m->sigla) selected @endif>
                        {{ $m->nombre }} ({{ $m->sigla }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filtrar</button>
            <a href="{{ route('asistencia.gestionar') }}" class="bg-gray-200 px-4 py-2 rounded">Limpiar</a>
        </div>
    </form>

    {{-- Resultados --}}
    @if(isset($asistencias) && $asistencias->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full border rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Docente (registro)</th>
                        <th class="px-4 py-2 text-left">Materia</th>
                        <th class="px-4 py-2 text-left">Grupo</th>
                        <th class="px-4 py-2 text-left">Modalidad</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($asistencias as $a)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $a->fecha }}</td>
                            <td class="px-4 py-2">
                                {{ optional($a->docente->usuario)->nombre ?? $a->docente_registro }}
                                <span class="text-gray-400">({{ $a->docente_registro }})</span>
                            </td>
                            <td class="px-4 py-2">
                                {{ optional($a->horarioMateria->grupoMateria->materia)->nombre ?? '—' }}
                                <span class="text-gray-400 text-xs">({{ optional($a->horarioMateria->grupoMateria->materia)->sigla }})</span>
                            </td>
                            <td class="px-4 py-2">{{ optional($a->horarioMateria->grupoMateria->grupo)->nombre ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $a->modalidad }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-sm
                                    @if($a->estado == 'pendiente') bg-yellow-100 text-yellow-800
                                    @elseif($a->estado == 'confirmado' || $a->estado == 'aprobada') bg-green-100 text-green-800
                                    @elseif($a->estado == 'rechazada') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($a->estado) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-600 mt-4">No hay asistencias registradas con esos filtros.</p>
    @endif

</div>
@endsection