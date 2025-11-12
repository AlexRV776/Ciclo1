<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\HorarioMateria;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AsistenciaController extends Controller
{
    public function index()
    {
        $docente = Auth::user()->docente;

        $diaActual = Carbon::now('America/La_Paz')->locale('es')->dayName;
        $diaActual = Str::of($diaActual)->ascii()->lower();

        $horarios = HorarioMateria::with(['grupoMateria.materia', 'grupoMateria.grupo', 'horario'])
            ->whereHas('grupoMateria', function ($q) use ($docente) {
                $q->where('docente_registro', $docente->registro);
            })
            ->whereHas('horario', function ($q) use ($diaActual) {
                $q->where('dia', $diaActual);
            })
            ->get();

        //Registrar en bitácora
        registrarBitacora(Auth::user(), 'ver_asistencia', request(), 'Ingresó al listado de asistencias.');

        return view('asistencia.index', compact('horarios'));
    }

    public function marcar($id)
    {
        $hm = HorarioMateria::with(['grupoMateria.materia', 'horario'])->findOrFail($id);
        return view('asistencia.marcar', compact('hm'));
    }

    public function guardar(Request $request, $id)
    {
        $hm = HorarioMateria::findOrFail($id);
        $docente = Auth::user()->docente;
        $hoy = Carbon::today()->toDateString();

        if (
            Asistencia::where('docente_registro', $docente->registro)
                ->where('horario_materia_id', $hm->id)
                ->where('fecha', $hoy)
                ->exists()
        ) {
            return redirect()->route('asistencia.index')->with('error', 'Ya marcaste asistencia para esta clase.');
        }

        $modalidad = $request->input('modalidad');

        $asistencia = Asistencia::create([
            'fecha' => $hoy,
            'modalidad' => $modalidad,
            'estado' => 'confirmado',
            'docente_registro' => $docente->registro,
            'horario_materia_id' => $hm->id,
        ]);

        //Registrar en bitácora
        registrarBitacora(Auth::user(), 'marcar_asistencia', $request, "Marcó asistencia del docente {$docente->registro} en modalidad {$modalidad} para el horario ID {$hm->id}");

        return redirect()->route('asistencia.index')->with('success', 'Asistencia marcada correctamente.');
    }

    public function gestionar()
    {
        $materias = Materia::all();

        //Registrar en bitácora
        registrarBitacora(Auth::user(), 'gestionar_asistencias', request(), 'Ingresó al módulo de gestión de asistencias.');

        return view('asistencia.gestionar', compact('materias'));
    }

    public function filtrar(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'materia_id' => 'nullable|exists:materias,id'
        ]);

        $fecha = $request->fecha;
        $materia_id = $request->materia_id;

        $query = Asistencia::with(['docente.usuario', 'horarioMateria.grupoMateria.materia'])
            ->where('fecha', $fecha);

        if ($materia_id) {
            $query->whereHas('horarioMateria.grupoMateria.materia', function ($q) use ($materia_id) {
                $q->where('id', $materia_id);
            });
        }

        $asistencias = $query->orderBy('estado', 'asc')->get();
        $materias = Materia::all();

        return view('asistencia.gestionar', compact('asistencias', 'fecha', 'materias', 'materia_id'));
    }
}
