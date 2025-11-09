<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body class="font-[Poppins] bg-gray-100 min-h-screen flex">

    {{-- ✅ SIDEBAR OSCURO (Estilo ChatGPT) --}}
    <aside id="sidebar"
        class="bg-gray-900 text-gray-300 w-64 min-h-screen fixed md:relative transform md:translate-x-0 -translate-x-full transition-transform duration-300 z-40">

        {{-- Header --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-700">
            <h2 class="text-lg font-semibold text-white">Menú</h2>
            <button onclick="toggleSidebar()" class="md:hidden text-white text-xl">✖</button>
        </div>

        {{-- ✅ MENÚ DEL USUARIO (ESTILO GOOGLE) --}}
        @auth
        <div class="relative p-4 border-b border-gray-700">

            <button onclick="togglePerfilMenu()" class="flex items-center space-x-3 hover:opacity-80 w-full">

                {{-- Foto / Inicial --}}
                <div class="bg-blue-600 text-white w-10 h-10 flex items-center justify-center rounded-full text-lg">
                    {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
                </div>

                {{-- Nombre --}}
                <div class="flex-1 text-white">
                    <div class="font-semibold">{{ Auth::user()->nombre }}</div>
                    <div class="text-sm text-gray-400">{{ Auth::user()->correo }}</div>
                </div>

            </button>

            {{-- ✅ POPUP flotante --}}
            <div id="perfilMenu"
                class="hidden absolute left-4 right-4 mt-4 bg-gray-900 text-white rounded-xl shadow-xl border border-gray-700 p-4 z-50">

                <div class="text-center mb-3">
                    <div class="bg-blue-600 w-16 h-16 rounded-full mx-auto flex items-center justify-center text-xl">
                        {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
                    </div>

                    <p class="mt-2 font-semibold">{{ Auth::user()->nombre }}</p>
                    <p class="text-sm text-gray-300">{{ Auth::user()->correo }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        Rol: {{ Auth::user()->rol->nombre }}
                    </p>
                </div>

                <a href="{{ route('perfil.edit') }}"
                    class="block text-sm bg-blue-600 hover:bg-blue-700 text-center py-2 rounded-lg mb-2">
                    ✏️ Editar perfil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-sm bg-red-600 hover:bg-red-700 text-center py-2 rounded-lg">
                        🚪 Cerrar sesión
                    </button>
                </form>

            </div>
        </div>
        @endauth

        {{-- ✅ Menú general --}}
        <nav class="p-4 space-y-2 text-sm">
            <a href="{{ url('/') }}" class="block py-2 px-3 rounded-lg hover:bg-gray-800 transition">🏠 Inicio</a>

            <a href="{{ route('register') }}" class="block py-2 px-3 rounded-lg hover:bg-gray-800 transition">
                📝 Registrar
            </a>

            @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full text-left py-2 px-3 rounded-lg hover:bg-gray-800 transition text-red-400">
                    🚪 Cerrar sesión
                </button>
            </form>
            @endauth
        </nav>

        {{-- ✅ Menú según permisos --}}
        @auth
        @if(Auth::user()->rol && Auth::user()->rol->permisos)
            <div class="mt-4 px-4 text-xs uppercase text-gray-500">Accesos</div>

            <nav class="p-4 space-y-2 text-sm">

                {{-- ✅ PERMISO: ver_materias --}}
                @if(Auth::user()->rol->permisos->contains('nombre', 'ver_materias'))
                    <a href="{{ route('admin.materias.index') }}" 
                    class="block py-2 px-3 rounded-lg hover:bg-gray-800 transition">
                    📘 Materias
                    </a>
                @endif

                {{-- ✅ PERMISO: ver_horarios --}}
                @if(Auth::user()->rol->permisos->contains('nombre', 'ver_horarios'))
                    <a href="{{ route('admin.horario.index') }}" 
                    class="block py-2 px-3 rounded-lg hover:bg-gray-800 transition">
                    🕒 Horarios
                    </a>
                @endif

                {{-- ✅ PERMISO: ver_grupos --}}
                @if(Auth::user()->rol->permisos->contains('nombre', 'ver_grupos'))
                    <a href="{{ route('admin.grupos.index') }}" 
                    class="block py-2 px-3 rounded-lg hover:bg-gray-800 transition">
                    👥 Grupos
                    </a>
                @endif

                {{-- ✅ PERMISO: confirmar_asistencia --}}
                @if(Auth::user()->rol->permisos->contains('nombre', 'confirmar_asistencia'))
                    <a href="{{ url('/asistencia/pendientes') }}" 
                    class="block py-2 px-3 rounded-lg hover:bg-gray-800 transition">
                    ✅ Confirmar Asistencias
                    </a>
                @endif

                {{-- ✅ PERMISO: gestionar_asistencias --}}
                @if(Auth::user()->rol->permisos->contains('nombre', 'gestionar_asistencias'))
                    <a href="{{ url('/admin/asistencias') }}" 
                    class="block py-2 px-3 rounded-lg hover:bg-gray-800 transition">
                    ⚙️ Gestionar Asistencias
                    </a>
                @endif

                {{-- ✅ PERMISO NUEVO: ver_aulas / reserva de aulas --}}
                @if(Auth::user()->rol->permisos->contains('nombre', 'ver_aulas'))
                    <a href="{{ route('reservas.index') }}" 
                    class="block py-2 px-3 rounded-lg hover:bg-gray-800 transition">
                    🏫 Reservar Aulas
                    </a>
                @endif

            </nav>
        @endif
        @endauth

        {{-- ✅ Administración --}}
        @auth
        @if(Auth::user()->rol && Auth::user()->rol->nombre === 'Administrador')
        <div class="mt-6 px-4 text-xs uppercase text-gray-500">Administración</div>

        <nav class="p-4 space-y-2 text-sm">
            <a href="{{ route('admin.materias.index') }}" class="block py-2 px-3 rounded-lg hover:bg-gray-800">📘 Materias</a>
            <a href="{{ route('admin.grupos.index') }}" class="block py-2 px-3 rounded-lg hover:bg-gray-800">👥 Grupos</a>
            <a href="{{ route('admin.aulas.index') }}" class="block py-2 px-3 rounded-lg hover:bg-gray-800">🏫 Aulas</a>
            <a class="block py-2 px-3 rounded-lg hover:bg-gray-800">👨‍🏫 Docentes</a>
            <a href="{{ route('admin.bitacora') }}" class="block py-2 px-3 rounded-lg hover:bg-gray-800">📜 Bitácora</a>
            <a href="{{ route('admin.horario.index') }}" class="block py-2 px-3 rounded-lg hover:bg-gray-800">🕒 Horario</a>
            <a href="{{ route('admin.grupo_materia.index') }}" class="block py-2 px-3 rounded-lg hover:bg-gray-800">➕ Asignar Materia a Grupo</a>
            <a href="{{ route('admin.horario_materia.index') }}" class="block py-2 px-3 rounded-lg hover:bg-gray-800">➕ Asignar Aula a Materia</a>
            <a href="{{ route('admin.roles.index') }}"
            class="block px-4 py-2 text-gray-300 hover:bg-gray-700 transition">
            🔧 Gestionar Roles
            <a href="{{ route('admin.permisos.index') }}"
                class="block px-4 py-2 text-gray-300 hover:bg-gray-700 transition">
                🛡️ Gestionar Permisos
            </a>
            <a href="{{ route('admin.usuarios.index') }}"
                class="block px-4 py-2 text-gray-300 hover:bg-gray-700 transition">
                👤 Gestionar Usuarios
            </a>
        </a>
        </nav>
        @endif
        @endauth

    </aside>

    {{-- ✅ Botón menú móvil --}}
    <button onclick="toggleSidebar()"
        class="md:hidden fixed top-4 left-4 bg-gray-900 text-white px-3 py-2 rounded-lg shadow-lg z-50">
        ☰
    </button>

    {{-- ✅ Contenido --}}
    <main class="flex-1 p-6 md:ml-64 transition-all">
        <div class="bg-white shadow-lg rounded-xl p-6 animate-fadeIn">
            @yield('content')
        </div>
    </main>

    {{-- ✅ Scripts --}}
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
        }

        function togglePerfilMenu() {
            document.getElementById('perfilMenu').classList.toggle('hidden');
        }

        // Cerrar al hacer clic fuera
        window.addEventListener('click', function(e) {
            if (!e.target.closest('#perfilMenu') &&
                !e.target.closest('button[onclick="togglePerfilMenu()"]')) {
                document.getElementById('perfilMenu')?.classList.add('hidden');
            }
        });
    </script>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn .4s ease-in-out; }
    </style>

</body>
</html>