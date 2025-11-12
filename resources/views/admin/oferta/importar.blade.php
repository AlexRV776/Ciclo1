@extends('layouts.base')

@section('content')
<div class="container mt-5">
    <h2>Importar Oferta Académica</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('oferta.importar') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="archivo" class="form-label">Archivo Excel (.xlsx)</label>
            <input type="file" name="archivo" id="archivo" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Importar</button>
    </form>
</div>
@endsection