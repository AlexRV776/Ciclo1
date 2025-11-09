@extends('layouts.base')

@section('title', 'Marcar Asistencia')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-2xl font-bold mb-4">Mis clases de hoy</h2>

    {{-- ALERTAS --}}
    @if(session('success'))
        <script>Swal.fire('Éxito', '{{ session('success') }}', 'success');</script>
    @endif

    @if(session('error'))
        <script>Swal.fire('Error', '{{ session('error') }}', 'error');</script>
    @endif

    {{-- LISTA DE CLASES --}}
    @foreach ($horarios as $hm)
        @php
            $ahora = \Carbon\Carbon::now('America/La_Paz');

            // Minutos permitidos
            $horaInicio = \Carbon\Carbon::parse($hm->horario->hora_inicio, 'America/La_Paz');
            $inicioPermitido = $horaInicio->copy()->subMinutes(5);
            $finPermitido    = $horaInicio->copy()->addMinutes(30);

            $puede = $ahora->between($inicioPermitido, $finPermitido);

            // Si YA marcó asistencia hoy
            $asistencia = \App\Models\Asistencia::where('docente_registro', Auth::user()->docente->registro)
                ->where('horario_materia_id', $hm->id)
                ->whereDate('fecha', $ahora->toDateString())
                ->first();
        @endphp

        <div class="p-4 mb-4 border rounded-lg shadow">
            <h3 class="text-lg font-bold">{{ $hm->grupoMateria->materia->nombre }}</h3>
            <p>Grupo: {{ $hm->grupoMateria->grupo->nombre }}</p>
            <p>Horario: <b>{{ $hm->horario->hora_inicio }} - {{ $hm->horario->hora_fin }}</b></p>

            {{-- ✅ Mostrar botón si no tiene asistencia y está en hora --}}
            @if(!$asistencia && $puede)
                <div class="mt-3 p-3 bg-green-100 border-l-4 border-green-600 text-green-800">
                    ✅ Puedes marcar asistencia ahora.
                    <br>

                    <a href="{{ route('asistencia.marcar', $hm->id) }}"
                       class="mt-2 inline-block bg-green-600 px-3 py-2 text-white rounded hover:bg-green-700">
                       Marcar asistencia
                    </a>
                </div>
            @endif

            {{-- ✅ Mostrar si ya está marcada --}}
            @if($asistencia)
                <div class="mt-3 p-3 bg-blue-100 border-l-4 border-blue-600 text-blue-800">
                    ✅ Asistencia marcada ({{ $asistencia->modalidad }}) — Estado: {{ $asistencia->estado }}
                </div>
            @endif

            {{-- ❌ Mostrar aviso si NO está en hora --}}
            @if(!$asistencia && !$puede)
                <div class="mt-3 p-3 bg-gray-100 border-l-4 border-gray-500 text-gray-600">
                    ⏳ Aún no puedes marcar asistencia.
                </div>
            @endif
        </div>

    @endforeach
</div>

@endsection