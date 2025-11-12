@extends('layouts.base')

@section('title', 'Importar Usuarios Excel')

@section('content')
    <h2 class="text-2xl font-semibold mb-4">📥 Importar Usuarios desde Excel</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.usuario.importar.post') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label for="archivo" class="block font-semibold mb-1">Seleccionar archivo Excel (.xlsx / .xls):</label>
            <input type="file" name="archivo" id="archivo" class="border p-2 rounded w-full" required>
            @error('archivo')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            🚀 Importar Usuarios
        </button>
    </form>
@endsection
