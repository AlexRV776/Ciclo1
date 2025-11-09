<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Aula;
use App\Models\HorarioMateria;
use App\Models\ReservaAula;

class AulaController extends Controller
{
    // Listar aulas
    public function index()
    {
        $aulas = Aula::all();
        return view('admin.aulas.index', compact('aulas'));
    }

    // Mostrar formulario para crear
    public function create()
    {
        return view('admin.aulas.create');
    }

    // Guardar nueva aula
    public function store(Request $request)
    {
        $request->validate([
            'nro' => 'required|integer|unique:aulas,nro',
            'capacidad' => 'required|integer|min:1',
            'piso' => 'required|integer',
        ]);

        Aula::create($request->all());

        return redirect()->route('admin.aulas.index')->with('success', 'Aula creada correctamente.');
    }

    // Mostrar formulario para editar
    public function edit($nro)
    {
        $aula = Aula::findOrFail($nro);
        return view('admin.aulas.edit', compact('aula'));
    }

    // Actualizar aula
    public function update(Request $request, $nro)
    {
        $aula = Aula::findOrFail($nro);

        $request->validate([
            'capacidad' => 'required|integer|min:1',
            'piso' => 'required|integer',
        ]);

        $aula->update($request->all());

        return redirect()->route('admin.aulas.index')->with('success', 'Aula actualizada correctamente.');
    }

    // Eliminar aula
    public function destroy($nro)
    {
        $aula = Aula::findOrFail($nro);
        $aula->delete();

        return redirect()->route('admin.aulas.index')->with('success', 'Aula eliminada correctamente.');
    }

    public function disponibles(Request $request)
    {
        $dia = $request->input('dia', 'Lunes');
        $hora_inicio = $request->input('hora_inicio', '08:00:00');
        $hora_fin = $request->input('hora_fin', '09:00:00');

        $aulasOcupadasHM = HorarioMateria::whereHas('horario', function($q) use ($dia, $hora_inicio, $hora_fin) {
            $q->where('dia', $dia)
            ->where(function($query) use ($hora_inicio, $hora_fin) {
                $query->whereBetween('hora_inicio', [$hora_inicio, $hora_fin])
                        ->orWhereBetween('hora_fin', [$hora_inicio, $hora_fin]);
            });
        })->pluck('nro');

        $aulasOcupadasReserva = ReservaAula::where('dia', $dia)
            ->where('estado', '!=', 'rechazado')
            ->where(function($query) use ($hora_inicio, $hora_fin) {
                $query->whereBetween('hora_inicio', [$hora_inicio, $hora_fin])
                    ->orWhereBetween('hora_fin', [$hora_inicio, $hora_fin]);
            })
            ->pluck('aula_nro');

        $aulasLibres = Aula::whereNotIn('nro', $aulasOcupadasHM)
            ->whereNotIn('nro', $aulasOcupadasReserva)
            ->get();

        return view('admin.aulas.disponibles', compact('aulasLibres', 'dia', 'hora_inicio', 'hora_fin'));
    }
    public function aulasLibres(Request $request)
    {
        $dia = $request->input('dia', 'Lunes');
        $hora_inicio = $request->input('hora_inicio', '10:00:00');
        $hora_fin = $request->input('hora_fin', '11:00:00');

        $aulasOcupadasHM = HorarioMateria::whereHas('horario', function($q) use ($dia, $hora_inicio, $hora_fin) {
            $q->where('dia', $dia)
            ->where(function($query) use ($hora_inicio, $hora_fin) {
                $query->whereBetween('hora_inicio', [$hora_inicio, $hora_fin])
                        ->orWhereBetween('hora_fin', [$hora_inicio, $hora_fin]);
            });
        })->pluck('nro');

        $aulasOcupadasReserva = ReservaAula::where('dia', $dia)
            ->where('estado', '!=', 'rechazado')
            ->where(function($query) use ($hora_inicio, $hora_fin) {
                $query->whereBetween('hora_inicio', [$hora_inicio, $hora_fin])
                    ->orWhereBetween('hora_fin', [$hora_inicio, $hora_fin]);
            })
            ->pluck('aula_nro');

        $aulasLibres = Aula::whereNotIn('nro', $aulasOcupadasHM)
            ->whereNotIn('nro', $aulasOcupadasReserva)
            ->get();

        return view('admin.aulas.libres', compact('aulasLibres', 'dia', 'hora_inicio', 'hora_fin'));
    }
}