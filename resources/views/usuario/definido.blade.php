@extends('layouts.base')

@section('title', 'Panel de Usuario')

@section('content')
<div class="bg-white shadow-lg rounded-2xl p-6 w-full max-w-3xl text-center mx-auto">

    {{-- Título de bienvenida --}}
    <h2 class="text-2xl font-bold text-gray-800 mb-4">
        Bienvenido, {{ $usuario->nombre }}
    </h2>

    {{-- Mostrar rol del usuario --}}
    <p class="text-gray-600 mb-6">
        Rol: <strong>{{ $usuario->rol ? $usuario->rol->nombre : 'Sin rol' }}</strong>
    </p>

    {{-- Contenedor de botones --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 justify-center">

        {{-- ========================================================= --}}
        {{-- ✅ MOSTRAR BOTÓN "Marcar Asistencia" SOLO PARA DOCENTES --}}
        {{-- ========================================================= --}}
        @if($usuario->rol && strtolower($usuario->rol->nombre) === 'docente' && $usuario->docente)
            <a href="{{ route('asistencia.index') }}"
                class="bg-yellow-600 text-white p-3 rounded-xl hover:bg-yellow-700 transition font-medium block">
                ✅ Marcar Asistencia
            </a>
        @endif

        {{-- ========================================================= --}}
        {{-- ✅ MOSTRAR LOS DEMÁS PERMISOS BASADOS EN SU ROL --}}
        {{-- ========================================================= --}}
        @foreach($usuario->rol->permisos ?? [] as $permiso)
            @php
                $config = [
                    'ver_materias' => ['📘', 'bg-blue-600', '/materias', 'Ver Materias'],
                    'ver_horarios' => ['🕒', 'bg-green-600', '/horarios', 'Ver Horarios'],
                    'ver_grupos' => ['👥', 'bg-purple-600', '/grupos', 'Ver Grupos'],
                    'gestionar_asistencias' => ['⚙️', 'bg-red-600', '/admin/asistencias', 'Gestionar Asistencias'],
                    'confirmar_asistencia' => ['🔐', 'bg-indigo-600', '/asistencia/pendientes', 'Confirmar Asistencias'],
                ];
            @endphp

            @if(isset($config[$permiso->nombre]))
                @php [$icon, $color, $ruta, $texto] = $config[$permiso->nombre]; @endphp

                <a href="{{ url($ruta) }}"
                    class="{{ $color }} text-white p-3 rounded-xl hover:opacity-90 transition font-medium block">
                    {{ $icon }} {{ $texto }}
                </a>
            @endif
        @endforeach

    </div>

    <p class="text-gray-500 mt-8">Solo ves las opciones según tu rol y permisos asignados.</p>
</div>
@endsection 