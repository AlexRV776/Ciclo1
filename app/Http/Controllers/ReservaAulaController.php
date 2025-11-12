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

        $dia = strtolower(Carbon::parse($fecha)->locale('es')->dayName);

        $aulasOcupadasPorClases = HorarioMateria::whereNotNull('nro')
            ->whereHas('horario', function ($q) use ($dia, $inicio, $fin) {
                $q->where('dia', $dia)
                  ->where('hora_inicio', '<', $fin)
                  ->where('hora_fin', '>', $inicio);
            })
            ->pluck('nro')
            ->unique()
            ->values();

        $aulasOcupadasPorReservas = ReservaAula::where('fecha', $fecha)
            ->whereIn('estado', ['pendiente', 'aprobada'])
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('hora_inicio', '<', $fin)
                  ->where('hora_fin', '>', $inicio);
            })
            ->pluck('aula_id')
            ->unique()
            ->values();

        $aulasOcupadas = $aulasOcupadasPorClases
            ->merge($aulasOcupadasPorReservas)
            ->unique()
            ->values();

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

        // Registrar bitácora
        registrarBitacora($usuario, 'reserva_aula', $request, 'Realizó una solicitud de reserva de aula');

        return redirect()->route('reservas.index')
            ->with('success', 'Solicitud enviada correctamente.');
    }

    public function formDisponibles()
    {
        return view('reservas.buscar');
    }

    public function listado()
    {
        $reservas = ReservaAula::with(['aula', 'usuario'])
            ->orderBy('fecha', 'desc')
            ->get();

        foreach ($reservas as $reserva) {
            $reserva->dia = ucfirst(Carbon::parse($reserva->fecha)->locale('es')->dayName);
        }

        return view('reservas.listado', compact('reservas'));
    }
}
