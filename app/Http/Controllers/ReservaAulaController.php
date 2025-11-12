<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\HorarioMateria;
use App\Models\ReservaAula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservaAulaController extends Controller
{
    public function index()
    {
        $docente = Auth::user()->docente;
        return view('reservas.index', compact('docente'));
    }

    public function disponibles(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $fecha = $request->fecha;
        $inicio = $request->hora_inicio;
        $fin = $request->hora_fin;

        // Día en lowercase, tal como tienes en la BD
        $dia = strtolower(Carbon::parse($fecha)->locale('es')->dayName);

        // 1) Aulas ocupadas por clases (horario_materia) — extraemos el campo 'nro'
        $aulasOcupadasPorClases = HorarioMateria::whereNotNull('nro')
            ->whereHas('horario', function ($q) use ($dia, $inicio, $fin) {
                $q->where('dia', $dia)
                  ->where('hora_inicio', '<', $fin)
                  ->where('hora_fin', '>', $inicio);
            })
            ->pluck('nro')
            ->unique()
            ->values(); // colección de números de aula

        // 2) Aulas ocupadas por reservas en la misma fecha y horario
        $aulasOcupadasPorReservas = ReservaAula::where('fecha', $fecha)
            ->whereIn('estado', ['pendiente', 'aprobada']) // considerar solo reservas activas
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('hora_inicio', '>', $fin)
                  ->where('hora_inicio', '<', $fin)
                  ->where('hora_fin', '>', $inicio)
                  ->where('hora_fin', '<', $inicio);
            })
            ->pluck('aula_id')
            ->unique()
            ->values();

        // 3) Combinar ambos conjuntos de aulas ocupadas
        $aulasOcupadas = $aulasOcupadasPorClases->merge($aulasOcupadasPorReservas)->unique()->values();

        // 4) Recuperar aulas que NO están ocupadas
        $aulasLibres = Aula::whereNotIn('nro', $aulasOcupadas)
            ->orderBy('nro')
            ->get();

        return view('reservas.disponibles', [
            'aulas' => $aulasLibres,
            'fecha' => $fecha,
            'inicio' => $inicio,
            'fin' => $fin
        ]);
    }

    public function confirmar(Request $request)
    {
        $aula = Aula::findOrFail($request->aula_id);
        $usuario = Auth::user();

        return view('reservas.confirmar', [
            'aula' => $aula,
            'fecha' => $request->fecha,
            'inicio' => $request->hora_inicio,
            'fin' => $request->hora_fin,
            'docente' => $usuario
        ]);
    }

    public function reservar(Request $request)
    {
        $request->validate([
            'aula_id' => 'required|exists:aulas,nro',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $usuario = Auth::user();

        ReservaAula::create([
            'usuario_registro' => $usuario->registro,
            'aula_id' => $request->aula_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'estado' => 'pendiente'
        ]);

        return redirect()->route('reservas.index')
            ->with('success', 'Solicitud enviada correctamente.');
    }

    public function formDisponibles()
    {
        return view('reservas.buscar');
    }

    public function listado()
    {
        $reservas = \App\Models\ReservaAula::with(['aula', 'usuario'])
            ->orderBy('fecha', 'desc')
            ->get();

        foreach ($reservas as $reserva) {
            $reserva->dia = ucfirst(Carbon::parse($reserva->fecha)->locale('es')->dayName);
        }

        return view('reservas.listado', compact('reservas'));
    }
}