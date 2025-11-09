@extends('layouts.base')

@section('title', 'Aulas Disponibles')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold mb-3">Aulas libres el {{ ucfirst($dia) }} de {{ $hora_inicio }} a {{ $hora_fin }}</h2>

    @if ($aulasLibres->isEmpty())
        <div class="alert alert-warning">No hay aulas disponibles en ese horario.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nro</th>
                    <th>Capacidad</th>
                    <th>Piso</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($aulasLibres as $aula)
                    <tr>
                        <td>{{ $aula->nro }}</td>
                        <td>{{ $aula->capacidad }}</td>
                        <td>{{ $aula->piso }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection