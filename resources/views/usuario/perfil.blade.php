@extends('layouts.base')

@section('title', 'Editar Perfil')

@section('content')

<div class="bg-white p-8 rounded-2xl shadow-xl max-w-lg mx-auto">

    <h2 class="text-2xl font-bold mb-4 text-center">Editar Perfil</h2>

    <form method="POST" action="{{ route('perfil.update') }}" class="space-y-4">
        @csrf

        <div>
            <label class="font-semibold">Nombre</label>
            <input type="text" name="nombre" class="input" value="{{ Auth::user()->nombre }}" required>
        </div>

        <div>
            <label class="font-semibold">Correo</label>
            <input type="email" name="correo" class="input" value="{{ Auth::user()->correo }}" required>
        </div>

        <div>
            <label class="font-semibold">Nueva contraseña (opcional)</label>
            <input type="password" name="password" class="input">
        </div>

        <button class="w-full bg-blue-600 text-white py-2 rounded-xl">Guardar</button>
    </form>
</div>

<style>
.input {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 8px;
}
</style>

@endsection