@extends('layouts.base')

@section('title', 'Confirmar asistencia')

@section('content')

<div class="bg-white p-6 rounded-xl shadow w-full max-w-lg text-center">

    <h2 class="text-2xl font-bold mb-4">Escanear QR para confirmar</h2>

    <p class="text-gray-600 mb-4">
        Escanea este código para completar tu asistencia.
    </p>

    <img src="https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl={{ urlencode($url) }}"
         class="mx-auto border rounded-lg shadow">

    <p class="text-gray-500 mt-4">
        Expira: {{ $asistencia->qr_expira }}
    </p>

</div>

@endsection