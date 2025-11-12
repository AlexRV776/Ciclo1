<?php

namespace App\Http\Controllers;


use App\Models\Asistencia;
use App\Models\HorarioMateria;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;   // ✅ AGREGA ESTO

class AsistenciaController extends Controller
{
    
    public function index()
    {
        $docente = Auth::user()->docente;

        // Obtener el día actual en español (ej: "sábado")
        $diaActual = Carbon::now('America/La_Paz')->locale('es')->dayName;

        // Normalizar: quitar acentos y forzar a minúsculas (sábado → sabado)
        $diaActual = Str::of($diaActual)->ascii()->lower();

        // Trae SOLO horarios del docente en ese día
        $horarios = HorarioMateria::with(['grupoMateria.materia', 'grupoMateria.grupo', 'horario'])
            ->whereHas('grupoMateria', function ($q) use ($docente) {
                $q->where('docente_registro', $docente->registro);
            })
            ->whereHas('horario', function ($q) use ($diaActual) {
                $q->where('dia', $diaActual);
            })
            ->get();

        return view('asistencia.index', compact('horarios'));
    }
    // Nueva función para mostrar opciones de marcado
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

        // Evita marcar doble el mismo día
        if (Asistencia::where('docente_registro', $docente->registro)
            ->where('horario_materia_id', $hm->id)
            ->where('fecha', $hoy)
            ->exists()) 
        {
            return redirect()->route('asistencia.index')->with('error', 'Ya marcaste asistencia para esta clase.');
        }

        // Modalidad: "virtual" o "presencial"
        $modalidad = $request->input('modalidad');

        // ✅ Ambas modalidades pasan directamente a "asistido"
        Asistencia::create([
            'fecha' => $hoy,
            'modalidad' => $modalidad,
            'estado' => 'confirmado',
            'docente_registro' => $docente->registro,
            'horario_materia_id' => $hm->id,
        ]);

        return redirect()->route('asistencia.index')->with('success', 'Asistencia marcada correctamente.');
    }

    // Mostrar formulario para seleccionar fecha
    public function gestionar()
    {
        $materias = Materia::all(); // Traer todas las materias
        return view('asistencia.gestionar', compact('materias')); // Pasar a la vista
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
            $query->whereHas('horarioMateria.grupoMateria.materia', function($q) use ($materia_id) {
                $q->where('id', $materia_id);
            });
        }

        $asistencias = $query->orderBy('estado', 'asc')->get();

        $materias = Materia::all(); // También pasamos materias aquí para el select
        return view('asistencia.gestionar', compact('asistencias', 'fecha', 'materias', 'materia_id'));
    }

}