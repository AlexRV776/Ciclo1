@extends('layouts.base')

@section('content')
<div class="container mt-5">
    <h3>📘 Importar Maestros de Oferta</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('notificaciones'))
        <div class="alert alert-warning">
            <strong>Advertencias de carga horaria:</strong>
            <ul>
            @foreach (session('notificaciones') as $msg)
                <li>{{ $msg }}</li>
            @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('oferta.importar.post') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="archivo" class="form-label">Selecciona tu archivo Excel (.xlsx)</label>
            <input type="file" name="archivo" id="archivo" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">📤 Importar</button>
    </form>
</div>
@endsection