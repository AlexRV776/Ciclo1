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
            'hora_inicio' => 'required',
            'hora_fin' => 'required'
        ]);

        $fecha = $request->fecha;
        $inicio = $request->hora_inicio;
        $fin = $request->hora_fin;

        // Obtener día de la semana en texto EXACTO igual que en tu BD
        $dia = ucfirst(strtolower(\Carbon\Carbon::parse($fecha)->locale('es')->dayName));

        // ✅ 1. AULAS OCUPADAS POR CLASES (horario_materia)
        $aulasOcupadasClases = HorarioMateria::whereHas('horario', function ($q) use ($dia, $inicio, $fin) {
                $q->where('dia', $dia)
                ->where(function ($c) use ($inicio, $fin) {
                        $c->whereBetween('hora_inicio', [$inicio, $fin])
                        ->orWhereBetween('hora_fin', [$inicio, $fin])
                        ->orWhereRaw('? BETWEEN hora_inicio AND hora_fin', [$inicio]);
                });
            })
            ->pluck('nro'); // aquí tienes el número de aula (campo nro)

        // ✅ 2. AULAS OCUPADAS POR RESERVAS DE OTROS DOCENTES
        $aulasOcupadasReservas = ReservaAula::where('fecha', $fecha)
            ->where('estado', '!=', 'rechazada')
            ->where(function ($q) use ($inicio, $fin) {
                $q->whereBetween('hora_inicio', [$inicio, $fin])
                ->orWhereBetween('hora_fin', [$inicio, $fin])
                ->orWhereRaw('? BETWEEN hora_inicio AND hora_fin', [$inicio]);
            })
            ->pluck('aula_id');

        // ✅ Convertir nro de aula a ID real de aulas disponibles
        $aulasOcupadasIDs = Aula::whereIn('nro', $aulasOcupadasClases)->pluck('nro');

        // ✅ 3. AULAS DISPONIBLES (NO en clase y NO reservadas)
        $aulas = Aula::whereNotIn('nro', $aulasOcupadasIDs)
                    ->whereNotIn('nro', $aulasOcupadasReservas)
                    ->get();

        return view('reservas.disponibles', [
            'aulas' => $aulas,
            'fecha' => $fecha,
            'inicio' => $inicio,
            'fin' => $fin
        ]);
    }
    public function reservar(Request $request)
    {
        $request->validate([
            'aula_id' => 'required|exists:aulas,nro',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
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
    public function confirmar(Request $request)
    {
        $aula = Aula::findOrFail($request->aula_id);

        $usuario= Auth::user();

        return view('reservas.confirmar', [
            'aula' => $aula,
            'fecha' => $request->fecha,
            'inicio' => $request->hora_inicio,
            'fin' => $request->hora_fin,
            'docente' => $usuario
        ]);
    }
}