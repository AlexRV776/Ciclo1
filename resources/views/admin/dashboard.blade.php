@extends('layouts.base')

@section('title', 'Panel de Administrador')

@section('content')
<div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-2xl text-center">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Bienvenido, Administrador</h2>
    <p class="text-gray-600 mb-8">Selecciona una de las siguientes opciones:</p>

    

    <form action="{{ route('logout') }}" method="POST" class="mt-10">
        @csrf
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg">
            Cerrar sesión
        </button>
    </form>
</div>
@endsection