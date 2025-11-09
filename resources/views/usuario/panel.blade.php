@extends('layouts.base')

@section('title', 'Panel de Usuario')

@section('content')

<div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-3xl mx-auto">

    {{-- Título --}}
    <h2 class="text-3xl font-bold text-gray-800 mb-3 text-center">
        Bienvenido, {{ $usuario->name }}
    </h2>

    {{-- Rol --}}
    <p class="text-gray-600 text-center mb-8">
        Rol: <strong>{{ $usuario->rol?->nombre ?? 'Sin rol' }}</strong>
    </p>

    {{-- ACCESOS DINÁMICOS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- ✅ Acceso único para docentes: marcar asistencia --}}
        @if($usuario->tienePermiso('marcar_asistencia') && $usuario->docente)
            <a href="{{ route('asistencia.index') }}"
                class="bg-yellow-600 text-white p-4 rounded-xl shadow hover:bg-yellow-700 transition font-semibold text-center">
                ✅ Marcar Asistencia
            </a>
        @endif

        {{-- ✅ Resto de accesos según permisos --}}
        @foreach($usuario->rol->permisos ?? [] as $permiso)

            @php
                $botones = [
                    'ver_materias' => [
                        'icon' => '📘',
                        'color' => 'bg-blue-600',
                        'url' => '/materias',
                        'texto' => 'Materias'
                    ],

                    'ver_horarios' => [
                        'icon' => '🕒',
                        'color' => 'bg-green-600',
                        'url' => '/horarios',
                        'texto' => 'Horarios'
                    ],

                    'ver_grupos' => [
                        'icon' => '👥',
                        'color' => 'bg-purple-600',
                        'url' => '/grupos',
                        'texto' => 'Grupos'
                    ],

                    'confirmar_asistencia' => [
                        'icon' => '✅',
                        'color' => 'bg-indigo-600',
                        'url' => '/asistencia/pendientes',
                        'texto' => 'Confirmar Asistencias'
                    ],

                    'gestionar_asistencias' => [
                        'icon' => '⚙️',
                        'color' => 'bg-red-600',
                        'url' => '/admin/asistencias',
                        'texto' => 'Administrar Asistencias'
                    ],
                ];
            @endphp

            @if(isset($botones[$permiso->nombre]))
                @php
                    $b = $botones[$permiso->nombre];
                @endphp

                <a href="{{ url($b['url']) }}"
                    class="{{ $b['color'] }} text-white p-4 rounded-xl shadow hover:opacity-90 transition font-semibold text-center">
                    {{ $b['icon'] }} {{ $b['texto'] }}
                </a>
            @endif

        @endforeach
    </div>

    <p class="text-gray-500 mt-10 text-center">
        Solo ves las opciones que tu rol y permisos te permiten.
    </p>

</div>

@endsection